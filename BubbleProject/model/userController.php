<?php

class Users
{
    private $userData;

    const saltPW = "saltedPW";


    public function __construct($configFile) // $configFile declared in header.php
    {
        if ($ini = parse_ini_file($configFile)) {
            $userDB = new PDO(
                "mysql:host=" . $ini['servername'] .
                ";port=" . $ini['port'] .
                ";dbname=" . $ini['dbname'],
                $ini['username'],
                $ini['password']
            );

            $userDB->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $userDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->userData = $userDB;
        } else {
            throw new Exception("<h2>Creation of database object failed!</h2>", 0, null);
        }
    }


    public function userSignup($userName, $PW, $userInnie, $userBio, $hasBubbleCode, $bubbleCode = null, $bubbleName = null)
    {
        $isUserAdded = false;
        $userTable = $this->userData;
        $salt = random_bytes(32);

        try {
            // Begin a transaction
            $userTable->beginTransaction();

            // Insert the new user into bubble_users
            $stmt = $userTable->prepare("INSERT INTO bubble_users 
                                         SET userName = :uName, userPW = :uPW, userSalt = :uSalt, 
                                             userInnie = :uInnie, userBio = :uBio, userJoined = NOW()");

            $bindParameters = array(
                ":uName" => $userName,
                ":uPW" => sha1($salt . $PW),
                ":uSalt" => $salt,
                ":uInnie" => $userInnie,
                ":uBio" => $userBio
            );

            $isUserAdded = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);

            if (!$isUserAdded) {
                $userTable->rollBack();
                return false;
            }

            // Get the newly inserted user ID
            $userID = $userTable->lastInsertId();

            if ($hasBubbleCode) {
                // User has a Bubble code, find the bubbleID and assign it to the user
                $stmt = $userTable->prepare("SELECT bubbleID FROM bubbles WHERE bubbleCode = :bubbleCode");
                $stmt->bindParam(':bubbleCode', $bubbleCode);
                $stmt->execute();

                $bubbleID = $stmt->fetchColumn();

                if (!$bubbleID) {
                    // If no BubbleID is found, rollback
                    echo ("Could not find bubble");
                    $userTable->rollBack();
                    return false;
                }
            } else {
                // User doesn't have a Bubble code, insert the new bubble and get the bubbleID
                $stmt = $userTable->prepare("INSERT INTO Bubbles (bubbleCode, bubbleName, userID) 
                                             VALUES (:bubbleCode, :bubbleName, :userID)");
                $stmt->execute(
                    array(
                        ':bubbleCode' => $bubbleCode,
                        ':bubbleName' => $bubbleName,
                        ':userID' => $userID
                    )
                );

                if ($stmt->rowCount() > 0) {
                    $bubbleID = $userTable->lastInsertId();
                } else {
                    // If insertion fails, rollback
                    $userTable->rollBack();
                    return false;
                }

                if ($stmt->rowCount() > 0) {
                    $bubbleID = $userTable->lastInsertId();

                    // Update isModerator field in bubble_users
                    $stmt = $userTable->prepare("UPDATE bubble_users SET isModerator = 'YES' WHERE userID = :userID");
                    $stmt->execute(array(':userID' => $userID));

                } else {
                    // If insertion fails, rollback
                    $userTable->rollBack();
                    return false;
                }

            }

            // Update bubble_users with the bubbleID
            $stmt = $userTable->prepare("UPDATE bubble_users SET bubbleID = :bubbleID WHERE userID = :userID");
            $stmt->execute(array(':bubbleID' => $bubbleID, ':userID' => $userID));

            // Commit the transaction
            $userTable->commit();

            return true;
        } catch (Exception $e) {
            // Rollback on error
            $userTable->rollBack();
            throw $e;
        }
    }

    // compare the signed up userInnie to ones store in the db. If the userInniq exists already, an error will show in signup.php.
    function userUniqueInnie($userInnie)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT count(*) FROM bubble_users WHERE userInnie=:userInnie");


        $stmt->bindParam(
            ':userInnie',
            $userInnie
        );

        $stmt->execute();
        $number_of_rows = $stmt->fetchColumn();
        if ($number_of_rows > 0) {
            return false;
        } else {
            return true;
        }
    }

    // compare the signed up userName to ones store in the db. If the userName exists already, an error will show in signup.php.
    function userUniqueUN($userName)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT count(*) FROM bubble_users WHERE userName=:userName");


        $stmt->bindParam(
            ':userName',
            $userName
        );

