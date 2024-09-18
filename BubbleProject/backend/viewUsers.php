<!DOCTYPE html>
<?php
ob_start();
require '../include/header.php';
require '../include/logic/php/php_viewUsers.php';
?>
<link rel="stylesheet" href="../include/stylesheets/global.css">
<link rel="stylesheet" href="../include/stylesheets/viewProfiles.css">
<div class="fullVH">
   <div class="container viewProfileContainer">
      <?php if (!empty($userInfo)): ?>
         <div class="profileContainer">
            <div class="row">
               <div class="col-md-4">
                  <img id="newUserPP" class="newUserPP rounded-circle img-overlay" />
                  <div class="img-container">
                     <?php
                     $defaultAvie = "../include/default-avie/default-avie.jpg";
                     if (is_null($userInfo['userPic']) || empty($userInfo['userPic'])) {
                        echo "<img src= '$defaultAvie' class='ProfilePics rounded-circle' alt='profile picture'>";
                     } else {
                        echo "<img src='" . $userInfo['userPic'] . "' class='ProfilePics rounded-circle' alt='profile picture'>";
                     }
                     ?>

                     <div class="changePPBox">
                        <form action="editUserPic.php" method="POST" enctype="multipart/form-data">
                           <div>
                              <input type="hidden" id="userID" name="userID" class="form-control"
                                 value="<?php echo $userInfo['userID'] ?>" required>
                           </div>
                           <div class="img-overlay">
                              <label class="custom-file-upload">
                                 <input type="file" id="userProfilePicture" name="userProfilePicture" class="form-control"
                                    accept="image/*" required>
                              </label>
                           </div>
                           <br>
                           <div id="btnUpdatePPBox">
                           </div>
                           <p id="photoIndicator"></p>
                        </form>
                     </div>
                  </div>
               </div>

               <div class="col-md-7">
                  <h1>
                     <?php echo $userInfo['userInnie']; ?>
                  </h1>
                  <small id="joinDate" class="form-text text-muted profDetails">
                     Joined: <b>
                        <?php echo date("Y-m-d", strtotime($userInfo['userJoined'])); ?>
                     </b>
                  </small>

                  <div class="row userRating showCount">
                     <div class="col-lg-12">
                        <?php
                        if (!empty($userInfo['groupName'])) {
                           echo "Group: " . htmlspecialchars($userInfo['groupName']);
                        } else if ($userInfo['isModerator'] === 'YES') {
                           echo "Moderator";
                        } else {
                           echo "No Group Assigned";
                        }
                        ?>
                        <br>
                        <button class="customBtn" style="margin-right: 15px; margin-top: 10px;"><a style="color: white;"
                              href="sendMemo.php?userID=<?= $userInfo['userID']; ?>">Send Message</a></button>
                        <br>
                     </div>
                  </div>

                  <small class="form-text text-muted userBio">Biography</small>
                  <p>
                     <?php echo $userInfo['userBio']; ?>
                  </p>
               </div>
               <!-- The following code would be inside the 'profileHeader' div in your viewProfile.php file, most likely in the same area where you have the "Edit" button currently -->
               <div class="col-md-1">
                  <!-- Check if the currently logged in user's ID matches the ID of the profile being viewed -->
                  <?php if ($modCheck['isModerator'] == 'YES' || $modCheck['isOwner'] == 'YES'): ?>
                     <p style="text-align: right;"><a href="editUserProfile.php?userID=<?= $userInfo['userID']; ?>"><button
                              class="customBtn"><i class="fa-solid fa-pen-to-square"></i></button></a>
                     <p>
                     <?php endif ?>
               </div>
            </div> <!-- Row -->
         </div>

         <div class="productContainer">
            <div class="content">
               <?php if ($userInfo['isModerator'] != 'YES' && $userInfo['isOwner'] != 'YES'): ?>
                  <a href="displayResults.php?inputName=&groupName=<?php echo $userInfo['groupName'] ?>&search=Search">
                     View all tasks assigned to: <?php echo $userInfo['groupName'] ?>
                  </a>
               <?php endif ?>

               <?php if ($userInfo['isModerator'] == 'YES' || $userInfo['isOwner'] == 'YES'): ?>
                  <p>User is a moderator or owner.</p>
               <?php endif ?>

            </div>
         </div>
      <?php else:
         echo 'User does not exist, or could not be retrieved. Redirecting back to your profile.';
         echo '<script>setTimeout(function() { window.location.href = "viewProfile.php"; }, 3500);</script>';
         ?>
      <?php endif ?>
   </div>
   </body>

   </html>
   <script src="../include/logic/JS/js_updatePP.js"></script>