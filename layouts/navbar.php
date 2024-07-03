 <!-- ======= Header ======= -->
 <header id="header" class="header fixed-top d-flex align-items-center">

   <div class="d-flex align-items-center justify-content-between logo-bg toggle-sidebar">
     <i class="bi bi-list toggle-sidebar-btn"></i>
     <a href="<?php
              if ($_SESSION['user_role'] == 'Admin') {
                echo BASE_URL . '/admin/dashboard.php';
              } else {
                echo BASE_URL . '/staff/dashboard.php';
              }
              ?>" class="logo d-flex align-items-center ps-2 pe-custom">
       <img src="<?php echo BASE_URL . '/assets/img/logo-no-background.png' ?>" alt="" height="40">
     </a>
   </div><!-- End Logo -->

   <div class="header-date ps-3 d-md-block d-none">
     <strong><?php echo date("F j, Y, g:i a"); ?></strong>
   </div>

   <nav class="header-nav ms-auto pe-2">
     <ul class="d-flex align-items-center">

       <li class="nav-item dropdown">
         <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
           <img src="<?php echo BASE_URL . '/uploads/users/' ?><?php echo  $_SESSION['image']; ?>" alt="Profile" class="rounded-circle">
           <span class="d-none d-md-block dropdown-toggle px-2"><?php echo $_SESSION['username']; ?></span>
         </a><!-- End Profile Image Icon -->

         <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
           <li class="dropdown-header">
             <h6><?php echo $_SESSION['name']; ?></h6>
             <span><?php echo $_SESSION['user_role']; ?></span>
           </li>
           <li>
             <hr class="dropdown-divider">
           </li>

           <li>
             <a class="dropdown-item d-flex align-items-center" href="<?php echo BASE_URL . '/profile.php' ?>">
               <i class="bi bi-person"></i>
               <span>My Profile</span>
             </a>
           </li>
           <li>
             <hr class="dropdown-divider">
           </li>

           <li>
             <a class="dropdown-item d-flex align-items-center" href="<?php echo BASE_URL . '/profile-setting.php' ?>">
               <i class="bi bi-gear"></i>
               <span>Account Settings</span>
             </a>
           </li>
           <li>
             <hr class="dropdown-divider">
           </li>
           <li>
             <hr class="dropdown-divider">
           </li>

           <li>
             <a class="dropdown-item d-flex align-items-center" href="<?php echo BASE_URL . '/auth/logout-process.php' ?>">
               <i class="bi bi-box-arrow-right"></i>
               <span>Sign Out</span>
             </a>
           </li>

         </ul><!-- End Profile Dropdown Items -->
       </li><!-- End Profile Nav -->

     </ul>
   </nav><!-- End Icons Navigation -->

 </header><!-- End Header -->