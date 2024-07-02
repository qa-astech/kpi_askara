import {
  IsEmpty, resetInputExceptChoice, sendViaFetchForm, AlertElemBS5, ConfirmElemBS5,
  parseVersion, compareVersions, childVersion, number_format_big, monthAcro
} from '../../../third-party/utility-yudhi/utils.js';

$.fn.dataTable.ext.errMode = 'none';

const alertComponent = new AlertElemBS5('alertComponent1');
const confirmComponent = new ConfirmElemBS5('confirmComponent1');
const dgUtamaYearBtn = document.getElementById('dgUtamaYearBtn');
const btnMenuPrintDgUtama = document.getElementById('btnMenuPrintDgUtama');
const btnReloadDgUtama = document.getElementById('btnReloadDgUtama');
const dgUtamaYearInput = document.getElementById('dgUtamaYearInput');
const titleYearKPI = document.getElementById('titleYearKPI');
const dgUtamaTbody = document.querySelector('table#dgUtama > tbody');
const thUpper = document.getElementById('thUpper');
const thBottom = document.getElementById('thBottom');

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

let funBtnReloadDgUtama = () => {};
let yearProgress;
let indexRowEditor = 0;

const getKpiNow = async (year, copyTemplate = false) => {
  return new Promise(async (resolve) => {
    const sendData = new FormData();
    sendData.append('year_kpi', year);
    sendData.append('copy_kpi', copyTemplate ? 'copy' : 'normal');
    sendData.append('year_baseline', yearProgress);
    const getResult = await sendViaFetchForm('route.php?act=getKpiBisnisUnit', sendData);
    resolve(getResult);
  });
}

const getDataBisnis = async () => {
  return new Promise(async (resolve) => {
    const getResult = await sendViaFetchForm('route.php?act=getDataBisnisUnit');
    resolve(getResult);
  });
}

const funViewKPIYear = async (year) => {
  titleYearKPI.innerText = `LOADING....`;
  
  try {
    const jsonData = await getKpiNow(year);
    titleYearKPI.innerText = `KPI (${year})`;
    btnReloadDgUtama.removeEventListener('click', funBtnReloadDgUtama);
    btnMenuPrintDgUtama.disabled = false;
    btnReloadDgUtama.disabled = false;
    dgUtamaTbody.innerHTML = null;
    
    if (jsonData.length > 0) {
      for (const objectData of jsonData) {
        const elemTr = document.createElement('tr');
        const elemTd = `
          <td class="align-middle text-center">${objectData.id_kpicorp}</td>
          <td class="align-middle text-center">${objectData.name_perspective}</td>
          <td class="align-middle">${objectData.text_sobject}</td>
          <td class="align-middle">
            <span style="padding-left: ${(objectData.index_kpibunit.split('.').length - 2) * 8}px;">${objectData.index_kpibunit}</span>
          </td>
          <td class="align-middle">${objectData.name_kpibunit}</td>
          <td class="align-middle">${objectData.control_cek_kpibunit}</td>
          <td class="align-middle">${objectData.polaritas_kpibunit}</td>
          <td class="align-middle text-center">${objectData.name_satuan}</td>
          <td class="align-middle text-end">${objectData.target_kpibunit}</td>
        `;
        elemTr.innerHTML = elemTd;
        const jsonHeadBisnis = await getDataBisnis();
        for (let i = 1; i <= jsonHeadBisnis.length; i++) {
          let symbolCascade = '';
          if (objectData[`ck_${i}`] === 'triangle') {
            symbolCascade = `<span class="triangle-hollow"></span>`;
          } else if (objectData[`ck_${i}`] === 'full-round') {
            symbolCascade = `<span class="circle"></span>`;
          } else if (objectData[`ck_${i}`] === 'half-round') {
            symbolCascade = `<span class="circle-hollow"></span>`;
          }
          const additionalTd = `
            <td class="align-middle text-center">${objectData[`ck_${i}`] !== null ? symbolCascade : '-'}</td>
            <td class="align-middle text-center">${objectData[`tk_${i}`] !== null ? objectData[`tk_${i}`] : '-'}</td>
            <td class="align-middle text-center"> - </td>
          `;
          elemTr.innerHTML += additionalTd;
        }
        dgUtamaTbody.append(elemTr);
      }

    } else {
      btnMenuPrintDgUtama.disabled = true;
    }

    funBtnReloadDgUtama = async (e) => {
      await funViewKPIYear(yearProgress);
    }
    btnReloadDgUtama.addEventListener('click', funBtnReloadDgUtama);
  } catch (error) {
    const errorMsg = 'Terjadi kesalahan, Coba beberapa saat lagi!';
    titleYearKPI.innerText = errorMsg;
    alertComponent.sendAnAlertOnCatch(errorMsg);
    console.error('An error occurred:', error);
  }
}

document.addEventListener("DOMContentLoaded", async () => {

  btnMenuPrintDgUtama.disabled = true;
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