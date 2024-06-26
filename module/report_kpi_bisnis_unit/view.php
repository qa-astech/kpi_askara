<?php
  require_once('../notes_form.php');
  require_once('../header_view.php');
?>
  <title>REPORT KPI REALIZATION BUSSINES UNIT</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php
    require_once('../body_main_js.php');
  ?>
  <h1 class="text-center fw-bold m-0 pb-4 pt-1">REPORT KPI REALIZATION BUSSINES UNIT</h1>

  <!-- Table Utama -->
  <div class="container-fluid px-5 pb-4">
    <div class="d-flex flex-wrap justify-content-between">
      <div>
        <div class="d-inline-flex flex-wrap mb-3 border border-primary rounded py-2 px-3" style="gap: 1em;" id="mainPage">
          <label for="dgUtamaYearInput" class="form-label mb-0 fs-4">Tahun</label>
          <select class="form-select dgUtamaYearInput" name="dgUtamaYearInput" id="dgUtamaYearInput" data-placeholder="Masukan tahun..." style="width: 140px;"></select>
          <!-- <input type="number" class="form-control form-control-sm" id="dgUtamaYearInput" placeholder="Tahun KPI..." aria-label="dgUtamaYearInput" aria-describedby="dgUtamaYearInput" style="width: 150px;"> -->
          <button class="btn btn-sm btn-process" type="button" id="dgUtamaYearBtn">
            <span class="d-inline-block ps-1">PROSES</span>
          </button>
        </div>
        <div class="d-flex flex-wrap mb-2" style="gap: 1em;">
          <div class="btn-group" role="group" aria-label="Button Print">
            <button type="button" id="btnMenuPrintDgUtama" class="btn btn-sm btn-print dropdown-toggle" data-bs-toggle="dropdown" data-bs-target="#menuPrintDgUtama" aria-expanded="false">
              <span class="d-inline-block ps-1">Cetak</span>
            </button>
            <ul class="dropdown-menu dropdown-print" id="menuPrintDgUtama">
              <li>
                <button type="button" class="dropdown-item" id="btnExcelDetailDgUtama">
                  <i class="fa-solid fa-file-excel"></i>
                  <span class="d-inline-block ps-1">Detail Excel</span>
                </button>
              </li>
              <li>
                <button type="button" class="dropdown-item" id="btnPDFDetailDgUtama">
                  <i class="fa-solid fa-file-pdf"></i>
                  <span class="d-inline-block ps-1">Detail PDF</span>
                </button>
              </li>
            </ul>
          </div>
          <button type="button" id="btnReloadDgUtama" class="btn rounded btn-sm btn-reload"></button>
        </div>
      </div>
    </div>
    <div class="d-block overflow-auto w-100 position-relative rounded-top">
      <table id="dgUtama" class="table table-hover table-striped table-bordered w-100 nowrap table-nowrap">
        <thead class="table-dark">
          <tr>
            <th class="align-middle text-center" colspan="19" id="titleYearKPI">KPI ()</th>
          </tr>
          <tr id="thUpper">
            <th class="align-middle text-center" rowspan="2">Kode Document</th>
            <th class="align-middle text-center" rowspan="2">Perspektif</th>
            <th class="align-middle text-center" rowspan="2">Strategi Objektif</th>
            <th class="align-middle text-center" rowspan="2">Index</th>
            <th class="align-middle text-center" rowspan="2">Nama KPI</th>
            <th class="align-middle text-center" rowspan="2">Control Cek</th>
            <th class="align-middle text-center" rowspan="2">Polaritas</th>
            <th class="align-middle text-center" rowspan="2">UOM</th>
            <th class="align-middle text-center" rowspan="2">Target Korporat</th>
          </tr>
          <tr id="thBottom">
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <script type="module" src="scripts.js"></script>

</body>
</html>