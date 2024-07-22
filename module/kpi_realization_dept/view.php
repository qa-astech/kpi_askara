<?php
  require_once('../header_view.php');
  require_once('../notes_form.php');
?>
  <title>KPI REALIZATION</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php
    require_once('../body_main_js.php');
  ?>
  <h1 class="text-center fw-bold m-0 pb-4 pt-2">KPI REALIZATION</h1>

  <!-- Table Utama -->
  <div class="container-fluid">
    <div class="d-flex flex-wrap mb-3" style="gap: 1.5rem;">
      <div>
        <table class="mb-3">
          <tbody>
            <tr>
              <td class="pe-3"><span class="form-label mb-0">Tahun <span class="fw-bold text-danger">*</span></span></td>
              <td>
                <input type="number" class="form-control form-control-sm" id="dgUtamaYearInput" placeholder="Tahun..." aria-label="dgUtamaYearInput" aria-describedby="dgUtamaYearInput" style="width: 120px;">
              </td>
            </tr>
            <tr>
              <td class="pt-2 pe-3"><span class="form-label mb-0">Bulan <span class="fw-bold text-danger">*</span></span></td>
              <td class="pt-2">
                <div class="d-flex justify-content-between" style="width: 340px;">
                  <div style="width: 160px;"><select class="form-select form-select-sm" name="dgUtamaDateInput1" id="dgUtamaDateInput1" data-placeholder="Bulan..."></select></div>
                  <div>-</div>
                  <div style="width: 160px;"><select class="form-select form-select-sm" name="dgUtamaDateInput2" id="dgUtamaDateInput2" data-placeholder="Bulan..."></select></div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="d-flex flex-wrap justify-content-end" style="gap: 1em;">
          <button type="button" id="btnResetDgUtama" class="btn rounded btn-sm btn-reset">
            <span class="d-inline-block ps-1">Reset</span>
          </button>
          <button type="button" id="btnSelectDgUtama" class="btn rounded btn-sm btn-select">
            <span class="d-inline-block ps-1">Pilih KPI</span>
          </button>
        </div>
        <div id="card-choice" class="text-end"></div>
      </div>
      <div class="mb-0" role="alert">
        <div class="alert border">
          <h5 class="fw-bold mb-3 text-decoration-underline">Indikator Pencapaian</h5>
          <div>
            <p class="fw-bold mb-1"><span class="circle bg-info"></span> ISTIMEWA (101% - 120%)</p>
            <p class="fw-bold mb-1"><span class="circle bg-success"></span> BAGUS (91% - 100%)</p>
            <p class="fw-bold mb-1"><span class="circle bg-warning"></span> CUKUP (80% - 90%)</p>
            <p class="fw-bold mb-1"><span class="circle bg-danger"></span> KURANG (< 80%)</p>
          </div>
        </div>
        <div class="border rounded p-2">
          <p class="fw-bold mb-1">Pengguna Terakhir :</p>
          <p id="dgUtamaUserEntry">-</p>
          <p class="fw-bold mb-1">Pembaharuan Terakhir :</p>
          <p id="dgUtamaLastUpdate" class="mb-0">-</p>
        </div>
      </div>
      <div class="noteDgUtama">
        <?php
          echo $notesForm;
        ?>
      </div>
    </div>
    <!-- <div class="d-block overflow-auto w-100 position-relative rounded-top"> -->
      <form enctype="multipart/form-data" id="formRealization">
        <table id="dgUtama" class="table table-hover table-striped table-bordered w-100 table-nowrap">
          <thead class="table-dark">
            <tr id="trHeaderUtama">
              <th class="align-middle text-center" rowspan="2">Kode Document</th>
              <th class="align-middle text-center" rowspan="2">Perspektif</th>
              <th class="align-middle text-center" rowspan="2">Strategi Objektif</th>
              <th class="align-middle text-center" rowspan="2">Layout KPI</th>
              <th class="align-middle text-center" rowspan="2">Definisi KPI</th>
              <th class="align-middle text-center" rowspan="2">Control Cek</th>
              <th class="align-middle text-center" rowspan="2">Target Korporat</th>
              <th class="align-middle text-center" rowspan="2">Target Bisnis Unit</th>
              <th class="align-middle text-center" rowspan="2">Satuan</th>
              <th class="align-middle text-center" rowspan="2">Formula</th>
              <th class="align-middle text-center" rowspan="2" id="beforeBulanHeader" style="border-right: 1px solid var(--bs-danger);">Polaritas</th>
            </tr>
            <tr id="monthTarget">
            </tr>
          </thead>
          <tbody id="idTargetTbody"></tbody>
        </table>
      </form>
    <!-- </div> -->
  </div>

  <div class="modal fade" id="selectKPIModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Pilih Jenis KPI </h4>
          <button type="button" class="btn-close me-1" data-bs-dismiss="modal" data-bs-target="#selectKPIModal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="selectKPIModalForm" enctype="multipart/form-data" method="post">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" data-bs-target="#selectKPIModal"><i class="fa-solid fa-xmark pe-1"></i> Batal</button>
          <button type="button" class="btn btn-sm btn-primary" id="selectKPIModalSave"><i class="fa-solid fa-floppy-disk pe-1"></i> Pilih KPI</button>
        </div>
      </div>
    </div>
  </div>

  <script type="module" src="scripts.js"></script>
  <iframe src="" frameborder="0" width="0" height="0" class="d-none" id="iframeDownload"></iframe>

</body>
</html>