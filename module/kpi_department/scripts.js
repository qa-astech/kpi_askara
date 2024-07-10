import {
  IsEmpty, resetInputExceptChoice, sendViaFetchForm, AlertElemBS5, ConfirmElemBS5, number_format,
  parseVersion, compareVersions, childVersion, number_format_big, monthAcro,
  checkBooleanFromServer
} from '../../../third-party/utility-yudhi/utils.js';
$.fn.dataTable.ext.errMode = 'none';

const elemenMonth = document.querySelectorAll('tr.month-target');

const alertComponent = new AlertElemBS5('alertComponent1');
const confirmComponent = new ConfirmElemBS5('confirmComponent1');
const dgUtamaTbody = document.querySelector('table#dgUtama > tbody');
const dgUtamaYearInput = document.getElementById('dgUtamaYearInput');
const dgUtamaYearBtn = document.getElementById('dgUtamaYearBtn');
const dgUtamaUserEntry = document.getElementById('dgUtamaUserEntry');
const dgUtamaLastUpdate = document.getElementById('dgUtamaLastUpdate');
const btnEditDgUtama = document.getElementById('btnEditDgUtama');
const btnPublishDgUtama = document.getElementById('btnPublishDgUtama');
// const btnMenuPrintDgUtama = document.getElementById('btnMenuPrintDgUtama');
// const btnExcelDetailDgUtama = document.getElementById('btnExcelDetailDgUtama');
// const btnPDFDetailDgUtama = document.getElementById('btnPDFDetailDgUtama');
const btnReloadDgUtama = document.getElementById('btnReloadDgUtama');
const dgUtamaYear = document.getElementById('dgUtamaYear');
const dgUtamaYear1 = document.getElementById('dgUtamaYear1');
const modalEditor = bootstrap.Modal.getOrCreateInstance('#modalEditor');
const modalEditorTahunKPI = document.getElementById('modalEditorTahunKPI');
const modalEditorCompanyKPI = document.getElementById('modalEditorCompanyKPI');
const modalEditorDepartmentKPI = document.getElementById('modalEditorDepartmentKPI');
const btnAddNewDgEditor = document.getElementById('btnAddNewDgEditor');
const btnAddChildDgEditor = document.getElementById('btnAddChildDgEditor');
const btnDeleteDgEditor = document.getElementById('btnDeleteDgEditor');
const formEditor = document.getElementById('formEditor');
const modalEditorYear1 = document.getElementById('modalEditorYear1');
const modalEditorBtnSave = document.getElementById('modalEditorBtnSave');
const modalAddFresh = bootstrap.Modal.getOrCreateInstance('#modalAddFresh');
const modalAddFreshBackdrop = document.getElementById('modalAddFreshBackdrop');
const modalAddFreshBtnSave = document.getElementById('modalAddFreshBtnSave');

