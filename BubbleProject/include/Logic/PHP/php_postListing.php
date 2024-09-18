<?php
if (!array_key_exists('isLoggedIn', $_SESSION) || !$_SESSION['isLoggedIn']) {
   header("location: ../login.php");
   exit;
}

$message = "";
# -- Important -- #
# Set the session outside of the post request, so that the forms can get pre-filled. 
$userID = $_SESSION['userID'];
$userInfo = $userDatabase->getUserDetails($userID);
$groupList = $userDatabase->getAllGroups($userID);


# ----------------#

if (isPostRequest()) {
   $groupName = filter_input(INPUT_POST, 'inputGroup');
   $groupID = $userDatabase->getGroupIDByName($groupName, $userID);

   if ($groupID === false) {
      $message = "Error: Group not found.";
   } else {
      $taskTitle = filter_input(INPUT_POST, 'inputTaskTitle');
      $taskDesc = filter_input(INPUT_POST, 'inputTaskDesc');
      $timeTaskDue = filter_input(INPUT_POST, 'inputTimeTaskDue');

      if (
         $userDatabase->postTask(
            $userID,
            $groupID,
            $taskTitle,
            $taskDesc,
            $timeTaskDue
        )
      ) {
         header("location: ../backend/viewProfile.php");
      } else {
         $message = "Error posting new task, please try again.";
      }
   }
}

?>