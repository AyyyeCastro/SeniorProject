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
      
      <div class="productContainer">
         <div class="col-md-12 col-md-12 stillToDoDiv">Still To-Do</div>
         <div class="container">
            <div class="row timeline">
               <?php
               $groupedTasks = [];
               // Group tasks by date
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
</div>
</body>

</html>
