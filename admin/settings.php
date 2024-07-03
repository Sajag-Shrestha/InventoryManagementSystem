<?php
require_once(__DIR__ . '/../configuration.php');
require(BASE_PATH . '/layouts/header.php');
require(BASE_PATH . '/layouts/navbar.php');
require(BASE_PATH . '/layouts/sidebar.php');
?>

<main id="main" class="main">
  <div class="page">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center text-center">
              <strong class="mx-auto">
                <i class="bi bi-person-fill-gear pe-1"></i>
                Manage Settings
              </strong>
            </div>
            <div class="card-body">
              
              <div class="table-responsive">
                <table class="table table-striped table-bordered text-center align-middle">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Setting</th>
                      <th scope="col">Value</th>
                      <th scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $select = "SELECT * FROM settings";
                    $result = mysqli_query($con, $select);
                    $i = 0;
                    while ($data = mysqli_fetch_array($result)) {
                    ?>
                      <tr>
                        <th scope="row"><?php echo ++$i; ?></th>
                        <td><?php echo $data['name']; ?></td>
                        <td><?php echo $data['value']; ?></td>
                        <td>
                          <a class="btn btn-primary btn-sm " href="settings-edit.php?id=<?php echo $data['id']; ?>" role="button"><i class="bi bi-pencil-square"> Edit</i></a>
                        </td>
                      </tr>
                    <?php
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
</main>

<?php
require(BASE_PATH . '/layouts/footer.php');
?>