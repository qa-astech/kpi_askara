import {
  IsEmpty, resetInputExceptChoice, sendViaFetchForm, AlertElemBS5, ConfirmElemBS5,
  parseVersion, compareVersions, childVersion, number_format, number_format_big, monthAcro
} from '../../../third-party/utility-yudhi/utils.js';

$.fn.dataTable.ext.errMode = 'none';
const alertComponent = new AlertElemBS5('alertComponent1');
const confirmComponent = new ConfirmElemBS5('confirmComponent1');
const dgUtamaYearBtn = document.getElementById('dgUtamaYearBtn');
const btnAddFreshDgUtama = document.getElementById('btnAddFreshDgUtama');
const btnAddCopyDgUtama = document.getElementById('btnAddCopyDgUtama');
const btnEditDgUtama = document.getElementById('btnEditDgUtama');
const btnPublishDgUtama = document.getElementById('btnPublishDgUtama');
// const btnMenuPrintDgUtama = document.getElementById('btnMenuPrintDgUtama');
// const btnExcelDetailDgUtama = document.getElementById('btnExcelDetailDgUtama');
// const btnPDFDetailDgUtama = document.getElementById('btnPDFDetailDgUtama');
const btnReloadDgUtama = document.getElementById('btnReloadDgUtama');
const dgUtamaYearInput = document.getElementById('dgUtamaYearInput');
const dgUtamaYear3 = document.getElementById('dgUtamaYear3');
const dgUtamaYear2 = document.getElementById('dgUtamaYear2');
const dgUtamaYear1 = document.getElementById('dgUtamaYear1');
const titleYearKPI = document.getElementById('titleYearKPI');
const dgUtamaUserEntry = document.getElementById('dgUtamaUserEntry');
const dgUtamaLastUpdate = document.getElementById('dgUtamaLastUpdate');
const dgUtamaTbody = document.querySelector('table#dgUtama > tbody');
const modalEditor = bootstrap.Modal.getOrCreateInstance('#modalEditor');
const formEditor = document.getElementById('formEditor');
const modalEditorTahunKPI = document.getElementById('modalEditorTahunKPI');
const modalEditorTahunTemplate = document.getElementById('modalEditorTahunTemplate');
const btnAddNewDgEditor = document.getElementById('btnAddNewDgEditor');
const btnAddChildDgEditor = document.getElementById('btnAddChildDgEditor');
const btnDeleteDgEditor = document.getElementById('btnDeleteDgEditor');
const dgEditorYearBaseline3 = document.getElementById('dgEditorYearBaseline3');
const dgEditorYearBaseline2 = document.getElementById('dgEditorYearBaseline2');
const dgEditorYearBaseline1 = document.getElementById('dgEditorYearBaseline1');
const modalEditorBtnSave = document.getElementById('modalEditorBtnSave');
const modalCopy = bootstrap.Modal.getOrCreateInstance('#modalCopy');
const modalCopyYearRadio1 = document.getElementById('modalCopyYearRadio1');
const modalCopyYear1 = document.getElementById('modalCopyYear1');
const modalCopyYearRadio2 = document.getElementById('modalCopyYearRadio2');
const modalCopyYear2 = document.getElementById('modalCopyYear2');
const modalCopyYearRadio3 = document.getElementById('modalCopyYearRadio3');
const modalCopyYear3 = document.getElementById('modalCopyYear3');
const modalCopyBtnSave = document.getElementById('modalCopyBtnSave');
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

let funBtnAddFreshDgUtama = () => {};
let funBtnAddCopyDgUtama = () => {};
let funBtnEditDgUtama = () => {};
let funBtnPublishDgUtama = () => {};
// let funBtnExcelDetailDgUtama = () => {};
// let funBtnPDFDetailDgUtama = () => {};
let funModalEditorBtnSave = async () => {};
const funBtnReloadDgUtama = async () => {
  if (yearProgress) {
    await funViewKPIYear(yearProgress);
  }
}
let openModalEditor = async () => {}

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

let yearProgress;
let saveStateformEditor = false;
let deleteDataKPI = [];
let indexRowEditor = 0;
let stateTargetInput = [];
let stateYearInput;