$('#strategi_object').select2({
  theme: "bootstrap-5",
  width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
  placeholder: $( this ).data( 'placeholder' ),
  dropdownParent: $( '#modalAddFresh' ),
  allowClear: true,
  ajax: {
    url: "../strategic_objective/route.php?act=jsonStrategicObjective",
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

$('#dgUtamaCompanyInput').select2({
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

$(`#dgUtamaDepartmentInput`).select2({
  theme: "bootstrap-5",
  width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
  placeholder: $( this ).data( 'placeholder' ),
  allowClear: true,
  ajax: {
    url: "../detail_company/route.php?act=searchDepartmentFromCompany",
    dataType: 'json',
    method: 'POST',
    delay: 250,
    data: function (params) {
      return {
        q: params.term,
        page: params.page || 1,
        id_company: $(`#dgUtamaCompanyInput`).val()
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

let yearProgress;
let companyProgress;
let departmentProgress;
let nameCompanyProgress;
let nameDepartmentProgress;
let saveStateformEditor = false;
let deleteDataKPI = [];
let indexRowEditor = 0;
let stateYearKpi = null;

let getResponsePolaritas;
const getJsonPolaritas = async () => {
  return new Promise(function (resolve, reject) {
      let xhr = new XMLHttpRequest();
      xhr.open('GET', '../../json/polaritas.json', true);
      xhr.responseType = 'json';
      xhr.onload = function () {
          if (this.status >= 200 && this.status < 300) {
              resolve(xhr.response);
          } else {
              reject({
                  status: this.status,
                  statusText: xhr.statusText
              });
          }
      };
      xhr.onerror = function () {
          reject({
              status: this.status,
              statusText: xhr.statusText
          });
      };
      xhr.send();
  });
}

let funBtnEditDgUtama = async () => {}
let funBtnPublishDgUtama = async () => {}
// let funBtnExcelDetailDgUtama = async () => {}
// let funBtnPDFDetailDgUtama = async () => {}
const funBtnReloadDgUtama = async () => {
  await funViewKPIYear();
}
let funModalEditorBtnSave = async () => {}
let openModalEditor = async () => {}
let stateCutOffInput = [];
let stateTargetInput = [];

const closeAlertComp = async () => {
  confirmComponent.confirmModal.hide();
}

const closeConfirmComp = async () => {
  await closeAlertComp();
  await funBtnReloadDgUtama();
}

const diseventAlert = () => {
  alertComponent.alertElem.removeEventListener('hidden.bs.modal', closeModalEditor);
  alertComponent.alertElem.removeEventListener('hidden.bs.modal', closeConfirmComp);
}

const getKpiNow = async () => {
  return new Promise(async (resolve, reject) => {
    const sendData = new FormData();
    sendData.append('year_kpi', yearProgress);
    sendData.append('company_kpi', companyProgress);
    sendData.append('department_kpi', departmentProgress);
    const getResult = await sendViaFetchForm('route.php?act=getKpiDepartment', sendData);
    if (!IsEmpty(getResult)) {
      resolve(getResult);
    } else {
      reject('Data tidak ditemukan! Konfirmasi dengan QMS!');
    }
  });
}

const insertingTdEditor = async (data, funInsert, addDataEditor, indexParent = null, isSupport = false) => {
  const elemTr = document.createElement('tr');
  elemTr.dataset.indexRow = indexRowEditor;
  elemTr.dataset.indexParent = !IsEmpty(indexParent) ? indexParent : data.index_sobject;
  elemTr.dataset.rowJson = JSON.stringify(data);
  let cascadeElem;
  if (isSupport) {
    cascadeElem = `
    <div class="mt-1">
      <input type="radio" class="btn-check cascade_kpidept" id="cascade_tri_${indexRowEditor}" name="cascade_kpidept[${indexRowEditor}]" autocomplete="off" value="triangle">
      <label class="btn btn-outline-danger text-center fw-bold" for="cascade_tri_${indexRowEditor}"><span class="triangle-hollow"></span></label>
    </div>
    `;
  } else {
    cascadeElem = `
    <div class="d-flex align-items-center justify-content-center mt-1" style="gap: .5rem;">
      <div>
        <input type="radio" class="btn-check cascade_kpidept" id="cascade_full_${indexRowEditor}" name="cascade_kpidept[${indexRowEditor}]" autocomplete="off" value="full-round" data-index="${indexRowEditor}">
        <label class="btn btn-outline-danger text-center fw-bold" for="cascade_full_${indexRowEditor}"><span class="circle"></span></label>
      </div>
      <div>
        <input type="radio" class="btn-check cascade_kpidept" id="cascade_half_${indexRowEditor}" name="cascade_kpidept[${indexRowEditor}]" autocomplete="off" value="half-round">
        <label class="btn btn-outline-danger text-center fw-bold" for="cascade_half_${indexRowEditor}"><span class="circle-hollow"></span></label>
      </div>
    </div>
    `;
  }
  let monthElem = '';
  monthAcro.forEach(objectMonth => {
    monthElem += `
      <td>
        <input
          type="text"
          class="form-control target_month"
          name="target_month[${indexRowEditor}][${objectMonth.number}]"
          id="target_month_${indexRowEditor}_${objectMonth.number}"
          data-month="${objectMonth.acronim}"
          style="width: 240px;"
        >
      </td>
    `;
  });
  const elemTd = `
    <input type="hidden" name="status_kpi[${indexRowEditor}]" id="status_kpi_${indexRowEditor}">
    <input type="hidden" name="id_sobject[${indexRowEditor}]" id="id_sobject_${indexRowEditor}">
    <input type="hidden" name="id_kpidept[${indexRowEditor}]" id="id_kpidept_${indexRowEditor}">
    <input type="hidden" name="id_kpibunit[${indexRowEditor}]" id="id_kpibunit_${indexRowEditor}">
    <input type="hidden" name="target_kpibunit[${indexRowEditor}]" id="target_kpibunit_${indexRowEditor}">
    <input type="hidden" name="target_kpicorp[${indexRowEditor}]" id="target_kpicorp_${indexRowEditor}">
    <input type="hidden" name="month_kpidept[${indexRowEditor}]" id="month_kpidept_${indexRowEditor}">
    <td class="text-center" style="white-space: nowrap;">${data.name_perspective}</td>
    <td style="white-space: nowrap;">${data.text_sobject}</td>
    <td><input type="text" class="form-control index_kpidept" name="index_kpidept[${indexRowEditor}]" id="index_kpidept_${indexRowEditor}" readonly></td>
    <td><textarea class="form-control name_kpidept" name="name_kpidept[${indexRowEditor}]" id="name_kpidept_${indexRowEditor}" style="width: 300px; height: 2.375rem;"></textarea></td>
    <td><textarea class="form-control define_kpidept" name="define_kpidept[${indexRowEditor}]" id="define_kpidept_${indexRowEditor}" style="width: 300px; height: 2.375rem;"></textarea></td>
    <td><input type="text" class="form-control control_cek_kpidept" name="control_cek_kpidept[${indexRowEditor}]" id="control_cek_kpidept_${indexRowEditor}" style="width: 220px; height: 2.375rem;"></td>
    <td class="text-end">${!IsEmpty(data.baseline_1, true) ? number_format_big(data.baseline_1, 2, '.', ',') : '-'}</td>
    <td class="text-center">${cascadeElem}</td>
    <td><select class="form-select satuan_kpidept" name="satuan_kpidept[${indexRowEditor}]" id="satuan_kpidept_${indexRowEditor}" data-placeholder="Masukan satuan..." style="width: 150px;"></select></td>
    <td><select class="form-select formula_kpidept" name="formula_kpidept[${indexRowEditor}]" id="formula_kpidept_${indexRowEditor}" data-placeholder="Masukan formula..." style="width: 150px;"></select></td>
    <td><select class="form-select polaritas_kpidept" name="polaritas_kpidept[${indexRowEditor}]" id="polaritas_kpidept_${indexRowEditor}" data-placeholder="Masukan polaritas..." style="width: 120px;"></select></td>
    <td><input type="number" class="form-control date_cutoff" name="date_cutoff[${indexRowEditor}]" id="date_cutoff_${indexRowEditor}"></td>
    <td class="text-end">${isSupport ? '-' : !IsEmpty(data.target_kpicorp, true) ? number_format_big(data.target_kpicorp, 2, '.', ',') : '-'}</td>
    <td class="text-end">${isSupport ? '-' : !IsEmpty(data.target_kpibunit, true) ? number_format_big(data.target_kpibunit, 2, '.', ',') : '-'}</td>
    ${monthElem}
    <td><select class="form-select data_avail_kpidept" name="data_avail_kpidept[${indexRowEditor}]" id="data_avail_kpidept_${indexRowEditor}" data-placeholder="Masukan departemen..." style="width: 250px;"></select></td>
    <td><select class="form-select userpic_kpidept" name="userpic_kpidept[${indexRowEditor}]" id="userpic_kpidept_${indexRowEditor}" data-placeholder="Masukan user..." style="width: 220px;"></select></td>
  `;
  elemTr.innerHTML = elemTd;
  elemTr.addEventListener('click', (e) => {
    if (e.target.localName !== 'input' && e.target.localName !== 'textarea' && e.target.localName !== 'span' && e.target.localName !== 'label') {
      elemTr.classList.contains('selected') ? elemTr.classList.remove('selected') : elemTr.classList.add('selected');
      removeSelectEditor(elemTr);
    }
  });
  await funInsert(elemTr);
  const indexRowNow = indexRowEditor;
  indexRowEditor++;
  await addFunSelectEditor(indexRowNow);
  await addDataEditor(elemTr, indexRowNow);
  await addIndexingKPI(elemTr);
}

const insertTdEditor = async (jsonData) => {
  for (const objectData of jsonData) {
    await insertingTdEditor(objectData,
      async (element, index) => {
        const tbodyElem = document.querySelector('table#dgEditor > tbody');
        tbodyElem.append(element);
      }, async (element, index) => {
        const status_kpi = document.getElementById(`status_kpi_${index}`);
        const id_sobject = document.getElementById(`id_sobject_${index}`);
        const id_kpidept = document.getElementById(`id_kpidept_${index}`);
        const id_kpibunit = document.getElementById(`id_kpibunit_${index}`);
        const target_kpibunit = document.getElementById(`target_kpibunit_${index}`);
        const target_kpicorp = document.getElementById(`target_kpicorp_${index}`);
        const month_kpidept = document.getElementById(`month_kpidept_${index}`);
        const index_kpidept = document.getElementById(`index_kpidept_${index}`);
        const name_kpidept = document.getElementById(`name_kpidept_${index}`);
        const define_kpidept = document.getElementById(`define_kpidept_${index}`);
        const control_cek_kpidept = document.getElementById(`control_cek_kpidept_${index}`);
        const date_cutoff = document.getElementById(`date_cutoff_${index}`);
        const cascade_elem = element.querySelectorAll('input.cascade_kpidept');
        const target_month_elem = element.querySelectorAll('input.target_month');
        const cascade_tri = document.getElementById(`cascade_tri_${index}`);
        const cascade_full = document.getElementById(`cascade_full_${index}`);
        const cascade_half = document.getElementById(`cascade_half_${index}`);
        
        status_kpi.value = objectData.status_kpi ?? null;
        id_sobject.value = objectData.id_sobject ?? null;
        id_kpidept.value = objectData.id_kpidept ?? null;
        id_kpibunit.value = objectData.id_kpibunit ?? null;
        target_kpibunit.value = !IsEmpty(objectData.target_kpibunit, true) ? number_format_big(objectData.target_kpibunit, 2, '.', ',') : null;
        target_kpicorp.value = !IsEmpty(objectData.target_kpicorp, true) ? number_format_big(objectData.target_kpicorp, 2, '.', ',') : null;
        month_kpidept.value = objectData.month_kpidept ?? null;
        index_kpidept.value = objectData.index_kpidept ?? null;
        name_kpidept.value = objectData.name_kpidept ?? null;
        define_kpidept.value = objectData.define_kpidept ?? null;
        control_cek_kpidept.value = objectData.control_cek_kpidept ?? null;
        date_cutoff.value = objectData.date_cutoff ?? null;

        if (objectData.cascade_kpidept === 'triangle') {
          cascade_tri.checked = true;
        } else if (objectData.cascade_kpidept === 'full-round') {
          cascade_full.checked = true;
        } else if (objectData.cascade_kpidept === 'half-round') {
          cascade_half.checked = true;
        }

        // Set value satuan
        if (!IsEmpty(objectData.id_satuan)) {
          const optionSatuan = new Option(objectData.name_satuan, objectData.id_satuan, true, true);
          const dataSatuan = {};
          dataSatuan.id = objectData.id_satuan;
          dataSatuan.text = objectData.name_satuan;
          $('#satuan_kpidept_' + index).append(optionSatuan).trigger('change');
          $('#satuan_kpidept_' + index).trigger({
            type: 'select2:select',
            params: {
              data: dataSatuan
            }
          });
        }

        // Set value formula
        if (!IsEmpty(objectData.id_formula)) {
          const optionFormula = new Option(objectData.name_formula, objectData.id_formula, true, true);
          const dataFormula = {};
          dataFormula.id = objectData.id_formula;
          dataFormula.text = objectData.name_formula;
          $('#formula_kpidept_' + index).append(optionFormula).trigger('change');
          $('#formula_kpidept_' + index).trigger({
            type: 'select2:select',
            params: {
              data: dataFormula
            }
          });
        }

        // Set value data availability
        if (!IsEmpty(objectData.data_avail_id_department)) {
          const optionDataAvailability = new Option(objectData.data_avail_name_department, objectData.data_avail_id_department, true, true);
          const dataDataAvailability = {};
          dataDataAvailability.id = objectData.data_avail_id_department;
          dataDataAvailability.text = objectData.data_avail_name_department;
          $('#data_avail_kpidept_' + index).append(optionDataAvailability).trigger('change');
          $('#data_avail_kpidept_' + index).trigger({
            type: 'select2:select',
            params: {
              data: dataDataAvailability
            }
          });
        }

        // Set value user PIC
        if (!IsEmpty(objectData.data_avail_id_usersetup)) {
          const optionUsers = new Option(objectData.data_avail_fullname_users, objectData.data_avail_id_usersetup, true, true);
          const dataUsers = {};
          dataUsers.id = objectData.data_avail_id_usersetup;
          dataUsers.text = objectData.data_avail_fullname_users;
          $('#userpic_kpidept_' + index).append(optionUsers).trigger('change');
          $('#userpic_kpidept_' + index).trigger({
            type: 'select2:select',
            params: {
              data: dataUsers
            }
          });
        }

        // Set value polaritas
        if (!IsEmpty(objectData.polaritas_kpidept)) {
          $('#polaritas_kpidept_' + index).val(objectData.polaritas_kpidept).trigger('change');
        }

        if ( !IsEmpty(objectData.month_kpidept) && !IsEmpty(objectData.month_kpidept.slice(1, -1)) ) {
          const monthSelector = objectData.month_kpidept.slice(1, -1).split(",");
          target_month_elem.forEach(elemMonth => {
            const monthValue = elemMonth.dataset.month;
            if (!monthSelector.includes(monthValue)) {
              elemMonth.disabled = true;
            }
            // else {
            //   const getMonthNumber = monthAcro.filter((monthObj) => monthObj.acronim === monthValue)[0].number;
            //   elemMonth.value = !IsEmpty(objectData.target_month[getMonthNumber], true) ? number_format_big(objectData.target_month[getMonthNumber], 2, '.', ',') : null;
            // }
          });
        }

        target_month_elem.forEach(elemMonth => {
          const monthValue = elemMonth.dataset.month;
          const getMonthNumber = monthAcro.filter((monthObj) => monthObj.acronim === monthValue)[0].number;
          elemMonth.value = !IsEmpty(objectData.target_month[getMonthNumber], true) ? number_format_big(objectData.target_month[getMonthNumber], 2, '.', ',') : null;
        });

        const disableInput = () => {
          $('#satuan_kpidept_' + index)[0].disabled = true;
          $('#formula_kpidept_' + index)[0].disabled = true;
          $('#data_avail_kpidept_' + index)[0].disabled = true;
          $('#userpic_kpidept_' + index)[0].disabled = true;
          $('#polaritas_kpidept_' + index)[0].disabled = true;
        }
        if (objectData.status_kpi === 'kpi_department_corps') {
          disableInput();
        }
        if (checkBooleanFromServer(objectData.terbit_kpidept)) {
          disableInput();
          index_kpidept.disabled = true;
          target_kpibunit.disabled = true;
          date_cutoff.disabled = true;
          cascade_elem.forEach(elemCascade => {
            elemCascade.disabled = true;
          });
          target_month_elem.forEach(elemMonth => {
            elemMonth.disabled = true;
          });
        } else {
          if (parseInt(objectData.target_kpibunit) === 0) {
            cascade_half.disabled = true;
            cascade_full.checked = true;
            cascade_full.disabled = true;
            monthAcro.forEach(objectMonth => {
              const elemTargetMonth = document.getElementById(`target_month_${index}_${objectMonth.number}`);
              elemTargetMonth.value = 0;
              elemTargetMonth.readOnly = true;
            });
          }
        }

      }, objectData.index_parent, objectData.cascade_kpidept === 'triangle'
    )
  }
}

const funViewKPIYear = async () => {
  dgUtamaYear.innerText = `LOADING....`;
  
  await getKpiNow()
  .then(jsonData => {

    dgUtamaYear.innerText = `KPI ${nameCompanyProgress} - ${nameDepartmentProgress} (${yearProgress})`;
    dgUtamaYear1.innerText = yearProgress - 1;
    btnEditDgUtama.removeEventListener('click', funBtnEditDgUtama);
    btnPublishDgUtama.removeEventListener('click', funBtnPublishDgUtama);
    // btnExcelDetailDgUtama.removeEventListener('click', funBtnExcelDetailDgUtama);
    // btnPDFDetailDgUtama.removeEventListener('click', funBtnPDFDetailDgUtama);
    btnReloadDgUtama.removeEventListener('click', funBtnReloadDgUtama);
    btnEditDgUtama.disabled = false;
    btnPublishDgUtama.disabled = false;
    // btnMenuPrintDgUtama.disabled = false;
    // btnExcelDetailDgUtama.disabled = false;
    // btnPDFDetailDgUtama.disabled = false;
    btnReloadDgUtama.disabled = false;
    dgUtamaTbody.innerHTML = null;
    
    let lateDate;
    let lateUser;
    for (const objectData of jsonData) {
      if (!IsEmpty(objectData.last_update)) {
        const lastUpdate = new Date(objectData.last_update);
        if (lateDate === undefined || lastUpdate >= lateDate) {
          lateDate = lastUpdate;
          lateUser = objectData.fullname_entry;
        }
      }

      const elemTr = document.createElement('tr');
      let symbolCascade = '';
      if (objectData.cascade_kpidept === 'triangle') {
        symbolCascade = `<span class="triangle-hollow"></span>`;
      } else if (objectData.cascade_kpidept === 'full-round') {
        symbolCascade = `<span class="circle"></span>`;
      } else if (objectData.cascade_kpidept === 'half-round') {
        symbolCascade = `<span class="circle-hollow"></span>`;
      }

      let monthElem = '';
      monthAcro.forEach(objectMonth => {
        monthElem += `<td class="text-end">${ !IsEmpty(objectData['target_month'][objectMonth.number], true) ? number_format_big(objectData['target_month'][objectMonth.number], 2, '.', ',') : '-' }</td>
        `;
      });
      const elemTd = `
        <td class="${IsEmpty(objectData.id_kpidept) ? 'text-danger' : ''} text-center">${objectData.id_kpidept ? objectData.id_kpidept : '(Silahkan atur terlebih dahulu)'}</td>
        <td class="text-center">${objectData.name_perspective}</td>
        <td>${objectData.text_sobject}</td>
        <td>
          <span style="padding-left: ${(objectData.index_kpidept.split('.').length - 2) * 12}px;">(${objectData.index_kpidept}) ${objectData.name_kpidept}</span>
        </td>
        <td>${objectData.define_kpidept}</td>
        <td>${objectData.control_cek_kpidept}</td>
        <td class="text-end">${!IsEmpty(objectData.baseline_1, true) ? number_format_big(objectData.baseline_1, 2, '.', ',') : '-'}</td>
        <td class="text-end">${objectData.status_kpi === 'kpi_department_support' ? '-' : !IsEmpty(objectData.target_kpicorp, true) ? number_format_big(objectData.target_kpicorp, 2, '.', ',') : '-'}</td>
        <td class="text-end">${objectData.status_kpi === 'kpi_department_support' ? '-' : !IsEmpty(objectData.target_kpibunit, true) ? number_format_big(objectData.target_kpibunit, 2, '.', ',') : '-'}</td>
        <td class="text-center">${objectData.name_satuan}</td>
        <td class="text-center">${objectData.name_formula}</td>
        <td class="text-center">${objectData.polaritas_kpidept.toUpperCase()}</td>
        <td class="text-center">${symbolCascade}</td>
        <td class="text-center">${objectData.date_cutoff ?? ''}</td>
        ${monthElem}
        <td class="text-center">${objectData.data_avail_name_department ?? ''}</td>
        <td class="text-center">${objectData.data_avail_fullname_users ?? ''}</td>
        <td class="text-center ${checkBooleanFromServer(objectData.terbit_kpidept) ? 'text-success' : 'text-danger'} fw-bold">${checkBooleanFromServer(objectData.terbit_kpidept) ? 'Sudah Terbit' : 'Belum Terbit'}</td>
      `;

      elemTr.innerHTML = elemTd;
      dgUtamaTbody.append(elemTr);
    }

    if (lateDate) {
      const monthLast = monthAcro.filter((arrData) => parseInt(arrData.number) === (parseInt(lateDate.getMonth()) + 1))[0];
      dgUtamaUserEntry.innerText = lateDate instanceof Date && !isNaN(lateDate.valueOf()) ?
        lateDate.getDate() + ' ' + monthLast.full + ' ' + lateDate.getFullYear() + ' ' + lateDate.getHours() + ':' + lateDate.getMinutes() + ':' + lateDate.getSeconds() : '';
      dgUtamaLastUpdate.innerText = lateUser ?? '';
    }

    funBtnEditDgUtama = async (e) => {
      await resetModalEditor();
      openModalEditor = async () => {
        await defaultOpenModalEditor();
        await insertTdEditor(jsonData);
      }
      funModalEditorBtnSave = async () => {
        modalEditorBtnSave.disabled = true;
        // alertComponent.alertElem.removeEventListener('hidden.bs.modal', closeModalEditor);
        diseventAlert();
        if (saveStateformEditor === false) {
          saveStateformEditor = true;
          try {
            const sendData = new FormData(formEditor);
            sendData.append('year_kpi', yearProgress);
            sendData.append('company_kpi', companyProgress);
            sendData.append('department_kpi', departmentProgress);
            sendData.append('deleteDataKPI', JSON.stringify(deleteDataKPI));
            const getResponse = await sendViaFetchForm('route.php?act=editKpiDepartment', sendData);
            alertComponent.sendAnAlertOnTry(getResponse, closeModalEditor);
          } catch (error) {
            alertComponent.sendAnAlertOnCatch(error);
          } finally {
            modalEditorBtnSave.disabled = false;
            saveStateformEditor = false;
          }
        }
      }
      modalEditor._element.addEventListener('shown.bs.modal', openModalEditor);
      modalEditorBtnSave.addEventListener('click', funModalEditorBtnSave);
      modalEditor.show();
    }
    btnEditDgUtama.addEventListener('click', funBtnEditDgUtama);

    // funBtnExcelDetailDgUtama = async (e) => {}
    // btnExcelDetailDgUtama.addEventListener('click', funBtnExcelDetailDgUtama);

    // funBtnPDFDetailDgUtama = async (e) => {}
    // btnPDFDetailDgUtama.addEventListener('click', funBtnPDFDetailDgUtama);

    funBtnPublishDgUtama = async (e) => {
      confirmComponent.setupconfirm('Terbit KPI', 'bg-primary', 'text-white', 'Anda yakin ingin terbitkan KPI ini sekarang?');
      // alertComponent.alertElem.removeEventListener('hidden.bs.modal', closeConfirmComp);
      diseventAlert();
      if (!IsEmpty(yearProgress) && !IsEmpty(companyProgress) && !IsEmpty(departmentProgress)) {
        confirmComponent.btnConfirm.addEventListener('click', async () => {
          try {
            const sendData = new FormData();
            sendData.append('year_kpi', yearProgress);
            sendData.append('company_kpi', companyProgress);
            sendData.append('department_kpi', departmentProgress);
            const getResponse = await sendViaFetchForm('route.php?act=publishKpiDepartment', sendData);
            alertComponent.sendAnAlertOnTry(getResponse, closeConfirmComp);
          } catch (error) {
            alertComponent.sendAnAlertOnCatch(error);
          }
        });
        confirmComponent.btnCancel.addEventListener('click', closeAlertComp);
        confirmComponent.confirmModal.show();
      }
    }
    btnPublishDgUtama.addEventListener('click', funBtnPublishDgUtama);
    btnReloadDgUtama.addEventListener('click', funBtnReloadDgUtama);

  })
  .catch(error => {
    const errorMsg = error ?? 'Terjadi kesalahan, Coba beberapa saat lagi!';
    dgUtamaYear.innerText = errorMsg;
    alertComponent.sendAnAlertOnCatch(errorMsg);
    console.error('An error occurred:', error);
  })
}

const resetModalEditor = async () => {
  const tbody = document.querySelector('table#dgEditor tbody');
  modalEditorTahunKPI.innerText = null;
  modalEditorCompanyKPI.innerText = null;
  modalEditorDepartmentKPI.innerText = null;
  modalEditorYear1.innerText = null;
  indexRowEditor = 0;
  deleteDataKPI = [];
  tbody.innerHTML = null;

  modalEditorBtnSave.removeEventListener('click', funModalEditorBtnSave);
  modalEditor._element.removeEventListener('shown.bs.modal', openModalEditor);
  // alertComponent.alertElem.removeEventListener('hidden.bs.modal', closeModalEditor);
}
const defaultOpenModalEditor = async () => {
  modalEditorTahunKPI.innerText = yearProgress;
  modalEditorCompanyKPI.innerText = nameCompanyProgress;
  modalEditorDepartmentKPI.innerText = nameDepartmentProgress;
  modalEditorYear1.innerText = yearProgress - 1;
}
const closeModalEditor = async () => {
  modalEditor.hide();
  await funViewKPIYear();
}
const addFunSelectEditor = async (index) => {
  $(`#satuan_kpidept_${index}`).select2({
    theme: "bootstrap-5",
    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    placeholder: $( this ).data( 'placeholder' ),
    dropdownParent: $('#modalEditor'),
    allowClear: true,
    ajax: {
      url: "../master_satuan/route.php?act=jsonSatuan",
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
  $(`#formula_kpidept_${index}`).select2({
    theme: "bootstrap-5",
    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    placeholder: $( this ).data( 'placeholder' ),
    dropdownParent: $('#modalEditor'),
    allowClear: true,
    ajax: {
      url: "../master_formula/route.php?act=jsonFormula",
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
  $(`#data_avail_kpidept_${index}`).select2({
    theme: "bootstrap-5",
    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    placeholder: $( this ).data( 'placeholder' ),
    dropdownParent: $('#modalEditor'),
    allowClear: true,
    ajax: {
      url: "../detail_company/route.php?act=searchDepartmentFromCompany",
      dataType: 'json',
      method: 'POST',
      delay: 250,
      data: function (params) {
        return {
          q: params.term,
          page: params.page || 1,
          id_company: companyProgress
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
  $(`#userpic_kpidept_${index}`).select2({
    theme: "bootstrap-5",
    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    placeholder: $( this ).data( 'placeholder' ),
    dropdownParent: $('#modalEditor'),
    allowClear: true,
    ajax: {
      url: "../users_setup/route.php?act=searchDeptComp",
      dataType: 'json',
      method: 'POST',
      delay: 250,
      data: function (params) {
        return {
          q: params.term,
          page: params.page || 1,
          id_company: companyProgress,
          id_department: $(`#data_avail_kpidept_${index}`).val()
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
  $(`#polaritas_kpidept_${index}`).select2({
    theme: "bootstrap-5",
    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    placeholder: $( this ).data( 'placeholder' ),
    dropdownParent: $('#modalEditor'),
    allowClear: true,
    data: getResponsePolaritas,
  });

  monthAcro.forEach(objectMonth => {
    stateTargetInput[objectMonth.number] = [];
  });
  monthAcro.forEach(objectMonth => {
    const target_month = document.getElementById(`target_month_${index}_${objectMonth.number}`);
    stateTargetInput[objectMonth.number][index] = null;
    target_month.addEventListener('focusout', (e) => {
      stateTargetInput[objectMonth.number][index] = stateTargetInput[objectMonth.number][index] ?? null;
      const input = e.target;
      const value = input.value.trim().replace(/,/g, "");
      const numericDotRegex = /^[0-9.]*$/;
      const realValue = number_format_big(value, 2, '.', ',');
      if (IsEmpty(value, true, true)) {
        input.value = null;
      } else if (!numericDotRegex.test(value)) {
        input.value = stateTargetInput[objectMonth.number][index];
      } else {
        stateTargetInput[objectMonth.number][index] = realValue;
        input.value = realValue;
      }
    });
    target_month.addEventListener('focusin', (e) => {
      const input = e.target;
      const value = input.value.trim().replace(/,/g, "");
      input.value = value;
    });
  });

  const date_cutoff = document.getElementById('date_cutoff_' + index);
  stateCutOffInput[index] = null;
  date_cutoff.addEventListener('keyup', (e) => {
    const input = e.target;
    const value = input.value.trim().replace(/,/g, "");
    const numericRegex = /^[0-9]*$/;
    if (IsEmpty(value)) {
      input.value = null;
    } else if (!numericRegex.test(value)) {
      input.value = stateCutOffInput[index];
    } else {
      stateCutOffInput[index] = input.value;
    }

    if (input.value < 1 || input.value > 31) {
      if(!e.target.classList.contains('border-danger')) e.target.classList.add('border-danger');
    } else {
      e.target.classList.remove('border-danger');
    }
  });

}
const addIndexingKPI = async (elemTr) => {
  const indexRow = parseInt(elemTr.dataset.indexRow);
  const jsonData = JSON.parse(elemTr.dataset.rowJson);
  const indexParent = elemTr.dataset.indexParent;
  const trElem = document.querySelectorAll('table#dgEditor > tbody tr');
  let indexKPI = 0;
  if (trElem.length > 0) {
    for (const elemCheck of trElem) {
      const indexParentCheck = elemCheck.dataset.indexParent;
      const jsonDataCheck = JSON.parse(elemCheck.dataset.rowJson);
      if (indexParent === indexParentCheck && jsonData.index_sobject === jsonDataCheck.index_sobject && jsonData.alias_perspective === jsonDataCheck.alias_perspective) {
        indexKPI++;
      }
    }
  }
  const elemKPI = document.getElementById('index_kpidept_' + indexRow);
  elemKPI.value = indexParent + '.' + indexKPI;
}
const removeSelectEditor = (elemTarget) => {
  const dgEditorTr = document.querySelectorAll('table#dgEditor > tbody tr');
  dgEditorTr.forEach(element => {
    if(element !== elemTarget) element.classList.remove('selected');
  });
}

const loadDataUtama = async () => {
  const companyJson = $('#dgUtamaCompanyInput').select2('data');
  const departmentJson = $('#dgUtamaDepartmentInput').select2('data');
  yearProgress = dgUtamaYearInput.value;
  companyProgress = companyJson[0].id;
  departmentProgress = departmentJson[0].id;
  nameCompanyProgress = companyJson[0].text;
  nameDepartmentProgress = departmentJson[0].text;
  await funViewKPIYear();
}

document.addEventListener("DOMContentLoaded", async () => {
  
  // const sendDataPolaritas = new URLSearchParams();
  // getResponsePolaritas = await sendViaFetchForm('../../json/polaritas.json', sendDataPolaritas);
  getResponsePolaritas = await getJsonPolaritas();

  btnEditDgUtama.disabled = true;
  btnPublishDgUtama.disabled = true;
  // btnMenuPrintDgUtama.disabled = true;
  // btnExcelDetailDgUtama.disabled = true;
  // btnPDFDetailDgUtama.disabled = true;
  btnReloadDgUtama.disabled = true;
  dgUtamaYearInput.value = null;

  dgUtamaYearBtn.addEventListener('click', async (e) => {
    await loadDataUtama();
  });
  // dgUtamaYearInput.addEventListener('keyup', async (e) => {
  //   if (e.type === 'keyup' && e.key === 'Enter') {
  //     await loadDataUtama();
  //   }
  // });
  dgUtamaYearInput.addEventListener('keyup', async (e) => {
    if (e.type === 'keyup' && e.key === 'Enter') {
      await loadDataUtama();
    } else {
      stateYearKpi = stateYearKpi ?? null;
      const input = e.target;
      const value = input.value;
      const numericRegex = /^[0-9]*$/;
      if (IsEmpty(value, true)) {
        input.value = null;
      } else if (!numericRegex.test(value)) {
        input.value = stateYearKpi;
      } else {
        stateYearKpi = input.value;
      }
      if (input.value < 0) {
        if(!e.target.classList.contains('border-danger')) e.target.classList.add('border-danger');
      } else {
        e.target.classList.remove('border-danger');
      }
    }
  });


  // $('#dgUtamaCompanyInput').on('select2:select', async (e) => {
  //   await loadDataUtama();
  // });
  // $('#dgUtamaDepartmentInput').on('select2:select', async (e) => {
  //   await loadDataUtama();
  // });

  modalAddFresh._element.addEventListener('show.bs.modal', () => {
    if (!modalAddFreshBackdrop.classList.contains('show')) modalAddFreshBackdrop.classList.add('show');
    modalAddFreshBackdrop.classList.remove('d-none');
    $('#strategi_object').val(null).trigger('change');
  });
  modalAddFresh._element.addEventListener('hide.bs.modal', () => {
    modalAddFreshBackdrop.classList.remove('show');
    if (!modalAddFreshBackdrop.classList.contains('d-none')) modalAddFreshBackdrop.classList.add('d-none');
  });

  btnAddNewDgEditor.addEventListener('click', (e) => {
    modalAddFresh.show();
  });

  btnAddChildDgEditor.addEventListener('click', async (e) => {
    const trRow = document.querySelector('table#dgEditor > tbody > tr.selected');
    if (!IsEmpty(trRow)) {

      const jsonData = JSON.parse(trRow.dataset.rowJson);
      if (jsonData.hasOwnProperty('id_kpidept')) {
        delete jsonData['id_kpidept'];
      }
      if (jsonData.hasOwnProperty('id_kpibunit')) {
        delete jsonData['id_kpibunit'];
      }
      if (jsonData.hasOwnProperty('terbit_kpidept')) {
        delete jsonData['terbit_kpidept'];
      }
      const indexInputForParent = trRow.querySelector("input.index_kpidept").value;
      const beginIndexAddElem = jsonData.alias_perspective + indexInputForParent + '.';
      const elementSame = [];

      await insertingTdEditor(jsonData,
        async (element, index) => {
          const trElem = document.querySelectorAll('table#dgEditor > tbody tr');
          if (trElem.length > 0) {
            for (const elemCheck of trElem) {
              const indexInputCheck = elemCheck.querySelector("input.index_kpidept").value;
              const jsonDataCheck = JSON.parse(elemCheck.dataset.rowJson);
              const trueIndex = jsonDataCheck.alias_perspective + indexInputCheck;
              if (trueIndex.includes(beginIndexAddElem)) {
                elementSame.push(elemCheck);
              }
            }
          }
          if (elementSame.length > 0) {
            elementSame[elementSame.length - 1].after(element);
          } else {
            trRow.after(element);
          }
        }, async (element, index) => {
          const cascade_tri = document.getElementById('cascade_tri_' + index);
          const id_sobject = document.getElementById('id_sobject_' + index);
          cascade_tri.checked = true;
          id_sobject.value = jsonData.id_sobject;
        }, indexInputForParent, true
      );
    }
  });

  btnDeleteDgEditor.addEventListener('click', (e) => {
    const trRowSelector = document.querySelector('table#dgEditor > tbody > tr.selected');
    if (!IsEmpty(trRowSelector)) {
      const jsonData = JSON.parse(trRowSelector.dataset.rowJson);
      if (jsonData.terbit_kpidept !== 't') {

        const indexSelector = trRowSelector.querySelector("input.index_kpidept").value;
        const arr1 = parseVersion(indexSelector);
        const arr1Second = parseVersion(indexSelector);
        arr1Second.pop();
        const findAlter1 = jsonData.alias_perspective + arr1Second.join('.') + '.';
        const dotCount = arr1.length - 1;
        confirmComponent.setupconfirm('Hapus Data', 'bg-danger', 'text-white', 'Apakah anda yakin ingin menghapus data tersebut? aksi ini akan menghapus turunannya sekaligus!');
  
        const funRemoveElem = async () => {
          const elemAll = document.querySelectorAll(`table#dgEditor > tbody tr`);
          elemAll.forEach(elemCheck => {
            const jsonDataCheck = JSON.parse(elemCheck.dataset.rowJson);
            const indexInputCheck = elemCheck.querySelector("input.index_kpidept").value;
            const arr2 = parseVersion(indexInputCheck);
            const findAlter2 = jsonDataCheck.alias_perspective + indexInputCheck;
            if (findAlter2.includes(findAlter1) && childVersion(arr1, arr2)) {
              if (jsonDataCheck.id_kpidept) {
                const arrInput = {};
                arrInput.status_kpi = jsonData.status_kpi;
                arrInput.id_kpidept = jsonData.id_kpidept;
                deleteDataKPI.push(arrInput);
              }
              elemCheck.remove();
            }
          });
          trRowSelector.remove();
        }

        const funRewriteIndex = async () => {
          const elemToBeRerite = document.querySelectorAll(`table#dgEditor > tbody tr`);
          elemToBeRerite.forEach(elemCheck => {
            const jsonDataCheck = JSON.parse(elemCheck.dataset.rowJson);
            const indexInputCheck = elemCheck.querySelector("input.index_kpidept").value;
            const arr2 = parseVersion(indexInputCheck);
            const findAlter2 = jsonDataCheck.alias_perspective + indexInputCheck;
            const comparisonResult = compareVersions(arr1, arr2);
            if (findAlter2.includes(findAlter1) && comparisonResult < 0) {
              const splitArr = indexInputCheck.split('.');
              splitArr[dotCount] = parseInt(splitArr[dotCount]) - 1;
              elemCheck.querySelector("input.index_kpidept").value = splitArr.join('.');
            }
          });
        }
  
        confirmComponent.btnConfirm.addEventListener('click', async () => {
          await funRemoveElem();
          await funRewriteIndex();
          confirmComponent.confirmModal.hide();
        });
        confirmComponent.btnCancel.addEventListener('click', () => {
          confirmComponent.confirmModal.hide();
        });
        confirmComponent.confirmModal.show();
      }
    }
  });

  modalAddFreshBtnSave.addEventListener('click', async (e) => {
    const jsonDataSelect = $('#strategi_object').select2('data');
    let jsonData = {};
    jsonData.id_sobject = jsonDataSelect[0].id;
    jsonData.text_sobject = jsonDataSelect[0].text;
    jsonData.id_perspective = jsonDataSelect[0].id_perspective;
    jsonData.name_perspective = jsonDataSelect[0].name_perspective;
    jsonData.index_perspective = jsonDataSelect[0].index_perspective;
    jsonData.alias_perspective = jsonDataSelect[0].alias_perspective;
    jsonData.index_sobject = jsonDataSelect[0].index_sobject;
    const beginIndexAddElem = jsonDataSelect[0].alias_perspective + jsonDataSelect[0].index_sobject + '.';
    const regexBeginIndexAddElem = new RegExp(beginIndexAddElem.replace(".", "\\."));

    await insertingTdEditor(jsonData,
      async (element, index) => {
        const tbodyElem = document.querySelector('table#dgEditor > tbody');
        const trElem = document.querySelectorAll('table#dgEditor > tbody tr');
        const elementSame = [];
        const elementSmall = [];
        const elementSameSmall = [];
        const elementSameBig = [];
        if (trElem.length > 0) {
          for (const elemCheck of trElem) {
            const indexInputCheck = elemCheck.querySelector("input.index_kpidept").value;
            const jsonDataCheck = JSON.parse(elemCheck.dataset.rowJson);
            const trueIndex = jsonDataCheck.alias_perspective + indexInputCheck;
            if ( regexBeginIndexAddElem.test(trueIndex) ) {
              elementSame.push(elemCheck);
            }
            if (jsonDataCheck.alias_perspective === jsonData.alias_perspective && parseInt(jsonDataCheck.index_sobject) > parseInt(jsonData.index_sobject)) {
              elementSameBig.unshift(elemCheck);
            }
            if (jsonDataCheck.alias_perspective === jsonData.alias_perspective && parseInt(jsonDataCheck.index_sobject) < parseInt(jsonData.index_sobject)) {
              elementSameSmall.push(elemCheck);
            }
            if (jsonDataCheck.index_perspective > jsonData.index_perspective) {
              elementSmall.unshift(elemCheck);
            }
          }
          if (elementSame.length > 0) {
            elementSame[elementSame.length - 1].after(element);
          } else if (elementSameBig.length > 0) {
            elementSameBig[elementSameBig.length - 1].before(element);
          } else if (elementSameSmall.length > 0) {
            elementSameSmall[elementSameSmall.length - 1].after(element);
          } else if (elementSmall.length > 0) {
            elementSmall[elementSmall.length - 1].before(element);
          } else {
            tbodyElem.append(element);
          }
        } else {
          tbodyElem.append(element);
        }
      }, async (element, index) => {
        const cascade_tri = document.getElementById('cascade_tri_' + index);
        const id_sobject = document.getElementById('id_sobject_' + index);
        cascade_tri.checked = true;
        id_sobject.value = jsonData.id_sobject;
      }, null, true
    );
    modalAddFresh.hide();
  });

  elemenMonth.forEach(element => {
    let elemTr = '';
    for (const valueMonth of monthAcro) {
      elemTr += `<th class="align-middle text-center">${valueMonth.full}</th>`;
    }
    element.innerHTML = elemTr;
  });

  modalEditor._element.addEventListener('hidden.bs.modal', (e) => {
    stateTargetInput = [];
    stateCutOffInput = [];
  });

});