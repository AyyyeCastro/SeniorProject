<!DOCTYPE html>
<?php
require "../include/header.php";
require '../include/logic/php/php_displayResults.php';
?>
<link rel="stylesheet" href="../include/stylesheets/global.css">
<link rel="stylesheet" href="../include/stylesheets/displayResultscopy.css">
<link rel="stylesheet" href="../include/stylesheets/viewTimeline.css">
<div class="container-fluid">
   <div class="container fullVH">

      <?php if ($search_option == 'Products' || $search_option == $selected || $search_option == $selected): ?>

         <form method="get" action="displayResults.php" class="filterContainer">
            <div class="row">
               <input type="hidden" name="inputName" value="<?php echo $taskTitle; ?>">

               <div class="col-md-3">
                  <select class="form-control" id="groupName" name="groupName">
                     <?php
                     $selectedCat = (isset($_GET["groupName"])) ? $_GET["groupName"] : '';
                     echo '<option value="" disabled ' . (($selectedCat == '') ? 'selected' : '') . '>Choose Group</option>';
                     foreach ($groupList as $category) {
                        $selected = ($category['groupName'] == $selectedCat) ? 'selected' : '';
                        echo '<option value="' . $category['groupName'] . '" ' . $selected . '>' . $category['groupName'] . '</option>';
                     }
                     ?>
                  </select>
               </div>
            </div>

            <div class="row">
               <!-- Buttons -->
               <div class="col-md-4 filterBtns">
                  <!-- Search with criteria entered -->
                  <button class="customBtn" type="submit" name="search" value="Search">Apply</button>
                  <a href="http://localhost/se417/backend/displayResults.php?search_option=Products&inputName=&search=Search"><button type="button" class="customerOtherBtn">Reset
                        Filter</button></a>
               </div>
            </div>

            <div class="row resultFor">
            <div class="col-md-12">
               <?php if (isGetRequest())  {
                  if (isset($_GET["search"]) && !empty($taskTitle)) {
                     echo 'Results for: <b>'.$taskTitle.'</b>';
                  }
               } ?>
               </p>
            </div>
            </div>
         </form>


<div class="timeLineContainer">
   <div class="row timeline">
      <?php 
      if (!empty($listArray)) {
         $groupedTasks = [];
         // Group tasks by date from search results
         foreach ($listArray as $row) {
            if ($row['isTaskDone'] != 'YES') {
               $dueDate = $row['timeTaskDue'];
               if (!isset($groupedTasks[$dueDate])) {
                  $groupedTasks[$dueDate] = [];
               }
               $groupedTasks[$dueDate][] = $row;
            }
         }

         // Display tasks in timeline format
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
         <?php endforeach; 
      } else {
         echo '<div class="col-md-12 displayMsg">No tasks are assigned, or match your criteria.</div>';
      }
      ?>
   </div>
</div>

         <?php endif; ?>

         <?php if ($search_option == 'Sellers'): ?>
            <div class="row resultFor">
               <p>
                  <?php if (isPostRequest()) {
                     if (isset($_POST["search"])) {
                        echo 'Results for: ' . '<b>' . $userInnie . '</b>';
                     }
                  } ?>
               </p>
            </div>
            <table class="table table-hover">

               <tbody>
                  <!-- For every value stored in the array we declared in the PHP section -->
                  <?php foreach ($userArray as $row): ?>
                     <tr>
                        <td>
                           <form action="" method="post">
                              <input type="hidden" name="p_id" value="<?= $row['userID']; ?>" />
                           </form>
                        </td>
                        <!-- Display it's value, AND IMPORTANTLY set the links to lead to the user's profile according by userID -->
                        <td><img
                              src="<?php echo (is_null($row['userPic']) || empty($row['userPic'])) ? $defaultAvie : $row['userPic']; ?>"
                              class="ProfilePics rounded-circle" alt="profile picture" style="border: solid 2px blue;"></td>
                        <td><a href="viewUsers.php?userID=<?= $row['userID']; ?>" style="font-size: 20px;"><?=
                             $row['userInnie']; ?></a></td>
                     </tr>
                  <?php endforeach; ?>
                  <!-- END for-loop -->
               </tbody>
            </table>
         <?php endif; ?>
         <!-- END for-loop -->
         </tbody>
         </table>
      </div>
   </div>
   </body>

   </html>