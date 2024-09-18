<!DOCTYPE html>
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
require '../include/functions.php';
require '../model/userController.php';
$configFile = '../model/dbconfig.ini';
try {
  $userDatabase = new Users($configFile);
} catch (Exception $error) {
  echo "<h2>" . $error->getMessage() . "</h2>";
}
?>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PlugIn</title>

  <!-- stylesheets -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
    integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500&display=swap" rel="stylesheet">
  <!-- scripts -->
  <script src="https://cdn.tiny.cloud/1/9yk0iyxnanrkhcdqgc0l40rq3lxpl4ji336zutoiwao5vbd7/tinymce/5/tinymce.min.js"
    referrerpolicy="origin"></script>
  <script src="https://kit.fontawesome.com/7a790d5aa6.js" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
    integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
    integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
    crossorigin="anonymous"></script>

</head>
<style>
  /* Hide topNav links by default */

  .topNav {
    background-color: #4F539F !important;
    z-index: 100 !important;
    padding: 15px;
  }

  .topNav .navbar-nav .nav-item {
    display: none;
  }


  /* Custom styles for side-navbar */
  .side-navbar {
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    min-width: 100px;
    padding: 15px;
    overflow-x: show;
    background-color: #f8f9fa;
    box-shadow: 5px 0px 10px 10px rgba(108, 122, 137, .1);
    z-index: 10 !important;
  }

  .firstItemLoggedIn {
    margin-top: 30vh;
  }

  .sideButton {
    padding-top: 65px;
    justify-content: center;
  }


  .side-navbar .dropdown-menu {
    position: relative;
  }

  .side-navbar-collapse {
    margin-left: 200px;
    padding: 25px;
  }

  /* Hide side-navbar on smaller screens */
  @media (max-width: 1740px) {
    .side-navbar {
      display: none;
    }

    .side-navbar-collapse {
      margin-left: 0;
      padding-left: 0;
    }

    .topNav .navbar-nav .nav-item {
      display: inline;
    }

  }

  /* Other styles */
  .navbar {
    border-bottom: 1px solid rgba(186, 186, 186, .4);
    background-clip: padding-box;
    -webkit-background-clip: padding-box;
  }

  body,
  html {
    font-size: 16px;
    font-family: 'Sora', sans-serif;
    color: black;
  }

  .make-centered {
    height: 100%;
    display: flex;
    align-items: center;
  }

  a {
    color: #506d90;
    text-decoration: none;
  }

  a .hover {
    color: #506d90;
    font-weight: bold;
    text-decoration: none;
  }

  .topNav a,
  .side-navbar a {
    color: #E2DFD2 !important;
    text-decoration: none;
  }

  .topNav a:hover {
    color: #f8f9fa !important;
  }

  .faCustomColor {
    color: #E2DFD2 !important;
  }

  .dropdown-item:hover {
    background-color: #525466 !important;
  }

  .navbar-brand {
    margin-left: 5px;
    color: #E2DFD2 !important;
  }

  .siteIcon {
    border-radius: 10px;
  }

  .bubbleLogoText {
    color: white;
    font-weight: 700;
    margin-top: 15px;
  }

  .dropdown-menu {
    background-color: #5e5f74 !important;
    border: 1px solid #E2DFD2 !important;
  }
</style>
<?php if (!isUserLoggedIn()) { ?>
  <style>
    .topNav .navbar-nav .nav-item {
      display: inline;
    }
  </style>
<?php } ?>
</head>

