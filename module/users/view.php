<?php
  require_once('../header_view.php');
  require_once('../notes_form.php');
?>
  <title>DATA PENGGUNA</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php
    require_once('../body_main_js.php');
  ?>
  <h1 class="text-center fw-bold m-0 pb-3 pt-1">DATA PENGGUNA</h1>

  <!-- Table Utama -->
  <div class="container-fluid px-5 pb-4">
    <table id="dgUtama" class="table table-hover table-striped table-bordered w-100 nowrap">
      <thead class="table-dark">
        <tr id="dgUtamaFilter">
          <th class="filterTable">NIK</th>
          <th class="filterTable">Panggilan (Username)</th>
          <th class="filterTable">Nama Lengkap</th>
          <th class="filterTable">Pengguna Terakhir</th>
          <th class="filterTable">Pembaharuan Terakhir</th>
        </tr>
        <tr>
          <th class="align-middle text-center w-0" id="thDetailDgUtama">Peran<br>Kerja</th>
          <th class="align-middle text-center">NIK</th>
          <th class="align-middle text-center">Panggilan (Username)</th>
          <th class="align-middle text-center">Nama Lengkap</th>
          <th class="align-middle text-center">Pengguna Terakhir</th>
          <th class="align-middle text-center">Pembaharuan Terakhir</th>
        </tr>
      </thead>
    </table>
  </div>

  <!-- Modal Utama -->
	<div class="modal fade" id="modalUtama" tabindex="-1" role="dialog" aria-labelledby="modalUtama" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
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
                <label for="users_nik">NIK <span class="fw-bold text-danger">*</span></label>
                <input type="text" class="form-control" id="users_nik" name="users_nik" aria-describedby="users_nik" placeholder="Masukkan nik...">
              </div>
              <div class="form-group">
                <label for="users_username">Panggilan (username) <small class="font-weight-bold text-danger">*</small></label>
                <input type="text" class="form-control" id="users_username" name="users_username" aria-describedby="users_username" placeholder="Masukkan username...">
              </div>
              <div class="form-group">
                <label for="users_fullname">Nama Lengkap <small class="font-weight-bold text-danger">*</small></label>
                <input type="text" class="form-control" id="users_fullname" name="users_fullname" aria-describedby="users_fullname" placeholder="Masukkan fullname...">
              </div>
              <div class="form-group">
                <label for="users_password">Kata Sandi <small class="font-weight-bold text-danger">*</small></label>
                <input type="password" class="form-control" id="users_password" name="users_password" aria-describedby="users_password" placeholder="Masukkan password...">
              </div>
              <div class="form-group">
                <label for="users_confirm_password">Konfirmasi Kata Sandi <small class="font-weight-bold text-danger">*</small></label>
                <input type="password" class="form-control" id="users_confirm_password" name="users_confirm_password" aria-describedby="users_confirm_password" placeholder="Masukkan password...">
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

  <!-- Modal Detail -->
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
                <div class="d-flex justify-content-between ">
                  <label for="users_company">Perusahaan <small class="font-weight-bold text-danger">Step - 1 (*)</small></label>
                  <label class="form-check">
                    <input class="form-check-input" type="checkbox" value="true" id="checkDivCorps" name="checkDivCorps">
                    <span class="form-check-label fw-bold" for="checkDivCorps">
                      Divisi Korporat ?
                    </span>
                  </label>
                </div>
                <select class="form-select" name="users_company" id="users_company" data-placeholder="Masukan perusahaan..."></select>
              </div>

              <!-- Default -->
              <div class="form-group form-default">
                <label for="users_section" class="d-block">Department - Divisi <small class="font-weight-bold text-danger">Step - 2 (*)</small></label>
                <select class="form-select" name="users_section" id="users_section" data-placeholder="Masukan departemen - divisi..."></select>
              </div>
              <div class="form-group form-default">
                <label for="users_position">Posisi <small class="font-weight-bold text-danger">Step - 3 (*)</small></label>
                <select class="form-select" name="users_position" id="users_position" data-placeholder="Masukan posisi..."></select>
              </div>
              <div class="form-group form-default">
                <label for="users_plant">Plan <small class="font-weight-bold text-danger">Step - 4 (*)</small></label>
                <select class="form-select" name="users_plant" id="users_plant" data-placeholder="Masukan plan..."></select>
              </div>
              <div class="form-group form-default">
                <label for="users_golongan">Golongan <small class="font-weight-bold text-danger">Step - 5 (*)</small></label>
                <select class="form-select" name="users_golongan" id="users_golongan" data-placeholder="Masukan golongan..."></select>
              </div>

              <!-- Divisi Korporat -->
              <div class="form-group form-corps">
                <label for="users_section_corps" class="d-block">Department - Divisi <small class="font-weight-bold text-danger">Step - 2 (*)</small></label>
                <select class="form-select" name="users_section_corps" id="users_section_corps" data-placeholder="Masukan departemen - divisi..."></select>
              </div>
              <div class="form-group form-corps">
                <label for="users_position_corps">Posisi <small class="font-weight-bold text-danger">Step - 3 (*)</small></label>
                <select class="form-select" name="users_position_corps" id="users_position_corps" data-placeholder="Masukan posisi..."></select>
              </div>
              <div class="form-group form-corps">
                <label for="users_golongan_corps">Golongan <small class="font-weight-bold text-danger">*#</small></label>
                <input type="number" class="form-control" id="users_golongan_corps" name="users_golongan_corps" aria-describedby="users_golongan_corps" placeholder="Masukkan golongan..." min="0" max="5">
                <small class="fst-italic">Notes <span class="fw-bold text-danger">#</span> Maksimal angka adalah 5</small>
              </div>

              <div class="form-group">
                <div class="d-block pb-1">Status Aktif <small class="font-weight-bold text-danger">*</small></div>
                <div class="d-flex justify-content-center align-items-center" style="gap: 1em;">
                  <label for="status_active1" class="form-check">
                    <input type="radio" name="status_active" id="status_active1" class="form-check-input float-none d-inline-block" value="false">
                    <span class="form-check-label ps-2">Nonaktif</span>
                  </label>
                  <label for="status_active2" class="form-check">
                    <input type="radio" name="status_active" id="status_active2" class="form-check-input float-none d-inline-block" value="true">
                    <span class="form-check-label ps-2">Aktif</span>
                  </label>
                </div>
              </div>
              <div class="form-group">
                <div class="d-block pb-1">Peran Kerja Utama <small class="font-weight-bold text-danger">#</small></div>
                <div class="d-flex justify-content-center align-items-center">
                  <label for="users_role_utama" class="form-check">
                    <input type="checkbox" name="users_role_utama" id="users_role_utama" class="form-check-input float-none d-inline-block" value="true">
                    <span class="form-check-label ps-2">Jadikan Peran Utama</span>
                  </label>
                </div>
                <small class="fst-italic">Notes <span class="fw-bold text-danger">#</span> Jika diceklis, maka pada saat login akan langsung menjadi peran ini!</small>
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
	        <h5 class="modal-title fw-bold text-white">Peran Kerja</h5>
	        <button type="button" class="btn btn-x" data-bs-dismiss="modal" data-bs-target="#modalDetailView" aria-label="Close">
	        </button>
	      </div>
	      <div class="modal-body">
          <div class="container-fluid">
            <table class="table table-borderless w-100 nowrap table-information">
              <tbody>
                <tr>
                  <th>NIK</th>
                  <th class="px-1">:</th>
                  <td id="modalDetailViewNik"></td>
                </tr>
                <tr>
                  <th>Nama Lengkap</th>
                  <th class="px-1">:</th>
                  <td id="modalDetailViewFullname"></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="container-fluid">
            <table id="dgDetail" class="table table-hover table-striped table-bordered w-100 nowrap">
              <thead class="table-dark">
                <tr id="dgDetailFilter">
                  <th class="filterTable">Kode Detail</th>
                  <th class="filterTable">Perusahaan</th>
                  <th class="filterTable">Departemen</th>
                  <th class="filterTable">Divisi</th>
                  <th class="filterTable">Posisi</th>
                  <th class="filterTable">Plan</th>
                  <th class="filterTable">Golongan</th>
                  <th>Status Aktif</th>
                  <th>Peran Kerja Utama</th>
                  <th class="filterTable">Pengguna Terakhir</th>
                  <th class="filterTable">Pembaharuan Terakhir</th>
                </tr>
                <tr>
                  <th class="align-middle text-center">Kode Detail</th>
                  <th class="align-middle text-center">Perusahaan</th>
                  <th class="align-middle text-center">Departemen</th>
                  <th class="align-middle text-center">Divisi</th>
                  <th class="align-middle text-center">Posisi</th>
                  <th class="align-middle text-center">Plan</th>
                  <th class="align-middle text-center">Golongan</th>
                  <th class="align-middle text-center">Status Aktif</th>
                  <th class="align-middle text-center">Peran Kerja Utama</th>
                  <th class="align-middle text-center">Pengguna Terakhir</th>
                  <th class="align-middle text-center">Pembaharuan Terakhir</th>
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