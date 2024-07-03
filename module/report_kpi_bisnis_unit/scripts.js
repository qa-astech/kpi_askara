import {
  IsEmpty, resetInputExceptChoice, sendViaFetchForm, AlertElemBS5, ConfirmElemBS5,
  parseVersion, compareVersions, childVersion, number_format_big, monthAcro
} from '../../../third-party/utility-yudhi/utils.js';

$.fn.dataTable.ext.errMode = 'none';

const alertComponent = new AlertElemBS5('alertComponent1');
const confirmComponent = new ConfirmElemBS5('confirmComponent1');
const dgTahunanTbody = document.querySelector('table#dgTahunan > tbody');
const dgBulananTbody = document.querySelector('table#dgBulanan > tbody');
const yearKPI = document.querySelectorAll('.yearKPI');
const dgUtamaYearInput = document.getElementById('dgUtamaYearInput');
const dgUtamaCompanyInput = document.getElementById('dgUtamaCompanyInput');
const dgUtamaYearBtn = document.getElementById('dgUtamaYearBtn');
const dgUtamaUserEntry = document.getElementById('dgUtamaUserEntry');
const dgUtamaLastUpdate = document.getElementById('dgUtamaLastUpdate');

// Button Laporan
const btnLaporanTahunan = document.getElementById('btnLaporanTahunan');
const btnMenuPrintDgBulanan = document.getElementById('btnMenuPrintDgBulanan');
const menuPrintDgBulanan = document.getElementById('menuPrintDgBulanan');
const btnLaporanBulanan = document.getElementById('btnLaporanBulanan');
const btnLaporanQuartal1 = document.getElementById('btnLaporanQuartal1');

// Table
const dgTahunan = document.getElementById('dgTahunan');
const dgTahunanTitle = document.getElementById('dgTahunanTitle');
const trUpperTahunan = document.getElementById('trUpperTahunan');
const trBottomTahunan = document.getElementById('trBottomTahunan');
const dgBulanan = document.getElementById('dgBulanan');
const dgBulananTitle = document.getElementById('dgBulananTitle');
const trUpperBulanan = document.getElementById('trUpperBulanan');

// State Proses
let yearProgress;
let bisnisUnitProgress;
let getDataDepartment;

const getKpiNow = async () => {
  return new Promise(async (resolve) => {
    if (yearProgress && bisnisUnitProgress) {
      const sendData = new FormData();
      sendData.append('year_kpi', yearProgress);
      sendData.append('company_kpi', bisnisUnitProgress);
      const getResult = await sendViaFetchForm('route.php?act=getKpiBisnisUnit', sendData);
      resolve(getResult);
    } else {
      reject('Lengkapi data tahun dan bisnis unit sebelum diproses!');
    }
  });
}