        $stmt->execute();
        $number_of_rows = $stmt->fetchColumn();
        if ($number_of_rows > 0) {
            return false;
        } else {
            return true;
        }
    }


    public function getDatabaseRef()
    {
        return $this->userData;
    }


    // compare login info to the db. If the information is correct, allow login.
    public function isUserTrue($userName, $PW)
    {
        $isUserTrue = false;
        $userTable = $this->userData;

        $stmt = $userTable->prepare("SELECT userPW, userSalt FROM bubble_users WHERE userName =:userName");

        $stmt->bindValue(':userName', $userName);

        $ifUserFound = ($stmt->execute() && $stmt->rowCount() > 0);

        if ($ifUserFound) {
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            $hashPW = sha1($results['userSalt'] . $PW);
            $isUserTrue = ($hashPW == $results['userPW']);
        }
        return $isUserTrue;
    }

    // get's user details by their userInnie. (acceptable since innie is a unique identifer)
    public function getProfileByName($profileName)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT * FROM bubble_users WHERE userInnie = :profileName");
        $bindParameters = array(":profileName" => $profileName);

        if ($stmt->execute($bindParameters) && $stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // gets user details by the userID including Group Name by joining the two tables.
    public function getUserDetails($userID)
    {
        $userTable = $this->userData;

        // Query to get user details along with the group name and all columns from the bubbles table
        $stmt = $userTable->prepare("
            SELECT pu.*, bg.groupName, b.bubbleCode,b.bubbleName, b.bubbleID 
            FROM bubble_users pu
            LEFT JOIN bubble_groups bg ON pu.groupID = bg.groupID
            LEFT JOIN bubbles b ON pu.bubbleID = b.bubbleID
            WHERE pu.userID = :userID
        ");

        $stmt->bindParam(':userID', $userID);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserBubbleID($userID)
    {
        $userTable = $this->userData;

        $stmt = $userTable->prepare("SELECT bubbleID FROM bubble_users WHERE userID = :userID");
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();

        // Fetch bubbleID directly as a scalar value
        return $stmt->fetchColumn();
    }

    public function getVisitingUserBubbleID($userID)
    {
        $userTable = $this->userData;

        $stmt = $userTable->prepare("SELECT bubbleID FROM bubble_users WHERE userID = :userID");
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();

        // Fetch bubbleID directly as a scalar value
        return $stmt->fetchColumn();
    }


    // Get all users from the db. 
    public function getAllUsers($userID)
    {
        $userTable = $this->userData;

        // Retrieve the bubbleID for the given userID
        $stmt = $userTable->prepare("SELECT bubbleID FROM bubble_users WHERE userID = :userID");
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $bubbleID = $stmt->fetchColumn();

        if ($bubbleID) {
            // Select all users associated with the bubbleID
            $stmt = $userTable->prepare("SELECT userInnie FROM bubble_users WHERE bubbleID = :bubbleID ORDER BY userInnie ASC");

            $stmt->bindParam(':bubbleID', $bubbleID);
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        return false;
    }


    public function getUsersByGroup($groupName)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT userInnie FROM bubble_users WHERE groupName = :groupName ORDER BY userInnie ASC");
        $stmt->bindParam(':groupName', $groupName, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // modTools.php function, used to update userInnie.
    // IMPORTANT NOTE: WHERE userInnie = :oldInnie AND isOwner ='NO'
    // isOwner is plugin's profile. Mods can not update the userInnie of an owner. (they are of higher clearance).
    // same logic can be used to create elevated roles, such as 'admins'. 

    public function getSelectedUsersID($selectedUserID)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT * FROM bubble_users WHERE userID = :selectedUserID");
        $bindParameters = array(":selectedUserID" => $selectedUserID);

        if ($stmt->execute($bindParameters) && $stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function modUpdateUser($userID, $newInnie, $oldInnie, $groupID)
    {
        $isInnieUpdated = false;
        $userTable = $this->userData;

        // Retrieve the bubbleID for the given userID
        $stmt = $userTable->prepare("SELECT bubbleID FROM bubble_users WHERE userID = :userID AND isModerator = 'YES'");
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $bubbleID = $stmt->fetchColumn();

        if ($bubbleID) {
            // Update the userInnie and groupID where the bubbleID matches and isOwner is 'NO'
            $stmt = $userTable->prepare("UPDATE bubble_users SET userInnie = :newInnie, groupID = :groupID 
            WHERE userInnie = :oldInnie AND bubbleID = :bubbleID AND isOwner = 'NO'");

            $bindParameters = array(
                ":newInnie" => $newInnie,
                ":oldInnie" => $oldInnie,
                ":bubbleID" => $bubbleID,
                ":groupID" => $groupID
            );

            $isInnieUpdated = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        }

        return $isInnieUpdated;
    }


    // modTools.php function, used to update categories.
    public function modUpdateCat($newCat, $oldCat)
    {
        $isCatUpdated = false;
        $userTable = $this->userData;

        $stmt = $userTable->prepare("UPDATE plugin_categories SET catGenre = :newCat 
        WHERE catGenre = :oldCat");

        $bindParameters = array(
            ":newCat" => $newCat,
            ":oldCat" => $oldCat
        );

        $isCatUpdated = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        return ($isCatUpdated);
    }

    public function modUpdateGroup($userID, $newGroupName, $oldGroupName)
    {
        $isGroupUpdated = false;
        $userTable = $this->userData;

        // Retrieve the bubbleID for the given userID, but only if the user is a moderator
        $stmt = $userTable->prepare("SELECT bubbleID FROM bubble_users WHERE userID = :userID AND isModerator = 'YES'");
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $bubbleID = $stmt->fetchColumn();

        if ($bubbleID) {
            // Update the group name with the bubbleID
            $stmt = $userTable->prepare("UPDATE bubble_groups SET groupName = :newGroupName 
            WHERE groupName = :oldGroupName AND bubbleID = :bubbleID");

            $bindParameters = array(
                ":newGroupName" => $newGroupName,
                ":oldGroupName" => $oldGroupName,
                ":bubbleID" => $bubbleID
            );

            $isGroupUpdated = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        }

        return $isGroupUpdated;
    }

    // modTools.php function, used to update conditions.
    public function modUpdateCond($newCond, $oldCond)
    {
        $isCondUpdated = false;
        $userTable = $this->userData;

        $stmt = $userTable->prepare("UPDATE plugin_conditions SET condType = :newCond 
        WHERE condType = :oldCond");

        $bindParameters = array(
            ":newCond" => $newCond,
            ":oldCond" => $oldCond
        );

        $isCondUpdated = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        return ($isCondUpdated);
    }

    // modTools.php function, used to delete conditions.
    public function modDeleteCond($inputCond)
    {
        $isCondDeleted = false;
        $userTable = $this->userData;

        $stmt = $userTable->prepare("DELETE FROM plugin_conditions WHERE condType = :inputCond");

        $bindParameters = array(
            ":inputCond" => $inputCond
        );

        $isCondDeleted = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        return ($isCondDeleted);
    }

    // modTools.php function, used to delete user accounts.
    public function modDeleteUser($userID, $inputInnie)
    {
        $userTable = $this->userData;

        // Retrieve the bubbleID for the given userID
        $stmt = $userTable->prepare("SELECT bubbleID FROM bubble_users WHERE userID = :userID AND isModerator = 'YES'");
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $bubbleID = $stmt->fetchColumn();

        if ($bubbleID) {
            // Delete the user where the bubbleID matches and isOwner is 'NO'
            $stmt = $userTable->prepare("DELETE FROM bubble_users WHERE userInnie = :inputInnie AND bubbleID = :bubbleID AND isOwner = 'NO'");

            $bindParameters = array(
                ":inputInnie" => $inputInnie,
                ":bubbleID" => $bubbleID
            );

            if ($stmt->execute($bindParameters) && $stmt->rowCount() > 0) {
                return true;
            }
        }

        return false;
    }


    // Get's the userID from the logged in session['userID']. Where this function is called, it checks if isModerator =='YES'. 
    public function isUserMod($sessionID)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT * FROM bubble_users WHERE userID = :sessionID");
        $bindParameters = array(":sessionID" => $sessionID);

        if ($stmt->execute($bindParameters) && $stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Get's the userID from the logged in session['userID']. Where this function is called, it checks if isModerator =='YES'. 
    public function headerModCheck($sessionID)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT * FROM bubble_users WHERE userID = :sessionID");
        $bindParameters = array(":sessionID" => $sessionID);

        if ($stmt->execute($bindParameters) && $stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user['isModerator'] == 'YES') {
                return true;
            }
        }
        return false;
    }


    // Get's the details of a customer for a transaction. (Customer may be synonymous as 'receiver' in functionality)
    public function getCustomerDetails($senderID)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT * FROM bubble_users WHERE userID = :senderID");
        $bindParameters = array(":senderID" => $senderID);

        if ($stmt->execute($bindParameters) && $stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // gets the userID where the userName is a match. Acceptable since userNames are also unique identifiers. 
    public function getUserId($username)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT userID FROM bubble_users WHERE userName = :username");
        $bindParameters = array(":username" => $username);
        $stmt->execute($bindParameters);
        $user = $stmt->fetch();
        return $user['userID'];
    }

    // gets the userID where the userInnie is a match. Acceptable since userInnie are also unique identifiers. 
    public function getUserIdByInnie($userInnie)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT userID FROM bubble_users WHERE userInnie = :userInnie");
        $bindParameters = array(":userInnie" => $userInnie);


        $stmt->execute($bindParameters);
        return $stmt->fetch();
    }


    // Why those functions over the general getUserDetails? I personally like it when code is more readable/understandable 
    // to the situation. I dont mind re-doing a method that gatheres the same data, but in a more specific way to whats needed. 
    // for me readability > minimalism. 


    // Updates the user's database information when their userID is a match.
    public function updateProfile($userName, $userInnie, $userBio, $userID, $isModerator)
    {

        $userTable = $this->userData;
        $stmt = $userTable->prepare("UPDATE bubble_users SET userName = :uName, userInnie = :uInnie, 
        userBio = :uBio, isModerator=:isModerator
        WHERE userID = :userID");

        $bindParameters = array(
            ":uName" => $userName,
            ":uInnie" => $userInnie,
            ":uBio" => $userBio,
            ":userID" => $userID,
            ":isModerator" => $isModerator
        );

        return $stmt->execute($bindParameters);
    }

    // Update the user's team code -> relates to all things they see as apart of the team.
    public function updateTeamCode($userID, $userBubbleCode)
    {

        $userTable = $this->userData;
        $stmt = $userTable->prepare("UPDATE bubble_users SET userBubbleCode = :uTeamCode
        WHERE userID = :userID");

        $bindParameters = array(
            ":userID" => $userID,
            ":uTeamCode" => $userBubbleCode,
        );

        return $stmt->execute($bindParameters);
    }

    // User can delete their accounts. UserID must match the logged in $_Session['userID']
    public function deleteAccount($sessionID)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("DELETE FROM bubble_users WHERE userID = :sessionID");
        $bindParameters = array(":sessionID" => $sessionID);

        if ($stmt->execute($bindParameters) && $stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
    // Whent the user delete's their account, delete all listings tied to their account.
    public function deleteAccountListings($sessionID)
    {
        $isListingDeleted = false;
        $userTable = $this->userData;
        $stmt = $userTable->prepare("DELETE FROM bubble_tasks WHERE userID = :sessionID");
        $bindParameters = array(":sessionID" => $sessionID);

        $isListingDeleted = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        return ($isListingDeleted);
    }

    // Allows user to update their password.
    public function updatePW($userPW, $userID)
    {
        $userTable = $this->userData;

        $salt = random_bytes(32);
        $hashedPW = sha1($salt . $userPW);
        $stmt = $userTable->prepare("UPDATE bubble_users SET userPW = :uPW, userSalt = :uSalt WHERE userID = :userID");
        $bindParameters = array(
            ":uPW" => $hashedPW,
            ":uSalt" => $salt,
            ":userID" => $userID,
        );

        return $stmt->execute($bindParameters);
    }

    // Allows user to update their profile picture.
    public function updatePP($fileDestination, $userID)
    {
        $isUpdated = false;
        $userTable = $this->userData;

        $stmt = $userTable->prepare("UPDATE bubble_users SET userPic = :fileDestination WHERE userID = :userID");

        $bindParameters = array(

            ":fileDestination" => $fileDestination,
            ":userID" => $userID
        );

        $isUpdated = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        return ($isUpdated);
    }

    // user for the search functionality. Finds a user by their userInnie. (again -> unique identifier)
    public function findUserByInnie($userID, $userInnie)
    {
        $results = array();
        $binds = array();
        $isFirstClause = true;
        $userTable = $this->userData;

        // Retrieve the bubbleID for the given userID
        $stmt = $userTable->prepare("SELECT bubbleID FROM bubble_users WHERE userID = :userID");
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $bubbleID = $stmt->fetchColumn();

        if ($bubbleID) {
            // Start constructing the SQL query
            $sql = "SELECT userID, userName, userInnie, userBio, userPic FROM bubble_users WHERE bubbleID = :bubbleID";

            // Add search criteria
            if (!empty($userInnie)) {
                $sql .= " AND userInnie LIKE :userInnie";
                $binds['userInnie'] = '%' . $userInnie . '%';
            }

            // Bind bubbleID
            $binds['bubbleID'] = $bubbleID;

            $sql .= " ORDER BY userInnie";

            // Prepare and execute the query
            $stmt = $userTable->prepare($sql);

            if ($stmt->execute($binds) && $stmt->rowCount() > 0) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        return $results;
    }


    #######################################################################################
    #######################################################################################
    ################# -- BEGINNING OF USER LISTINGS (MAIN)-- ##########################
    #######################################################################################
    #######################################################################################


    // functions are self explanitory here :D 
    public function getAllCategories()
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT catGenre FROM plugin_categories ORDER BY catGenre ASC");

        $stmt->execute();
        return $stmt->fetchAll();
    }


    // Get a list of all the groups. RESTRICT the query to groups specific to the user's own Bubble.
    public function getAllGroups($userID)
    {
        $userTable = $this->userData;

        // Retrieve the bubbleID for the given userID
        $stmt = $userTable->prepare("SELECT bubbleID FROM bubble_users WHERE userID = :userID");
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $bubbleID = $stmt->fetchColumn();

        if ($bubbleID) {
            // Select group names associated with the bubbleID
            $stmt = $userTable->prepare("SELECT * FROM bubble_groups WHERE bubbleID = :bubbleID ORDER BY groupName ASC");

            $stmt->bindParam(':bubbleID', $bubbleID);
            $stmt->execute();
            return $stmt->fetchAll();
        }

        // Return an empty array if no bubbleID is found or the user is not a moderator
        return [];
    }


    public function getAllConditions()
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT condType FROM plugin_conditions");

        $stmt->execute();
        return $stmt->fetchAll();
    }


    public function getAllListings($userID)
    {
        $userTable = $this->userData;

        // Retrieve the user's bubbleID, groupID, and isModerator status
        $stmt = $userTable->prepare("SELECT bubbleID, groupID, isModerator FROM bubble_users WHERE userID = :userID");
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userDetails) {
            $bubbleID = $userDetails['bubbleID'];
            $groupID = $userDetails['groupID'];
            $isModerator = $userDetails['isModerator'];

            if ($isModerator === 'YES') {
                // If the user is a moderator, retrieve all listings related to the bubbleID
                $stmt = $userTable->prepare("SELECT * FROM bubble_tasks WHERE bubbleID = :bubbleID ORDER BY timeTaskDue DESC");
                $stmt->bindParam(':bubbleID', $bubbleID);
            } else {
                // If the user is not a moderator, retrieve listings related to the groupID
                $stmt = $userTable->prepare("SELECT * FROM bubble_tasks WHERE groupID = :groupID AND bubbleID = :bubbleID ORDER BY timeTaskDue DESC");
                $stmt->bindParam(':groupID', $groupID);
                $stmt->bindParam(':bubbleID', $bubbleID);
            }

            $stmt->execute();
            return $stmt->fetchAll();
        }

        return false;
    }


    // for modTools.php


    public function modDeleteGroup($userID, $inputGroup)
    {
        $isGroupDeleted = false;
        $userTable = $this->userData;
    
        try {
            // Begin a transaction
            $userTable->beginTransaction();
    
            // Retrieve the bubbleID for the given userID (ensure the user is a moderator)
            $stmt = $userTable->prepare("SELECT bubbleID FROM bubble_users WHERE userID = :userID AND isModerator = 'YES'");
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();
            $bubbleID = $stmt->fetchColumn();
    
            if ($bubbleID) {
                // Retrieve the groupID for the group we want to delete
                $stmt = $userTable->prepare("SELECT groupID FROM bubble_groups WHERE groupName = :inputGroup AND bubbleID = :bubbleID");
                $stmt->execute(array(':inputGroup' => $inputGroup, ':bubbleID' => $bubbleID));
                $groupID = $stmt->fetchColumn();
    
                if ($groupID) {
                    // Delete the group associated with the bubbleID
                    $stmt = $userTable->prepare("DELETE FROM bubble_groups WHERE groupID = :groupID AND bubbleID = :bubbleID");
                    $stmt->execute(array(':groupID' => $groupID, ':bubbleID' => $bubbleID));
    
                    if ($stmt->rowCount() > 0) {
                        // Now delete all tasks assigned to this groupID in the bubble_tasks table
                        $stmt = $userTable->prepare("DELETE FROM bubble_tasks WHERE groupID = :groupID");
                        $stmt->execute(array(':groupID' => $groupID));
    
                        // Commit the transaction after deleting both the group and its tasks
                        $userTable->commit();
                        $isGroupDeleted = true;
                    } else {
                        // Rollback if group deletion failed
                        $userTable->rollBack();
                    }
                } else {
                    // Rollback if no groupID found
                    $userTable->rollBack();
                }
            }
        } catch (Exception $e) {
            // Rollback on any error
            $userTable->rollBack();
            throw $e;
        }
    
        return $isGroupDeleted;
    }
    
    

    // for modTools.php
    public function modNewCat($inputCat)
    {
        $isCatPosted = false;
        $userTable = $this->userData;

        $stmt = $userTable->prepare("INSERT INTO plugin_categories SET catGenre = :inputCat");

        $bindParameters = array(
            ":inputCat" => $inputCat,
        );

        $isCatPosted = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        return ($isCatPosted);
    }

    // Create a new group.
    public function modNewGroup($userID, $inputGroup)
    {
        $isGroupPosted = false;
        $userTable = $this->userData;

        // Retrieve the bubbleID for the given userID, but only if the user is a moderator
        $stmt = $userTable->prepare("SELECT bubbleID FROM bubble_users WHERE userID = :userID AND isModerator = 'YES'");
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $bubbleID = $stmt->fetchColumn();

        if ($bubbleID) {
            // Generate a unique random pastel HEX color, loop until it finds a unique one
            do {
                // Generate RGB components with a preference for pastel tones
                $r = mt_rand(130, 230); // Red component
                $g = mt_rand(130, 230); // Green component
                $b = mt_rand(130, 230); // Blue component

                // Convert RGB to HEX
                $groupColor = sprintf("#%02X%02X%02X", $r, $g, $b);

                // Check if the generated color already exists in the bubble
                $stmt = $userTable->prepare("SELECT COUNT(*) FROM bubble_groups WHERE bubbleID = :bubbleID AND groupColor = :groupColor");
                $stmt->bindParam(':bubbleID', $bubbleID);
                $stmt->bindParam(':groupColor', $groupColor);
                $stmt->execute();
                $colorExists = $stmt->fetchColumn();
            } while ($colorExists > 0);

            // Insert the new group with the bubbleID and unique groupColor
            $stmt = $userTable->prepare("INSERT INTO bubble_groups (groupName, bubbleID, groupColor) VALUES (:inputGroup, :bubbleID, :groupColor)");

            $bindParameters = array(
                ":inputGroup" => $inputGroup,
                ":bubbleID" => $bubbleID,
                ":groupColor" => $groupColor
            );

            $isGroupPosted = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        }


        return $isGroupPosted;
    }




    // for modTools.php
    public function modNewCond($inputCond)
    {
        $iCondPosted = false;
        $userTable = $this->userData;

        $stmt = $userTable->prepare("INSERT INTO plugin_conditions SET condType = :inputCond");

        $bindParameters = array(
            ":inputCond" => $inputCond,
        );

        $iCondPosted = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        return ($iCondPosted);
    }

    // Important. Take note of how the listing pictures are stored to fileDestinationX.
    // This is the logic for how pictures are stored in most functions. 

    public function postTask(
        $userID,
        $groupID,
        $taskTitle,
        $taskDesc,
        $timeTaskDue
    ) {
        $isListPosted = false;
        $userTable = $this->userData;

        // Retrieve the bubbleID for the given userID
        $stmt = $userTable->prepare("SELECT bubbleID FROM bubble_users WHERE userID = :userID");
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $bubbleID = $stmt->fetchColumn();

        if ($bubbleID) {
            // Insert the new task with bubbleID
            $stmt = $userTable->prepare("INSERT INTO bubble_tasks 
                (userID, bubbleID, groupID, taskTitle, taskDesc, taskPostedOn, timeTaskDue) 
                VALUES (:userID, :bubbleID, :groupID, :taskTitle, :taskDesc,NOW(), :timeTaskDue)");

            $bindParameters = array(
                ":userID" => $userID,
                ":bubbleID" => $bubbleID,
                ":groupID" => $groupID,
                ":taskTitle" => $taskTitle,
                ":taskDesc" => $taskDesc,
                ":timeTaskDue" => $timeTaskDue
            );

            $isListPosted = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        }

        return ($isListPosted);
    }


    public function getGroupIDByName($groupName, $userID)
    {
        $userTable = $this->userData;

        // Retrieve the bubbleID for the given userID
        $stmt = $userTable->prepare("SELECT bubbleID FROM bubble_users WHERE userID = :userID");
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $bubbleID = $stmt->fetchColumn();

        if ($bubbleID) {
            // Retrieve the groupID based on groupName and bubbleID
            $stmt = $userTable->prepare("SELECT groupID FROM bubble_groups WHERE groupName = :groupName AND bubbleID = :bubbleID");
            $stmt->bindParam(':groupName', $groupName);
            $stmt->bindParam(':bubbleID', $bubbleID);
            $stmt->execute();
            return $stmt->fetchColumn();
        }

        return false;
    }



    public function getGroupTasks($groupID)
    {
        $userTable = $this->userData;

        // Prepare a SQL statement that joins bubble_tasks with bubble_groups based on groupID
        $stmt = $userTable->prepare("
            SELECT bt.*, bg.groupName 
            FROM bubble_tasks bt
            LEFT JOIN bubble_groups bg ON bt.groupID = bg.groupID
            WHERE bt.groupID = :groupID
        ");

        // Bind the groupID parameter
        $stmt->bindParam(':groupID', $groupID);

        // Execute the query
        $stmt->execute();

        // Return all results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getAllTasks($userID)
    {
        $userTable = $this->userData;

        // Retrieve the user's bubbleID, groupID, and isModerator status
        $stmt = $userTable->prepare("SELECT bubbleID, groupID, isModerator FROM bubble_users WHERE userID = :userID ");
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userDetails) {
            $bubbleID = $userDetails['bubbleID'];
            $groupID = $userDetails['groupID'];
            $isModerator = $userDetails['isModerator'];

            if ($isModerator === 'YES') {
                // If the user is a moderator, retrieve tasks related to the bubbleID
                $sql = "SELECT bt.*, bg.* 
                        FROM bubble_tasks bt
                        LEFT JOIN bubble_groups bg ON bt.groupID = bg.groupID
                        WHERE bt.bubbleID = :bubbleID
                        ORDER BY bt.timeTaskDue ASC";
                $stmt = $userTable->prepare($sql);
                $stmt->bindParam(':bubbleID', $bubbleID);
            } else {
                // If the user is not a moderator, retrieve tasks related to the groupID within the same bubbleID
                $sql = "SELECT bt.*, bg.* 
                        FROM bubble_tasks bt
                        LEFT JOIN bubble_groups bg ON bt.groupID = bg.groupID
                        WHERE bt.groupID = :groupID AND bt.bubbleID = :bubbleID
                        ORDER BY bt.timeTaskDue ASC";
                $stmt = $userTable->prepare($sql);
                $stmt->bindParam(':groupID', $groupID);
                $stmt->bindParam(':bubbleID', $bubbleID);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;
    }

    public function getCompleteTasks($userID)
    {
        $userTable = $this->userData;

        // Retrieve the user's bubbleID, groupID, and isModerator status
        $stmt = $userTable->prepare("SELECT bubbleID, groupID, isModerator FROM bubble_users WHERE userID = :userID");
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userDetails) {
            $bubbleID = $userDetails['bubbleID'];
            $groupID = $userDetails['groupID'];
            $isModerator = $userDetails['isModerator'];

            if ($isModerator === 'YES') {
                // If the user is a moderator, retrieve tasks related to the bubbleID where isTaskDone is 'YES'
                $sql = "SELECT bt.*, bg.* 
                    FROM bubble_tasks bt
                    LEFT JOIN bubble_groups bg ON bt.groupID = bg.groupID
                    WHERE bt.bubbleID = :bubbleID AND bt.isTaskDone = 'YES'
                    ORDER BY bt.timeTaskDue ASC";
                $stmt = $userTable->prepare($sql);
                $stmt->bindParam(':bubbleID', $bubbleID);
            } else {
                // If the user is not a moderator, retrieve tasks related to the groupID within the same bubbleID where isTaskDone is 'YES'
                $sql = "SELECT bt.*, bg.* 
                    FROM bubble_tasks bt
                    LEFT JOIN bubble_groups bg ON bt.groupID = bg.groupID
                    WHERE bt.groupID = :groupID AND bt.bubbleID = :bubbleID AND bt.isTaskDone = 'YES'
                    ORDER BY bt.timeTaskDue ASC";
                $stmt = $userTable->prepare($sql);
                $stmt->bindParam(':groupID', $groupID);
                $stmt->bindParam(':bubbleID', $bubbleID);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;
    }





    public function updateUserListing(
        $taskID,
        $groupID,
        $taskDesc,
        $taskTitle,
        $timeTaskDue
    ) {
        $isListPosted = false;
        $userTable = $this->userData;

        $stmt = $userTable->prepare("UPDATE bubble_tasks SET groupID = :groupID, 
        taskDesc = :taskDesc, taskTitle = :taskTitle, timeTaskDue = :timeTaskDue,
        taskUpdatedOn=NOW() WHERE taskID = :taskID");

        $bindParameters = array(
            ":taskID" => $taskID,
            ":groupID" => $groupID,
            ":taskDesc" => $taskDesc,
            ":taskTitle" => $taskTitle,
            ":timeTaskDue" => $timeTaskDue,
        );

        $isListPosted = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        return ($isListPosted);
    }

    public function markTaskComplete(
        $taskID,
    ) {
        $isListPosted = false;
        $userTable = $this->userData;

        $stmt = $userTable->prepare("UPDATE bubble_tasks SET  
        isTaskDone = 'YES', timeTaskComplete=NOW() WHERE taskID = :taskID");

        $bindParameters = array(
            ":taskID" => $taskID,
        );

        $isListPosted = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        return ($isListPosted);
    }



    public function getListForm($taskID)
    {
        $userTable = $this->userData;

        // Prepare a SQL statement that joins bubble_tasks with bubble_groups to get the groupName
        $stmt = $userTable->prepare("
            SELECT bt.*, bg.groupName 
            FROM bubble_tasks bt
            LEFT JOIN bubble_groups bg ON bt.groupID = bg.groupID
            WHERE bt.taskID = :taskID
        ");

        // Bind the taskID parameter
        $stmt->bindParam(':taskID', $taskID);

        // Execute the query
        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }



    public function deleteUserLising($userID, $taskID)
    {
        $userTable = $this->userData;

        // Retrieve the user's bubbleID and isModerator status
        $stmt = $userTable->prepare("SELECT bubbleID, isModerator FROM bubble_users WHERE userID = :userID");
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userDetails) {
            $userBubbleID = $userDetails['bubbleID'];
            $isModerator = $userDetails['isModerator'];

            if ($isModerator === 'YES') {
                // Retrieve the task's bubbleID to ensure it matches the user's bubbleID
                $stmt = $userTable->prepare("SELECT bubbleID FROM bubble_tasks WHERE taskID = :taskID");
                $stmt->bindParam(':taskID', $taskID);
                $stmt->execute();
                $taskBubbleID = $stmt->fetchColumn();

                if ($taskBubbleID === $userBubbleID) {
                    // Task belongs to the user's bubbleID, proceed with deletion
                    $stmt = $userTable->prepare("DELETE FROM bubble_tasks WHERE taskID = :taskID");
                    $bindParameters = array(":taskID" => $taskID);

                    if ($stmt->execute($bindParameters) && $stmt->rowCount() > 0) {
                        return true; // Return true on successful deletion
                    }
                }
            }
        }
        return false; // Return false if the task couldn't be deleted
    }



    #############################################################
    #############################################################
    ### MESSAGE MANAGEMENT ###

    public function sendMessage(
        $parentID,
        $senderID,
        $receiverID,
        $taskID,
        $messageTitle,
        $messageDesc,
        $senderInnie,
        $receiverInnie,
        $isMessageReplied
    ) {
        $isMsgSent = false;
        $userTable = $this->userData;

        $salt = random_bytes(32);

        $stmt = $userTable->prepare("INSERT INTO bubble_messages SET parentID=:parentID, senderID = :senderID,
        receiverID = :receiverID, taskID = :taskID, messageTitle = :messageTitle, messageDesc = :messageDesc, 
        messageSentOn = NOW(), senderInnie=:senderInnie, receiverInnie=:receiverInnie,
        isMessageReplied=:isMessageReplied, isMemo = 'NO'");

        $bindParameters = array(
            ":parentID" => $parentID,
            ":senderID" => $senderID,
            ":receiverID" => $receiverID,
            ":taskID" => $taskID,
            ":messageTitle" => $messageTitle,
            ":messageDesc" => $messageDesc,
            ":senderInnie" => $senderInnie,
            ":receiverInnie" => $receiverInnie,
            ":isMessageReplied" => $isMessageReplied,

        );

        $isMsgSent = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        return ($isMsgSent);
    }

    public function sendMemo(
        $parentID,
        $senderID,
        $receiverID,
        $messageTitle,
        $messageDesc,
        $senderInnie,
        $receiverInnie,
        $isMessageReplied
    ) {
        $isMsgSent = false;
        $userTable = $this->userData;

        $stmt = $userTable->prepare("INSERT INTO bubble_messages SET parentID=:parentID, senderID = :senderID,
        receiverID = :receiverID, messageTitle = :messageTitle, messageDesc = :messageDesc, 
        messageSentOn = NOW(), senderInnie=:senderInnie, receiverInnie=:receiverInnie,
        isMessageReplied=:isMessageReplied, isMemo = 'YES'");
    
        $bindParameters = array(
            ":parentID" => $parentID,
            ":senderID" => $senderID,
            ":receiverID" => $receiverID,
            ":messageTitle" => $messageTitle,
            ":messageDesc" => $messageDesc,
            ":senderInnie" => $senderInnie,
            ":receiverInnie" => $receiverInnie,
            ":isMessageReplied" => $isMessageReplied,
        );

        $isMsgSent = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        return ($isMsgSent);
    }

    // Every message sent from a request sets isMessageReplied == 'NO'.
    // This allows manipulation of CSS where a message is replied to, or not.
    // When a user replies to a message, this updates the previous isMessageReplied to 'YES'. 
    public function updateIsMessageReplied($priorMessageID, $updateStatus)
    {

        $isMsgSent = false;
        $userTable = $this->userData;

        $stmt = $userTable->prepare("UPDATE bubble_messages SET isMessageReplied = :updateStatus 
        WHERE messageID = :priorMessageID");

        $bindParameters = array(
            ":updateStatus" => $updateStatus,
            ":priorMessageID" => $priorMessageID
        );

        $isMsgSent = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        return ($isMsgSent);
    }
    // note: the cycle is repeated.
    // 1: first message: isMessageReplied == 'NO'.
    // 2: reply message: isMessageReplied == 'YES' FOR THE PREVIOUS messageID (1). isMessageReplied == 'NO' for the new messageID (2).


    // Hides the conversation from everyone involved in the message. 
    // Does NOT delete the message from the database. 
    // In the future, modTools.php for a more elevated admin could retrieve messages if needed (?). 
    public function inboxHideConvo($parentID)
    {

        $isMsgHidden = false;
        $userTable = $this->userData;

        $stmt = $userTable->prepare("UPDATE bubble_messages SET isMessageHidden = 'YES' 
        WHERE parentID = :parentID");

        $bindParameters = array(
            ":parentID" => $parentID
        );

        $isMsgHidden = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        return ($isMsgHidden);
    }



    public function getAllMessages($userID)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT * FROM bubble_messages WHERE (receiverID = :userID AND isMemo = 'NO') ORDER BY messageSentOn DESC");
        $bindParameters = array(
            ":userID" => $userID,
        );
        $stmt->execute($bindParameters);
        return $stmt->fetchAll();
    }

    public function getAllMemos($userID)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT * FROM bubble_messages 
            WHERE (receiverID = :userID AND isMemo = 'YES') 
            AND messageSentOn = (
                SELECT MAX(messageSentOn) 
                FROM bubble_messages AS bm 
                WHERE bm.parentID = bubble_messages.parentID
            )
            ORDER BY messageSentOn DESC
        ");
        $bindParameters = array(
            ":userID" => $userID,
        );
        $stmt->execute($bindParameters);
        return $stmt->fetchAll();
    }
    
    
    

    // Gets the previous messages tied to the conversation (parentID of a message thread)
    public function getMessageCrumbs($taskID, $parentID, $messageSentOn)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT * FROM bubble_messages WHERE (taskID=:taskID AND parentID = :parentID AND messageSentOn < :messageSentOn AND isMemo = 'NO') ORDER BY messageSentOn DESC");
        $bindParameters = array(
            ":taskID" => $taskID,
            ":parentID" => $parentID,
            ":messageSentOn" => $messageSentOn
        );

        $stmt->execute($bindParameters);
        return $stmt->fetchAll();
    }

    public function getMemoCrumbs($parentID, $messageSentOn)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT * FROM bubble_messages WHERE (parentID = :parentID AND messageSentOn < :messageSentOn AND isMemo = 'YES') ORDER BY messageSentOn DESC");
        $bindParameters = array(
            ":parentID" => $parentID,
            ":messageSentOn" => $messageSentOn
        );

        $stmt->execute($bindParameters);
        return $stmt->fetchAll();
    }



    public function getMessageDetails($messageID)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT * FROM bubble_messages WHERE messageID = :messageID");
        $bindParameters = array(
            ":messageID" => $messageID
        );
        $stmt->execute($bindParameters);
        /* learn from mistake:
        use fetch() here, not fetchall();
        */
        return $stmt->fetch();
    }


    #############################################################
    #############################################################
    ### NAME SEARCHING ###

    public function findListAdvanced($userID, $taskTitle, $taskDesc, $groupName)
    {
        $results = array();
        $binds = array();
        $isFirstClause = true;
        $userTable = $this->userData;

        // Retrieve the user's bubbleID, groupID, and isModerator status
        $stmt = $userTable->prepare("SELECT bubbleID, groupID, isModerator FROM bubble_users WHERE userID = :userID");
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userDetails) {
            $bubbleID = $userDetails['bubbleID'];
            $groupID = $userDetails['groupID'];
            $isModerator = $userDetails['isModerator'];

            // Start constructing the SQL query
            $sql = "SELECT bubble_tasks.*, bubble_groups.groupName, bubble_groups.groupColor 
                    FROM bubble_tasks 
                    JOIN bubble_groups ON bubble_tasks.groupID = bubble_groups.groupID";

            // Add search criteria
            if (!empty($taskTitle) || !empty($taskDesc)) {
                if ($isFirstClause) {
                    $sql .= " WHERE ";
                    $isFirstClause = false;
                } else {
                    $sql .= " AND ";
                }
                $sql .= " (taskTitle LIKE :taskTitle OR taskDesc LIKE :taskDesc)";
                $binds['taskTitle'] = '%' . $taskTitle . '%';
                $binds['taskDesc'] = '%' . $taskDesc . '%';
            }

            if (isset($groupName)) {
                if ($isFirstClause) {
                    $sql .= " WHERE ";
                    $isFirstClause = false;
                } else {
                    $sql .= " AND ";
                }
                $sql .= "  groupName LIKE :groupName";
                $binds['groupName'] = '%' . $groupName . '%';
            }

            // Add filter based on user's role
            if ($isModerator === 'YES') {
                // If the user is a moderator, filter by bubbleID
                if ($isFirstClause) {
                    $sql .= " WHERE ";
                    $isFirstClause = false;
                } else {
                    $sql .= " AND ";
                }
                $sql .= " bubble_tasks.bubbleID = :bubbleID";
                $binds['bubbleID'] = $bubbleID;
            } else {
                // If the user is not a moderator, filter by groupID
                if ($isFirstClause) {
                    $sql .= " WHERE ";
                    $isFirstClause = false;
                } else {
                    $sql .= " AND ";
                }
                $sql .= " bubble_tasks.groupID = :groupID";
                $binds['groupID'] = $groupID;
            }

            // Order by taskTitle
            $sql .= " ORDER BY bubble_tasks.timeTaskDue";

            // Prepare and execute the query
            $stmt = $userTable->prepare($sql);

            if ($stmt->execute($binds) && $stmt->rowCount() > 0) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        return $results;
    }



    // if (isset($listProdCat)) {
    //     if ($isFirstClause) {
    //         $sql .= " WHERE ";
    //         $isFirstClause = false;
    //     } else {
    //         $sql .= " AND ";
    //     }
    //     $sql .= "  listProdCat LIKE :listProdCat";
    //     $binds['listProdCat'] = $listProdCat;
    // }

    // if (isset($listCond)) {
    //     if ($isFirstClause) {
    //         $sql .= " WHERE ";
    //         $isFirstClause = false;
    //     } else {
    //         $sql .= " AND ";
    //     }
    //     $sql .= "  listCond LIKE :listCond";
    //     $binds['listCond'] = $listCond;
    // }

    // if (isset($listState)) {
    //     if ($isFirstClause) {
    //         $sql .= " WHERE ";
    //         $isFirstClause = false;
    //     } else {
    //         $sql .= " AND ";
    //     }
    //     $sql .= " listState LIKE :listState";
    //     $binds['listState'] = '%' . $listState . '%';
    // }
    ###############################################
    ########### CONFIRMING SALE ##################

    public function confirmSale($taskID, $senderID, $receiverID, $orderID, $senderInnie, $receiverInnie)
    {
        $isTaskDone = false;
        $userTable = $this->userData;

        $stmt = $userTable->prepare("UPDATE bubble_tasks SET isTaskDone='YES', senderID=:senderID, receiverID=:receiverID,timeTaskComplete=NOW(), orderID = :orderID, senderInnie=:senderInnie, receiverInnie=:receiverInnie WHERE taskID =:taskID");

        $bindParameters = array(
            ":taskID" => $taskID,
            ":senderID" => $senderID,
            ":receiverID" => $receiverID,
            ":orderID" => $orderID,
            ":senderInnie" => $senderInnie,
            ":receiverInnie" => $receiverInnie,
        );

        $isTaskDone = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        return ($isTaskDone);
    }

    public function defaultSaleMsg(
        $parentID,
        $senderID,
        $receiverID,
        $taskID,
        $messageTitle,
        $messageDesc,
        $senderInnie,
        $receiverInnie
    ) {
        $isMsgSent = false;
        $userTable = $this->userData;

        $salt = random_bytes(32);

        $stmt = $userTable->prepare("INSERT INTO bubble_messages SET parentID=:parentID, senderID = :senderID, receiverID = :receiverID, 
        taskID = :taskID, messageTitle = :messageTitle, messageDesc = :messageDesc,messageSentOn = NOW(), 
        senderInnie=:senderInnie, receiverInnie=:receiverInnie");

        $bindParameters = array(
            ":parentID" => $parentID,
            ":senderID" => $senderID,
            ":receiverID" => $receiverID,
            ":taskID" => $taskID,
            ":messageTitle" => $messageTitle,
            ":messageDesc" => $messageDesc,
            ":senderInnie" => $senderInnie,
            ":receiverInnie" => $receiverInnie,

        );

        $isMsgSent = ($stmt->execute($bindParameters) && $stmt->rowCount() > 0);
        return ($isMsgSent);
    }

    public function getSaleHistory($userID)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT * FROM bubble_tasks WHERE userID = :userID AND isTaskDone = 'Yes' ORDER BY timeTaskComplete DESC");
        $bindParameters = array(
            ":userID" => $userID
        );
        $stmt->execute($bindParameters);
        return $stmt->fetchAll();
    }
    public function getPurchaseHistory($userID)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT * FROM bubble_tasks WHERE receiverID = :userID AND isTaskDone = 'Yes' ORDER BY timeTaskComplete DESC");
        $bindParameters = array(
            ":userID" => $userID
        );
        $stmt->execute($bindParameters);
        return $stmt->fetchAll();
    }

    ###########################################
    ########### USER RATINGS ##################

    public function giveUserRating($userID, $userRating, $orderID)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("INSERT INTO plugin_user_ratings SET userID=:userID, userRating=:userRating, orderID=:orderID");
        $bindParameters = array(
            ":userID" => $userID,
            "userRating" => $userRating,
            "orderID" => $orderID
        );
        $stmt->execute($bindParameters);
        return true;
    }


    function isAlreadyRated($orderID)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT count(*) FROM plugin_user_ratings WHERE orderID=:orderID");


        $stmt->bindParam(
            ':orderID',
            $orderID
        );

        $stmt->execute();
        $number_of_rows = $stmt->fetchColumn();
        if ($number_of_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getAvgRating($userID)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT AVG(userRating) AS userRating FROM plugin_user_ratings WHERE userID = :userID");
        $stmt->execute(array(':userID' => $userID));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $rating = round($result['userRating'] * 2) / 2;
            return $rating >= 1 ? $rating : 0;
        } else {
            return 0;
        }
    }


    function getRatingCount($userID)
    {
        $userTable = $this->userData;
        $stmt = $userTable->prepare("SELECT count(userID) FROM plugin_user_ratings WHERE userID=:userID");


        $stmt->bindParam(
            ':userID',
            $userID
        );

        $stmt->execute();
        $number_of_rows = $stmt->fetchColumn();
        if ($number_of_rows > 0) {
            return $number_of_rows;
        }
    }

}

?>