const closeConfirmComp = async () => {
  confirmComponent.confirmModal.hide();
}
const closeAlertComp = async () => {
  await closeConfirmComp();
  await funBtnReloadDgUtama();
}

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

const insertTdEditor = async (jsonData) => {
  for (const objectData of jsonData) {
    objectData.terbit_kpicorp = objectData.status_copy === 'copy' ? 'f' : objectData.terbit_kpicorp;
    await insertingTdEditor(objectData, async (element, index) => {
      const tbodyElem = document.querySelector('table#dgEditor > tbody');
      tbodyElem.append(element);
    }, async (index) => {

      const id_sobject = document.getElementById('id_sobject_' + index);
      const kpicorp_id = document.getElementById('kpicorp_id_' + index);
      const index_kpi_corp = document.getElementById('index_kpi_corp_' + index);
      const name_kpi_corp = document.getElementById('name_kpi_corp_' + index);
      const define_kpi_corp = document.getElementById('define_kpi_corp_' + index);
      const control_cek_kpi_corp = document.getElementById('control_cek_kpi_corp_' + index);
      const target_kpi_corp = document.getElementById('target_kpi_corp_' + index);
      id_sobject.value = objectData.id_sobject ?? null;
      kpicorp_id.value = objectData.id_kpicorp ?? null ;
      index_kpi_corp.value = objectData.index_kpicorp ?? null;
      name_kpi_corp.value = objectData.name_kpicorp ?? null;
      define_kpi_corp.value = objectData.define_kpicorp ?? null;
      control_cek_kpi_corp.value = objectData.control_cek_kpicorp ?? null;
      target_kpi_corp.value = !IsEmpty(objectData.target_kpicorp, true) ? number_format_big(objectData.target_kpicorp, 2, '.', ',') : null;

      // Set value company
      if (!IsEmpty(objectData.id_satuan)) {
        const optionSatuan = new Option(objectData.name_satuan, objectData.id_satuan, true, true);
        const dataSatuan = {};
        dataSatuan.id = objectData.id_satuan;
        dataSatuan.text = objectData.name_satuan;
        $('#satuan_kpi_corp_' + index).append(optionSatuan).trigger('change');
        $('#satuan_kpi_corp_' + index).trigger({
          type: 'select2:select',
          params: {
            data: dataSatuan
          }
        });
      }

      // Set value company
      if (!IsEmpty(objectData.id_formula)) {
        const optionFormula = new Option(objectData.name_formula, objectData.id_formula, true, true);
        const dataFormula = {};
        dataFormula.id = objectData.id_formula;
        dataFormula.text = objectData.name_formula;
        $('#formula_kpi_corp_' + index).append(optionFormula).trigger('change');
        $('#formula_kpi_corp_' + index).trigger({
          type: 'select2:select',
          params: {
            data: dataFormula
          }
        });
      }

      // Set value polaritas
      if (!IsEmpty(objectData.polaritas_kpicorp)) {
        $('#polaritas_kpi_corp_' + index).val(objectData.polaritas_kpicorp).trigger('change');
      }

      index_kpi_corp.disabled = objectData.terbit_kpicorp === 't';
      name_kpi_corp.disabled = objectData.terbit_kpicorp === 't';
      define_kpi_corp.disabled = objectData.terbit_kpicorp === 't';
      control_cek_kpi_corp.disabled = objectData.terbit_kpicorp === 't';
      target_kpi_corp.disabled = objectData.terbit_kpicorp === 't';
      $('#satuan_kpi_corp_' + index)[0].disabled = objectData.terbit_kpicorp === 't';
      $('#formula_kpi_corp_' + index)[0].disabled = objectData.terbit_kpicorp === 't';
      $('#polaritas_kpi_corp_' + index)[0].disabled = objectData.terbit_kpicorp === 't';

    }, objectData.index_parent)
  }
}

