<!DOCTYPE html>
<?php
ob_start();
require '../include/header.php';
require '../include/logic/php/php_editListing.php';
$modCheck = $userDatabase->getUserDetails($userID);


if ($_SESSION['userID'] == $taskDetails['userID'] || $modCheck['isModerator'] == 'YES'):
   ?>

   <link rel="stylesheet" href="../include/stylesheets/global.css">
   <link rel="stylesheet" href="../include/stylesheets/editListing.css">
   <div class="container-fluid">
      <div class="container editListingContainer">
         <form action="editListing.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="visitID" value="<?php echo $taskDetails['userID']; ?>">

            <div>
               <input type="hidden" id="taskID" name="taskID" value="<?= $taskDetails['taskID']; ?>" />
            </div>


            <div class="row displayContent">
               <div class="col-md-12">
                  <label for="inputGroup">Assigned Group:</label>
                  <select class="form-control" id="inputGroup" name="inputGroup" required>
                     <option value="" disabled>Choose Group</option>
                     <?php
                     foreach ($groupList as $group) {
                        $selected = ($group['groupID'] == $taskDetails['groupID']) ? 'selected' : '';
                        echo '<option value="' . $group['groupID'] . '" ' . $selected . '>' . $group['groupName'] . '</option>';
                     }
                     ?>
                  </select>
               </div>
            </div>

            <div class="row displayContent">
               <div class="col-md-12">
                  <label for="inputTimeTaskDue">Due Date:</label>
                  <input type="date" class="form-control" id="inputTimeTaskDue" name="inputTimeTaskDue"
                     value="<?php echo $taskDetails['timeTaskDue']; ?>" required>
               </div>
            </div>

            <div class="row displayContent">
               <div class="col-md-12">
                  <label for="inputTaskTitle">Task Title:</label>
                  <input type="text" class="form-control" id="inputTaskTitle" name="inputTaskTitle"
                     value="<?php echo $taskDetails['taskTitle']; ?>" required>
               </div>
            </div>


            <div class="row displayContent">
               <div class="col-md-12">
                  <label for="inputTaskDesc">Product Description:</label>
                  <textarea id="inputTaskDesc" class="form-control" name="inputTaskDesc" rows="5"
                     required><?php echo $taskDetails['taskDesc']; ?></textarea>
                  <script>
                     tinymce.init({
                        selector: '#inputTaskDesc',
                        plugins: 'quickbars table image link lists media autoresize help',
                        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncentre alignright alignjustify | indent outdent | bullist numlist',
                        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
                     });
                     tinymce.init({
                        selector: '#inputTaskDesc',
                        height: 500,
                        menubar: true,
                        plugins: [
                           'quickbars, advlist autolink lists link image charmap print preview anchor',
                           'searchreplace visualblocks code fullscreen',
                           'insertdatetime media table paste code help wordcount',
                           'autoresize', 'emoticons', 'fullscreen', 'hr', 'image', 'preview'
                        ], quickbars_image_toolbar: true,
                        toolbar: 'formatselect  undo redo | formatselect | bold italic backcolor image | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent hr | removeformat | fullscreen preview | emoticons table',
                        content_css: '//www.tiny.cloud/css/codepen.min.css'
                     });
                  </script>

               </div>
            </div>

            <div class="row displayContent">
               <div class="col-md-12">
               </div>
            </div>
            <div class="row rowOfBtns">
               <div class="col-md-12 text-right postBtns">
                  <a href="viewProfile.php" class=""
                     onclick="return confirm('This will remove all progress. Leave page?')">Cancel</a>
                  <input type="submit" class="customBtn" name="updateBtn" value="Update" />
               </div>
            </div>
         </form>
      </div> <!-- main div -->
   </div>
   </body>

   </html>
<?php else:
   header("location: ../backend/viewProfile.php"); ?>
<?php endif ?>