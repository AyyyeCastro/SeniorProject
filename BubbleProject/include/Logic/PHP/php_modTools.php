<?php

// NOTE. modTools was not a core design/agreement to the project. This was done at the last minute for extra work! 
// There may be some unknown (to me) bugs.



if (!array_key_exists('isLoggedIn', $_SESSION) || !$_SESSION['isLoggedIn']) {
   header("location: ../login.php");
   exit;
}
$userID = $_SESSION['userID'];

$message = "";
$groupList = $userDatabase->getAllGroups($userID);
$userList = $userDatabase->getAllUsers($userID);
$condList = $userDatabase->getAllConditions();

if (isPostRequest()) {

   if (isset($_POST['updateGroupBtn'])) {
      $oldGroupName = filter_input(INPUT_POST, 'oldGroupName');
      $newGroupName = filter_input(INPUT_POST, 'inputGroup');

      if ($userDatabase->modUpdateGroup($userID, $newGroupName, $oldGroupName)) {
         echo '<script>setTimeout(function() { window.location.href = "modTools.php"; }, 2);</script>';
      }
   }

   if (isset($_POST['deleteGroupBtn'])) {
      $inputGroup = filter_input(INPUT_POST, 'inputGroup');

      if ($userDatabase->modDeleteGroup($userID, $inputGroup)) {
         echo '<script>setTimeout(function() { window.location.href = "modTools.php"; }, 2);</script>';
      }
   }
   if (isset($_POST['insertGroupBtn'])) {
      $inputGroup = filter_input(INPUT_POST, 'inputGroup');

      if ($userDatabase->modNewGroup($userID, $inputGroup)) {
         echo '<script>setTimeout(function() { window.location.href = "modTools.php"; }, 2);</script>';
      }
   }

   if (isset($_POST['updateUserBtn'])) {
      $oldInnie = filter_input(INPUT_POST, 'userInnie');
      $newInnie = filter_input(INPUT_POST, 'inputInnie');
      $groupID = filter_input(INPUT_POST, 'groupID'); // Get the selected group ID
  
      if ($userDatabase->modUpdateUser($userID, $newInnie, $oldInnie, $groupID)) {
          echo '<script>setTimeout(function() { window.location.href = "modTools.php"; }, 2);</script>';
      } else {
          echo '<div class="container" id="warnMod" style="color: red;font-size: 52px;"><div class="row"><div class="col-md-12">User Innie doesn\'t match or was an admin.</div></div></div>';
          echo '<script>setTimeout(function() { window.location.href = "modTools.php"; }, 3500);</script>';
      }
  }
  
   if (isset($_POST['deleteUserBtn'])) {
      $inputInnie = filter_input(INPUT_POST, 'inputInnie');
      echo $inputInnie;

      if ($userDatabase->modDeleteUser($userID, $inputInnie)){
         echo '<script>setTimeout(function() { window.location.href = "modTools.php"; }, 2);</script>';
      }
      else{
         echo '<div class="container" id="warnMod" style="color: red;font-size: 52px;><div class="row"><div class="col-md-12">User Innie doesnt match or was an admin.</div></div></div>';
         echo '<script>setTimeout(function() { window.location.href = "modTools.php"; }, 3500);</script>';
      }
   }
   
}
?>