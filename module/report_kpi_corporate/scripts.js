import {
  IsEmpty, resetInputExceptChoice, sendViaFetchForm, AlertElemBS5, ConfirmElemBS5,
  parseVersion, compareVersions, childVersion,
  number_format_big,
  monthAcro
} from '../../../third-party/utility-yudhi/utils.js';

$.fn.dataTable.ext.errMode = 'none';
const alertComponent = new AlertElemBS5('alertComponent1');
const confirmComponent = new ConfirmElemBS5('confirmComponent1');
const dgTahunanTbody = document.querySelector('table#dgTahunan > tbody');
const dgBulananTbody = document.querySelector('table#dgBulanan > tbody');
const yearKPI = document.querySelectorAll('.yearKPI');
const dgUtamaUserEntry = document.getElementById('dgUtamaUserEntry');
const dgUtamaLastUpdate = document.getElementById('dgUtamaLastUpdate');
const dgTahunanTitle = document.getElementById('dgTahunanTitle');
const dgBulananTitle = document.getElementById('dgBulananTitle');
const dgUtamaYear3 = document.getElementById('dgUtamaYear3');
const dgUtamaYear2 = document.getElementById('dgUtamaYear2');
const dgUtamaYear1 = document.getElementById('dgUtamaYear1');

const dgUtamaYearInput = document.getElementById('dgUtamaYearInput');
const dgUtamaYearBtn = document.getElementById('dgUtamaYearBtn');

// button print
const btnMenuPrintDgBulanan = document.getElementById('btnMenuPrintDgBulanan');
const menuPrintDgBulanan = document.getElementById('menuPrintDgBulanan');
const btnLaporanTahunan = document.getElementById('btnLaporanTahunan');
const btnLaporanBulanan = document.getElementById('btnLaporanBulanan');
const btnLaporanQuartal1 = document.getElementById('btnLaporanQuartal1');
// table
const dgBulanan = document.getElementById('dgBulanan');
const trUpperBulanan = document.getElementById('trUpperBulanan');
const trBottomBulanan = document.getElementById('trBottomBulanan');
const dgTahunan = document.getElementById('dgTahunan');
const trUpperTahunan = document.getElementById('trUpperTahunan');
const trBottomTahunan = document.getElementById('trBottomTahunan');

// State Proses
let yearProgress;
let getDataBisnisUnit;

const getKpiNow = async (year) => {
  return new Promise(async (resolve) => {
    const sendData = new FormData();
    sendData.append('year_kpi', year);
    const getResult = await sendViaFetchForm('route.php?act=getKpiCorporate', sendData);
    resolve(getResult);
  });
}

const funViewKPIYear = async (year) => {
  
  await getKpiNow(year)
  .then(jsonData => {
    dgUtamaYear3.innerText = year - 3;
    dgUtamaYear2.innerText = year - 2;
    dgUtamaYear1.innerText = year - 1;
    dgTahunanTitle.innerText = `Laporan Tahunan (${year})`;
    dgBulananTitle.innerText = `Laporan Bulanan (${year})`;
    yearKPI.forEach(element => {
      element.innerText = ` (${year})`;
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
            <span style="padding-left: ${(objectData.index_kpicorp.split('.').length - 2) * 12}px;">${objectData.index_kpicorp} ${objectData.name_kpicorp}</span>
          </td>
          <td class="align-middle">${objectData.define_kpicorp}</td>
          <td class="align-middle">${objectData.control_cek_kpicorp}</td>
          <td class="align-middle text-center">${objectData.name_satuan}</td>
          <td class="align-middle">${objectData.name_formula}</td>
          <td class="align-middle">${objectData.polaritas_kpicorp}</td>
          <td class="align-middle text-end">${!IsEmpty(objectData.baseline_3, true) ? number_format_big(objectData.baseline_3, 2, '.', ',') : '-'}</td>
          <td class="align-middle text-end">-</td>
          <td class="align-middle text-end">-</td>
          <td class="align-middle text-end">${!IsEmpty(objectData.baseline_2, true) ? number_format_big(objectData.baseline_2, 2, '.', ',') : '-'}</td>
          <td class="align-middle text-end">-</td>
          <td class="align-middle text-end">-</td>
          <td class="align-middle text-end">${!IsEmpty(objectData.baseline_1, true) ? number_format_big(objectData.baseline_1, 2, '.', ',') : '-'}</td>
          <td class="align-middle text-end">-</td>
          <td class="align-middle text-end">-</td>
          <td class="align-middle text-end">${!IsEmpty(objectData.target_kpicorp, true) ? number_format_big(objectData.target_kpicorp, 2, '.', ',') : ''}</td>
        `;
        const yearData = Object.keys(objectData.year).map((key) => objectData.year[key]);
        for (const bisnisUnit of getDataBisnisUnit) {
          const currentData = yearData.filter((value) => value.compkpi_id === bisnisUnit.id_company);
          const targetBisnis = currentData && currentData.length > 0 ? currentData[0].target_bisnis : null;
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
            <span style="padding-left: ${(objectData.index_kpicorp.split('.').length - 2) * 12}px;">${objectData.index_kpicorp} ${objectData.name_kpicorp}</span>
          </td>
          <td class="align-middle">${objectData.define_kpicorp}</td>
          <td class="align-middle">${objectData.control_cek_kpicorp}</td>
          <td class="align-middle text-center">${objectData.name_satuan}</td>
          <td class="align-middle">${objectData.name_formula}</td>
          <td class="align-middle">${objectData.polaritas_kpicorp}</td>
          <td class="align-middle text-end">${!IsEmpty(objectData.target_kpicorp, true) ? number_format_big(objectData.target_kpicorp, 2, '.', ',') : ''}</td>
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
  dgUtamaYearBtn.disabled = true;
  btnMenuPrintDgBulanan.disabled = true;
  btnLaporanTahunan.disabled = true;
  dgUtamaYearInput.value = null;

  const sendDataBisnis = new FormData();
  getDataBisnisUnit = await sendViaFetchForm('route.php?act=getDataBisnisUnit', sendDataBisnis);
  if (getDataBisnisUnit.response === 'error') {
    alertComponent.sendAnAlertOnCatch(getDataBisnisUnit.alert);
  } else {

    dgUtamaYearInput.disabled = false;
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
    // $('#dgUtamaYearInput').on('select2:select', async (e) => {
    // });
  
    dgUtamaYearBtn.addEventListener('click', async (e) => {
      yearProgress = dgUtamaYearInput.value;
      await funViewKPIYear(yearProgress);
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
    for (const bisnisUnit of getDataBisnisUnit) {
      extraUpperTahunan += `
        <th class="align-middle text-center created-js" colspan='3'>${bisnisUnit.name_company}</th>
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