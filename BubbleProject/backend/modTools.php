<?php
ob_start();
require '../include/header.php';
require '../include/logic/php/php_modTools.php';

$userID = $_SESSION['userID'];
$modCheck = $userDatabase->getUserDetails($userID);

if ($modCheck['isModerator'] == 'YES'):
   ?>
   <link rel="stylesheet" href="../include/stylesheets/global.css">
   <link rel="stylesheet" href="../include/stylesheets/modTools.css">
   <div class="container-fluid">
      <div class="container">
         <div class="row">
            <div class="col-md-12">
               <form method="POST" action="modTools.php" class="formContainer">
                  <div class="form-group">
                     <h2>GROUPS<h2>
                  </div>
                  <div class="form-group">
                     <label for="oldGroupName">Select Group</label>
                     <select class="form-control" name="oldGroupName">
                        <option value="" disabled selected>Group Name</option>
                        <?php
                        foreach ($groupList as $group) {
                           echo '<option value="' . $group['groupName'] . '">' . $group['groupName'] . '</option>';
                        }
                        ?>
                     </select>
                  </div>
                  <div class="form-group">
                     <label for="inputGroup">Confirm name:</label>
                     <input type="text" class="form-control" name="inputGroup" required>
                     <small class="form-text text-muted">
                        Select a group and type it's updated name. Type a group and click 'new', for a new group. Select a
                        group and re-type it's name to delete.
                     </small>
                  </div>
                  <div class="form-group">
                     <button type="submit" class="customBtn" name="updateGroupBtn"
                        onclick="return confirm('Do you want to update the group?')">
                        <i class="fa-solid fa-square-pen fa-xl"></i> Update
                     </button>
                     <button type="submit" class="warningBtn" name="deleteGroupBtn"
                        onclick="return confirm('Are you sure you want to delete this group? It is a permenant decision.')">
                        <i class="fa-solid fa-trash fa-xl"></i> Delete
                     </button>
                     <button type="submit" class="customerOtherBtn" name="insertGroupBtn"
                        onclick="return confirm('Insert this new group?')">
                        <i class="fa-solid fa-circle-plus"></i> New
                     </button>
                  </div>
               </form>
            </div>

            <!-- <div class="col-md-6">
               <form method="POST" action="modTools.php" class="formContainer" id='userForm'>
                  <div class="form-group">
                     <h2>CONDITION MANAGEMENT<h2>
                  </div>
                  <div class="form-group">
                     <label for="oldCond">Category to Update/Delete</label>
                     <select class="form-control" name="oldCond">
                        <option value="" disabled selected>Choose category</option>
                        <?php
                        foreach ($condList as $condition) {
                           echo '<option value="' . $condition['condType'] . '">' . $condition['condType'] . '</option>';
                        }
                        ?>
                     </select>
                  </div>
                  <div class="form-group">
                     <label for="inputCond">Product Condition:</label>
                     <input type="text" class="form-control" name="inputCond" required>
                     <small class="form-text text-muted">
                        Select a condition and type the updated name. Select a condition and re-type the name to delete.
                     </small>
                  </div>
                  <div class="form-group">
                     <button type="submit" class="customBtn" name="updateCondBtn"
                        onclick="return confirm('Do you want to update this condition?')">
                        <i class="fa-solid fa-square-pen fa-xl"></i> Update
                     </button>
                     <button type="submit" class="warningBtn" name="deleteCondBtn"
                        onclick="return confirm('Are you sure you want to delete this condition? It is a permenant decision.')">
                        <i class="fa-solid fa-trash fa-xl"></i> Delete
                     </button>
                     <button type="submit" class="customerOtherBtn" name="insertCondBtn"
                        onclick="return confirm('Insert this new condition?')">
                        <i class="fa-solid fa-circle-plus"></i> New
                     </button>
                  </div>
               </form>
            </div> -->

            <div class="col-md-12">
               <form method="POST" action="modTools.php" class="formContainer" id='userForm'>
                  <div class="form-group">
                     <h2>USER MANAGEMENT<h2>
                  </div>
                  <div class="form-group">
                     <label for="userInnie">Users to Update/Delete</label>
                     <select class="form-control" name="userInnie">
                        <option value="" disabled selected>Choose user</option>
                        <?php
                        foreach ($userList as $users) {
                           echo '<option value="' . $users['userInnie'] . '">' . $users['userInnie'] . '</option>';
                        }
                        ?>
                     </select>
                  </div>
                  <div class="form-group">
                     <label for="inputInnie">Confirm/Update User:</label>
                     <input type="text" class="form-control" name="inputInnie" required>
                     <small class="form-text text-muted">
                        Select a user and type their updated Innie. Select a user and re-type their innie to delete.
                     </small>
                  </div>
                  <div class="form-group">
                     <label for="groupID">Confirm/Update Group:</label>
                     <select class="form-control" name="groupID" required>
                        <option value="" disabled selected>Choose group</option>
                        <?php
                        foreach ($groupList as $group) {
                           echo '<option value="' . $group['groupID'] . '">' . $group['groupName'] . '</option>';
                        }
                        ?>
                     </select>
                  </div>
                  <div class="form-group">
                     <button type="submit" class="customBtn" name="updateUserBtn"
                        onclick="return confirm('Do you want to update this user\'s details?')">
                        <i class="fa-solid fa-square-pen fa-xl"></i> Update
                     </button>
                     <button type="submit" class="warningBtn" name="deleteUserBtn"
                        onclick="return confirm('Are you sure you want to delete this user? It is a permanent decision.')">
                        <i class="fa-solid fa-trash fa-xl"></i> Delete User
                     </button>
                  </div>
               </form>
            </div>

         </div>
      </div>
   </div>
<?php else:
   header("location: ../backend/viewProfile.php"); ?>
<?php endif ?>