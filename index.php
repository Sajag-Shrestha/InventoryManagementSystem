<?php require('layouts/login_header.php'); ?>

<head>
  <title>Login</title>
</head>

<body>
  <main class="">
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center pb-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center align-items-center pb-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/inventify-high-resolution-logo-purple-transparent.png" alt="" height="60">
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">
                <div class="card-body justify-content-center align-items-center gap-2 d-flex login-header">
                  <i class="bi bi-grid-3x3-gap-fill fs-4"></i>
                  <h5 class="card-title text-center fw-bold py-1 fs-5 m-0">Login</h5>
                </div>

                <div class="card-body login-form">
                  <?php if (isset($_GET['success'])) { ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                      <strong>Account Registered Successfully!</strong>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\">"; ?>
                  <?php } ?>

                  <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <strong>Login Failed!</strong>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\">"; ?>
                  <?php } ?>

                  <?php if (isset($_GET['empty'])) { ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                      <strong>All Fields are Required!</strong>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\">"; ?>
                  <?php } ?>

                  <form class="row g-3 needs-validation" action="auth/login-process.php" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="btn-group col-12 d-flex justify-content-center gap-2 m-0 pt-4 px-4" role="group" aria-label="Basic radio toggle button group">
                      <input type="radio" class="btn-check" name="user_role" id="btnradio2" value="Staff" autocomplete="off">
                      <label class="btn btn-outline" for="btnradio2">Staff</label>

                      <input type="radio" class="btn-check" name="user_role" id="btnradio3" value="Admin" autocomplete="off">
                      <label class="btn btn-outline" for="btnradio3">Admin</label>
                    </div>

                    <div class="col-12">
                      <div class="input-group has-validation">
                        <input type="text" name="username" class="form-control" id="yourUsername" placeholder="Username" required>
                        <div class="invalid-feedback">Please enter your username.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <input type="password" name="password" class="form-control" id="yourPassword" placeholder="Password" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>

                    <div class="col-12 pt-2">
                      <button class="btn btn-submit w-100 fw-bold" name="submit" type="submit">Login</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0 pb-3 ">Don't have an account? <a href="register.php">Create an account</a></p>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
</body>
