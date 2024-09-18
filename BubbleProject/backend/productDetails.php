<!DOCTYPE html>
<?php
ob_start();
require "../include/header.php";
require "../include/logic/php/php_productDetails.php";
?>
<link rel="stylesheet" href="../include/stylesheets/global.css">
<link rel="stylesheet" href="../include/stylesheets/productDetails.css">
<?php if (empty($listDetails)) {
   header('Location: plugInHome.php');
} ?>
<div class="container">
   <div class="row">
      <div class="col-lg-12 listImgBox">
         <input type="hidden" name="userID" value="<?= $sellerInfo['userID']; ?>" />
         <input type="hidden" name="p_id" value="<?= $listDetails['taskID']; ?>" />
      </div>

      <div class="col-lg-12 listInfoBox">
         <?php if ($listDetails['isTaskDone'] != 'YES'): ?>
            <div class="col-lg-12 listBuyBox">
               <div class="listProdPrice">DUE
                  <?= $listDetails['timeTaskDue']; ?>
               </div>
               <div class="listCond">

               </div>
               <div class="btnRequest">
                  <a href="requestProduct.php?taskID=<?= $listDetails['taskID']; ?>""><button class=" btn btn-md
                     btn-primary btnBuyNow">Request Help</button></a>
               </div>
            </div>
         <?php else: ?>
            <div class="col-md-12 listBuyBox">
               <div class="listProdPrice">DUE
                  <?= $listDetails['timeTaskDue']; ?>
               </div>

               <div class="underBtnText">
                  <div class="timeListsold">COMPLETED ON:
                     <?= $listDetails['timeTaskComplete']; ?>
                  </div>
                  <div class="customerInnie">
                     LISTED BY:
                     <a href="viewUsers.php?userID=<?= $sellerInfo['userID']; ?>">
                        <?= $sellerInfo['userInnie']; ?>
                     </a>
                  </div>
                  <div class="listSeller">ASSIGNED:
                        <?= $listDetails['groupName']; ?>
                  </div>
               </div>
            </div>
         <?php endif ?>
      </div>

      <div class="row requestInfo">
         <div class="col-sm-12 pageTitle">
            About...
         </div>
         <div class="col-lg-12 listImgBox">
            <!-- posted from this user -->
            <input type="hidden" name="sellerID" value="<?= $listDetails['userID']; ?>" />
            <!-- product taskID -->
            <input type="hidden" name="p_id" value="<?= $listDetails['taskID']; ?>" />
         </div>

         <div class="col-lg-12 listInfoBox">
            <div class="listProdTitle">
               <?= $listDetails['taskTitle']; ?>
            </div>
            <div class="listProdCat"> Listed in:
               <?= $listDetails['groupName']; ?>
            </div>
            <div class="listPostedOn">Posted on:
               <?php echo date("Y-m-d", strtotime($listDetails['taskPostedOn'])); ?>
            </div>
         </div>

         <div class="col-lg-12 listDesc">
            <b>Description</b>
            <?= $listDetails['taskDesc']; ?>
         </div>
      </div> <!-- close requestInfo row-->

   </div>
</div>

</body>

</html>


<script>
   // JQuery script not by me. Easy enough now that I see it, but this was Google 101.
   $(document).ready(function () {
      $('.thumb-imgs img').click(function () {
         $('.main-img img').attr('src', $(this).attr('src'));
      });
   });
</script>`