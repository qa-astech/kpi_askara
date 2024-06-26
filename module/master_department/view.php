<?php
  require_once('../header_view.php');
  require_once('../notes_form.php');
  require_once('component_form_add.php');
  require_once('../master_section/component_form_add.php');
?>
  <title>LIST DEPARTMENT</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php
    require_once('../body_main_js.php');
  ?>
  <h1 class="text-center fw-bold m-0 pb-3 pt-1">LIST DEPARTMENT</h1>

  <!-- Table Utama -->
  <div class="container-fluid px-5 pb-4">
    <table id="dgUtama" class="table table-hover table-striped table-bordered w-100 nowrap">
      <thead class="table-dark">
        <tr id="dgUtamaFilter">
          <th class="filterTable">Kode Departemen</th>
          <th class="filterTable">Nama Departemen</th>
          <th class="filterTable">Singkatan (Alias)</th>
          <th class="filterTable">Pengguna Terakhir</th>
          <th class="filterTable">Pembaharuan Terakhir</th>
        </tr>
        <tr>
          <th class="align-middle text-center w-0" id="thDetailDgUtama">List<br>Divisi</th>
          <th id="thIdDepartment" class="align-middle text-center">Kode Departemen</th>
          <th id="thNameDepartment" class="align-middle text-center">Nama Departemen</th>
          <th id="thAliasDepartment" class="align-middle text-center">Singkatan (Alias)</th>
          <th id="thUserEntry" class="align-middle text-center">Pengguna Terakhir</th>
          <th id="thLastUpdate" class="align-middle text-center">Pembaharuan Terakhir</th>
        </tr>
      </thead>
    </table>
  </div>

  <!-- Modal Utama -->
	<?php
    echo $componentAddDepartment;
  ?>

  <!-- Modal Divisi -->
  <div class="modal-backdrop fade d-none" id="modalDivisiBackdrop" style="z-index: 2005;"></div>
  <?php
    echo $componentAddDivisi;
  ?>

  <!-- Modal Divisi View -->
  <div class="modal fade" id="modalDivisiView" tabindex="-1" role="dialog" aria-labelledby="modalDivisiView" aria-hidden="true" style="z-index: 2000;">
	  <div class="modal-dialog modal-fullscreen" role="document">
	    <div class="modal-content">
	      <div class="modal-header bg-dark">
	        <h5 class="modal-title fw-bold text-white">List Divisi</h5>
	        <button type="button" class="btn btn-x" data-bs-dismiss="modal" data-bs-target="#modalDivisiView" aria-label="Close">
	        </button>
	      </div>
	      <div class="modal-body">
          <div class="container-fluid px-5">
            <table class="table table-borderless w-100 nowrap table-information">
              <tbody>
                <tr>
                  <th>Kode Departemen</th>
                  <th class="px-1">:</th>
                  <td id="modalDivisiViewIdDepartment"></td>
                </tr>
                <tr>
                  <th>Nama Departemen</th>
                  <th class="px-1">:</th>
                  <td id="modalDivisiViewNameDepartment"></td>
                </tr>
                <tr>
                  <th>Alias Departemen</th>
                  <th class="px-1">:</th>
                  <td id="modalDivisiViewAliasDepartment"></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="container-fluid px-5">
            <table id="dgDivisi" class="table table-hover table-striped table-bordered w-100 nowrap">
              <thead class="table-dark">
                <tr id="dgDivisiFilter">
                  <th class="filterTable">Kode Divisi</th>
                  <th class="filterTable">Nama Divisi</th>
                  <th class="filterTable">Pengguna Terakhir</th>
                  <th class="filterTable">Pembaharuan Terakhir</th>
                </tr>
                <tr>
                  <th id="thIdSection" class="align-middle text-center">Kode Divisi</th>
                  <th id="thNameSection" class="align-middle text-center">Nama Divisi</th>
                  <th id="thUserEntry" class="align-middle text-center">Pengguna Terakhir</th>
                  <th id="thLastUpdate" class="align-middle text-center">Pembaharuan Terakhir</th>
                </tr>
              </thead>
            </table>
          </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-sm btn-keluar" data-bs-dismiss="modal" data-bs-target="#modalDivisiView">
            <span class="ps-1">Keluar</span>
          </button>
	      </div>
	    </div>
	  </div>
	</div>

  <script type="module" src="scripts.js"></script>

</body>
</html>