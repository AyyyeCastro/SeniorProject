<?php
if (!array_key_exists('isLoggedIn', $_SESSION) || !$_SESSION['isLoggedIn']) {
   header("location: ../login.php");
   exit;
}

# -- Important -- #
# Set the session outside of the post request, so that the forms can get pre-filled. 

/* sender's info */
$userID = $_SESSION['userID'];
$senderInfo = $userDatabase->getUserDetails($userID);

/* receiver's info */
$receiverID = $userID;
$receiverInnie = $_GET['receiverInnie'];

$receiverID = $_GET['receiverID'];

/* message info */
$parentID = $_GET['parentID'];
$messageID = $_GET['messageID'];
$messageDetails = $userDatabase->getMessageDetails($messageID);
$messageSentOn = $messageDetails['messageSentOn'];

/* Get receiver's mini-profile  */
$profileName = $messageDetails['senderInnie'];
$profileInfo = $userDatabase->getProfileByName($profileName);

/* Get requested product info */
$taskID = $messageDetails['taskID'];
$listDetails = $userDatabase->getListForm($taskID);

/* bread crumbs */
$convoDetails = $userDatabase->getMessageCrumbs($taskID, $parentID, $messageSentOn);
$memoDetails = $userDatabase->getMemoCrumbs($parentID, $messageSentOn);

$deleteMessage = [];
/* Get seller's info (logged in user)
Redeclare $userID as the logged in user's ID -> $receiverID
*/
#----------------#
if (isPostRequest()) {

   if (isset($_POST['btnSend'])) {
      $taskID = filter_input(INPUT_POST, 'taskID');
      $parentID = $_POST['parentID'];
      /* get the $userID thensent it to the method */
      $receiverID = filter_input(INPUT_POST, 'receiverID');
      $receiverInnie = filter_input(INPUT_POST, 'receiverInnie');
      $senderID = filter_input(INPUT_POST, 'senderID');
      $senderInnie = filter_input(INPUT_POST, 'senderInnie');
      $messageTitle = filter_input(INPUT_POST, 'messageTitle');
      $messageDesc = filter_input(INPUT_POST, 'messageDesc');
      $isMessageReplied = filter_input(INPUT_POST, 'isMessageReplied');

      /* for updateIsRepliedMessage function */
      $priorMessageID = filter_input(INPUT_POST, 'priorMessageID');
      $updateStatus = filter_input(INPUT_POST, 'updateStatus');


      if (
         $userDatabase->sendMessage(
            $parentID,
            $senderID,
            $receiverID,
            $taskID,
            $messageTitle,
            $messageDesc,
            $senderInnie,
            $receiverInnie,
            $isMessageReplied
        )
         && $userDatabase->updateIsMessageReplied($priorMessageID, $updateStatus)
      ) {
         header('Location: viewInbox.php');
         $message = "Your message Was Sent!";

      } else {
         $message = "Error sending message, please try again.";
      }
   }
   if (isset($_POST['btnConfirmSale'])) {
      $taskID = filter_input(INPUT_POST, 'taskID');
      $listProdCat = filter_input(INPUT_POST, 'listProdCat');
      $listProdPrice = filter_input(INPUT_POST, 'listProdPrice');
      $listProdTitle = filter_input(INPUT_POST, 'listProdTitle');
      $listCond = filter_input(INPUT_POST, 'listCond');
      $orderID = uniqid();
      $isTaskDone = filter_input(INPUT_POST, 'isTaskDone');

      /* defaul sale msg */
      $parentID = $_POST['parentID'];
      /* get the $userID thensent it to the method */
      $receiverID = filter_input(INPUT_POST, 'receiverID');
      $receiverInnie = filter_input(INPUT_POST, 'receiverInnie');
      $senderID = filter_input(INPUT_POST, 'senderID');
      $senderInnie = filter_input(INPUT_POST, 'senderInnie');
      $messageTitle = filter_input(INPUT_POST, 'messageTitle');
      $messageDesc = filter_input(INPUT_POST, 'messageDesc');
      $isMessageReplied = filter_input(INPUT_POST, 'isMessageReplied');


      /* for updateIsRepliedMessage function */
      $priorMessageID = filter_input(INPUT_POST, 'priorMessageID');
      $updateStatus = filter_input(INPUT_POST, 'updateStatus');

      if (
         $userDatabase->defaultSaleMsg(
            $parentID,
            $senderID,
            $receiverID,
            $taskID,
            $messageTitle,
            $messageDesc,
            $senderInnie,
            $receiverInnie
         )
         && $userDatabase->confirmSale($taskID, $senderID, $receiverID, $orderID, $senderInnie, $receiverInnie)
         && $userDatabase->updateIsMessageReplied($priorMessageID, $updateStatus)
      ) {
         header('Location: viewSaleHistory.php');
      } else {
         header('Location: viewSaleHistory.php');
      }
   }

}
?>