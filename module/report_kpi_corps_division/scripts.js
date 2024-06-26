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
const titleYearKPI = document.getElementById('titleYearKPI');
const beforeBulanHeader = document.getElementById('beforeBulanHeader');
const monthTarget = document.getElementById('monthTarget');
// menuPrintDgUtama
// dgUtama
const dgUtamaTbody = document.querySelector('table#dgUtama > tbody');
const modalEditor = bootstrap.Modal.getOrCreateInstance('#modalEditor');
const thUpper = document.getElementById('thUpper');
const thBottom = document.getElementById('thBottom');
const idTargetTbody = document.getElementById('idTargetTbody');

const monthAcro = [
  {
    acronim: 'jan',
    number: '1',
    full: 'Januari'
  },
  {
    acronim: 'feb',
    number: '2',
    full: 'Februari'
  },
  {
    acronim: 'mar',
    number: '3',
    full: 'Maret'
  },
  {
    acronim: 'apr',
    number: '4',
    full: 'April'
  },
  {
    acronim: 'mei',
    number: '5',
    full: 'Mei'
  },
  {
    acronim: 'jun',
    number: '6',
    full: 'Juni'
  },
  {
    acronim: 'jul',
    number: '7',
    full: 'Juli'
  },
  {
    acronim: 'agu',
    number: '8',
    full: 'Agustus'
  },
  {
    acronim: 'sep',
    number: '9',
    full: 'September'
  },
  {
    acronim: 'okt',
    number: '10',
    full: 'Oktober'
  },
  {
    acronim: 'nov',
    number: '11',
    full: 'November'
  },
  {
    acronim: 'des',
    number: '12',
    full: 'Desember'
  }
];

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
let openModalEditor = async () => {}
let yearProgress;
let indexRowEditor = 0;

const getKpiNow = async (year, copyTemplate = false) => {
  return new Promise(async (resolve) => {
    const sendData = new FormData();
    sendData.append('year_kpi', year);
    const getResult = await sendViaFetchForm('route.php?act=getKpiDivisi', sendData);
    resolve(getResult);
  });
}

const getHeadDivisi = async () => {
  return new Promise(async (resolve) => {
    const getResult = await sendViaFetchForm('route.php?act=getHeadDivisi');
    resolve(getResult);
  });
}

const getDataDivisi = async (year,dept) => {
  return new Promise(async (resolve) => {
    const sendData = new FormData();
    sendData.append('year_kpi', year);
    sendData.append('department_kpi', dept);
    const getResult = await sendViaFetchForm('route.php?act=getDataDivisi', sendData);
    resolve(getResult);
  });
}

