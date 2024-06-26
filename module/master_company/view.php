<?php
  require_once('../header_view.php');
  require_once('../notes_form.php');
  require_once('component_form_add.php');
?>
  <title>LIST PERUSAHAAN</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php
    require_once('../body_main_js.php');
  ?>
  <h1 class="text-center fw-bold m-0 pb-3 pt-1">LIST PERUSAHAAN</h1>

  <!-- Table Utama -->
  <div class="container-fluid px-5 pb-4">
    <table id="dgUtama" class="table table-hover table-striped table-bordered w-100 nowrap">
      <thead class="table-dark">
        <tr id="dgUtamaFilter">
          <th class="filterTable">Kode Perusahaan</th>
          <th class="filterTable">Nama Perusahaan</th>
          <th class="filterTable">Singkatan (Alias)</th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th class="filterTable">Pengguna Terakhir</th>
          <th class="filterTable">Pembaharuan Terakhir</th>
        </tr>
        <tr>
          <th class="align-middle text-center w-0" id="thDetailDgUtama">Detail<br>Struktur</th>
          <th id="thIdCompany" class="align-middle text-center">Kode Perusahaan</th>
          <th id="thNameCompany" class="align-middle text-center">Nama Perusahaan</th>
          <th id="thAliasCompany" class="align-middle text-center">Singkatan (Alias)</th>
          <th id="thGroup" class="align-middle text-center">Grup?</th>
          <th id="thCustomer" class="align-middle text-center">Customer?</th>
          <th id="thSupplier" class="align-middle text-center">Supplier?</th>
          <th id="thLogoCompany" class="align-middle text-center">Logo Perusahaan</th>
          <th id="thUserEntry" class="align-middle text-center">Pengguna Terakhir</th>
          <th id="thLastUpdate" class="align-middle text-center">Pembaharuan Terakhir</th>
        </tr>
      </thead>
    </table>
  </div>

  <!-- Modal Utama -->
	<?php
    echo $componentAddCompany;
  ?>

  <!-- Modal Divisi -->
  <div class="modal-backdrop fade d-none" id="modalDetailBackdrop" style="z-index: 2005;"></div>
  <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetail" aria-hidden="true" style="z-index: 2005;">
	  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
	    <div class="modal-content">
	      <div class="modal-header bg-dark">
	        <h5 class="modal-title fw-bold text-white" id="modalDetailTitle"></h5>
	        <button type="button" class="btn btn-x" data-bs-dismiss="modal" data-bs-target="#modalDetail" aria-label="Close">
	        </button>
	      </div>
	      <div class="modal-body">
	      	<div class="container">
            <?php
              echo $notesForm;
            ?>
	      		<form enctype="multipart/form-data" id="modalDetailForm">
              <div class="form-group">
                <label for="det_company_id" class="d-block">Kode Detail Struktur <small class="font-weight-bold text-danger">(auto)</small></label>
                <input type="text" class="form-control" id="det_company_id" aria-describedby="det_company_id" placeholder="Masukkan Kode..." disabled>
              </div>
              <div class="form-group">
                <label for="section_id">Departemen - Divisi <span class="font-weight-bold text-danger">*</span></label>
                <select class="form-select" name="section_id" id="section_id" data-placeholder="Masukan departemen - divisi..."></select>
              </div>
              <div class="form-group">
                <label for="position_id">Posisi <span class="font-weight-bold text-danger">*</span></label>
                <select class="form-select" name="position_id" id="position_id" data-placeholder="Masukan posisi..."></select>
              </div>
              <div class="form-group">
                <label for="plant_id">Plan <span class="font-weight-bold text-danger">*</span></label>
                <select class="form-select" name="plant_id" id="plant_id" data-placeholder="Masukan plant..."></select>
              </div>
              <div class="form-group">
                <label for="golongan">Golongan <span class="font-weight-bold text-danger">*#</span></label>
                <input type="number" class="form-control" id="golongan" name="golongan" aria-describedby="golongan" placeholder="Masukkan golongan..." min="0" max="5">
                <small class="fst-italic">Maksimal angka adalah 5 <span class="fw-bold text-danger">#</span></small>
              </div>
		      	</form>
	      	</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-sm btn-keluar" data-bs-dismiss="modal" data-bs-target="#modalDetail">
            <span class="ps-1">Keluar</span>
          </button>
	        <button type="button" class="btn btn-sm btn-save" id="modalDetailBtnSave">
            <span class="ps-1">Simpan</span>
          </button>
	      </div>
	    </div>
	  </div>
	</div>

  <!-- Modal Divisi View -->
  <div class="modal fade" id="modalDetailView" tabindex="-1" role="dialog" aria-labelledby="modalDetailView" aria-hidden="true" style="z-index: 2000;">
	  <div class="modal-dialog modal-fullscreen" role="document">
	    <div class="modal-content">
	      <div class="modal-header bg-dark">
	        <h5 class="modal-title fw-bold text-white">Detail Struktur</h5>
	        <button type="button" class="btn btn-x" data-bs-dismiss="modal" data-bs-target="#modalDetailView" aria-label="Close">
	        </button>
	      </div>
	      <div class="modal-body">
          <div class="container-fluid px-5">
            <table class="table table-borderless w-100 nowrap table-information">
              <tbody>
                <tr>
                  <th>Kode Perusahaan</th>
                  <th class="px-1">:</th>
                  <td id="modalDetailViewIdCompany"></td>
                </tr>
                <tr>
                  <th>Nama Perusahaan</th>
                  <th class="px-1">:</th>
                  <td id="modalDetailViewNameCompany"></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="container-fluid px-5">
            <table id="dgDetail" class="table table-hover table-striped table-bordered w-100 nowrap">
              <thead class="table-dark">
                <tr id="dgDetailFilter">
                  <th class="filterTable">Kode Detail Struktur</th>
                  <th class="filterTable">Departemen</th>
                  <th class="filterTable">Divisi</th>
                  <th class="filterTable">Posisi</th>
                  <th class="filterTable">Plan</th>
                  <th class="filterTable">Golongan</th>
                  <th class="filterTable">Pengguna Terakhir</th>
                  <th class="filterTable">Pembaharuan Terakhir</th>
                </tr>
                <tr>
                  <th id="thIdDetCompany" class="align-middle text-center">Kode Detail Struktur</th>
                  <th id="thDepartemen" class="align-middle text-center">Departemen</th>
                  <th id="thDivisi" class="align-middle text-center">Divisi</th>
                  <th id="thPosisi" class="align-middle text-center">Posisi</th>
                  <th id="thPlan" class="align-middle text-center">Plan</th>
                  <th id="thGolongan" class="align-middle text-center">Golongan</th>
                  <th id="thUserEntry" class="align-middle text-center">Pengguna Terakhir</th>
                  <th id="thLastUpdate" class="align-middle text-center">Pembaharuan Terakhir</th>
                </tr>
              </thead>
            </table>
          </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-sm btn-keluar" data-bs-dismiss="modal" data-bs-target="#modalDetailView">
            <span class="ps-1">Keluar</span>
          </button>
	      </div>
	    </div>
	  </div>
	</div>

  <script type="module" src="scripts.js"></script>

</body>
</html>