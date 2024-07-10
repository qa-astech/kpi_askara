import {
  IsEmpty, resetInputExceptChoice, sendViaFetchForm, AlertElemBS5, ConfirmElemBS5,
  parseVersion, compareVersions, childVersion,
  monthAcro,
  number_format_big
} from '../../../third-party/utility-yudhi/utils.js';

$.fn.dataTable.ext.errMode = 'none';

const alertComponent = new AlertElemBS5('alertComponent1');
const confirmComponent = new ConfirmElemBS5('confirmComponent1');
const dgUtamaYearInput = document.getElementById('dgUtamaYearInput');
const dgUtamaCompanyInput = document.getElementById('dgUtamaCompanyInput');
const dgUtamaDepartmentInput = document.getElementById('dgUtamaDepartmentInput');
const dgUtamaYearBtn = document.getElementById('dgUtamaYearBtn');
const dgUtamaUserEntry = document.getElementById('dgUtamaUserEntry');
const dgUtamaLastUpdate = document.getElementById('dgUtamaLastUpdate');
const dgUtamaYear1 = document.getElementById('dgUtamaYear1');
const btnLaporan = document.getElementById('btnLaporan');
const yearKPI = document.getElementById('yearKPI');
const h1Page = document.getElementById('h1Page');

// table
const dgLaporan = document.getElementById('dgLaporan');
const dgLaporanTbody = document.querySelector('table#dgLaporan > tbody');
const trUpperLaporan = document.getElementById('trUpperLaporan');
const trBottomLaporan = document.getElementById('trBottomLaporan');

// State Proses
let yearProgress;
let departmentProgress;

const clearingOnSelectYear = (e) => {
  $(dgUtamaDepartmentInput).val(null).trigger('change');
}

const getKpiNow = async () => {
  return new Promise(async (resolve) => {
    if (yearProgress && departmentProgress) {
      const sendData = new FormData();
      sendData.append('year_kpi', yearProgress);
      sendData.append('department_kpi', departmentProgress);
      const getResult = await sendViaFetchForm('route.php?act=getKpiDivisionCorporate', sendData);
      resolve(getResult);
    } else {
      reject('Lengkapi data tahun dan bisnis unit sebelum diproses!');
    }
  });
}

