<?php
/* get the list ID from the URL, send it to the method */
$parentID = NULL;
$userID = filter_input(INPUT_GET, 'userID');
$userInfo = $userDatabase->getUserDetails($userID);
/* get the seller's info */
$sellerInfo = $userDatabase->getUserDetails($userID);
/* get customer's info */
$senderID = $_SESSION['userID'];
$customerInfo = $userDatabase->getCustomerDetails($senderID);
/* Set empty arrays */
$fileDestination = "";

if (!array_key_exists('isLoggedIn', $_SESSION) || !$_SESSION['isLoggedIn']) {
   $_SESSION['visitCrumb'] = 'backend/productDetails.php?taskID=' . $taskID;
   header("location: ../login.php");
   exit;
}

if (isPostRequest()) {
   $parentID = uniqid();
   $taskID = filter_input(INPUT_POST, 'taskID');
   $senderID = $_SESSION['userID'];
   /* get the $userID thensent it to the method */
   $sellerID = filter_input(INPUT_POST, 'sellerID');
   $sellerInnie = filter_input(INPUT_POST, 'sellerInnie');
   $customerInnie = filter_input(INPUT_POST, 'customerInnie');
   $messageTitle = filter_input(INPUT_POST, 'messageTitle');
   $messageDesc = filter_input(INPUT_POST, 'messageDesc');
   $userInfo = $userDatabase->getUserDetails($userID);
   $sellerInfo = $userDatabase->getUserDetails($userID);
   $customerInfo = $userDatabase->getCustomerDetails($senderID);
   echo $isMessageReplied;


   if (
      $userDatabase->sendMemo(
         $parentID,
         $senderID,
         $sellerID,
         $messageTitle,
         $messageDesc,
         $customerInnie,
         $sellerInnie,
         $isMessageReplied
     )
   ) {
      header("location: viewProfile.php");
      $message = "Your Request Was Sent!";

   } else {
      $message = "Error sending message, please try again.";
   }
}
?>