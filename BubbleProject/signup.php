<!DOCTYPE html>
<?php
session_start();
include_once 'include/functions.php';
include_once 'model/userController.php';

$_SESSION['isLoggedIn'] = false;


$message = "";
$configFile = 'model/dbconfig.ini';
try {
  $userDatabase = new Users($configFile);
} catch (Exception $error) {
  echo "<h2>" . $error->getMessage() . "</h2>";
}

if (isPostRequest()) {
  $userName = filter_input(INPUT_POST, 'userName');
  $PW = filter_input(INPUT_POST, 'userPW');
  $userInnie = filter_input(INPUT_POST, 'userInnie');
  $userBio = filter_input(INPUT_POST, 'userBio');
  $hasBubbleCode = filter_input(INPUT_POST, 'hasBubbleCode') === 'yes';
  $bubbleCode = $hasBubbleCode ? filter_input(INPUT_POST, 'bubbleCode') : filter_input(INPUT_POST, 'generatedBubbleCode');
  $bubbleName = !$hasBubbleCode ? filter_input(INPUT_POST, 'bubbleName') : null;

  $message = "";

  if ($userDatabase->userUniqueInnie($userInnie) && $userDatabase->userUniqueUN($userName)) {
    $userDatabase->userSignup(
      $userName,
      $PW,
      $userInnie,
      $userBio,
      $hasBubbleCode,
      $bubbleCode,
      $bubbleName
    );
    $message = "Signed up. Redirecting you back to login...";
  } else if (!$userDatabase->userUniqueInnie($userInnie) && !$userDatabase->userUniqueUN($userName)) {
    $message = "Both Innie handle and username already exist.";
  } else if (!$userDatabase->userUniqueInnie($userInnie)) {
    $message = "Innie handle already exists.";
  } else if (!$userDatabase->userUniqueUN($userName)) {
    $message = "Username already exists.";
  }
}



?>

<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bubble</title>

  <!-- stylesheets -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
    integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500&display=swap" rel="stylesheet">
  <!-- scripts -->
  <script src="https://cdn.tiny.cloud/1/9yk0iyxnanrkhcdqgc0l40rq3lxpl4ji336zutoiwao5vbd7/tinymce/5/tinymce.min.js"
    referrerpolicy="origin"></script>
  <script src="https://kit.fontawesome.com/7a790d5aa6.js" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
</head>
<style>
  body,
  html {
    height: 100vh;
    font-size: 16px;
    font-family: 'Sora', sans-serif;
    color: #4F539F;
  }

  .formWebName {
    text-align: center;
    color: #757790;
    /* E1B0F8 cotton candy pink*/
    font-size: 65px;
  }

  .bodyContainer {
    background-image: url("./include/materials/signUpImage.png  ");
    background-size: cover;
    /* Cover the entire container */
    background-position: center;
    /* Center the image within the container */
    background-repeat: no-repeat;
    /* Prevent tiling the image */
    padding: 35px;
    height: 100%;
  }

  .signUpContainer {
    border-radius: 15px;
    background-color: #FFFFFF;
    max-width: 600px;
    min-height: 800px;
    padding: 100px;
  }

  form label {
    color: #757790;
  }

  form input {
    background-color: #D9D9D9 !important;
    color: #757790 !important;
  }

  .formBtns {
    margin-top: 15px;
    float: right;
    margin-left: 10px;
    padding: 10px;
    border-radius: 10px;
    min-width: 100px;
  }

  .formBtn1 {
    color: white;
    background-color: #757790;
    border: 2px solid #757790;
  }

  .formBtn2 {
    color: #4C49D7;
    border: 2px solid #757790;
  }

  .formWarning {
    margin-top: 150px;
    color: #4C49D7;
    padding: 15px;
    background-color: #FAF9F6;
    border: dotted 4px #757790;
  }

  .formBtns:hover {
    color: white;
    background-color: #4F539F;
  }
</style>
<link rel="stylesheet" href="../include/stylesheets/global.css">
<link rel="stylesheet" href="../include/stylesheets/plugInHome.css">

<body>
  <div class="container-fluid bodyContainer">
    <div class="container signUpContainer">

      <div class="row">
        <div class="col-md-12">
          <p class="formWebName">Bubble</p>
        </div>
      </div>
      <form action="signup.php" method="POST" enctype="multipart/form-data">
        <div>
          <label for="username">Username</label>
          <input type="text" id="userName" name="userName" class="form-control" required>
          <small id="innieHelp" class="form-text text-muted">Create a login username, do not share this with
            anyone.</small>
        </div>

        <br>
        <div>
          <label for="userPW">Password</label>
          <input type="password" id="userPW" name="userPW" class="form-control" required>
          <small id="innieHelp" class="form-text text-muted">Create a login password, do not share this with
            anyone.</small>
        </div>

        <br>
        <div>
          <label for="userInnie">Your Public Handle (@)</label>
          <input type="text" id="userInnie" name="userInnie" class="form-control" maxlength="15" required>
          <small id="innieHelp" class="form-text text-muted">This will be what people in your Bubble see you as.</small>
        </div>

        <div>
          <!-- Hidden input, that will create the default bio "Say something about yourself". This connects the bio to the user's id upon sign up. -->
          <input type="hidden" id="userBio" name="userBio" class="form-control" value="Say something about yourself..."
            required>
        </div>

        <br>
        <!-- Radio buttons for bubble selection -->
        <div>
          <label>Do you have a bubble code?</label><br>
          <input type="radio" id="existingBubble" name="hasBubbleCode" value="yes" onclick="toggleBubbleOptions()"
            checked>
          <label for="existingBubble">Yes</label>
          <input type="radio" id="newBubble" name="hasBubbleCode" value="no" onclick="toggleBubbleOptions()">
          <label for="newBubble">No</label>
        </div>

        <!-- Section for entering existing bubble code -->
        <div id="bubbleCodeSection">
          <br>
          <label for="bubbleCode">Enter Bubble Code</label>
          <input type="text" id="bubbleCode" name="bubbleCode" class="form-control" required>
        </div>

        <!-- Section for creating a new bubble -->
        <div id="newBubbleSection" style="display: none;">
          <br>
          <label for="bubbleName">Name Your Bubble:</label>
          <input type="text" id="bubbleName" name="bubbleName" class="form-control">
          <br>
          <label for="generatedBubbleCode">Bubble Code:</label>
          <input type="text" id="generatedBubbleCode" name="generatedBubbleCode" class="form-control" readonly>
        </div>

        <button type="submit" class="btn formBtns formBtn1">Sign Up</button>
      </form>

      <?php
      if ($message) { ?>
        <div class="row formWarning">
          <div class="col-md-12">
            <?php
            echo $message;
            echo '<script>setTimeout(function() { window.location.href = "login.php"; }, 3500);</script>';
            ?>

          </div>
        </div>
      <?php }
      ?>

    </div>
  </div>
</body>

</html>
</body>

</html>


<!-- Bubble code javascript -->
<script>
  function toggleBubbleOptions() {
    const hasBubbleCode = document.querySelector('input[name="hasBubbleCode"]:checked').value;

    if (hasBubbleCode === 'yes') {
      document.getElementById('bubbleCodeSection').style.display = 'block';
      document.getElementById('newBubbleSection').style.display = 'none';
    } else {
      document.getElementById('bubbleCodeSection').style.display = 'none';
      document.getElementById('newBubbleSection').style.display = 'block';
      // Generate a random 10-character alphanumeric code
      document.getElementById('generatedBubbleCode').value = Math.random().toString(36).substring(2, 12).toUpperCase();
    }
  }
</script>