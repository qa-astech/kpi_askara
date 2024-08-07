<?php
  require_once('../header_view.php');
  require_once('../notes_form.php');
?>
  <title>KPI BISNIS UNIT</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php
    require_once('../body_main_js.php');
  ?>
  <h1 class="text-center fw-bold m-0 pb-4 pt-2">KPI UNIT BISNIS</h1>

  <!-- Table Utama -->
  <div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between">
      <div>
        <table class="mb-3">
          <tbody>
            <tr>
              <td class="pe-3"><label for="dgUtamaYearInput" class="form-label mb-0 fs-5">Tahun <span class="fw-bold text-danger">*</span></label></td>
              <td class="pe-3"><input type="number" class="form-control form-control-sm" id="dgUtamaYearInput" placeholder="Tahun KPI..." aria-label="dgUtamaYearInput" aria-describedby="dgUtamaYearInput" style="width: 200px;"></td>
              <td></td>
            </tr>
            <tr>
              <td class="pt-2 pe-3"><label for="dgUtamaCompanyInput" class="form-label mb-0 fs-5">Perusahaan <span class="fw-bold text-danger">*</span></label></td>
              <td class="pt-2 pe-3">
                <select class="form-select form-select-sm" name="dgUtamaCompanyInput" id="dgUtamaCompanyInput" data-placeholder="Masukan Perusahaan..." style="width: 310px;"></select>
              </td>
              <td class="pt-2">
                <button class="btn btn-sm btn-process" type="button" id="dgUtamaYearBtn">
                  <span class="d-inline-block ps-1">PROSES</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="d-flex flex-wrap mb-2" style="gap: 1em;">
          <button type="button" id="btnEditDgUtama" class="btn rounded btn-sm btn-edit">
            <span class="d-inline-block ps-1">Ubah</span>
          </button>
          <button type="button" id="btnChangePIC" class="btn rounded btn-sm btn-change-pic">
            <span class="d-inline-block ps-1">Ubah User PIC</span>
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
      <table id="dgUtama" class="table table-hover table-striped table-bordered w-100 table-nowrap">
        <thead class="table-dark">
          <tr>
            <th class="align-middle text-center" colspan="17" id="dgUtamaYear">KPI ()</th>
          </tr>
          <tr>
            <th class="align-middle text-center">Kode Document</th>
            <th class="align-middle text-center">Perspektif</th>
            <th class="align-middle text-center">Strategi Objektif</th>
            <th class="align-middle text-center">Layout KPI</th>
            <th class="align-middle text-center">Definisi KPI</th>
            <th class="align-middle text-center">Control Cek</th>
            <th class="align-middle text-center">Baseline (<span id="dgUtamaYear1"></span>)</th>
            <th class="align-middle text-center">Target Korporat</th>
            <th class="align-middle text-center">Target Bisnis Unit</th>
            <th class="align-middle text-center">Satuan</th>
            <th class="align-middle text-center">Formula</th>
            <th class="align-middle text-center">Polaritas</th>
            <th class="align-middle text-center">Aliran (Cascade)</th>
            <th class="align-middle text-center">Bulan Pengisian</th>
            <th class="align-middle text-center">Ketersediaan Data</th>
            <th class="align-middle text-center">User PIC</th>
            <th class="align-middle text-center">Terbit KPI</th>
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
	        <h5 class="modal-title fw-bold text-white" id="modalEditorTitle">KPI</h5>
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
                <span class="d-inline-block ps-1">Tambah turunan pendukung</span>
              </button>
              <button type="button" id="btnDeleteDgEditor" class="btn rounded btn-sm btn-delete">
                <span class="d-inline-block ps-1">Hapus</span>
              </button>
            </div>
          </div>
          <!-- <div class="container-fluid px-0" style="overflow: auto;"> -->
            <form enctype="multipart/form-data" id="formEditor">
              <table id="dgEditor" class="table table-striped table-bordered w-100 nowrap dg-editor">
                <thead class="table-dark">
                  <tr>
                    <th class="align-middle text-center">Perspektif<br></th>
                    <th class="align-middle text-center">Strategi Objektif<br><small class="text-danger fw-bold">(unik - KPI)</small></th>
                    <th class="align-middle text-center">Index<br><small class="text-danger fw-bold">(auto | unik - KPI)</small></th>
                    <th class="align-middle text-center">Nama KPI <span class="text-danger fw-bold">*</span></th>
                    <th class="align-middle text-center">Definisi KPI <span class="text-danger fw-bold">*</span></th>
                    <th class="align-middle text-center">Control Cek <span class="text-danger fw-bold">*</span></th>
                    <th class="align-middle text-center">Baseline (<span id="modalEditorYear1"></span>)</th>
                    <th class="align-middle text-center">Target Korporat</th>
                    <th class="align-middle text-center">Aliran (Cascade) <span class="text-danger fw-bold">*</span></th>
                    <th class="align-middle text-center">Satuan <span class="text-danger fw-bold">*</span></th>
                    <th class="align-middle text-center">Formula <span class="text-danger fw-bold">*</span></th>
                    <th class="align-middle text-center">Polaritas <span class="text-danger fw-bold">*</span></th>
                    <th class="align-middle text-center">Target Bisnis Unit <span class="text-danger fw-bold">*</span></th>
                    <th class="align-middle text-center">Bulan Pengisian <span class="text-danger fw-bold">*</span></th>
                    <th class="align-middle text-center">Ketersediaan Data <span class="text-danger fw-bold">*</span></th>
                    <th class="align-middle text-center">User PIC <span class="text-danger fw-bold">*</span></th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </form>
          <!-- </div> -->
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

  <!-- Modal Add Fresh -->
  <div class="modal-backdrop fade d-none" id="modalAddFreshBackdrop" style="z-index: 2005;"></div>
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

  <!-- Modal Change PIC -->
  <div class="modal fade" id="modalChangeUser" tabindex="-1" role="dialog" aria-labelledby="modalChangeUser" aria-hidden="true" style="z-index: 2000;">
	  <div class="modal-dialog modal-fullscreen" role="document">
	    <div class="modal-content">
	      <div class="modal-header bg-dark">
	        <h5 class="modal-title fw-bold text-white" id="modalChangeUserTitle">Ubah PIC KPI</h5>
	        <button type="button" class="btn btn-x" data-bs-dismiss="modal" data-bs-target="#modalChangeUser" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
          <table class="table table-borderless w-100 nowrap table-information">
            <tbody>
              <tr>
                <th class="ps-0" style="white-space: nowrap;">Tahun KPI</th>
                <th class="px-1">:</th>
                <td id="modalChangeUserTahunKPI"></td>
              </tr>
            </tbody>
          </table>
          <form enctype="multipart/form-data" id="formChangeUser">
            <table id="dgChangeUser" class="table table-striped table-bordered w-100 nowrap dg-editor">
              <thead class="table-dark">
                <tr>
                  <th class="align-middle text-center">Perspektif</th>
                  <th class="align-middle text-center">Strategi Objektif</th>
                  <th class="align-middle text-center">Layout KPI</th>
                  <th class="align-middle text-center">Ketersediaan Data <span class="text-danger fw-bold">*</span></th>
                  <th class="align-middle text-center">User PIC <span class="text-danger fw-bold">*</span></th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </form>
	      </div>
	      <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-keluar" data-bs-dismiss="modal" data-bs-target="#modalChangeUser">
            <span class="ps-1">Keluar</span>
          </button>
	        <button type="button" class="btn btn-sm btn-save" id="modalChangeUserBtnSave">
            <span class="ps-1">Simpan</span>
          </button>
	      </div>
	    </div>
	  </div>
	</div>

  <script type="module" src="scripts.js"></script>

</body>
</html>