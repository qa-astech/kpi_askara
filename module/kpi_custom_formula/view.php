<?php
  require_once('../header_view.php');
  require_once('../notes_form.php');
?>
  <title>KPI CUSTOM FORMULA</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php
    require_once('../body_main_js.php');
  ?>
  <h1 class="text-center fw-bold m-0 pb-4 pt-2" id="titleUtama">KPI CUSTOM FORMULA</h1>

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
          </tbody>
        </table>
        <div class="d-flex flex-wrap justify-content-between" style="gap: 1em;">
          <button type="button" id="btnResetDgUtama" class="btn rounded btn-sm btn-reset">
            <span class="d-inline-block ps-1">Reset</span>
          </button>
          <button type="button" id="btnSelectDgUtama" class="btn rounded btn-sm btn-select">
            <span class="d-inline-block ps-1">Pilih KPI</span>
          </button>
        </div>
        <div id="cardChoice" class="text-end"></div>
      </div>
      <div class="mb-0" role="alert">
        <div class="border rounded p-2">
          <p class="fw-bold mb-1">Pengguna Terakhir :</p>
          <p id="dgUtamaUserEntry">-</p>
          <p class="fw-bold mb-1">Pembaharuan Terakhir :</p>
          <p id="dgUtamaLastUpdate" class="mb-0">-</p>
        </div>
      </div>
      <div class="noteRumus">
        <div class="alert alert-info" role="alert">
          <h4 class="fw-bold">Formula Bawaan</h4>
          <p>Secara bawaan, rumus perhitungan formula diatur sebagai berikut :</p>
          <div>
            <ul>
              <li><span class="fw-bold">Polaritas "MAX"</span></li>
            </ul>
            <p>Dalam polaritas "max" rumus pencapaian akan dihitung sebagai berikut = <span class="fw-bold">(Realisasi / Target)</span></p>
          </div>
          <div>
            <ul>
              <li><span class="fw-bold">Polaritas "MIN"</span></li>
            </ul>
            <p>Dalam polaritas "MIN" rumus pencapaian akan dihitung sebagai berikut = <span class="fw-bold">100 + (100 - (Realisasi / Target * 100))</span></p>
          </div>
        </div>
      </div>
      <div class="noteRumus">
        <div class="alert alert-info" role="alert">
          <h4 class="fw-bold">Catatan</h4>
          <p>Modul ini ditujukan untuk merubah formula dalam KPI, sesuai dengan ketentuan yang user mau. Silahkan kustomisasi formula perhitungan pencapain KPI dengan menekan tombol <span class="fw-bold text-danger">"Custom Formula"</span></p>
        </div>
      </div>
    </div>
    <div class="d-block overflow-auto w-100 position-relative rounded-top mb-5">
      <table id="dgUtama" class="table table-hover table-striped table-bordered w-100 table-nowrap">
        <thead class="table-dark">
          <tr>
            <th class="align-middle text-center" rowspan="3">Action</th>
            <th class="align-middle text-center" rowspan="3">Perspektif</th>
            <th class="align-middle text-center" rowspan="3">Strategi Objektif</th>
            <th class="align-middle text-center" rowspan="3">Layout KPI</th>
            <th class="align-middle text-center" rowspan="3">Definisi KPI</th>
            <th class="align-middle text-center" rowspan="3">Control Cek</th>
            <th class="align-middle text-center" rowspan="3">Satuan</th>
            <th class="align-middle text-center" rowspan="3">Polaritas</th>
            <th class="align-middle text-center" colspan="14">Target KPI</th>
          </tr>
          <tr>
            <th class="align-middle text-center" rowspan="2">Target Tahunan<br>(KPI Korporat)</th>
            <th class="align-middle text-center" rowspan="2">Target Tahunan<br>(KPI Bisnis Unit)</th>
            <th class="align-middle text-center" colspan="12">Target Bulanan</th>
          </tr>
          <tr id="listMonthRow"></tr>
        </thead>
        <tbody id="idTargetTbody"></tbody>
      </table>
    </div>
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

</body>
</html>