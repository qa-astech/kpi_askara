<?php
  require_once('../header_view.php');
  require_once('../notes_form.php');
?>
  <title>LIST FORMULA</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php
    require_once('../body_main_js.php');
  ?>
  <h1 class="text-center fw-bold m-0 pb-3 pt-1">LIST FORMULA</h1>

  <!-- Table Utama -->
  <div class="container-fluid px-5 pb-4">
    <table id="dgUtama" class="table table-hover table-striped table-bordered w-100 nowrap">
      <thead class="table-dark">
        <tr id="dgUtamaFilter">
          <th class="filterTable">Kode Formula</th>
          <th class="filterTable">Nama Formula</th>
          <th class="filterTable">Rumus Formula</th>
          <th class="filterTable">Pengguna Terakhir</th>
          <th class="filterTable">Pembaharuan Terakhir</th>
        </tr>
        <tr>
          <th id="thIdFormula" class="align-middle text-center">Kode Formula</th>
          <th id="thNameFormula" class="align-middle text-center">Nama Formula</th>
          <th id="thRumusFormula" class="align-middle text-center">Rumus Formula</th>
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
                <label for="formula_id">Kode Formula <small class="fw-bold text-danger">(auto)</small></label>
                <input type="text" class="form-control" id="formula_id" aria-describedby="formula_id" placeholder="Masukkan Kode..." disabled>
              </div>
              <div class="form-group">
                <label for="formula_name">Nama Formula <span class="fw-bold text-danger">*</span></label>
                <input type="text" class="form-control" id="formula_name" name="formula_name" aria-describedby="formula_name" placeholder="Masukkan Nama...">
              </div>
              <div class="form-group">
                <label for="formula_rumus">Rumus Formula <span class="fw-bold text-danger">*</span></label>
                <input type="text" class="form-control" id="formula_rumus" name="formula_rumus" aria-describedby="formula_rumus" placeholder="Masukkan Rumus...">
              </div>
              <!-- <div class="border rounded px-3 py-2">
                <h5 class="text-center">Simulasi formula</h5>
                <div class="d-flex" style="column-gap: 1rem; row-gap: .25rem;">
                  <div class="flex-fill">
                    <div class="form-group">
                      <label for="simulate_rumus">Rumus</label>
                      <input type="text" class="form-control" id="simulate_rumus" name="simulate_rumus" aria-describedby="simulate_rumus" placeholder="Masukkan Rumus...">
                    </div>
                    <div class="form-group">
                      <label for="simulate_hasil">Hasil</label>
                      <input type="text" class="form-control" id="simulate_hasil" name="simulate_hasil" aria-describedby="simulate_hasil" readonly>
                    </div>
                  </div>
                  <div class="position-relative">
                    <button type="button" class="btn btn-process position-relative" id="simulate_save" style="top: 50%; transform: translateY(-50%);">
                      <span class="ps-1">Simulate</span>
                    </button>
                  </div>
                </div>
              </div> -->
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

  <!-- <input type="text" name="contoh_formula" id="contoh_formula">
  <button type="button" id="button_formula">testup</button>
  <input type="text" name="result_formula" id="result_formula"> -->

  <script type="module" src="scripts.js"></script>

</body>
</html>