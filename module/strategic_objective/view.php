<?php
  require_once('../header_view.php');
  require_once('../notes_form.php');
?>
  <title>STRATEGI OBJEKTIF</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php
    require_once('../body_main_js.php');
  ?>
  <h1 class="text-center fw-bold m-0 pb-3 pt-1">STRATEGI OBJEKTIF</h1>

  <!-- Table Utama -->
  <div class="container-fluid px-5 pb-4">
    <table id="dgUtama" class="table table-hover table-striped table-bordered w-100 nowrap">
      <thead class="table-dark">
        <tr id="dgUtamaFilter">
          <th class="filterTable">Kode Strategic Objektif</th>
          <th class="filterTable">Perspektif</th>
          <th class="filterTable">Indeks Objektif</th>
          <th class="filterTable">Strategic Objektif</th>
          <th class="filterTable">Pengguna Terakhir</th>
          <th class="filterTable">Pembaharuan Terakhir</th>
        </tr>
        <tr>
          <th id="thIdStrategicObjective" class="align-middle text-center">Kode Strategic Objektif</th>
          <th id="thNamePerspective" class="align-middle text-center">Perspektif</th>
          <th id="thNameStrategicObjective" class="align-middle text-center">Indeks Objektif</th>
          <th id="thNameStrategicObjective" class="align-middle text-center">Strategic Objektif</th>
          <th id="thUserEntry" class="align-middle text-center">Pengguna Terakhir</th>
          <th id="thLastUpdate" class="align-middle text-center">Pembaharuan Terakhir</th>
        </tr>
      </thead>
    </table>
  </div>

  <!-- Modal Utama -->
	<div class="modal fade" id="modalUtama" tabindex="-1" role="dialog" aria-labelledby="modalUtama" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
	    <div class="modal-content">
	      <div class="modal-header bg-dark">
	        <h5 class="modal-title fw-bold text-white" id="modalUtamaTitle"></h5>
	        <button type="button" class="btn btn-x" data-bs-dismiss="modal" data-bs-target="#modalUtama" aria-label="Close">
	        </button>
	      </div>
	      <div class="modal-body">
	      	<div class="container">
            <?php
              echo $notesForm;
            ?>
	      		<form enctype="multipart/form-data" id="modalUtamaForm">
              <div class="form-group">
                <label for="sobject_id">Kode Strategic Objektif <small class="fw-bold text-danger">(auto)</small></label>
                <input type="text" class="form-control" id="sobject_id" aria-describedby="sobject_id" placeholder="Masukkan kode..." disabled>
              </div>
              <div class="form-group">
                <label for="perspective_id">Perspektif <span class="font-weight-bold text-danger">*</span></label>
                <select class="form-select" name="perspective_id" id="perspective_id" data-placeholder="Masukan perspektif..."></select>
              </div>
              <div class="form-group">
                <label for="sobject_name">Nama Strategic Objektif <span class="fw-bold text-danger">*</span></label>
                <input type="text" class="form-control" id="sobject_name" name="sobject_name" aria-describedby="sobject_name" placeholder="Masukkan nama...">
              </div>
              <div class="form-group">
                <label for="sobject_index">Indeks Strategic Objektif <small class="fw-bold text-danger">(auto saat tambah baru)</small><span class="fw-bold text-danger">*#</span></label>
                <input type="number" class="form-control" id="sobject_index" name="sobject_index" aria-describedby="sobject_index" placeholder="Masukkan index..." maxlength="10" disabled>
                <small class="fst-italic">Disaat edit jika memasukkan nomor yang sudah ada, nomor akan bertukar posisi atau ke nomor paling terakhir <span class="fw-bold text-danger">#</span></small>
              </div>
		      	</form>
	      	</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-sm btn-keluar" data-bs-dismiss="modal" data-bs-target="#modalUtama">
            <span class="ps-1">Keluar</span>
          </button>
	        <button type="button" class="btn btn-sm btn-save" id="modalUtamaBtnSave">
            <span class="ps-1">Simpan</span>
          </button>
	      </div>
	    </div>
	  </div>
	</div>

  <script type="module" src="scripts.js"></script>

</body>
</html>