<?php
  require_once('../header_view.php');
?>
  <title>USER ACCESS</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php
    require_once('../body_main_js.php');
  ?>
  <h1 class="text-center fw-bold m-0 pb-3">USER ACCESS</h1>

  <div class="container-fluid px-3 px-lg-4 px-xl-5" id="mainDiv">
    <div class="d-block pb-3">
      <div class="d-flex border border-askara rounded justify-content-center align-items-center py-2 px-3" style="max-width: 350px;">
        <div class="pe-3 white-space-nowrap">User :</div>
        <div class="w-100">
          <select name="fullname" id="fullname" class="w-100">
            <option value="" placeholder>Choose Users...</option>
          </select>
        </div>
      </div>
    </div>
    <div class="d-block">
      <h3 class="mt-4 fw-bold">List Menu</h3>
      <hr>
      <div id="showMenu"></div>
    </div>
  </div>

  <script type="module" src="scripts.js"></script>

</body>
</html>