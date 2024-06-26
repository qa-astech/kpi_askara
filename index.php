<?php
  require_once('change_role.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CPANEL ASKARA</title>

  <link rel="stylesheet" href="../third-party/bootstrap-5/css/bootstrap.min.css">
  <link rel="stylesheet" href="../third-party/select2-4.0.13/css/select2.min.css" />
  <link rel="stylesheet" href="../third-party/select2-4.0.13/css/select2-bootstrap-5-theme.min.css" />
  <link rel="stylesheet" href="../third-party/fontawesome-6/css/all.min.css">
  <link rel="stylesheet" href="css/index.css">
  <link rel="stylesheet" href="css/button.css">
  <link rel="stylesheet" href="css/special.css">

</head>
<body>

  <script type="text/javascript" src="../third-party/jquery-3/jquery-3.2.1.min.js"></script>
  <script src="../third-party/bootstrap-5/js/bootstrap.bundle.min.js"></script>

  <div class="modal fade" id="resetPassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5">Reset Kata Sandi</h1>
          <button type="button" class="btn-close me-1" data-bs-dismiss="modal" data-bs-target="#resetPassModal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formResetPass" enctype="multipart/form-data" method="post">
            <div class="mb-3 px-2">
              <label for="old_password" class="form-label">Kata Sandi Lama <span class="text-danger fw-bold">*</span></label>
              <div class="input-group">
                <input type="password" class="form-control" placeholder="Password" id="old_password" name="old_password">
                <button class="btn btn-outline-secondary seePass" type="button">
                  <i class="fas fa-eye-slash"></i>
                </button>
              </div>
            </div>
            <div class="mb-3 px-2">
              <label for="new_password" class="form-label">Kata Sandi Baru <span class="text-danger fw-bold">*</span></label>
              <div class="input-group">
                <input type="password" class="form-control" placeholder="Password" id="new_password" name="new_password">
                <button class="btn btn-outline-secondary seePass" type="button">
                  <i class="fas fa-eye-slash"></i>
                </button>
              </div>
            </div>
            <div class="mb-3 px-2">
              <label for="reset_password" class="form-label">Konfirmasi Kata Sandi <span class="text-danger fw-bold">*</span></label>
              <div class="input-group">
                <input type="password" class="form-control" placeholder="Password" id="confirm_password" name="confirm_password">
                <button class="btn btn-outline-secondary seePass" type="button">
                  <i class="fas fa-eye-slash"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" data-bs-target="#resetPassModal"><i class="fa-solid fa-xmark pe-1"></i> Batal</button>
          <button type="button" class="btn btn-sm btn-primary" id="saveResetPass"><i class="fa-solid fa-floppy-disk pe-1"></i> Ubah Password</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="detailRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-info">
          <h4 class="modal-title">Kartu Identitas</h4>
          <button type="button" class="btn-close me-1" data-bs-dismiss="modal" data-bs-target="#detailRoleModal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table>
            <tbody>
              <tr>
                <td class="pb-2 white-space-nowrap">Nama</td>
                <td class="pb-2 px-2">:</td>
                <td class="pb-2 w-100" id="roleName"></td>
              </tr>
              <tr>
                <td class="pb-2 white-space-nowrap">Perusahaan</td>
                <td class="pb-2 px-2">:</td>
                <td class="pb-2 w-100" id="roleCompany"></td>
              </tr>
              <tr>
                <td class="pb-2 white-space-nowrap">Jabatan</td>
                <td class="pb-2 px-2">:</td>
                <td class="pb-2 w-100" id="rolePosition"></td>
              </tr>
              <tr>
                <td class="pb-2 white-space-nowrap">Department</td>
                <td class="pb-2 px-2">:</td>
                <td class="pb-2 w-100" id="roleDepartment"></td>
              </tr>
              <tr>
                <td class="pb-2 white-space-nowrap">Divisi</td>
                <td class="pb-2 px-2">:</td>
                <td class="pb-2 w-100" id="roleSection"></td>
              </tr>
              <tr>
                <td class="pb-2 white-space-nowrap">Golongan</td>
                <td class="pb-2 px-2">:</td>
                <td class="pb-2 w-100" id="roleGolongan"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php
    echo $changeRole;
  ?>

  <nav class="navbar w-100">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <h1 class="m-0"><img id="navbarImage1" src="image/askara_y100.png" alt="Welcome to SITA-Asset_Management !"></h1>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="offcanvas offcanvas-start bg-light" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title">
            <img id="navbarImage2" src="image/askara_y100.png" alt="Welcome to SITA-Asset_Management !">
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body px-0">
          <h5 class="px-3 fs-6">Selamat Datang, <span class="fw-bold" id="nameNavbar">NANIH</span></h5>
          <div class="px-3 m-0 pb-3 d-flex justify-content-start align-items-center">
            <div class="fs-6 fst-italic" id="companyNavbar">NANIH</div>
            <button type="button" class="btn btn-sm btn-info ms-auto" id="detailInfoBtn">
              <i class="fa-solid fa-circle-info"></i>
              <span class="ps-1">Role</span>
            </button>
          </div>
          <div class="px-3 d-flex pb-3 justify-content-start align-items-center" style="gap: .5rem;">
            <button type="button" class="btn btn-sm btn-outline-primary" id="resetPassBtn">
              <i class="fa-solid fa-lock"></i>
              <span class="ps-1">Ubah Sandi</span>
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary" id="changeRoleBtn">
            <i class="fa-solid fa-person-walking-luggage"></i>
              <span class="ps-1">Ganti Role</span>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger ms-auto" id="logoutBtn">
              <i class="fa-solid fa-power-off"></i>
              <span class="ps-1">Keluar</span>
            </button>
          </div>
          <div class="border-top border-bottom">
            <a class="nav-link ps-3 py-3" aria-current="page" href="#">
              <i class="fa-solid fa-house"></i>
              <span>Beranda</span>
            </a>
          </div>
          <div id="listMenu">
          </div>
      </div>
    </div>
  </nav>

  <main id="mainContent" class="pt-2 px-3">
    <ul class="nav nav-tabs flex-nowrap" style="overflow-x: auto; overflow-y: hidden;" id="tabMenu" role="tablist">
    </ul>
    <div class="tab-content" id="tabContent" style="height: calc(100% - 50px);">
      <div class="tab-pane fade w-100 h-100 show active" id="tabPanel" role="tabpanel" aria-labelledby="home-tab" tabindex="0" style="">
        <iframe src="" frameborder="0" class="w-100 h-100"></iframe>
      </div>
    </div>
  </main>

  <footer class="py-3 px-5">
    <i class="fas fa-copyright pe-1"></i>
    <span>2024 PT Askara Internal</span>
  </footer>

  <script type="module" src="index.js"></script>

</body>
</html>