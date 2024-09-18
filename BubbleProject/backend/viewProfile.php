<!DOCTYPE html>
<?php
ob_start();
require '../include/header.php';
require '../include/Logic/php/php_viewProfile.php';
?>
<link rel="stylesheet" href="../include/stylesheets/global.css">
<link rel="stylesheet" href="../include/stylesheets/viewProfilesOne.css">
<link rel="stylesheet" href="../include/stylesheets/viewTimeline.css">
<div class="fullVH">
   <div class="container viewProfileContainer">
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
                     <form action="editPP.php" method="POST" enctype="multipart/form-data">
                        <div>
                           <input type="hidden" id="userID" name="userID" class="form-control"
                              value="<?php echo $userID ?>" required>
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

               <div class="row userRating">
                  <div class="col-lg-12">
                     <?php
                     if (!empty($userInfo['groupName'])) {
                        echo "Group: " . htmlspecialchars($userInfo['groupName']);
                     } else if ($userInfo['isModerator'] === 'YES') {
                        echo ("<p class='bubbleCodeText'>" . $userInfo['bubbleCode'] . "<a id='copyButton' class='copy-button' onClick='copyBubbleCode(); return false;'>
                        <i class='fa fa-copy'></i></a></p>
                     </a>");
                     } else {
                        echo "No Group Assigned";
                     }
                     ?>
                  </div>
               </div>



               <small class="form-text text-muted userBio">Biography</small>
               <p>
                  <?php echo $userInfo['userBio']; ?>
               </p>
            </div>
            <div class="col-md-1">
               <!-- Check if the currently logged in user's ID matches the ID of the profile being viewed -->
               <?php if ($_SESSION['userID'] === $userInfo['userID']) { ?>
                  <p style="text-align: right;"><a href="editProfile.php"><button class="customBtn"><i
                              class="fa-solid fa-pen-to-square"></i></button></a>
                  <p>
                  <?php } ?>
            </div>
         </div> <!-- Row -->
      </div>

      <div class="col-md-12 col-md-12 stillToDoDiv productContainer">
   Still To-Do 
   <button class="customBtn firstCustomBtn" onclick="showTimeLineView()">
   <i class="fa-solid fa-timeline"></i>
   </button>
   <button class="customBtn" onclick="showStandardView()">
   <i class="fa-solid fa-bars"></i>
   </button>
</div>

<!-- Standard View -->
<div class="row rowContainer">
   <?php if (empty($userListLog)): ?>
      <div class="col-md-12 col-sm-12" style="padding: 15px;">
         No tasks assigned.
      </div>
   <?php endif; ?>
   <?php foreach ($userListLog as $row): ?>
      <?php if ($row['isTaskDone'] != 'YES'): ?>
         <div class="col-md-3 content">
            <div class="row">
               <a href="editListing.php?taskID=<?= $row['taskID']; ?>"><button class="customBtn showEditListing"
                     name="cancelbtn"><i class="fa-solid fa-pen-to-square"></i></button></a>

               <form action="viewProfile.php" method="post">
                  <button type="submit" class="customBtn showCompleteListing" name="btnComplete"
                     onclick="return confirm('Mark task as complete?')">
                     <i class="fa-solid fa-check"></i></button>

                  <input type="hidden" id="taskID" name="taskID" value="<?= $row['taskID']; ?>" />
               </form>

               <form action="viewProfile.php" method="post">
                  <button type="submit" class="customerOtherBtn showDeleteListing" name="btnDelete"
                     onclick="return confirm('Are you sure you want to delete this listing? It is a permanent decision.')"><i
                        class="fa-solid fa-trash"></i></button>

                  <input type="hidden" id="taskID" name="taskID" value="<?= $row['taskID']; ?>" />
               </form>
            </div>

            <div class="listProdPic" style="width: 100%; min-height: 30px; padding: 10px; border-radius: 5px; background-color:<?= $row['groupColor']; ?>">
               <div style="width: 100%;" class="rowGroupName">
                  <?= $row['groupName']; ?>
               </div>
            </div>
            <a href="productDetails.php?taskID=<?= $row['taskID']; ?>">
               <div class="listProdTitle" style="min-height: 80px;">
                  <?= $row['taskTitle']; ?>
               </div>
            </a>

            <div class="listProdPrice"> DUE <?= $row['timeTaskDue']; ?></div>
         </div>
      <?php endif; ?>
   <?php endforeach; ?>
</div>

<!-- Timeline View -->
<div class="timeLineView">
   <div class="row timeline">
      <?php
      $groupedTasks = [];
      foreach ($userListLog as $row) {
         if ($row['isTaskDone'] != 'YES') {
            $dueDate = $row['timeTaskDue'];
            if (!isset($groupedTasks[$dueDate])) {
               $groupedTasks[$dueDate] = [];
            }
            $groupedTasks[$dueDate][] = $row;
         }
      }

      foreach ($groupedTasks as $dueDate => $tasks): ?>
         <div class="col-md-3 tlItems">
            <span class="tlDate"><?= $dueDate; ?></span>
            <?php foreach ($tasks as $task): ?>
               <div class="tlContent">
                  <a href="productDetails.php?taskID=<?= $task['taskID']; ?>">
                     <div class="tlTitle"><?= $task['taskTitle']; ?></div>
                  </a>
                  <div class="tlGroupName" style="background-color:<?= $task['groupColor']; ?>;">
                     <?= $task['groupName']; ?>
                  </div>
               </div>
            <?php endforeach; ?>
         </div>
      <?php endforeach; ?>
   </div>
</div>
   </div>
</div>
</body>

</html>
<script src="../include/logic/JS/js_updatePP.js"></script>
<script>

   function copyBubbleCode() {
      // Get the bubble code text
      var bubbleCodeText = document.querySelector('.bubbleCodeText').innerText;

      // Create a temporary input element to hold the text for copying
      var tempInput = document.createElement('input');
      tempInput.value = bubbleCodeText;
      document.body.appendChild(tempInput);

      // Select the text and copy it
      tempInput.select();
      tempInput.setSelectionRange(0, 99999); // For mobile devices

      // Execute the copy command
      document.execCommand('copy');

      // Remove the temporary input element
      document.body.removeChild(tempInput);

      // Show alert to confirm copy
      alert('Bubble Code copied: ' + bubbleCodeText);
   }

   function showTimeLineView() {
   document.querySelector('.rowContainer').style.display = 'none'; // Hide standard view
   document.querySelector('.timeLineView').style.display = 'block'; // Show timeline view
   }

   function showStandardView() {
      document.querySelector('.timeLineView').style.display = 'none'; // Hide timeline view
      document.querySelector('.rowContainer').style.display = 'flex'; // Show standard view
   }

</script>