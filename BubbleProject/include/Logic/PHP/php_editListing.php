<?php
$message = "";
if (!array_key_exists('isLoggedIn', $_SESSION) || !$_SESSION['isLoggedIn']) {
   header("location: ../login.php");
   exit;
}

// set array
$deleteList = [];
// get logged in user's ID
$userID = $_SESSION['userID'];
// call the function, and set it to $userInfo to gather database details.
$userInfo = $userDatabase->getUserDetails($userID);

if (isGetRequest()) {
   # -- Important -- #
   # Set the session outside of the post request, 
   # so that the forms can get pre-filled. 
   $taskID = $_GET['taskID'];

   # array to store all of the list details.
   $taskDetails = $userDatabase->getListForm($taskID);
   $groupList = $userDatabase->getAllGroups($userID);
}


if (isPostRequest()) {
   if (isset($_POST['updateBtn'])) { // if updateBtn is clicked ->

      // variables from the updateUserListing() function is declared, as values sent from the HTML form.
      $taskID = filter_input(INPUT_POST, 'taskID');
      $taskTitle = filter_input(INPUT_POST, 'inputTaskTitle');
      $taskDesc = filter_input(INPUT_POST, 'inputTaskDesc');
      $groupID = filter_input(INPUT_POST, 'inputGroup');
      $timeTaskDue = filter_input(INPUT_POST, 'inputTimeTaskDue');


      # -- IMPORTANT!!! -- #
      # Information gathered from the db.
      $taskDetails = $userDatabase->getListForm($taskID);
      $groupList = $userDatabase->getAllGroups($userID);

      if (
         $userDatabase->updateUserListing(
            $taskID,
            $groupID,
            $taskDesc,
            $taskTitle,
            $timeTaskDue
        
      )) {
         // if updateUserListing() works, then redirect to the task details.
         header("location: productDetails.php?taskID=" . $taskID);
      } else {
         $message = "Error posting new listing, please try again.";
      }
   }
   if (isset($_POST['cancelBtn'])) {
      echo '<script>setTimeout(function() { window.location.href = "viewProfile.php"; }, 2);</script>';
   }
}
?>