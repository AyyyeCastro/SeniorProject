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

# ----------------#


if (isPostRequest()) {
   if (isset($_POST['updateBtn'])) {

      // variables utilized in the below functions, set as values sent from the HTML form.
      $userBubbleCode = filter_input(INPUT_POST, 'userBubbleCode');

      # Gather db info.
      $userInfo = $userDatabase->getUserDetails($userID);

      if ($userDatabase->updateTeamCode($userID, $userBubbleCode)) {
         echo '<script>setTimeout(function() { window.location.href = "editTeamCode.php"; }, 2);</script>';
      } else {
         $message = "Error in updating profile, please try again.";
      }
   }
}
?>