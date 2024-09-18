<!DOCTYPE html>
<?php
ob_start();
require "../include/header.php";
require "../include/logic/php/php_requestProduct.php";
?>
<link rel="stylesheet" href="../include/stylesheets/global.css">
<link rel="stylesheet" href="../include/stylesheets/requestProduct.css">
<div class="container">


   <div class="row">
      <div class="col-sm-12 requestForm">
         <!-- message info -->
         <form action="requestProduct.php" method="post" enctype="multipart/form-data">
            <br>
            <!-- hidden taskID -->
            <div>
               <input type="hidden" class="form-control" id="taskID" name="taskID"
                  value="<?= $listDetails['taskID']; ?>">
            </div>
            <!-- hidden userID SEND TO -->
            <div>
               <input type="hidden" class="form-control" id="senderID" name="senderID"
                  value="<?php echo $senderID; ?>">
            </div>
            <div>
               <input type="hidden" class="form-control" id="customerInnie" name="customerInnie"
                  value="<?= $customerInfo['userInnie']; ?>">
            </div>
            <div>
               <input type="hidden" class="form-control" id="sellerID" name="sellerID"
                  value="<?= $listDetails['userID']; ?>">
            </div>
            <!-- hidden condition -->
            <div>
               <input type="hidden" class="form-control" id="isMessageReplied" name="isMessageReplied" value="No">
            </div>
            <div>
               <label for="sellerInnie">To:</label>
               <input type="text" class="form-control" id="sellerInnie" name="sellerInnie"
                  value="<?= $sellerInfo['userInnie']; ?>" readonly>
            </div>
            <!-- hidden title -->
            <div>
               <input type="hidden" class="form-control" id="messageTitle" name="messageTitle"
                  value="Requested: <?= $listDetails['taskTitle']; ?>">
            </div>
            <div>
               <br>
               <label for="messageDesc">Message</label>
               <textarea id="messageDesc" class="form-control" name="messageDesc" rows="5"
               placeholder="Type something about your item. You can also include extra descriptionary images."></textarea>
                  <script>
               tinymce.init({
                  selector: '#messageDesc',
                  height: 200,
                  menubar: false,
                  plugins: [
                     'quickbars, advlist autolink lists link charmap print preview anchor',
                     'searchreplace visualblocks code fullscreen',
                     'insertdatetime table paste code help wordcount',
                     'autoresize', 'emoticons', 'fullscreen', 'hr', 'preview'
                  ], quickbars_image_toolbar: false,
                  toolbar: 'formatselect  undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent hr | removeformat | fullscreen preview | emoticons table',
                  content_css: '//www.tiny.cloud/css/codepen.min.css'
               });
            </script>
            </div>
            <br>

            <div class="row rowBtnPost">
               <div class="col-sm-12">
                  <button class="customBtn" name="btnSend">Request</button>
               </div>
            </div>
         </form>
      </div>
   </div>
   <br>
   <div class="row requestInfo">
      <div class="col-sm-12 pageTitle">
         About...
      </div>
      <div class="col-lg-8 listImgBox">
         <!-- posted from this user -->
         <input type="hidden" name="sellerID" value="<?= $listDetails['userID']; ?>" />
         <!-- product taskID -->
         <input type="hidden" name="p_id" value="<?= $listDetails['taskID']; ?>" />
      </div>

      <div class="col-sm-7 listInfoBox">
         <div class="listProdTitle">
            <?= $listDetails['taskTitle']; ?>
         </div>
         <div class="listProdCat"> Listed in:
            <?= $listDetails['groupName']; ?>
         </div>
         <div class="listPostedOn">Posted on:
            <?php echo date("Y-m-d", strtotime($listDetails['taskPostedOn'])); ?>
         </div>
         <div class="listProdPrice">
            <?= $listDetails['timeTaskDue']; ?>
         </div>
      </div>

      <div class="col-sm-12 listDesc">
         <b>Description</b>
         <?= $listDetails['taskDesc']; ?>
      </div>

   </div> <!-- close requestInfo row-->
</div> <!-- close container -->

</body>

</html>
<script src="../include/logic/JS/js_photoManagement.js"></script>