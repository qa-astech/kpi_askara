<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LOGIN - CPANEL</title>
  <link rel="stylesheet" href="../third-party/bootstrap-5/css/bootstrap.min.css">
  <link rel="stylesheet" href="../third-party/fontawesome-6/css/all.min.css">
  <link rel="stylesheet" href="css/login.css">
  <link rel="stylesheet" href="css/button.css">
</head>
<body>

  <script src="../third-party/jquery-3/jquery-3.2.1.min.js"></script>
  <script src="../third-party/bootstrap-5/js/bootstrap.bundle.min.js"></script>

  <div class="position-fixed d-inline-block rounded" id="cardLogin">
    <div class="card border-primary w-100">
      <div class="card-body">
        <form enctype="multipart/form-data" id="formDepan">
          <img src="image/askara_x32.png" alt="Logo Askara">
          <h1 class="card-title text-center fs-2 fw-bold m-0 py-2 cardLoginTitle">ASKARA GROUP</h1>
          <h2 class="card-title text-center fs-5 m-0 pb-3 cardLoginSubTitle">Key Performance Indicators</h2>
          <div class="mb-3">
            <label for="username" class="form-label">Nama Pengguna <span class="fw-bold text-danger" id="passwordAtt">*</span></label>
            <input type="text" class="form-control" id="username" name="username" aria-describedby="username" placeholder="Input nama...">
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi <span class="fw-bold text-danger" id="passwordAtt">*</span></label>
            <input type="password" class="form-control" id="password" name="password" aria-describedby="password" placeholder="Input sandi...">
          </div>
          <div class="d-block text-end">
            <button type="button" class="btn btn-primary fw-bold d-inline-block" id="submitForm">
              <i class="fa-solid fa-right-to-bracket pe-1"></i>
              <span>MASUK</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script type="module" src="login.js"></script>

</body>
</html>