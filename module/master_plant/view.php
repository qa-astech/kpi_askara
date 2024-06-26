<?php
  require_once('../header_view.php');
  require_once('../notes_form.php');
  require_once('component_form_add.php');
?>
  <title>LIST PLAN</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php
    require_once('../body_main_js.php');
  ?>
  <h1 class="text-center fw-bold m-0 pb-3 pt-1">LIST PLAN</h1>

  <!-- Table Utama -->
  <div class="container-fluid px-5 pb-4">
    <table id="dgUtama" class="table table-hover table-striped table-bordered w-100 nowrap">
      <thead class="table-dark">
        <tr id="dgUtamaFilter">
          <th class="filterTable">Kode Plan</th>
          <th class="filterTable">Nama Plan</th>
          <th class="filterTable">Singkatan (Alias)</th>
          <th class="filterTable">Pengguna Terakhir</th>
          <th class="filterTable">Pembaharuan Terakhir</th>
        </tr>
        <tr>
          <th id="thIdPlant" class="align-middle text-center">Kode Plan</th>
          <th id="thNamePlant" class="align-middle text-center">Nama Plan</th>
          <th id="thAliasPlant" class="align-middle text-center">Singkatan (Alias)</th>
          <th id="thUserEntry" class="align-middle text-center">Pengguna Terakhir</th>
          <th id="thLastUpdate" class="align-middle text-center">Pembaharuan Terakhir</th>
        </tr>
      </thead>
    </table>
  </div>

  <!-- Modal Utama -->
	<?php
    echo $componentAddPlant;
  ?>

  <script type="module" src="scripts.js"></script>

</body>
</html>