const funViewKPIYear = async () => {
  
  await getKpiNow()
  .then(jsonData => {
    dgTahunanTitle.innerText = `Laporan Tahunan (${yearProgress})`;
    dgBulananTitle.innerText = `Laporan Bulanan (${yearProgress})`;
    yearKPI.forEach(element => {
      element.innerText = ` (${yearProgress})`;
    });
    const dgUtamaYear1 = document.querySelectorAll('.dgUtamaYear1');
    dgUtamaYear1.forEach(element => {
      element.innerText = ` (${yearProgress - 1})`;
    });

    btnMenuPrintDgBulanan.disabled = false;
    btnLaporanTahunan.disabled = false;

    dgTahunanTbody.innerHTML = null;
    dgBulananTbody.innerHTML = null;
    
    if (jsonData.length > 0) {
      let lateDate = '';
      let lateUser = '';
      for (const objectData of jsonData) {
        const lastUpdate = new Date(objectData.last_update);
        if (lateDate === undefined || lastUpdate >= lateDate) {
          lateDate = lastUpdate;
          lateUser = objectData.fullname_entry;
        }

        // add tahunan
        const elemTrTahunan = document.createElement('tr');
        let elemTdTahunan = `
          <td class="align-middle text-center">${objectData.name_perspective}</td>
          <td class="align-middle">${objectData.text_sobject}</td>
          <td class="align-middle">
            <span style="padding-left: ${(objectData.index_kpibunit.split('.').length - 2) * 12}px;">${objectData.index_kpibunit} ${objectData.name_kpibunit}</span>
          </td>
          <td class="align-middle">${objectData.define_kpibunit}</td>
          <td class="align-middle">${objectData.control_cek_kpibunit}</td>
          <td class="align-middle text-center">${objectData.name_satuan}</td>
          <td class="align-middle">${objectData.name_formula}</td>
          <td class="align-middle">${objectData.polaritas_kpibunit}</td>
          <td class="align-middle text-end">${!IsEmpty(objectData.baseline_1, true) ? number_format_big(objectData.baseline_1, 2, '.', ',') : '-'}</td>
          <td class="align-middle text-end">-</td>
          <td class="align-middle text-end">-</td>
          <td class="align-middle text-end">${!IsEmpty(objectData.target_kpibunit, true) ? number_format_big(objectData.target_kpibunit, 2, '.', ',') : ''}</td>
        `;
        const yearData = Object.keys(objectData.year).map((key) => objectData.year[key]);
        for (const department of getDataDepartment) {
          const currentData = yearData.filter((value) => value.deptkpi_id === department.id_department);
          const targetBisnis = currentData && currentData.length > 0 ? currentData[0].target_department : null;
          const realisasi = currentData && currentData.length > 0 ? currentData[0].realisasi : null;
          elemTdTahunan += `
            <td class="align-middle text-end created-js">${targetBisnis ? number_format_big(targetBisnis, 2, '.', ',') : ''}</td>
            <td class="align-middle text-end created-js">${realisasi ? number_format_big(realisasi, 2, '.', ',') : ''}</td>
            <td class="align-middle text-end created-js"></td>
          `;
        }
        elemTdTahunan += `
          <td class="align-middle text-end">${!IsEmpty(objectData.totalRealisasi, true) ? number_format_big(objectData.totalRealisasi, 2, '.', ',') : ''}</td>
          <td class="align-middle text-end"></td>
        `;
        elemTrTahunan.innerHTML = elemTdTahunan;
        dgTahunanTbody.append(elemTrTahunan);

        // add bulanan
        const elemTrBulanan = document.createElement('tr');
        let elemTdBulanan = `
          <td class="align-middle text-center">${objectData.name_perspective}</td>
          <td class="align-middle">${objectData.text_sobject}</td>
          <td class="align-middle">
            <span style="padding-left: ${(objectData.index_kpibunit.split('.').length - 2) * 12}px;">${objectData.index_kpibunit} ${objectData.name_kpibunit}</span>
          </td>
          <td class="align-middle">${objectData.define_kpibunit}</td>
          <td class="align-middle">${objectData.control_cek_kpibunit}</td>
          <td class="align-middle text-center">${objectData.name_satuan}</td>
          <td class="align-middle">${objectData.name_formula}</td>
          <td class="align-middle">${objectData.polaritas_kpibunit}</td>
          <td class="align-middle text-end">${!IsEmpty(objectData.baseline_1, true) ? number_format_big(objectData.baseline_1, 2, '.', ',') : '-'}</td>
          <td class="align-middle text-end">${!IsEmpty(objectData.target_kpibunit, true) ? number_format_big(objectData.target_kpibunit, 2, '.', ',') : ''}</td>
        `;

        const monthData = Object.keys(objectData.month).map((key) => objectData.month[key]);
        for (const month of monthAcro) {
          const currentData = monthData.filter((value) => parseInt(value.month_realisasi) === parseInt(month.number));
          const realisasi = currentData && currentData.length > 0 ? currentData[0].realisasi : null;
          elemTdBulanan += `
            <td class="align-middle text-end created-js">${realisasi ? number_format_big(realisasi, 2, '.', ',') : ''}</td>
          `;
        }
        elemTdBulanan += `
          <td class="align-middle">${!IsEmpty(objectData.totalRealisasi, true) ? number_format_big(objectData.totalRealisasi, 2, '.', ',') : ''}</td>
          <td class="align-middle"></td>
        `;
        elemTrBulanan.innerHTML = elemTdBulanan;
        dgBulananTbody.append(elemTrBulanan);
        
      }
      dgUtamaUserEntry.innerText = lateUser;
      dgUtamaLastUpdate.innerText = lateDate.getFullYear() + '-' + lateDate.getMonth() + '-' + lateDate.getDate() + ' ' + lateDate.getHours() + ':' + lateDate.getMinutes() + ':' + lateDate.getSeconds();

    } else {
      btnMenuPrintDgBulanan.disabled = true;
      btnLaporanTahunan.disabled = true;
    }

  })
  .catch(error => {
    const errorMsg = 'Terjadi kesalahan, Coba beberapa saat lagi!';
    alertComponent.sendAnAlertOnCatch(errorMsg);
    console.error('An error occurred:', error);
  })
}

