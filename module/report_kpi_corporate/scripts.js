import {
  IsEmpty, resetInputExceptChoice, sendViaFetchForm, AlertElemBS5, ConfirmElemBS5,
  parseVersion, compareVersions, childVersion
} from '../../../third-party/utility-yudhi/utils.js';

$.fn.dataTable.ext.errMode = 'none';
const alertComponent = new AlertElemBS5('alertComponent1');
const confirmComponent = new ConfirmElemBS5('confirmComponent1');
const dgUtamaYearBtn = document.getElementById('dgUtamaYearBtn');
const btnMenuPrintDgUtama = document.getElementById('btnMenuPrintDgUtama');
const btnExcelDetailDgUtama = document.getElementById('btnExcelDetailDgUtama');
const btnPDFDetailDgUtama = document.getElementById('btnPDFDetailDgUtama');
const btnReloadDgUtama = document.getElementById('btnReloadDgUtama');
const dgUtamaYearInput = document.getElementById('dgUtamaYearInput');
const dgUtamaYear3 = document.getElementById('dgUtamaYear3');
const dgUtamaYear2 = document.getElementById('dgUtamaYear2');
const dgUtamaYear1 = document.getElementById('dgUtamaYear1');
const dgTarget = document.getElementById('dgTarget');
const titleYearKPI = document.getElementById('titleYearKPI');
const dgUtamaUserEntry = document.getElementById('dgUtamaUserEntry');
const dgUtamaLastUpdate = document.getElementById('dgUtamaLastUpdate');
// menuPrintDgUtama
// dgUtama
const dgUtamaTbody = document.querySelector('table#dgUtama > tbody');

$(`#dgUtamaYearInput`).select2({
  theme: "bootstrap-5",
  width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
  placeholder: $( this ).data( 'placeholder' ),
  dropdownParent: $('#mainPage'),
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

let funBtnExcelDetailDgUtama = () => {};
let funBtnPDFDetailDgUtama = () => {};
let funBtnReloadDgUtama = () => {};
let yearProgress;
let indexRowEditor = 0;

const getKpiNow = async (year, copyTemplate = false) => {
  return new Promise(async (resolve) => {
    const sendData = new FormData();
    sendData.append('year_kpi', year);
    sendData.append('copy_kpi', copyTemplate ? 'copy' : 'normal');
    sendData.append('year_baseline', yearProgress);
    const getResult = await sendViaFetchForm('route.php?act=getKpiCorporate', sendData);
    resolve(getResult);
  });
}

const funViewKPIYear = async (year) => {
  titleYearKPI.innerText = `LOADING....`;
  
  await getKpiNow(year)
  .then(jsonData => {
    titleYearKPI.innerText = `KPI (${year})`;
    dgUtamaYear3.innerText = year - 3;
    dgUtamaYear2.innerText = year - 2;
    dgUtamaYear1.innerText = year - 1;
    dgTarget.innerText = year;
    btnExcelDetailDgUtama.removeEventListener('click', funBtnExcelDetailDgUtama);
    btnPDFDetailDgUtama.removeEventListener('click', funBtnPDFDetailDgUtama);
    btnReloadDgUtama.removeEventListener('click', funBtnReloadDgUtama);
    btnMenuPrintDgUtama.disabled = false;
    btnExcelDetailDgUtama.disabled = false;
    btnPDFDetailDgUtama.disabled = false;
    btnReloadDgUtama.disabled = false;
    dgUtamaTbody.innerHTML = null;
    
    if (jsonData.length > 0) {
      let lateDate;
      let lateUser;
      for (const objectData of jsonData) {
        const lastUpdate = new Date(objectData.last_update);
        if (lateDate === undefined || lastUpdate >= lateDate) {
          lateDate = lastUpdate;
          lateUser = objectData.user_entry;
        }
        const elemTr = document.createElement('tr');
        const elemTd = `
          <td class="align-middle text-center">${objectData.id_kpicorp}</td>
          <td class="align-middle text-center">${objectData.name_perspective}</td>
          <td class="align-middle">${objectData.text_sobject}</td>
          <td class="align-middle">
            <span style="padding-left: ${(objectData.index_kpicorp.split('.').length - 2) * 8}px;">${objectData.index_kpicorp}</span>
          </td>
          <td class="align-middle">${objectData.name_kpicorp}</td>
          <td class="align-middle">${objectData.control_cek_kpicorp}</td>
          <td class="align-middle">${objectData.polaritas_kpicorp}</td>
          <td class="align-middle text-center">${objectData.name_satuan}</td>
          <td class="align-middle text-end">${IsEmpty(objectData.baseline_3) ? '-' : objectData.baseline_3}</td>
          <td class="align-middle"> - </td>
          <td class="align-middle text-end">${IsEmpty(objectData.baseline_2) ? '-' : objectData.baseline_2}</td>
          <td class="align-middle"> - </td>
          <td class="align-middle text-end">${IsEmpty(objectData.baseline_1) ? '-' : objectData.baseline_1}</td>
          <td class="align-middle"> - </td>
          <td class="align-middle text-end">${objectData.target_kpicorp}</td>
          <td class="align-middle"> - </td>
        `;
        elemTr.innerHTML = elemTd;
        dgUtamaTbody.append(elemTr);
      }
      dgUtamaUserEntry.innerText = lateUser;
      dgUtamaLastUpdate.innerText = lateDate.getFullYear() + '-' + lateDate.getMonth() + '-' + lateDate.getDate() + ' ' + lateDate.getHours() + ':' + lateDate.getMinutes() + ':' + lateDate.getSeconds();
      funBtnExcelDetailDgUtama = async (e) => {}
      funBtnPDFDetailDgUtama = async (e) => {}

      btnExcelDetailDgUtama.addEventListener('click', funBtnExcelDetailDgUtama);
      btnPDFDetailDgUtama.addEventListener('click', funBtnPDFDetailDgUtama);

    } else {
      funBtnExcelDetailDgUtama = async (e) => {}
      funBtnPDFDetailDgUtama = async (e) => {}

      btnMenuPrintDgUtama.disabled = true;
      btnExcelDetailDgUtama.disabled = true;
      btnPDFDetailDgUtama.disabled = true;
    }

    funBtnReloadDgUtama = async (e) => {
      await funViewKPIYear(yearProgress);
    }
    btnReloadDgUtama.addEventListener('click', funBtnReloadDgUtama);
  })
  .catch(error => {
    const errorMsg = 'Terjadi kesalahan, Coba beberapa saat lagi!';
    titleYearKPI.innerText = errorMsg;
    alertComponent.sendAnAlertOnCatch(errorMsg);
    console.error('An error occurred:', error);
  })
}

document.addEventListener("DOMContentLoaded", async () => {

  btnMenuPrintDgUtama.disabled = true;
  btnExcelDetailDgUtama.disabled = true;
  btnPDFDetailDgUtama.disabled = true;
  btnReloadDgUtama.disabled = true;

  dgUtamaYearInput.value = null;

  dgUtamaYearBtn.addEventListener('click', async (e) => {
    yearProgress = dgUtamaYearInput.value;
    await funViewKPIYear(yearProgress);
  });
  dgUtamaYearInput.addEventListener('keyup', async (e) => {
    if (e.type === 'keyup' && e.key === 'Enter') {
      yearProgress = dgUtamaYearInput.value;
      await funViewKPIYear(yearProgress);
    }
  });

});