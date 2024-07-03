 <!-- ======= Sidebar ======= -->
 <aside id="sidebar" class="sidebar">

   <ul class="sidebar-nav" id="sidebar-nav">

     <li class="nav-item">
       <a class="nav-link collapsed" href="<?php echo BASE_URL . '/staff/dashboard.php' ?>">
         <i class="material-symbols-outlined"">dashboard</i>
      <span>Dashboard</span>
    </a>
  </li><!-- End Dashboard Nav -->

 
  <li class=" nav-item">
           <a class="nav-link collapsed" data-bs-target="#categories" data-bs-toggle="collapse" href="#">
             <i class="material-symbols-outlined">category</i>
             <span>Categories</span><i class="bi bi-chevron-down ms-auto"></i>
           </a>
           <ul id="categories" class="nav-content collapse " data-bs-parent="#sidebar-nav">
             <li>
               <a href="<?php echo BASE_URL . '/staff/categories/create.php' ?>">
                 <i class="bi bi-circle"></i><span>Add Category</span>
               </a>
             </li>
             <li>
               <a href="<?php echo BASE_URL . '/staff/categories/index.php' ?>">
                 <i class="bi bi-circle"></i><span>Manage Categories</span>
               </a>
             </li>
           </ul>
     </li><!-- End Forms Nav -->
     <li class="nav-item">
       <a class="nav-link collapsed" data-bs-target="#products" data-bs-toggle="collapse" href="#">
         <i class="bi bi-box-seam-fill"></i><span>Products</span><i class="bi bi-chevron-down ms-auto"></i>
       </a>
       <ul id="products" class="nav-content collapse " data-bs-parent="#sidebar-nav">
         <li>
           <a href="<?php echo BASE_URL . '/staff/products/create.php' ?>">
             <i class="bi bi-circle"></i><span>Add Product</span>
           </a>
         </li>
         <li>
           <a href="<?php echo BASE_URL . '/staff/products/index.php' ?>">
             <i class="bi bi-circle"></i><span>Manage Products</span>
           </a>
         </li>
       </ul>
     </li><!-- End Forms Nav -->
     <li class="nav-item">
       <a class="nav-link collapsed" data-bs-target="#orders" data-bs-toggle="collapse" href="#">
         <i class="material-symbols-outlined">order_play</i>Orders</span><i class="bi bi-chevron-down ms-auto"></i>
       </a>
       <ul id="orders" class="nav-content collapse " data-bs-parent="#sidebar-nav">
         <li>
           <a href="<?php echo BASE_URL . '/staff/orders/create.php' ?>">
             <i class="bi bi-circle"></i><span>Add Order</span>
           </a>
         </li>
         <li>
           <a href="<?php echo BASE_URL . '/staff/orders/index.php' ?>">
             <i class="bi bi-circle"></i><span>Manage Orders</span>
           </a>
         </li>
       </ul>
     </li><!-- End Forms Nav -->
     <li class="nav-item">
       <a class="nav-link collapsed" href="<?php echo BASE_URL . '/staff/media/index.php' ?>">
         <i class="bi bi-images"></i>
         <span>Media</span>
       </a>
     </li><!-- End Profile Page Nav -->
   </ul>
   </li><!-- End Forms Nav -->

   </ul>

 </aside><!-- End Sidebar-->