const funViewKPIYear = async (year) => {
  titleYearKPI.innerText = `LOADING....`;
  
  await getKpiNow(year)
  .then(jsonData => {
    titleYearKPI.innerText = `KPI (${year})`;
    dgUtamaYear3.innerText = year - 3;
    dgUtamaYear2.innerText = year - 2;
    dgUtamaYear1.innerText = year - 1;
    btnAddFreshDgUtama.removeEventListener('click', funBtnAddFreshDgUtama);
    btnAddCopyDgUtama.removeEventListener('click', funBtnAddCopyDgUtama);
    btnEditDgUtama.removeEventListener('click', funBtnEditDgUtama);
    btnPublishDgUtama.removeEventListener('click', funBtnPublishDgUtama);
    // // btnExcelDetailDgUtama.removeEventListener('click', funBtnExcelDetailDgUtama);
    // // btnPDFDetailDgUtama.removeEventListener('click', funBtnPDFDetailDgUtama);
    btnReloadDgUtama.removeEventListener('click', funBtnReloadDgUtama);
    btnAddFreshDgUtama.disabled = false;
    btnAddCopyDgUtama.disabled = false;
    btnEditDgUtama.disabled = false;
    btnPublishDgUtama.disabled = false;
    // btnMenuPrintDgUtama.disabled = false;
    // btnExcelDetailDgUtama.disabled = false;
    // btnPDFDetailDgUtama.disabled = false;
    btnReloadDgUtama.disabled = false;
    dgUtamaTbody.innerHTML = null;
    
    if (jsonData.length > 0) {
      let lateDate;
      let lateUser;
      for (const objectData of jsonData) {
        const lastUpdate = new Date(objectData.last_update);
        if (lateDate === undefined || lastUpdate >= lateDate) {
          lateDate = lastUpdate;
          lateUser = objectData.fullname_entry;
        }
        const elemTr = document.createElement('tr');
        const elemTd = `
          <td class="${IsEmpty(objectData.id_kpibunit) ? 'text-danger' : ''} text-center">${objectData.id_kpicorp ? objectData.id_kpicorp : '(Silahkan atur terlebih dahulu)'}</td>
          <td class="text-center">${objectData.name_perspective}</td>
          <td>${objectData.text_sobject}</td>
          <td>
            <span style="padding-left: ${(objectData.index_kpicorp.split('.').length - 2) * 12}px;">(${objectData.index_kpicorp}) ${objectData.name_kpicorp}</span>
          </td>
          <td>${objectData.define_kpicorp}</td>
          <td>${objectData.control_cek_kpicorp}</td>
          <td class="text-center">${objectData.name_satuan}</td>
          <td class="text-end">${!IsEmpty(objectData.baseline_3, true) ? number_format_big(objectData.baseline_3, 2, '.', ',') : '-'}</td>
          <td class="text-end">${!IsEmpty(objectData.baseline_2, true) ? number_format_big(objectData.baseline_2, 2, '.', ',') : '-'}</td>
          <td class="text-end">${!IsEmpty(objectData.baseline_1, true) ? number_format_big(objectData.baseline_1, 2, '.', ',') : '-'}</td>
          <td class="text-end">${!IsEmpty(objectData.target_kpicorp, true) ? number_format_big(objectData.target_kpicorp, 2, '.', ',') : ''}</td>
          <td class="text-center fw-bold ${objectData.terbit_kpicorp === 't' ? 'text-success' : 'text-danger'}">${objectData.terbit_kpicorp === 't' ? 'Sudah Terbit' : ''}</td>
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

      funBtnAddFreshDgUtama = async (e) => {}
      funBtnAddCopyDgUtama = async (e) => {}
      funBtnEditDgUtama = async (e) => {
        await resetModalEditor();
        openModalEditor = async () => {
          defaultOpenModalEditor();
          modalEditorTahunTemplate.parentElement.classList.add('d-none');
          await insertTdEditor(jsonData);
        }
        funModalEditorBtnSave = async () => {
          modalEditorBtnSave.disabled = true;
          alertComponent.alertElem.removeEventListener('shown.bs.modal', closeModalEditor);
          if (saveStateformEditor === false) {
            saveStateformEditor = true;
            try {
              const sendData = new FormData(formEditor);
              sendData.append('year_kpi', yearProgress);
              sendData.append('deleteDataKPI', JSON.stringify(deleteDataKPI));
              const getResponse = await sendViaFetchForm('route.php?act=editKpiCorporate', sendData);
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
      // funBtnExcelDetailDgUtama = async (e) => {}
      // funBtnPDFDetailDgUtama = async (e) => {}
      funBtnPublishDgUtama = async (e) => {
        confirmComponent.setupconfirm('Terbit KPI', 'bg-primary', 'text-white', 'Anda yakin ingin terbitkan KPI ini sekarang?');
        alertComponent.alertElem.removeEventListener('shown.bs.modal', closeAlertComp);
        if (!IsEmpty(yearProgress)) {
          confirmComponent.btnConfirm.addEventListener('click', async () => {
            try {
              const sendData = new FormData();
              sendData.append('year_kpi', yearProgress);
              const getResponse = await sendViaFetchForm('route.php?act=publishKpiCorporate', sendData);
              alertComponent.sendAnAlertOnTry(getResponse, closeAlertComp);
            } catch (error) {
              alertComponent.sendAnAlertOnCatch(error);
            }
          });
          confirmComponent.btnCancel.addEventListener('click', closeConfirmComp);
          confirmComponent.confirmModal.show();
        }
      }

      btnEditDgUtama.addEventListener('click', funBtnEditDgUtama);
      // // btnExcelDetailDgUtama.addEventListener('click', funBtnExcelDetailDgUtama);
      // // btnPDFDetailDgUtama.addEventListener('click', funBtnPDFDetailDgUtama);
      btnPublishDgUtama.addEventListener('click', funBtnPublishDgUtama);

      btnAddFreshDgUtama.disabled = true;
      btnAddCopyDgUtama.disabled = true;
    } else {
      funBtnAddFreshDgUtama = async (e) => {
        await resetModalEditor();
        openModalEditor = async () => {
          defaultOpenModalEditor();
          modalEditorTahunTemplate.innerText = '(new template)';
        }
        funModalEditorBtnSave = async () => {
          modalEditorBtnSave.disabled = true;
          alertComponent.alertElem.removeEventListener('shown.bs.modal', closeModalEditor);
          if (saveStateformEditor === false) {
            saveStateformEditor = true;
            try {
              const sendData = new FormData(formEditor);
              sendData.append('year_kpi', yearProgress)
              const getResponse = await sendViaFetchForm('route.php?act=addKpiCorporate', sendData);
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
      funBtnAddCopyDgUtama = async (e) => {
        modalCopy.show();
      }
      funBtnEditDgUtama = async (e) => {}
      // funBtnExcelDetailDgUtama = async (e) => {}
      // funBtnPDFDetailDgUtama = async (e) => {}
      funBtnPublishDgUtama = async (e) => {}

      btnAddFreshDgUtama.addEventListener('click', funBtnAddFreshDgUtama);
      btnAddCopyDgUtama.addEventListener('click', funBtnAddCopyDgUtama);

      btnEditDgUtama.disabled = true;
      // btnMenuPrintDgUtama.disabled = true;
      // btnExcelDetailDgUtama.disabled = true;
      // btnPDFDetailDgUtama.disabled = true;
      btnPublishDgUtama.disabled = true;
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

const resetModalEditor = async () => {
  const tbody = document.querySelector('table#dgEditor tbody');
  modalEditorTahunTemplate.parentElement.classList.remove('d-none');
  modalEditorTahunKPI.innerText = null;
  modalEditorTahunTemplate.innerText = null;
  dgEditorYearBaseline3.innerText = null;
  dgEditorYearBaseline2.innerText = null;
  dgEditorYearBaseline1.innerText = null;
  indexRowEditor = 0;
  deleteDataKPI = [];
  tbody.innerHTML = null;
  modalEditorBtnSave.removeEventListener('click', funModalEditorBtnSave);
  modalEditor._element.removeEventListener('shown.bs.modal', openModalEditor);
  // alertComponent.alertElem.removeEventListener('shown.bs.modal', closeModalEditor);
}
const defaultOpenModalEditor = () => {
  modalEditorTahunKPI.innerText = yearProgress;
  dgEditorYearBaseline3.innerText = yearProgress - 3;
  dgEditorYearBaseline2.innerText = yearProgress - 2;
  dgEditorYearBaseline1.innerText = yearProgress - 1;
}

const closeModalEditor = async () => {
  modalEditor.hide();
  funViewKPIYear(yearProgress);
}

const insertingTdEditor = async (data, funInsert, addDataEditor, indexParent = null) => {
  const elemTr = document.createElement('tr');
  elemTr.dataset.indexRow = indexRowEditor;
  elemTr.dataset.indexParent = !IsEmpty(indexParent) ? indexParent : data.index_sobject;
  elemTr.dataset.rowJson = JSON.stringify(data);
  const elemTd = `
    <input type="hidden" name="id_sobject[${indexRowEditor}]" id="id_sobject_${indexRowEditor}">
    <input type="hidden" name="kpicorp_id[${indexRowEditor}]" id="kpicorp_id_${indexRowEditor}">
    <td class="text-center" style="white-space: nowrap;">${data.name_perspective}</td>
    <td style="white-space: nowrap;">${data.text_sobject}</td>
    <td><input type="text" class="form-control index_kpi_corp" name="index_kpi_corp[${indexRowEditor}]" id="index_kpi_corp_${indexRowEditor}" readonly></td>
    <td><textarea class="form-control name_kpi_corp" name="name_kpi_corp[${indexRowEditor}]" id="name_kpi_corp_${indexRowEditor}" style="width: 300px; height: 2.375rem;"></textarea></td>
    <td><textarea class="form-control define_kpi_corp" name="define_kpi_corp[${indexRowEditor}]" id="define_kpi_corp_${indexRowEditor}" style="width: 300px; height: 2.375rem;"></textarea></td>
    <td><input type="text" class="form-control control_cek_kpi_corp" name="control_cek_kpi_corp[${indexRowEditor}]" id="control_cek_kpi_corp_${indexRowEditor}" style="width: 220px; height: 2.375rem;"></td>
    <td class="text-center">${!IsEmpty(data.baseline_3) ? data.baseline_3 : '-'}</td>
    <td class="text-center">${!IsEmpty(data.baseline_2) ? data.baseline_2 : '-'}</td>
    <td class="text-center">${!IsEmpty(data.baseline_1) ? data.baseline_1 : '-'}</td>
    <td><input type="text" class="form-control target_kpi_corp" name="target_kpi_corp[${indexRowEditor}]" id="target_kpi_corp_${indexRowEditor}" style="width: 240px;" inputmode="numeric"></td>
    <td><select class="form-select satuan_kpi_corp" name="satuan_kpi_corp[${indexRowEditor}]" id="satuan_kpi_corp_${indexRowEditor}" data-placeholder="Masukan satuan..." style="width: 150px;"></select></td>
    <td><select class="form-select formula_kpi_corp" name="formula_kpi_corp[${indexRowEditor}]" id="formula_kpi_corp_${indexRowEditor}" data-placeholder="Masukan formula..." style="width: 150px;"></select></td>
    <td><select class="form-select polaritas_kpi_corp" name="polaritas_kpi_corp[${indexRowEditor}]" id="polaritas_kpi_corp_${indexRowEditor}" data-placeholder="Masukan polaritas..." style="width: 120px;"></select></td>
  `;
  elemTr.innerHTML = elemTd;
  elemTr.addEventListener('click', (e) => {
    if (e.target.localName !== 'input' && e.target.localName !== 'textarea' && e.target.localName !== 'span') {
      elemTr.classList.contains('selected') ? elemTr.classList.remove('selected') : elemTr.classList.add('selected');
      removeSelectEditor(elemTr);
    }
  });
  await funInsert(elemTr, indexRowEditor);
  const indexRowNow = indexRowEditor;
  indexRowEditor++;
  await addFunSelectEditor(indexRowNow);
  await addDataEditor(indexRowNow);
  await addIndexingKPI(elemTr);
}
const addFunSelectEditor = async (index) => {
  $(`#satuan_kpi_corp_${index}`).select2({
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
  $(`#formula_kpi_corp_${index}`).select2({
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
  $(`#polaritas_kpi_corp_${index}`).select2({
    theme: "bootstrap-5",
    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    placeholder: $( this ).data( 'placeholder' ),
    dropdownParent: $('#modalEditor'),
    allowClear: true,
    data: getResponsePolaritas,
  });

  const target_kpi_corp = document.getElementById('target_kpi_corp_' + index);
  stateTargetInput[index] = null;
  target_kpi_corp.addEventListener('focusout', (e) => {
    stateTargetInput[index] = stateTargetInput[index] ?? null;
    const input = e.target;
    const value = input.value.trim().replace(/,/g, "");
    const numericDotRegex = /^[0-9.]*$/;
    const realValue = number_format_big(value, 2, '.', ',');
    if (IsEmpty(value) && parseInt(value) !== 0) {
      input.value = null;
    } else if (parseInt(value) === 0) {
      input.value = 0;
    } else if (!numericDotRegex.test(value)) {
      input.value = stateTargetInput[index];
    } else {
      stateTargetInput[index] = realValue;
      input.value = realValue;
    }
  });
  target_kpi_corp.addEventListener('focusin', (e) => {
    const input = e.target;
    const value = input.value.trim().replace(/,/g, "");
    input.value = value;
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
  const elemKPI = document.getElementById('index_kpi_corp_' + indexRow);
  elemKPI.value = indexParent + '.' + indexKPI;
}

const removeSelectEditor = (elemTarget) => {
  const dgEditorTr = document.querySelectorAll('table#dgEditor > tbody tr');
  dgEditorTr.forEach(element => {
    if(element !== elemTarget) element.classList.remove('selected');
  });
}

document.addEventListener("DOMContentLoaded", async () => {
  // const sendDataPolaritas = new URLSearchParams();
  // getResponsePolaritas = await sendViaFetchForm('../../json/polaritas.json', sendDataPolaritas);
  getResponsePolaritas = await getJsonPolaritas();

  btnAddFreshDgUtama.disabled = true;
  btnAddCopyDgUtama.disabled = true;
  btnEditDgUtama.disabled = true;
  btnPublishDgUtama.disabled = true;
  // btnMenuPrintDgUtama.disabled = true;
  // btnExcelDetailDgUtama.disabled = true;
  // btnPDFDetailDgUtama.disabled = true;
  btnReloadDgUtama.disabled = true;

  dgUtamaYearInput.value = null;

  dgUtamaYearBtn.addEventListener('click', async (e) => {
    yearProgress = dgUtamaYearInput.value;
    await funBtnReloadDgUtama();
  });

  stateYearInput = null;
  dgUtamaYearInput.addEventListener('keyup', async (e) => {
    if (e.type === 'keyup' && e.key === 'Enter') {
      yearProgress = dgUtamaYearInput.value;
      await funBtnReloadDgUtama();
    } else {
      const input = e.target;
      const value = input.value;
      const numericRegex = /^[0-9]*$/;
      if (IsEmpty(value, true)) {
        input.value = null;
      } else if (!numericRegex.test(value)) {
        input.value = stateYearInput;
      } else {
        stateYearInput = input.value;
      }
      if (input.value < 0) {
        if(!e.target.classList.contains('border-danger')) e.target.classList.add('border-danger');
      } else {
        e.target.classList.remove('border-danger');
      }
    }
  });

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
      if (jsonData.hasOwnProperty('id_kpicorp')) {
        delete jsonData['id_kpicorp'];
      }
      if (jsonData.hasOwnProperty('terbit_kpicorp')) {
        delete jsonData['terbit_kpicorp'];
      }
      const indexInputForParent = trRow.querySelector("input.index_kpi_corp").value;
      const beginIndexAddElem = jsonData.alias_perspective + indexInputForParent + '.';
      const elementSame = [];

      await insertingTdEditor(jsonData, async (element) => {
        const trElem = document.querySelectorAll('table#dgEditor > tbody tr');
        if (trElem.length > 0) {
          for (const elemCheck of trElem) {
            const indexInputCheck = elemCheck.querySelector("input.index_kpi_corp").value;
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
      }, async (index) => {
        const id_sobject = document.getElementById('id_sobject_' + index);
        const kpicorp_id = document.getElementById('kpicorp_id_' + index);
        id_sobject.value = jsonData.id_sobject;
        kpicorp_id.value = null;
      }, indexInputForParent);
    }
  });

  btnDeleteDgEditor.addEventListener('click', (e) => {
    const trRowSelector = document.querySelector('table#dgEditor > tbody > tr.selected');
    if ( !IsEmpty(trRowSelector) ) {
      const jsonData = JSON.parse(trRowSelector.dataset.rowJson);
      if (jsonData.terbit_kpicorp !== 't') {
        const indexSelector = trRowSelector.querySelector("input.index_kpi_corp").value;
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
            const indexInputCheck = elemCheck.querySelector("input.index_kpi_corp").value;
            const arr2 = parseVersion(indexInputCheck);
            const findAlter2 = jsonDataCheck.alias_perspective + indexInputCheck;
            if (findAlter2.includes(findAlter1) && childVersion(arr1, arr2)) {
              if (jsonDataCheck.id_kpicorp) {
                const arrInput = {};
                arrInput.id_kpicorp = jsonDataCheck.id_kpicorp;
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
            const indexInputCheck = elemCheck.querySelector("input.index_kpi_corp").value;
            const arr2 = parseVersion(indexInputCheck);
            const findAlter2 = jsonDataCheck.alias_perspective + indexInputCheck;
            const comparisonResult = compareVersions(arr1, arr2);
            if (findAlter2.includes(findAlter1) && comparisonResult < 0) {
              const splitArr = indexInputCheck.split('.');
              splitArr[dotCount] = parseInt(splitArr[dotCount]) - 1;
              elemCheck.querySelector("input.index_kpi_corp").value = splitArr.join('.');
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
            const indexInputCheck = elemCheck.querySelector("input.index_kpi_corp").value;
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
      }, async (index) => {
        const id_sobject = document.getElementById('id_sobject_' + index);
        const kpicorp_id = document.getElementById('kpicorp_id_' + index);
        id_sobject.value = jsonData.id_sobject;
        kpicorp_id.value = null;
      }
    );
    modalAddFresh.hide();
  });

  modalCopyBtnSave.addEventListener('click', async (e) => {
    const elementRadio = document.querySelectorAll('input[name=modalCopyYearRadio]');
    let yearTemplate;
    elementRadio.forEach(element => {
      if (element.checked) {
        yearTemplate = element.value;
      }
    });
    const getDataTemplate = await getKpiNow(yearTemplate, true);
    modalCopy.hide();
    await resetModalEditor();
    openModalEditor = async () => {
      defaultOpenModalEditor();
      modalEditorTahunTemplate.innerText = yearTemplate;
      await insertTdEditor(getDataTemplate);
    }
    funModalEditorBtnSave = async () => {
      modalEditorBtnSave.disabled = true;
      alertComponent.alertElem.removeEventListener('shown.bs.modal', closeModalEditor);
      if (saveStateformEditor === false) {
        saveStateformEditor = true;
        try {
          const sendData = new FormData(formEditor);
          sendData.append('year_kpi', yearProgress)
          const getResponse = await sendViaFetchForm('route.php?act=addKpiCorporate', sendData);
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
  })

  modalCopy._element.addEventListener('shown.bs.modal', () => {
    modalCopyYear1.innerText = yearProgress - 3;
    modalCopyYear2.innerText = yearProgress - 2;
    modalCopyYear3.innerText = yearProgress - 1;
    modalCopyYearRadio1.value = yearProgress - 3;
    modalCopyYearRadio2.value = yearProgress - 2;
    modalCopyYearRadio3.value = yearProgress - 1;
  });

  modalEditor._element.addEventListener('hidden.bs.modal', (e) => {
    stateTargetInput = [];
  });

});