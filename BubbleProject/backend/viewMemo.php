<!DOCTYPE html>
<?php
ob_start();
require '../include/header.php';
require '../include/logic/php/php_viewMemo.php';
?>
<link rel="stylesheet" href="../include/stylesheets/global.css">
<link rel="stylesheet" href="../include/stylesheets/viewMessage.css">
<div class="container">
   <h2>Messaging</h2>

   
   <div class="row profileObject">
   <?php if (!empty($profileInfo)):?>
      <div class="col-md-4">
         <div class="img-container">
            <?php
            $defaultAvie = "../include/default-avie/default-avie.jpg";
            if (is_null($profileInfo['userPic']) || empty($profileInfo['userPic'])) {
               echo "<img src= '$defaultAvie' class='customerPP rounded-circle' alt='profile picture' style='border: solid 2px blue;'>";
            } else {
               echo "<img src='" . $profileInfo['userPic'] . "' class='customerPP rounded-circle' alt='profile picture' style='border: solid 2px blue;'>";
            }
            ?>
         </div>
      </div>

      <div class="col-md-6">
         <h1>
            <?php echo $profileInfo['userInnie']; ?>
         </h1>
         <small id="joinDate" class="form-text text-muted profDetails">
            Joined: <b>
               <?php echo date("Y-m-d", strtotime($profileInfo['userJoined'])); ?>
            </b>
         </small>
         <br>
         <small id="bioTitle" class="form-text text-muted">Biography</small>
         <p>
            <?php echo $profileInfo['userBio']; ?>
         </p>
      </div>
      <?php else: echo '<div class="noProduct">User no longer exists, or could be retrieved.</div>'; ?>
      <?php endif ?>
   </div> <!-- end user's profile -->
   


   <!-- Message -->
   <div class="row MessageContainer">

      <div class="col-lg-12">

         <div class="col-lg-12 msgHistory">
            <?php foreach ($memoDetails as $row): ?>
               <div class="row">
                  <?php if ($row['senderInnie'] == $senderInfo['userInnie']): ?>
                     <span class="selfMsg selfBG">
                        <p class="messageDesc">
                           <?php echo $row['messageDesc']; ?>
                        <p>
                        </p class="messageSentOn">sent:
                        <?php echo date("h:i A", strtotime($row['messageSentOn'])); ?>
                        On
                        <?php echo date("Y-m-d", strtotime($row['messageSentOn'])); ?>
                        </p>
                     </span>
                  <?php else: ?>
                     <span class="otherMsg otherBG">
                        <p class="messageDesc">
                           <?php echo $row['messageDesc']; ?>
                        </p>
                        </p class="messageSentOn">
                        sent:
                        <?php echo date("h:i A", strtotime($row['messageSentOn'])); ?>
                        On
                        <?php echo date("Y-m-d", strtotime($row['messageSentOn'])); ?>
                        </p>
                     </span>
                  <?php endif ?>
               </div>
            <?php endforeach; ?>
         </div>

         <div class="newestMessageBox">
            <div class="row-sm-12 newestMessage">
               <p>
                  <?php echo $messageDetails['messageDesc']; ?>
               </p>
               <p class="messageSentOn">sent:
                  <?php echo date("h:i A", strtotime($messageDetails['messageSentOn'])); ?>
                  On
                  <?php echo date("Y-m-d", strtotime($messageDetails['messageSentOn'])); ?>
               </p>
            </div>
      </div> <!-- end history and newest message -->


      <div class="col-lg-12 msgReply">
         <!-- reply -->
         <form action="viewMemo.php" method="post" enctype="multipart/form-data">
            <br>

            <!-- FOR UPDATING OLD MESSAGE CONDITION -->
            <div>
               <input type="hidden" class="form-control" id="priorMessageID" name="priorMessageID"
                  value="<?= $messageDetails['messageID']; ?>" readonly>
            </div>
            <!-- hidden condition: UPDATE Prior message-->
            <div>
               <input type="hidden" class="form-control" id="updateStatus" name="updateStatus" value="Yes">
            </div>
            <!-- END -->


            <div>
               <input type="hidden" class="form-control" id="parentID" name="parentID"
                  value="<?= $messageDetails['parentID']; ?>" readonly>
            </div>
            <!-- customer's id (who sent you the message) -->
            <div>
               <input type="hidden" class="form-control" id="receiverID" name="receiverID"
                  value="<?php echo $receiverID ?>">
            </div>
            <div>
               <input type="hidden" class="form-control" id="receiverInnie" name="receiverInnie"
                  value="<?php echo $receiverInnie ?>">
            </div>
            <div>
               <input type="hidden" class="form-control" id="senderID" name="senderID"
                  value="<?php echo $senderInfo['userID'] ?>">
            </div>
            <!-- hidden condition: INSERT FOR THIS MESSAGE -->
            <div>
               <input type="hidden" class="form-control" id="isMessageReplied" name="isMessageReplied" value="No">
            </div>
            <div>
               <input type="hidden" class="form-control" id="senderInnie" name="senderInnie"
                  value="<?php echo $senderInfo['userInnie'] ?>">
            </div>

            <!-- hidden title, autogenerate RE | to mark as a reply.-->
            <div>
               <input type="hidden" class="form-control" id="messageTitle" name="messageTitle"
                  value="<?= $messageDetails['messageTitle']; ?>">
            </div>
            <?php if ($messageDetails['isMessageReplied']!='Yes'):?>
            <!-- User enter message -->
            <div>
               <textarea class="form-control" id="messageDesc" name="messageDesc" rows="2" maxlength="275"
                  required></textarea>
            </div>
            <!-- User Send Pics -->
            <br>

         
            <div class="row rowBtnPost">
               <div class="col-sm-12">
                  <button class="customBtn" name="btnSend">Reply Back</button>
               </div>
            </div>
            <?php else: echo '<div class="noProduct">Already replied to this message.</div>'; ?>
            <?php endif ?>
         </form>
      </div>
   </div>
</div> </div>

   </div> <!-- close requestInfo row-->
</div>
</body>

</html>
<script src="../include/logic/JS/js_viewMessages.js"></script>