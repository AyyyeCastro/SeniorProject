<!DOCTYPE html>
<?php
require "../include/header.php";
?>
<link rel="stylesheet" href="../include/stylesheets/global.css">
<link rel="stylesheet" href="../include/stylesheets/plugInHome.css">

<?php if (!isUserLoggedIn()) { ?>
   <style>
      .row1container-fluid {
         background-color: #F2F2F2;
         margin-left: 0px !important;
      }
   </style>
<?php } ?>


<div class="container customContainer" style>

<div class="row1container-fluid">
   <div class="col-lg-12 mainContainer">
   </div>
</div>

<div class="row1container-fluid">
   <div class="col-lg-12 secondContainer">
   </div>
</div>
<div class="row1container-fluid">
   <div class="col-lg-12 thirdContainer">
   </div>
</div>
<div class="row1container-fluid">
   <div class="col-lg-12 fourthContainer">
   </div>
</div>

</div>

<!-- Footer -->
<footer class="text-center text-lg-start bg-light text-muted">
   <section class="">
      <div class="container text-center text-sm-start">
         <div class="row mt-3">
            <div class="col-xs-3 col-xs-4 col-xs-3 mx-auto mb-md-0 mb-4">
               <a href="https://github.com/AyyyeCastro" class="me-4 text-reset">
                  <i class="fab fa-github fa-xl"></i> Github
               </a>
            </div>
         </div>
      </div>
   </section>
   <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
      © 2024 Bubble
   </div>
</footer>
<!-- Footer -->
</body>

</html>