<body>
  <nav class="navbar topNav navbar-expand-xl navbar-light bg-light">
    <img src="../include/materials/bubbleIcon3.png" class="siteIcon" width="50" height="50"
      class="d-inline-block align-top" alt="">
    <a class="navbar-brand" href="plugInHome.php">
      <p class="bubbleLogoText">
        Bubble
      </p>
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <!-- Everyone -->

        <!-- Signed in only -->
        <?php if (isUserLoggedIn()): ?>
          <li class="nav-item"><samp></samp>
            <a class="nav-link make-centered" href="postListing.php">
              <button class="btn"><i class="fa-regular fa-calendar-plus fa-lg faCustomColor"></i></button> Create Task
            </a>
          </li>

          <li class="nav-item btnInbox">
            <a class="nav-link make-centered" href="viewMailbox.php">
              <button class="btn"><i class="fa-regular fa-paper-plane faCustomColor fa-lg"></i></button> Inbox
            </a>
          </li>
          <li class="nav-item btnInbox">
            <a class="nav-link make-centered" href="viewInbox.php">
              <button class="btn"><i class="fa-solid fa-ticket faCustomColor fa-lg"></i></button> Tickets
            </a>
          </li>

          <!-- add this li item after the Inbox button -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle make-centered" href="#" id="navbarDropdown" role="button"
              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <button class="btn"><i class="fa-solid fa-user fa-xl faCustomColor"></i></button> Account
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="viewProfile.php">
                <button class="btn"><i class="fa-regular fa-id-badge fa-lg faCustomColor"></i></button> Your Profile
              </a>
              <a class="dropdown-item" href="viewPurchaseHistory.php">
                <button class="btn"><i class="fa-solid fa-box-archive fa-lg faCustomColor"></i></button>Completed Tasks
              </a>
              <a class="dropdown-item" href="logoff.php" onclick="return confirm('Logout?')">
                <button class="btn"><i class="fa-solid fa-plug-circle-minus fa-xl faCustomColor"></i></button> Logout
              </a>
              <?php $sessionID = $_SESSION['userID'];
              if ($userDatabase->headerModCheck($sessionID)): ?>
                <a class="dropdown-item" href="modTools.php">
                  <button class="btn"><i class="fa-solid fa-screwdriver-wrench fa-xl faCustomColor"></i></button> Mod Tools
                </a>
              <?php endif ?>
            </div>
          </li>
        <?php endif ?>

        <!-- only if not signed in -->
        <?php if (!isUserLoggedIn()) { ?>
          <li class="nav-item">
            <a class="nav-link" href="../login.php"><i
                class="fa-solid fa-plug-circle-plus fa-xl faCustomColor"></i>Account</a>
          </li>
          <?php
        } ?>
      </ul>

      <?php if (isUserLoggedIn()): ?>
        <form class="form-inline my-2 my-lg-0" method="get" action="displayResults.php">
          <select class="form-control mr-sm-2" name="search_option">
            <option value="Products">Tasks</option>
            <option value="Sellers">Users</option>
          </select>
          <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="inputName" />
          <button class="btn btn-outline-primary my-2 my-sm-0" type="submit" name="search" value="Search"><i
              class="fa-solid fa-magnifying-glass"></i></button>
        </form>
      <?php endif ?>
    </div>
  </nav>

  <?php if (isUserLoggedIn()): ?>
    <div class="side-navbar">
      <ul class="navbar-nav">
        <?php if (isUserLoggedIn()): ?>
          <li class="nav-item">
            <a class="nav-link make-centered firstItemLoggedIn sideButton" href="postListing.php">
              <button class="btn"><i class="fa-regular fa-calendar-plus fa-xl"></i></button>
            </a>
          </li>
          <li class="nav-item make-centered sideButton">
            <a class="nav-link make-centered" href="viewMailbox.php">
              <button class="btn"><i class="fa-regular fa-paper-plane fa-xl"></i></button>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link make-centered sideButton" href="viewInbox.php">
              <button class="btn"><i class="fa-solid fa-ticket fa-xl"></i></button>
            </a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle make-centered sideButton" href="#" id="navbarDropdown" role="button"
              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <button class="btn"><i class="fa-solid fa-user fa-xl"></i></button>
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="viewProfile.php">
                <button class="btn"><i class="fa-regular fa-id-badge fa-lg faCustomColor"></i></button> Your Profile
              </a>
              <a class="dropdown-item" href="viewPurchaseHistory.php">
                <button class="btn"><i class="fa-solid fa-box-archive fa-lg faCustomColor"></i></button>Completed Tasks
              </a>
              <a class="dropdown-item" href="logoff.php" onclick="return confirm('Logout?')">
                <button class="btn"><i class="fa-solid fa-plug-circle-minus fa-lg faCustomColor"></i></button> Logout
              </a>
              <?php $sessionID = $_SESSION['userID'];
              if ($userDatabase->headerModCheck($sessionID)): ?>
                <a class="dropdown-item " href="modTools.php">
                  <button class="btn"><i class="fa-solid fa-screwdriver-wrench fa-lg faCustomColor"></i></button> Mod Tools
                </a>
              <?php endif ?>
            </div>
          </li>
        <?php endif ?>
        <?php if (!isUserLoggedIn()) { ?>
          <li class="nav-item">
            <a class="nav-link" href="../login.php"><i class="fa-solid fa-plug-circle-plus fa-xl"></i> Account</a>
          </li>
        <?php } ?>
      </ul>
    </div>
  <?php endif ?>
  </nav>