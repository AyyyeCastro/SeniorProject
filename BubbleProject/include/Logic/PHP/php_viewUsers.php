<?php

$message = "";
$userID = filter_input(INPUT_GET, 'userID');
$sessionUserID = $_SESSION['userID']; // The signed-in user's ID

if (!array_key_exists('isLoggedIn', $_SESSION) || !$_SESSION['isLoggedIn']) {
    $_SESSION['visitCrumb'] = 'backend/viewUsers.php?userID=' . $userID;
    header("location: ../login.php");
    exit;
}

// Fetch the bubbleID of the signed-in user
$sessionBubbleID = $userDatabase->getUserBubbleID($sessionUserID);
$visitingBubbleID = $userDatabase->getVisitingUserBubbleID($userID);
$userInfo = $userDatabase->getUserDetails($userID);
$groupID = $userInfo['groupID'];
$userListLog = $userDatabase->getGroupTasks($groupID);

// Check if the bubbleIDs match
if ($sessionBubbleID !== $visitingBubbleID) {
    // Redirect or show an error if the bubbleIDs do not match
    echo '<div class="container" id="warnMod" style="color: black; font-size: 48px;">
            <div class="row">
                <div class="col-md-12">Error: Profile does not exist.</div>
            </div>
          </div>';
    exit;
}

// Continue to fetch and display user details
$userInfo = $userDatabase->getUserDetails($userID);

$sessionID = $_SESSION['userID'];
$modCheck = $userDatabase->isUserMod($sessionID);
?>