document.addEventListener("DOMContentLoaded", async () => {

  dgUtamaYearInput.disabled = true;
  dgUtamaCompanyInput.disabled = true;
  dgUtamaYearBtn.disabled = true;
  btnMenuPrintDgBulanan.disabled = true;
  btnLaporanTahunan.disabled = true;
  dgUtamaYearInput.value = null;
  dgUtamaCompanyInput.value = null;

  const sendDataBisnis = new FormData();
  getDataDepartment = await sendViaFetchForm('../master_department/route.php?act=getAllDepartment', sendDataBisnis);
  if (getDataDepartment.response === 'error') {
    alertComponent.sendAnAlertOnCatch(getDataDepartment.alert);
  } else {

    dgUtamaYearInput.disabled = false;
    dgUtamaCompanyInput.disabled = false;
    dgUtamaYearBtn.disabled = false;
  
    $(`#dgUtamaYearInput`).select2({
      theme: "bootstrap-5",
      width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
      placeholder: $( this ).data( 'placeholder' ),
      allowClear: true,
      ajax: {
        url: "route.php?act=jsonTahun",
        dataType: 'json',
        method: 'POST',
        delay: 250,
        data: function (params) {
          return {
            q: params.term,
            page: params.page || 1
          };
        },
        processResults: function (data) {
          return {
            results: data.items,
            pagination: {
              more: data.pagination.more
            }
          };
        },
        cache: true
      },
    });

    $(`#dgUtamaCompanyInput`).select2({
      theme: "bootstrap-5",
      width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
      placeholder: $( this ).data( 'placeholder' ),
      allowClear: true,
      ajax: {
        url: "../master_company/route.php?act=jsonCompany",
        dataType: 'json',
        method: 'POST',
        delay: 250,
        data: function (params) {
          return {
            q: params.term,
            page: params.page || 1
          };
        },
        processResults: function (data) {
          return {
            results: data.results,
            pagination: {
              more: data.pagination.more
            }
          };
        },
        cache: true
      },
    });
  
    dgUtamaYearBtn.addEventListener('click', async (e) => {
      yearProgress = dgUtamaYearInput.value;
      bisnisUnitProgress = dgUtamaCompanyInput.value;
      await funViewKPIYear();
    });
      
    let extraUpperBulanan = '';
    for (const month of monthAcro) {
      extraUpperBulanan += `
        <th class="align-middle text-center created-js">${month.full}</th>
      `;
    }
    trUpperBulanan.innerHTML = extraUpperBulanan.trim();
  
    let extraUpperTahunan = trUpperTahunan.innerHTML.trim();
    let extraBottomTahunan = trBottomTahunan.innerHTML.trim();
    for (const department of getDataDepartment) {
      extraUpperTahunan += `
        <th class="align-middle text-center created-js" colspan='3'>${department.name_department}</th>
      `;
      extraBottomTahunan += `
        <th class="align-middle text-center created-js">Target</th>
        <th class="align-middle text-center created-js">Realisasi</th>
        <th class="align-middle text-center created-js">Pencapaian</th>
      `;
    }
    extraUpperTahunan += `
        <th class="align-middle text-center" rowspan="2">Total Realisasi</th>
        <th class="align-middle text-center" rowspan="2">Pencapaian<br>Keseluruhan</th>
    `;
    trBottomTahunan.innerHTML = extraBottomTahunan.trim();
    trUpperTahunan.innerHTML = extraUpperTahunan.trim();
  }


});