<!DOCTYPE html>
<?php
ob_start();
require '../include/header.php';
require '../include/logic/php/php_postListing.php';
?>
<link rel="stylesheet" href="../include/stylesheets/global.css">
<link rel="stylesheet" href="../include/stylesheets/postListing.css">
<div class="fullVH">
   <div class="container postListingContainer">
      <form action="postListing.php" method="post" enctype="multipart/form-data">
         <div>
            <select class="form-control" id="inputGroup" name="inputGroup" required>
               <option value="" disabled selected>Assign Group</option>
               <?php
               foreach ($groupList as $group) {
                  $selected = ($group['groupName'] == $taskDetails['listGroupName']) ? 'selected' : '';
                  echo '<option value="' . $group['groupName'] . '" ' . $selected . '>' . $group['groupName'] . '</option>';
               }
               ?>
            </select>
         </div>
         <br>
         <div>
            <label for="inputTimeTaskDue">Due Date:</label>
            <input type="date" class="form-control" id="inputTimeTaskDue" name="inputTimeTaskDue" required>
         </div>
         <br>
         <div>
            <label for="inputTaskTitle">Task Title:</label>
            <input type="text" class="form-control" id="inputTaskTitle" name="inputTaskTitle" maxlength="120" required>
         </div>
         <br>
         <div>
            <label for="inputTaskDesc">Task Description:</label> <!-- listSummary in the db -->
            <textarea id="inputTaskDesc" class="form-control" name="inputTaskDesc" rows="15"
               placeholder="Type something about your item. You can also include extra descriptionary images."></textarea>
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
                     'quickbars, advlist autolink lists link image charmap print preview',
                     'searchreplace visualblocks code fullscreen',
                     'insertdatetime media table paste code help wordcount',
                     'autoresize', 'emoticons', 'fullscreen', 'hr', 'image', 'preview'
                  ], quickbars_image_toolbar: true,
                  toolbar: 'formatselect  undo redo | formatselect | bold italic backcolor image | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent hr | removeformat | fullscreen preview | emoticons table',
                  content_css: '//www.tiny.cloud/css/codepen.min.css'
               });
            </script>
         </div>

         <br>

         <!-- manual photo submission is not needed with tinyMCE. However, the logic is still valuable. --> 
         <!-- <div class="row rowCustomFiles">
            <div class="col-sm-12">
               <small class="form-text text-muted">First photo is required.</small>
               <label for="sendPic" class="customFiles" id="customFile1"><i class="fa-solid fa-image fa-lg"></i>
                  Insert Photo
                  <input type="file" id="sendPic" name="sendPic" accept="image/*">
               </label>
               <label for="sendPic2" class="customFiles" id="customFile2"> +
                  <input type="file" id="sendPic2" name="sendPic2" accept="image/*">
               </label>
               <label for="sendPic3" class="customFiles" id="customFile3"> +
                  <input type="file" id="sendPic3" name="sendPic3" accept="image/*">
               </label>
               <label for="sendPic4" class="customFiles" id="customFile4"> +
                  <input type="file" id="sendPic4" name="sendPic4" accept="image/*">
               </label>
            </div>
         </div> -->

         <div class="row">
            <div class="col-sm-3 col-md-4 col-lg-3">
               <div class="preview-container">
                  <img id="prevImg" />
                  <span class="remove-btn" id="removeBtn1"><i class="fa-regular fa-square-minus"></i></span>
               </div>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-3">
               <div class="preview-container">
                  <img id="prevImg2" />
                  <span class="remove-btn" id="removeBtn2"><i class="fa-regular fa-square-minus"></i></span>
               </div>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-3">
               <div class="preview-container">
                  <img id="prevImg3" />
                  <span class="remove-btn" id="removeBtn3"><i class="fa-regular fa-square-minus"></i></span>
               </div>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-3">
               <div class="preview-container">
                  <img id="prevImg4" />
                  <span class="remove-btn" id="removeBtn4"><i class="fa-regular fa-square-minus"></i></span>
               </div>
            </div>
         </div>
         <div class="row rowBtnPost">
            <div class="col-sm-12">
               <a href="plugInHome.php" style="padding: 15px;">Cancel</a>
               <input type="submit" class="customBtn" value="Post Listing">
            </div>
         </div>
      </form>
   </div> <!-- main div -->
</div>
</body>

</html>
<script src="../include/logic/JS/js_photoManagement.js"></script>