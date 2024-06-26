<?php
  require_once('../header_view.php');
  require_once('../notes_form.php');
?>
  <title>MENU</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php
    require_once('../body_main_js.php');
  ?>
  <h1 class="text-center fw-bold m-0 pb-3 pt-1">MENU</h1>

  <!-- Table Utama -->
  <div class="container-fluid px-5 pb-4">
    <table id="dgUtama" class="table table-hover table-striped table-bordered w-100 nowrap">
      <thead class="table-dark">
        <!-- <tr id="dgUtamaFilter">
          <th class="filterTable">Kode Menu</th>
          <th class="filterTable">Layout Menu</th>
          <th></th>
          <th class="filterTable">Link Menu</th>
          <th class="filterTable">Pengguna Terakhir</th>
          <th class="filterTable">Pembaharuan Terakhir</th>
        </tr> -->
        <tr>
          <th class="align-middle text-center w-0" id="thDetailDgUtama">List<br>Akses</th>
          <th id="thKodeMenu" class="align-middle text-center">Kode Menu</th>
          <th id="thLayoutMenu" class="align-middle text-center">Layout Menu</th>
          <th id="thIconMenu" class="align-middle text-center">Icon Menu</th>
          <th id="thLinkMenu" class="align-middle text-center">Link Menu</th>
          <th id="thUserEntry" class="align-middle text-center">Pengguna Terakhir</th>
          <th id="thLastUpdate" class="align-middle text-center">Pembaharuan Terakhir</th>
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
                <label for="code_menu">Kode Menu <small class="fw-bold text-danger">(auto)</small></label>
                <input type="text" class="form-control" id="code_menu" aria-describedby="code_menu" placeholder="Masukkan Kode..." disabled>
              </div>
              <div class="form-group">
                <label for="title_menu">Nama Menu <span class="fw-bold text-danger">*</span></label>
                <input type="text" class="form-control" id="title_menu" name="title_menu" aria-describedby="title_menu" placeholder="Masukkan Nama...">
              </div>
              <div class="form-group">
                <label for="index_menu">Index Menu <span class="fw-bold text-danger">*#</span></label>
                <input type="text" class="form-control" id="index_menu" name="index_menu" aria-describedby="index_menu" placeholder="Masukkan Index...">
                <small class="fst-italic">Pakai titik <span class="fw-bold">'.'</span> untuk menentukan <span class="fw-bold">turunan menu</span> <span class="fw-bold text-danger">#</span></small>
              </div>
              <div class="form-group">
                <label for="status_menu">Tipe Menu <small class="fw-bold text-danger">(auto dari index)</small></label>
                <input type="text" class="form-control" id="status_menu" name="status_menu" aria-describedby="status_menu" placeholder="Menunggu input index..." readonly>
              </div>
              <div class="form-group">
                <label for="icon_menu">Ikon Menu <span class="font-weight-bold text-danger">*</span></label>
                <select class="form-select" name="icon_menu" id="icon_menu" data-placeholder="Cari ikon..."></select>
              </div>
              <div class="form-group">
                <div class="d-flex justify-content-start align-items-center pb-1" style="gap: 1rem;">
                  <label for="link_menu" class="d-inline-block">Link Menu <span class="fw-bold text-danger">#</span></label>
                  <label for="turunan_akhir" class="form-check" id="turunan_akhir_label">
                    <input type="checkbox" name="turunan_akhir" id="turunan_akhir" class="form-check-input" value="true">
                    <small class="form-check-label fst-italic">ENABLE ?</small>
                  </label>
                </div>
                <input type="text" class="form-control" id="link_menu" name="link_menu" aria-describedby="link_menu" placeholder="Masukkan Link..." disabled>
                <small class="fst-italic">Silahkan isi link menu ini jika butuh menu butuh link... <span class="fw-bold text-danger">#</span></small>
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
	  <div class="modal-dialog modal-dialog-centered" role="document">
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
                <label for="code_maccess" class="d-block">Kode Access <small class="font-weight-bold text-danger">(auto)</small></label>
                <input type="text" class="form-control" id="code_maccess" aria-describedby="code_maccess" placeholder="Masukkan Kode..." disabled>
              </div>
              <div class="form-group">
                <label for="name_maccess">Nama Access <span class="font-weight-bold text-danger">*</span></label>
                <input type="text" class="form-control" id="name_maccess" name="name_maccess" aria-describedby="name_maccess" placeholder="Masukkan nama...">
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

  <!-- Modal Detail View -->
  <div class="modal fade" id="modalDetailView" tabindex="-1" role="dialog" aria-labelledby="modalDetailView" aria-hidden="true" style="z-index: 2000;">
	  <div class="modal-dialog modal-fullscreen" role="document">
	    <div class="modal-content">
	      <div class="modal-header bg-dark">
	        <h5 class="modal-title fw-bold text-white">List Akses</h5>
	        <button type="button" class="btn btn-x" data-bs-dismiss="modal" data-bs-target="#modalDetailView" aria-label="Close">
	        </button>
	      </div>
	      <div class="modal-body">
          <div class="container-fluid px-5">
            <table class="table table-borderless w-100 nowrap table-information">
              <tbody>
                <tr>
                  <th>Kode Menu</th>
                  <th class="px-1">:</th>
                  <td id="modalDetailViewKodeMenu"></td>
                </tr>
                <tr>
                  <th>Nama Menu</th>
                  <th class="px-1">:</th>
                  <td id="modalDetailViewNameMenu"></td>
                </tr>
                <tr>
                  <th>Index Menu</th>
                  <th class="px-1">:</th>
                  <td id="modalDetailViewIndexMenu"></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="container-fluid px-5">
            <table id="dgDetail" class="table table-hover table-striped table-bordered w-100 nowrap">
              <thead class="table-dark">
                <tr id="dgDetailFilter">
                  <th class="filterTable">Kode Access</th>
                  <th class="filterTable">Nama Access</th>
                  <th class="filterTable">Pengguna Terakhir</th>
                  <th class="filterTable">Pembaharuan Terakhir</th>
                </tr>
                <tr>
                  <th id="thCodeAccess" class="align-middle text-center">Kode Access</th>
                  <th id="thNameAccess" class="align-middle text-center">Nama Access</th>
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