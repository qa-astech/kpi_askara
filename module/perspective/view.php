<?php
  require_once('../header_view.php');
  require_once('../notes_form.php');
?>
  <title>PERSPEKTIF</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php
    require_once('../body_main_js.php');
  ?>
  <h1 class="text-center fw-bold m-0 pb-3 pt-1">PERSPEKTIF</h1>

  <!-- Table Utama -->
  <div class="container-fluid px-5 pb-4">
    <table id="dgUtama" class="table table-hover table-striped table-bordered w-100 nowrap">
      <thead class="table-dark">
        <tr id="dgUtamaFilter">
          <th class="filterTable">ID Perspektif</th>
          <th class="filterTable">Nama Perspektif</th>
          <th class="filterTable">Indeks Perspektif</th>
          <th class="filterTable">Singkatan (Alias)</th>
          <th class="filterTable">Pengguna Terakhir</th>
          <th class="filterTable">Pembaharuan Terakhir</th>
        </tr>
        <tr>
          <th id="thIdPerspective" class="align-middle text-center">ID Perspektif</th>
          <th id="thNamePerspective" class="align-middle text-center">Nama Perspektif</th>
          <th id="thIndexPerspective" class="align-middle text-center">Indeks Perspektif</th>
          <th id="thAliasPerspective" class="align-middle text-center">Singkatan (Alias)</th>
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
                <label for="perspective_id">Kode Perspektif <small class="fw-bold text-danger">(auto)</small></label>
                <input type="text" class="form-control" id="perspective_id" aria-describedby="perspective_id" placeholder="Masukkan Kode..." disabled>
              </div>
              <div class="form-group">
                <label for="perspective_name">Nama Perspektif <span class="fw-bold text-danger">*</span></label>
                <input type="text" class="form-control" id="perspective_name" name="perspective_name" aria-describedby="perspective_name" placeholder="Masukkan Nama...">
              </div>
              <div class="form-group">
                <label for="perspective_name">Indeks Perspektif <span class="fw-bold text-danger">*</span></label>
                <input type="number" class="form-control" id="perspective_index" name="perspective_index" aria-describedby="perspective_index" placeholder="Masukkan Indeks...">
              </div>
              <div class="form-group">
                <label for="perspective_alias">Singkatan (Alias) <span class="fw-bold text-danger">#</span></label>
                <input type="text" class="form-control" id="perspective_alias" name="perspective_alias" aria-describedby="perspective_alias" placeholder="Masukkan Alias..." maxlength="10">
                <small class="fst-italic">Tidak boleh lebih dari 10 karakter <span class="fw-bold text-danger">#</span></small>
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