const funViewDataKpi = async () => {
  
  await getKpiNow()
  .then(jsonData => {
    yearKPI.innerText = ` (${yearProgress})`;
    dgUtamaYear1.innerText = ` (${yearProgress - 1})`;
    btnLaporan.disabled = false;
    dgLaporanTbody.innerHTML = null;
    
    if (jsonData.length > 0) {
      let lateDate = '';
      let lateUser = '';
      for (const objectData of jsonData) {
        const lastUpdate = new Date(objectData.last_update);
        if (lateDate === undefined || lastUpdate >= lateDate) {
          lateDate = lastUpdate;
          lateUser = objectData.fullname_entry;
        }
        const elemTrLaporan = document.createElement('tr');
        let elemTdLaporan = `
          <td class="align-middle text-center">${objectData.name_perspective}</td>
          <td class="align-middle">${objectData.text_sobject}</td>
          <td class="align-middle">
            <span style="padding-left: ${(objectData.index_kpidivcorp.split('.').length - 2) * 12}px;">${objectData.index_kpidivcorp} ${objectData.name_kpidivcorp}</span>
          </td>
          <td class="align-middle">${objectData.define_kpidivcorp}</td>
          <td class="align-middle">${objectData.control_cek_kpidivcorp}</td>
          <td class="align-middle text-center">${objectData.name_satuan}</td>
          <td class="align-middle">${objectData.name_formula}</td>
          <td class="align-middle">${objectData.polaritas_kpidivcorp}</td>
          <td class="align-middle text-end">${!IsEmpty(objectData.baseline_1, true) ? number_format_big(objectData.baseline_1, 2, '.', ',') : '-'}</td>
          <td class="align-middle text-end">${!IsEmpty(objectData.target_kpidivcorp, true) ? number_format_big(objectData.target_kpidivcorp, 2, '.', ',') : ''}</td>
        `;
        const monthData = Object.keys(objectData.month).map((key) => objectData.month[key]);
        for (const month of monthAcro) {
          const currentData = monthData.filter((value) => parseInt(value.month_realisasi) === parseInt(month.number));
          const target = currentData && currentData.length > 0 ? currentData[0].target : null;
          const realisasi = currentData && currentData.length > 0 ? currentData[0].realisasi : null;
          elemTdLaporan += `
            <td class="align-middle text-end">${target ? number_format_big(target, 2, '.', ',') : ''}</td>
            <td class="align-middle text-end">${realisasi ? number_format_big(realisasi, 2, '.', ',') : ''}</td>
            <td class="align-middle text-end"></td>
          `;
        }
        elemTdLaporan += `
          <td class="align-middle text-end">${!IsEmpty(objectData.totalRealisasi, true) ? number_format_big(objectData.totalRealisasi, 2, '.', ',') : ''}</td>
          <td class="align-middle text-end"></td>
        `;
        elemTrLaporan.innerHTML = elemTdLaporan;
        dgLaporanTbody.append(elemTrLaporan);
        
      }
      dgUtamaUserEntry.innerText = lateUser;
      dgUtamaLastUpdate.innerText = lateDate.getFullYear() + '-' + lateDate.getMonth() + '-' + lateDate.getDate() + ' ' + lateDate.getHours() + ':' + lateDate.getMinutes() + ':' + lateDate.getSeconds();

    } else {
      btnLaporan.disabled = true;
      throw new Error(`Data kpi gak keambil nih!!`);
    }

  })
  .catch(error => {
    const errorMsg = 'Terjadi kesalahan, Coba beberapa saat lagi!';
    alertComponent.sendAnAlertOnCatch(errorMsg);
    console.error('An error occurred:', error);
  })
}

document.addEventListener("DOMContentLoaded", async () => {

  btnLaporan.disabled = true;
  dgUtamaYearInput.value = null;
  dgUtamaDepartmentInput.value = null;

  $(dgUtamaYearInput).select2({
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
  $(dgUtamaYearInput).on('select2:select', clearingOnSelectYear);

  $(dgUtamaDepartmentInput).select2({
    theme: "bootstrap-5",
    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    placeholder: $( this ).data( 'placeholder' ),
    allowClear: true,
    ajax: {
      url: "route.php?act=jsonDepartment",
      dataType: 'json',
      method: 'POST',
      delay: 250,
      data: function (params) {
        return {
          q: params.term,
          page: params.page || 1,
          year: dgUtamaYearInput.value,
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

  dgUtamaYearBtn.addEventListener('click', async (e) => {
    yearProgress = dgUtamaYearInput.value;
    departmentProgress = dgUtamaDepartmentInput.value;
    const [getDepartmentObj] = $(dgUtamaDepartmentInput).select2('data');
    const nameDepartment = getDepartmentObj.text;
    h1Page.innerHTML = `LAPORAN KPI DIVISI KORPORAT (${yearProgress})<br>${nameDepartment.toUpperCase()}`;
    await funViewDataKpi();
  });
    
  let extraUpperLaporan = trUpperLaporan.innerHTML.trim();
  let extraBottomLaporan = trBottomLaporan.innerHTML.trim();
  for (const month of monthAcro) {
    extraUpperLaporan += `
      <th class="align-middle text-center created-js" colspan='3'>${month.full}</th>
    `;
    extraBottomLaporan += `
      <th class="align-middle text-center created-js">Target</th>
      <th class="align-middle text-center created-js">Realisasi</th>
      <th class="align-middle text-center created-js">Pencapaian</th>
    `;
  }
  trBottomLaporan.innerHTML = extraBottomLaporan.trim();
  trUpperLaporan.innerHTML = extraUpperLaporan.trim();

});