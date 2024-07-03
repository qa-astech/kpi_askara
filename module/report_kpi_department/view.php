<?php
  require_once('../notes_form.php');
  require_once('../header_view.php');
?>
  <title>LAPORAN KPI BISNIS UNIT</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php
    require_once('../body_main_js.php');
  ?>
  <h1 class="text-center fw-bold m-0 pb-4 pt-2">LAPORAN KPI BISNIS UNIT<span class="yearKPI"></span></h1>

  <div class="container-fluid px-3 pb-4">
    <div class="d-flex flex-wrap justify-content-between mb-3">
      <div>
        <table>
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
              <td class="pt-2"></td>
            </tr>
            <tr>
              <td class="pt-2 pe-3"><label for="dgUtamaDepartmentInput" class="form-label mb-0 fs-5">Departemen <span class="fw-bold text-danger">*</span></label></td>
              <td class="pt-2 pe-3">
                <select class="form-select form-select-sm" name="dgUtamaDepartmentInput" id="dgUtamaDepartmentInput" data-placeholder="Masukan Departemen..." style="width: 310px;"></select>
              </td>
              <td class="pt-2">
                <button class="btn btn-sm btn-process" type="button" id="dgUtamaYearBtn">
                  <span class="d-inline-block ps-1">PROSES</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
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
      <div class="my-3">
        <button type="button" id="btnLaporan" class="btn btn-sm btn-print rounded">
          <span class="d-inline-block ps-1">Cetak Laporan</span>
        </button>
      </div>
    </div>
    <div class="d-block overflow-auto w-100 position-relative rounded-top">
      <table id="dgLaporan" class="table table-hover table-striped table-bordered w-100 nowrap table-nowrap">
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
            <th class="align-middle text-center" rowspan="2">Baseline<span id="dgUtamaYear1"></span></th>
            <th class="align-middle text-center" rowspan="2">Target<br>Tahun<span class="yearKPI"></span></th>
            <th class="align-middle text-center" colspan="24">Realisasi Bulanan</th>
            <th class="align-middle text-center" rowspan="2">Total Realisasi</th>
            <th class="align-middle text-center" rowspan="2">Pencapaian<br>Keseluruhan</th>
          </tr>
          <tr id="trUpperLaporan">
          </tr>
          <tr id="trBottomLaporan">
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
    
  </div>

  <script type="module" src="scripts.js"></script>

</body>
</html>