const funViewKPIYear = async (year) => {
  titleYearKPI.innerText = `LOADING....`;
  
  try {
    const jsonData = await getKpiNow(year);
    titleYearKPI.innerText = `KPI (${year})`;
    btnExcelDetailDgUtama.removeEventListener('click', funBtnExcelDetailDgUtama);
    btnPDFDetailDgUtama.removeEventListener('click', funBtnPDFDetailDgUtama);
    btnReloadDgUtama.removeEventListener('click', funBtnReloadDgUtama);
    btnMenuPrintDgUtama.disabled = false;
    btnExcelDetailDgUtama.disabled = false;
    btnPDFDetailDgUtama.disabled = false;
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
            <span style="padding-left: ${(objectData.index_kpidivcorp.split('.').length - 2) * 8}px;">${objectData.index_kpidivcorp}</span>
          </td>
          <td class="align-middle">${objectData.name_kpidivcorp}</td>
          <td class="align-middle">${objectData.control_cek_kpidivcorp}</td>
          <td class="align-middle">${objectData.polaritas_kpidivcorp}</td>
          <td class="align-middle text-center">${objectData.name_satuan}</td>
          <td class="align-middle text-end">${objectData.target_kpicorp?? '-'}</td>
        `;
        elemTr.innerHTML = elemTd;
        const jsonHeadDivisi = await getHeadDivisi();
        // Iterate over jsonHeadDivisi and render additional columns
        for (let i = 1; i <= jsonHeadDivisi.length; i++) {
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
  } catch (error) {
    const errorMsg = 'Terjadi kesalahan, Coba beberapa saat lagi!';
    titleYearKPI.innerText = errorMsg;
    alertComponent.sendAnAlertOnCatch(errorMsg);
    console.error('An error occurred:', error);
  }
}

const funHeadDivisi = async () => {
  
  await getHeadDivisi()
  .then(jsonHeadDivisi => {
    const totalColumns = 10 + (jsonHeadDivisi.length * 3); 
    titleYearKPI.colSpan = totalColumns;
    
    for (const objectHeadDivisi of jsonHeadDivisi) {
      const elemUpper = document.createElement('th');
      elemUpper.className = 'align-middle text-center';
      elemUpper.colSpan = 3;
      elemUpper.innerHTML = `${objectHeadDivisi.name_department} <span class="fa fa-plus-circle" onclick="detail('${objectHeadDivisi.id_department}','${objectHeadDivisi.name_department}')"></span>`;
      
      const elemBottom1 = document.createElement('th');
      elemBottom1.className = 'align-middle text-center';
      elemBottom1.textContent = 'Casecade';
      
      const elemBottom2 = document.createElement('th');
      elemBottom2.className = 'align-middle text-center';
      elemBottom2.textContent = `Target`;
      
      const elemBottom3 = document.createElement('th');
      elemBottom3.className = 'align-middle text-center';
      elemBottom3.textContent = 'Realisasi';
      
      // Tambahkan elemen-elemen baru ke dalam elemen <tr> yang sesuai
      thUpper.appendChild(elemUpper);
      thBottom.appendChild(elemBottom1);
      thBottom.appendChild(elemBottom2);
      thBottom.appendChild(elemBottom3);
    }
  })
  .catch(error => {
    const errorMsg = 'Terjadi kesalahan, Coba beberapa saat lagi!';
    titleYearKPI.innerText = errorMsg;
    alertComponent.sendAnAlertOnCatch(errorMsg);
    console.error('An error occurred:', error);
  })
}

const detail = async (id_dept,dept) => {
  await resetModalEditor();
  openModalEditor = async () => {
    await defaultOpenModalEditor(dept);
    await insertTdEditor(yearProgress,id_dept);
  }
  modalEditor._element.addEventListener('shown.bs.modal', openModalEditor);
  modalEditor.show();
}
window.detail = detail

const resetModalEditor = async () => {
  const tbody = document.querySelector('table#dgEditor tbody');
  // modalEditorTitle.innerText = null;
  modalEditorTahunKPI.innerText = null;
  indexRowEditor = 0;
  tbody.innerHTML = null;
  modalEditor._element.removeEventListener('shown.bs.modal', openModalEditor);
  alertComponent.alertElem.removeEventListener('hidden.bs.modal', closeModalEditor);
}

const defaultOpenModalEditor = (dept) => {
  modalEditorTahunKPI.innerText = yearProgress;
  modalEditorDepartmentKPI.innerText = dept;
}

const closeModalEditor = async () => {
  modalEditor.hide();
  await funViewKPIYear();
}

const insertingTdEditor = async (data) => {
  const elemTr = document.createElement('tr');
  elemTr.dataset.indexRow = indexRowEditor;
  elemTr.dataset.rowJson = JSON.stringify(data);
  const trElem = document.createElement('tr');
  trElem.classList.add('border-0')
  let symbolCascade = '';
  if (data.cascade_kpidept === 'triangle') {
    symbolCascade = `<span class="triangle-hollow"></span>`;
  } else if (data.cascade_kpidept === 'full-round') {
    symbolCascade = `<span class="circle"></span>`;
  } else if (data.cascade_kpidept === 'half-round') {
    symbolCascade = `<span class="circle-hollow"></span>`;
  }
  trElem.innerHTML = `
    <td class="border">${data.id_realization}</td>
    <td class="border">${data.deptkpi_name}</td>
    <td class="border">${data.name_perspective}</td>
    <td class="border">${data.name_sobject}</td>
    <td class="border">${data.index_kpi_realization}</td>
    <td class="border">${data.name_kpi_realization}</td>
    <td class="border">${data.control_cek_kpi_realization}</td>
    <td class="border text-center">${data.name_formula}</td>
    <td class="border text-center">${data.polaritas_kpi_realization.toUpperCase()}</td>
    <td class="border text-center">${data.name_satuan}</td>
    <td class="border text-center">${symbolCascade}</td>
    <td class="border text-end">${data.target_kpidivcorp ?? '-'}</td>
  `;
  monthAcro.forEach(element => {
    const filteringArr = data.target_kpi.filter((objValue) => objValue.month === element.number)[0];
    console.log(data);
    if (filteringArr) {
      trElem.innerHTML += `
        <input type="hidden" name="valid_month[${data.id_realization}]" value="true">
        <td class="border" data-month="${element.number}" class="text-end">${filteringArr.target}</td>
        <td class="border" data-month="${element.number}"> - </td>
        <td class="border" data-month="${element.number}"></td>
        <td class="border" data-month="${element.number}">
          <div>
            <a target="_blank" href="" class="btn btn-download mr-3" style="width: 200px; color: #002255; background-color: #ffffff; border-color: #002255;" ><span class="d-none d-sm-inline"> View<span></a>
          </div>
        </td>
        <td class="border" data-month="${element.number}">${data.user_entry}</td>
        <td class="border" data-month="${element.number}">${data.last_update}</td>
      `;
    } else {
      trElem.innerHTML += `
        <td class="border" data-month="${element.number}"></td>
        <td class="border" data-month="${element.number}"></td>
        <td class="border" data-month="${element.number}"></td>
        <td class="border" data-month="${element.number}"></td>
        <td class="border" data-month="${element.number}"></td>
        <td class="border" data-month="${element.number}"></td>
      `;
    }
  });
  trElem.innerHTML += `<td class="border"><button type="button" class="btn btn-sm btn-download"><span class="ps-1">Download All Effidiance</span></button></td>`
  // monthTarget.innerHTML = headerItemBulanElem;
  idTargetTbody.append(trElem);
}

const insertTdEditor = async (year,dept) => {
  await getDataDivisi(year,dept)
  .then(jsonData => {
    if(jsonData.response=='error'){
      alertComponent.sendAnAlertOnCatch(jsonData.alert);
    }else{
      for (const objectData of jsonData) {
        insertingTdEditor(objectData)
      }
    }
    
  })
  .catch(error => {
    const errorMsg = error ?? 'Terjadi kesalahan, Coba beberapa saat lagi!';
    alertComponent.sendAnAlertOnCatch(errorMsg);
    console.error('An error occurred:', error);
  })
}

const headerBulanElem = [];
monthAcro.forEach(element => {
  const elemImplan = document.createElement('th');
  elemImplan.classList.add('align-middle', 'text-center', 'tdMonth');
  elemImplan.setAttribute('colspan', '6');
  elemImplan.dataset.month = element.number;
  elemImplan.innerHTML = element.full;
  headerBulanElem.push(elemImplan);
});
beforeBulanHeader.after(...headerBulanElem);

let headerItemBulanElem = '';
let testUp = 0;
monthAcro.forEach(element => {
  headerItemBulanElem += `
    <th class="align-middle text-center tdMonth" data-month="${element.number}">Target</th>
    <th class="align-middle text-center tdMonth" data-month="${element.number}">Realisasi</th>
    <th class="align-middle text-center tdMonth" data-month="${element.number}">Indikator<br>Pencapaian</th>
    <th class="align-middle text-center tdMonth" data-month="${element.number}">Effidiance</th>
    <th class="align-middle text-center tdMonth" data-month="${element.number}">Diinput<br>Oleh</th>
    <th class="align-middle text-center tdMonth" data-month="${element.number}">Waktu<br>Penginputan</th>
  `;
  testUp++;
});
monthTarget.innerHTML = headerItemBulanElem;

document.addEventListener("DOMContentLoaded", async () => {

  btnMenuPrintDgUtama.disabled = true;
  btnExcelDetailDgUtama.disabled = true;
  btnPDFDetailDgUtama.disabled = true;
  btnReloadDgUtama.disabled = true;
  funHeadDivisi();

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