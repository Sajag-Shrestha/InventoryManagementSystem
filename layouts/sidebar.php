 <!-- ======= Sidebar ======= -->
 <aside id="sidebar" class="sidebar">

<ul class="sidebar-nav" id="sidebar-nav">

  <li class="nav-item">
    <a class="nav-link collapsed" href="<?php echo BASE_URL . '/admin/dashboard.php' ?>">
    <i class="material-symbols-outlined"">dashboard</i>
      <span>Dashboard</span>
    </a>
  </li><!-- End Dashboard Nav -->

 
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-person-fill-gear"></i><span>Users</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="forms-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
      <li>
        <a href="<?php echo BASE_URL . '/admin/users/create.php' ?>">
          <i class="bi bi-circle"></i><span>Add User</span>
        </a>
      </li>
      <li>
        <a href="<?php echo BASE_URL . '/admin/users/index.php' ?>">
          <i class="bi bi-circle"></i><span>Manage Users</span>
        </a>
      </li>
    </ul>
  </li><!-- End Forms Nav -->
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#categories" data-bs-toggle="collapse" href="#">
    <i class="material-symbols-outlined">category</i>
    <span>Categories</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="categories" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="<?php echo BASE_URL . '/admin/categories/create.php' ?>">
          <i class="bi bi-circle"></i><span>Add Category</span>
        </a>
      </li>
      <li>
        <a href="<?php echo BASE_URL . '/admin/categories/index.php' ?>">
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
        <a href="<?php echo BASE_URL . '/admin/products/create.php' ?>">
          <i class="bi bi-circle"></i><span>Add Product</span>
        </a>
      </li>
      <li>
        <a href="<?php echo BASE_URL . '/admin/products/index.php' ?>">
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
        <a href="<?php echo BASE_URL . '/admin/orders/create.php' ?>">
          <i class="bi bi-circle"></i><span>Add Order</span>
        </a>
      </li>
      <li>
        <a href="<?php echo BASE_URL . '/admin/orders/index.php' ?>">
          <i class="bi bi-circle"></i><span>Manage Orders</span>
        </a>
      </li>
    </ul>
  </li><!-- End Forms Nav -->
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#media" data-bs-toggle="collapse" href="#">
      <i class="bi bi-images"></i><span>Media</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="media" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="<?php echo BASE_URL . '/admin/media/create.php' ?>">
          <i class="bi bi-circle"></i><span>Add Image</span>
        </a>
      </li>
      <li>
        <a href="<?php echo BASE_URL . '/admin/media/index.php' ?>">
          <i class="bi bi-circle"></i><span>Manage Images</span>
        </a>
      </li>
    </ul>
  </li><!-- End Forms Nav -->

  <li class="nav-heading">Admin</li>

  <li class="nav-item">
    <a class="nav-link collapsed" href="<?php echo BASE_URL . '/admin/settings.php' ?>">
      <i class="bi bi-gear-fill"></i>
      <span>Settings</span>
    </a>
  </li><!-- End Profile Page Nav -->

</ul>

</aside><!-- End Sidebar-->
