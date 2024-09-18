<!DOCTYPE html>
<?php
ob_start();
require '../include/header.php';
require '../include/logic/php/php_viewPurchaseHistory.php';
?>
<link rel="stylesheet" href="../include/stylesheets/global.css">
<link rel="stylesheet" href="../include/stylesheets/viewPurchaseHistory.css">
<div class="container-fluid fullVH">
   <div class="container inboxContainer">
      <!-- BEGIN TABLE -->
      <table class="table table-hover" id="userListLog">
         <thead>
            <tr>
               <th>Completed On</th>
               <th>Task</th> <!-- requested title -->
               <th>Assigned</th> <!-- time sent -->
            </tr>
         </thead>
         <tbody>
            <!-- For every value stored in the array we declared in the PHP section -->
            <?php foreach ($listDetails as $row): ?>
               <tr>
                  <td>
                     <p class="sentFrom">
                        <?php echo $row['timeTaskComplete']; ?>
                     </p>
                  </td>
                  <td>
                     <a href="productDetails.php?taskID=<?= $row['taskID']; ?>">
                        <p class="sentFrom">
                           <?php echo $row['taskTitle']; ?>
                        </p>
                     </a>
                  </td>
                  <td>
                     <p class="customLink">
                        <?php echo $row['groupName']; ?>
                     </p>
                  </td>
               </tr>
            <?php endforeach; ?>
            <!-- END for-loop -->
         </tbody>
      </table>
      <!-- END TABLE -->
   </div>
</div>
</body>

</html>