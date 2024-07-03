<?php
  require_once('../notes_form.php');
  require_once('../header_view.php');
?>
  <title>LAPORAN KPI KORPORAT</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php
    require_once('../body_main_js.php');
  ?>
  <h1 class="text-center fw-bold m-0 pb-4 pt-2">LAPORAN KPI KORPORAT<span class="yearKPI"></span></h1>

  <div class="container-fluid px-3 pb-4">
    <div class="d-flex flex-wrap justify-content-between">
      <div>
        <div class="d-inline-flex flex-wrap mb-3 border border-primary rounded py-2 px-3" style="gap: 1em;" id="mainPage">
          <label for="dgUtamaYearInput" class="form-label mb-0 fs-4">Tahun</label>
          <select class="form-select dgUtamaYearInput" name="dgUtamaYearInput" id="dgUtamaYearInput" data-placeholder="Masukan tahun..." style="width: 140px;"></select>
          <button class="btn btn-sm btn-process" type="button" id="dgUtamaYearBtn">
            <span class="d-inline-block ps-1">PROSES</span>
          </button>
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

    <div>
      <h2 id="dgTahunanTitle">Laporan Tahunan</h2>
      <div class="my-3">
        <button type="button" id="btnLaporanTahunan" class="btn btn-sm btn-print rounded">
          <span class="d-inline-block ps-1">Cetak Laporan Tahunan</span>
        </button>
      </div>
    </div>
    <div class="d-block overflow-auto w-100 position-relative rounded-top mb-3">
      <table id="dgTahunan" class="table table-hover table-striped table-bordered w-100 nowrap table-nowrap">
        <thead class="table-dark">
          <tr id="trUpperTahunan">
            <th class="align-middle text-center" rowspan="2">Perspektif</th>
            <th class="align-middle text-center" rowspan="2">Strategi Objektif</th>
            <th class="align-middle text-center" rowspan="2">Layout KPI</th>
            <th class="align-middle text-center" rowspan="2">Definisi KPI</th>
            <th class="align-middle text-center" rowspan="2">Control Cek</th>
            <th class="align-middle text-center" rowspan="2">UOM</th>
            <th class="align-middle text-center" rowspan="2">Formula</th>
            <th class="align-middle text-center" rowspan="2">Polaritas</th>
            <th class="align-middle text-center" colspan="3">Baseline (<span id="dgUtamaYear3"></span>)</th>
            <th class="align-middle text-center" colspan="3">Baseline (<span id="dgUtamaYear2"></span>)</th>
            <th class="align-middle text-center" colspan="3">Baseline (<span id="dgUtamaYear1"></span>)</th>
            <th class="align-middle text-center" rowspan="2">Target Korporat</th>
          </tr>
          <tr id="trBottomTahunan">
            <th class="align-middle text-center">Target</th>
            <th class="align-middle text-center">Realisasi</th>
            <th class="align-middle text-center">Pencapaian</th>
            <th class="align-middle text-center">Target</th>
            <th class="align-middle text-center">Realisasi</th>
            <th class="align-middle text-center">Pencapaian</th>
            <th class="align-middle text-center">Target</th>
            <th class="align-middle text-center">Realisasi</th>
            <th class="align-middle text-center">Pencapaian</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

    <div>
      <h2 id="dgBulananTitle">Laporan Bulanan</h2>
      <div class="my-3">
        <div class="btn-group" role="group" aria-label="Button Print">
          <button type="button" id="btnMenuPrintDgBulanan" class="btn btn-sm btn-print dropdown-toggle" data-bs-toggle="dropdown" data-bs-target="#menuPrintDgBulanan" aria-expanded="false">
            <span class="d-inline-block ps-1">Cetak</span>
          </button>
          <ul class="dropdown-menu dropdown-print" id="menuPrintDgBulanan">
            <li>
              <button type="button" class="dropdown-item" id="btnLaporanBulanan">
                <i class="fa-solid fa-file-excel"></i>
                <span class="d-inline-block ps-1">Laporan 12 Bulan</span>
              </button>
            </li>
            <li>
              <button type="button" class="dropdown-item" id="btnLaporanQuartal1">
                <i class="fa-solid fa-file-excel"></i>
                <span class="d-inline-block ps-1">Laporan Per-Quartal</span>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="d-block overflow-auto w-100 position-relative rounded-top">
      <table id="dgBulanan" class="table table-hover table-striped table-bordered w-100 nowrap table-nowrap">
        <thead class="table-dark">
          <tr>
            <th class="align-middle text-center" rowspan="2">Perspektif</th>
            <th class="align-middle text-center" rowspan="2">Strategi Objektif</th>
            <th class="align-middle text-center" rowspan="2">Layout KPI</th>
            <th class="align-middle text-center" rowspan="2">Definisi KPI</th>
            <th class="align-middle text-center" rowspan="2">Control Cek</th>
            <th class="align-middle text-center" rowspan="2">UOM</th>
            <th class="align-middle text-center" rowspan="2">Formula</th>
            <th class="align-middle text-center" rowspan="2">Polaritas</th>
            <th class="align-middle text-center" rowspan="2">Target<br>Tahun<span class="yearKPI"></span></th>
            <th class="align-middle text-center" colspan="12">Realisasi Bulanan</th>
            <th class="align-middle text-center" rowspan="2">Total Realisasi</th>
            <th class="align-middle text-center" rowspan="2">Pencapaian<br>Keseluruhan</th>
          </tr>
          <tr id="trUpperBulanan">
          </tr>
          <!-- <tr id="trBottomBulanan">
          </tr> -->
        </thead>
        <tbody></tbody>
      </table>
    </div>
    
  </div>

  <script type="module" src="scripts.js"></script>

</body>
</html>