<!DOCTYPE html>
<?php
require '../include/header.php';
require '../include/logic/php/php_viewMailbox.php';
?>
<link rel="stylesheet" href="../include/stylesheets/global.css">
<link rel="stylesheet" href="../include/stylesheets/viewInbox.css">
<div class="fullVH">
   <div class="container inboxContainer">
      <!-- BEGIN TABLE -->
      <table class="table table-hover" id="userListLog">
         <thead>
            <tr>
               <th></th> <!-- list id -->
               <th>From</th>
               <th></th> <!-- requested title -->
               <th></th>
               <th></th> <!-- time sent -->
            </tr>
         </thead>
         <tbody>
            <!-- For every value stored in the array we decl2ared in the PHP section -->
            <?php foreach ($messageLog as $row): ?>
               <?php if ($row['isMessageReplied'] == 'No' && $row['isMessageHidden'] != 'YES'): ?>
                  <tr class="unreplied <?php if (strpos($row['messageTitle'], 'RE | SALE CONFIRMED:') !== false)
                     echo 'completedSale'; ?>">
                     <td>
                        <form action="viewInbox.php" method="post" enctype="multipart/form-data">
                           <input type="hidden" name="parentID" id='parentID' value="<?= $row['parentID']; ?>" />
                           <button type="submit" class="customerOtherBtn" name="btnHideMsg" onclick="return confirm('This will delete the entire conversation from everyone in the group. Are you sure?')">
                              <i class="fa-solid fa-trash fa-xs"></i>
                           </button>
                        </form>
                     </td>
                     <td>
                        <p class="sentFrom">
                           <a href="viewUsers.php?userID=<?php echo $row['senderID'];?>">
                              <?php echo $row['senderInnie']; ?>
                           </a>
                        </p>
                     </td>
                     <td>
                        <a
                           href="viewMemo.php?messageID=<?php echo $row['messageID']; ?>&parentID=<?php echo $row['parentID']; ?>&receiverID=<?= $row['senderID']; ?>&receiverInnie=<?= $row['senderInnie']; ?>">
                           <?php echo $row['messageTitle']; ?>
                        </a>
                        <p class="subText">Unreplied</p>
                     </td>
                     <td>
                        <p class="messageSentOn">
                           <?php echo date("Y-m-d h:i A", strtotime($row['messageSentOn'])); ?>
                        </p>
                     </td>
                  </tr>
               <?php endif ?>
               <?php if ($row['isMessageReplied'] != 'No' && $row['isMessageHidden'] != 'YES'): ?>
                  <tr
                     class="replied <?php if (strpos($row['messageTitle'], 'RE | SALE CONFIRMED:') !== false)
                        echo 'completedSale'; ?>">
                     <td>
                        <form action="viewInbox.php" method="post" enctype="multipart/form-data">
                           <input type="hidden" name="parentID" id='parentID' value="<?= $row['parentID']; ?>" />
                           <button type="submit" class="customerOtherBtn" name="btnHideMsg" onclick="return confirm('Delete Entire Conversation?')"><i class="fa-solid fa-trash"></i></button>
                        </form>
                     </td>
                     <td>
                        <p class="sentFrom">
                           <a href="viewUsers.php?userID=<?php echo $row['senderID'];?>">
                              <?php echo $row['senderInnie']; ?>
                           </a>
                        </p>
                     </td>
                     <td>
                        <p class="taskID">   
                           <?php echo $row['taskID']; ?>
                        </p>
                     </td>
                     <td class="">
                        <a href="viewMemo.php?messageID=<?php echo $row['messageID']; ?>&parentID=<?php echo $row['parentID']; ?>&receiverID=<?= $row['senderID']; ?>&receiverInnie=<?= $row['senderInnie']; ?>"
                           class="customLink">
                           <?php echo $row['messageTitle']; ?>
                        </a>
                     </td>
                     <td>
                        <p class="messageSentOn">
                           <?php echo date("Y-m-d h:i A", strtotime($row['messageSentOn'])); ?>
                        </p>
                     </td>
                  </tr>
               <?php endif ?>
            <?php endforeach; ?>
            <!-- END for-loop -->
         </tbody>
      </table>
      <!-- END TABLE -->
   </div>
</div>
</body>

</html>