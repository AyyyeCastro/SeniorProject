<!DOCTYPE html>
<?php
include_once 'include/functions.php';
include_once 'model/userController.php';


$_SESSION['isLoggedIn'] = false;

$message = "";
if (isPostRequest()) {
   if (isset($_POST['login'])) {
      $userName = filter_input(INPUT_POST, 'userName');
      $PW = filter_input(INPUT_POST, 'userPW');

      $configFile =  'model/dbconfig.ini';
      try {
         $userDatabase = new Users($configFile);
      } catch (Exception $error) {
         echo "<h2>" . $error->getMessage() . "</h2>";
      }

      if ($userDatabase->isUserTrue($userName, $PW)) {
         session_start();
         $_SESSION['isLoggedIn'] = true;
         $_SESSION['userID'] = $userDatabase->getUserId($userName);
         $visitCrumb = $_SESSION['visitCrumb'] ?? 'backend/viewProfile.php';
         header("location: $visitCrumb");
      } else {
         $message = "Incorrect login credentials. Please try again.";
      }
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
    color: #757790;
  }

  .formWebName {
    text-align: center;
    color: #757790; /* E1B0F8 cotton candy pink*/
    font-size: 65px;
  }

  .bodyContainer {
    background-image: url("./include/materials/signUpImage.png  ");
  background-size:cover;    /* Cover the entire container */
  background-position: center; /* Center the image within the container */
  background-repeat: no-repeat; /* Prevent tiling the image */
    padding: 35px;
    height: 100%;
  }

  .loginContainer {
      border-radius: 15px;
      background-color: #FFFF;
      max-width: 600px;
      min-height: 600px;
      padding: 100px;
   }

  form label {
    color: #757790;
  }

  form input{
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
    color: #757790;
    border: 2px solid #757790;
  }

  .formWarning {
    margin-top: 150px;
    color: #757790;
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
      <div class="container loginContainer">
         <div class="row">
            <div class="col-md-12">
               <p class="formWebName">Bubble</p>
            </div>
         </div>
         <form action="login.php" method="POST">
            <div class="form-group">
               <input type="hidden" name="visitCrumb" value="<?php echo htmlspecialchars($_GET['visitCrumb'] ?? ''); ?>">
               <label for="username">Username</label>
               <input type="text" class="form-control" id="userName" name="userName" placeholder="Enter username">
            </div>
            <div class="form-group">
               <label for="password">Password</label>
               <input type="password" class="form-control" id="userPW" name="userPW" placeholder="Enter password">
            </div>
            <button type="submit" name="login" class="btn formBtns formBtn1">Log In</button>

            <a href="signup.php" class="btn formBtns formBtn2">Sign Up</a>
         </form>

         <?php
         if ($message) { ?>
            <div class="row formWarning">
               <div class="col-md-12">
                  <?php echo $message; ?>
               </div>
            </div>
         <?php }
         ?>
      </div>
   </div>
</body>

</html>