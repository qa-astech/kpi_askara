<?php
  require_once('../header_view.php');
  require_once('../notes_form.php');
?>
  <title>KPI KORPORAT</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php
    require_once('../body_main_js.php');
  ?>
  <h1 class="text-center fw-bold m-0 pb-4 pt-2">KPI KORPORAT</h1>

  <!-- Table Utama -->
  <div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between">
      <div>
        <div class="d-inline-flex flex-wrap mb-3 border border-primary rounded py-2 px-3" style="gap: 1em;">
          <label for="dgUtamaYearInput" class="form-label mb-0 fs-4">Tahun</label>
          <input type="number" class="form-control form-control-sm" id="dgUtamaYearInput" placeholder="Tahun KPI..." aria-label="dgUtamaYearInput" aria-describedby="dgUtamaYearInput" style="width: 150px;">
          <button class="btn btn-sm btn-process" type="button" id="dgUtamaYearBtn">
            <span class="d-inline-block ps-1">PROSES</span>
          </button>
        </div>
        <div class="d-flex flex-wrap mb-2" style="gap: 1em;">
          <button type="button" id="btnAddFreshDgUtama" class="btn rounded btn-sm btn-add">
            <span class="d-inline-block ps-1">Template baru</span>
          </button>
          <button type="button" id="btnAddCopyDgUtama" class="btn rounded btn-sm btn-add">
            <span class="d-inline-block ps-1">Salin template</span>
          </button>
          <button type="button" id="btnEditDgUtama" class="btn rounded btn-sm btn-edit">
            <span class="d-inline-block ps-1">Ubah</span>
          </button>
          <button type="button" id="btnPublishDgUtama" class="btn rounded btn-sm btn-publish">
            <span class="d-inline-block ps-1">Terbit KPI</span>
          </button>
          <button type="button" id="btnReloadDgUtama" class="btn rounded btn-sm btn-reload"></button>
        </div>
      </div>
      <div>
        <table>
          <tbody>
            <tr>
              <th>Pengguna Terakhir</th>
              <th class="px-2">:</th>
              <th id="dgUtamaUserEntry" class="text-danger"></th>
            </tr>
            <tr>
              <th>Pembaharuan Terakhir</th>
              <th class="px-2">:</th>
              <th id="dgUtamaLastUpdate" class="text-danger"></th>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="d-block overflow-auto w-100 position-relative rounded-top">
      <table id="dgUtama" class="table table-hover table-striped table-bordered w-100 nowrap table-nowrap">
        <thead class="table-dark">
          <tr>
            <th class="align-middle text-center" colspan="12" id="titleYearKPI">KPI ()</th>
          </tr>
          <tr>
            <th class="align-middle text-center" rowspan="2">Kode Document</th>
            <th class="align-middle text-center" rowspan="2">Perspektif</th>
            <th class="align-middle text-center" rowspan="2">Strategi Objektif</th>
            <th class="align-middle text-center" rowspan="2">Layout KPI</th>
            <th class="align-middle text-center" rowspan="2">Definisi KPI</th>
            <th class="align-middle text-center" rowspan="2">Control Cek</th>
            <th class="align-middle text-center" rowspan="2">Satuan</th>
            <th class="align-middle text-center" colspan="3">Baseline</th>
            <th class="align-middle text-center" rowspan="2">Target Korporat</th>
            <th class="align-middle text-center" rowspan="2">Terbit KPI</th>
          </tr>
          <tr>
            <th id="dgUtamaYear3" class="align-middle text-center"></th>
            <th id="dgUtamaYear2" class="align-middle text-center"></th>
            <th id="dgUtamaYear1" class="align-middle text-center"></th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <!-- Modal Editor -->
  <div class="modal fade" id="modalEditor" tabindex="-1" role="dialog" aria-labelledby="modalEditor" aria-hidden="true" style="z-index: 2000;">
	  <div class="modal-dialog modal-fullscreen" role="document">
	    <div class="modal-content">
	      <div class="modal-header bg-dark">
	        <h5 class="modal-title fw-bold text-white">KPI Korporat Editor</h5>
	        <button type="button" class="btn btn-x" data-bs-dismiss="modal" data-bs-target="#modalEditor" aria-label="Close">
	        </button>
	      </div>
	      <div class="modal-body">
          <div class="d-flex justify-content-start align-items-start" style="gap: 1.5rem;">
            <div>
              <table class="table table-borderless w-100 nowrap table-information">
                <tbody>
                  <tr>
                    <th class="ps-0">Tahun KPI<br><small class="text-danger fw-bold">(unik - KPI)</small></th>
                    <th class="px-1">:</th>
                    <td id="modalEditorTahunKPI"></td>
                  </tr>
                  <tr>
                    <th class="ps-0">Tahun Template</th>
                    <th class="px-1">:</th>
                    <td id="modalEditorTahunTemplate"></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div>
              <?php
                echo $notesForm;
              ?>
            </div>
          </div>
          <div class="container-fluid px-0">
            <div class="d-flex justify-content-start align-items-center mb-2" style="gap: 1em;">
              <button type="button" id="btnAddNewDgEditor" class="btn rounded btn-sm btn-add">
                <span class="d-inline-block ps-1">Tambah baru</span>
              </button>
              <button type="button" id="btnAddChildDgEditor" class="btn rounded btn-sm btn-add">
                <span class="d-inline-block ps-1">Tambah turunan</span>
              </button>
              <button type="button" id="btnDeleteDgEditor" class="btn rounded btn-sm btn-delete">
                <span class="d-inline-block ps-1">Hapus</span>
              </button>
            </div>
          </div>
          <form enctype="multipart/form-data" id="formEditor">
            <table id="dgEditor" class="table table-striped table-bordered w-100 nowrap dg-editor">
              <thead class="table-dark">
                <tr>
                  <th class="align-middle text-center" rowspan="2">Perspektif</th>
                  <th class="align-middle text-center" rowspan="2">Strategi Objektif<br><small class="text-danger fw-bold">(unik - KPI)</small></th>
                  <th class="align-middle text-center" rowspan="2">Index<br><small class="text-danger fw-bold">(auto | unik - KPI)</small></th>
                  <th class="align-middle text-center" rowspan="2">Nama KPI <span class="text-danger fw-bold"> *</span></th>
                  <th class="align-middle text-center" rowspan="2">Definisi KPI <span class="text-danger fw-bold">*</span></th>
                  <th class="align-middle text-center" rowspan="2">Control Cek <span class="text-danger fw-bold">*</span></th>
                  <th class="align-middle text-center" colspan="3">Baseline</th>
                  <th class="align-middle text-center" rowspan="2">Target Korporat <span class="text-danger fw-bold">*</span></th>
                  <th class="align-middle text-center" rowspan="2">Satuan <span class="text-danger fw-bold">*</span></th>
                  <th class="align-middle text-center" rowspan="2">Formula <span class="text-danger fw-bold">*</span></th>
                  <th class="align-middle text-center" rowspan="2">Polaritas <span class="text-danger fw-bold">*</span></th>
                </tr>
                <tr>
                  <th class="align-middle text-center" id="dgEditorYearBaseline3"></th>
                  <th class="align-middle text-center" id="dgEditorYearBaseline2"></th>
                  <th class="align-middle text-center" id="dgEditorYearBaseline1"></th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </form>
	      </div>
	      <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-keluar" data-bs-dismiss="modal" data-bs-target="#modalEditor">
            <span class="ps-1">Keluar</span>
          </button>
	        <button type="button" class="btn btn-sm btn-save" id="modalEditorBtnSave">
            <span class="ps-1">Simpan</span>
          </button>
	      </div>
	    </div>
	  </div>
	</div>

  <!-- Modal Copy -->
	<div class="modal fade" id="modalCopy" tabindex="-1" role="dialog" aria-labelledby="modalCopy" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
	    <div class="modal-content">
	      <div class="modal-header bg-dark">
	        <h5 class="modal-title fw-bold text-white">Salin Template</h5>
	        <button type="button" class="btn btn-x" data-bs-dismiss="modal" data-bs-target="#modalCopy" aria-label="Close">
	        </button>
	      </div>
	      <div class="modal-body">
	      	<div class="container">
            <h5 class="text-center">Pilih tahun yang ingin kamu copy templatenya!</h5>
            <div class="d-flex justify-content-center align-items-center py-3" style="gap: 1rem;">
              <label for="modalCopyYearRadio1" class="form-check">
                <input type="radio" name="modalCopyYearRadio" id="modalCopyYearRadio1" class="form-check-input float-none d-inline-block align-middle" value="stay">
                <span class="form-check-label fs-3 align-middle ps-2" id="modalCopyYear1"></span>
              </label>
              <label for="modalCopyYearRadio2" class="form-check">
                <input type="radio" name="modalCopyYearRadio" id="modalCopyYearRadio2" class="form-check-input float-none d-inline-block align-middle" value="change">
                <span class="form-check-label fs-3 align-middle ps-2" id="modalCopyYear2"></span>
              </label>
              <label for="modalCopyYearRadio3" class="form-check">
                <input type="radio" name="modalCopyYearRadio" id="modalCopyYearRadio3" class="form-check-input float-none d-inline-block align-middle" value="delete">
                <span class="form-check-label fs-3 align-middle ps-2" id="modalCopyYear3"></span>
              </label>
            </div>
	      	</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-sm btn-save" id="modalCopyBtnSave">
            <span class="ps-1">Lanjut</span>
          </button>
	      </div>
	    </div>
	  </div>
	</div>

  <!-- Modal Add Fresh -->
  <div class="modal-backdrop fade d-none" id="modalAddFreshBackdrop" style="z-index: 2000;"></div>
	<div class="modal fade" id="modalAddFresh" tabindex="-1" role="dialog" aria-labelledby="modalAddFresh" aria-hidden="true" style="z-index: 2005;">
	  <div class="modal-dialog modal-dialog-centered" role="document">
	    <div class="modal-content">
	      <div class="modal-header bg-dark">
	        <h5 class="modal-title fw-bold text-white">Masukan Strategi Objektif</h5>
	        <button type="button" class="btn btn-x" data-bs-dismiss="modal" data-bs-target="#modalAddFresh" aria-label="Close">
	        </button>
	      </div>
	      <div class="modal-body">
	      	<div class="container">
            <div class="form-group">
              <label for="strategi_object">Strategi Objektif</label>
              <select class="form-select" name="strategi_object" id="strategi_object" data-placeholder="Masukan posisi..."></select>
            </div>
	      	</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-sm btn-save" id="modalAddFreshBtnSave">
            <span class="ps-1">Input Data</span>
          </button>
	      </div>
	    </div>
	  </div>
	</div>

  <script type="module" src="scripts.js"></script>

</body>
</html>