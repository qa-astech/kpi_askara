import {
  IsEmpty, resetInputExceptChoice, sendViaFetchForm, AlertElemBS5, ConfirmElemBS5, number_format,
  parseVersion, compareVersions, childVersion, number_format_big, monthAcro,
  checkBooleanFromServer,
  CheckIsObjectEmpty
} from '../../../third-party/utility-yudhi/utils.js';
$.fn.dataTable.ext.errMode = 'none';

const alertComponent = new AlertElemBS5('alertComponent1');
const confirmComponent = new ConfirmElemBS5('confirmComponent1');
const listMonthRow = document.getElementById('listMonthRow');
const titleUtama = document.getElementById('titleUtama');
const btnResetDgUtama = document.getElementById('btnResetDgUtama');
const btnSelectDgUtama = document.getElementById('btnSelectDgUtama');
const cardChoice = document.getElementById('cardChoice');
const dgUtama = document.getElementById('dgUtama');
const dgUtamaYearInput = document.getElementById('dgUtamaYearInput');
const dgUtamaUserEntry = document.getElementById('dgUtamaUserEntry');
const dgUtamaLastUpdate = document.getElementById('dgUtamaLastUpdate');
const selectKPIModal = document.getElementById('selectKPIModal');
const selectKPIModalForm = document.getElementById('selectKPIModalForm');
const selectKPIModalSave = document.getElementById('selectKPIModalSave');
const bsModalSelectKPI = new bootstrap.Modal(selectKPIModal);
const idTargetTbody = document.getElementById('idTargetTbody');

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

let saveProgramKPI = false;
let saveStateSelectKPI = false;
let yearKPI = null;

const resetProgram = (e) => {
  cardChoice.innerHTML = null;
  idTargetTbody.innerHTML = null;
  btnSelectDgUtama.disabled = false;
  dgUtamaYearInput.disabled = false;
  alertComponent.alertElem.removeEventListener('hidden.bs.modal', resetProgram);
}

document.addEventListener("DOMContentLoaded", async () => {
  
  getResponsePolaritas = await getJsonPolaritas();

  let elemTr = '';
  for (const valueMonth of monthAcro) {
    elemTr += `<th class="align-middle text-center">${valueMonth.full}</th>`;
  }
  listMonthRow.innerHTML = elemTr;

  dgUtamaYearInput.value = null;
  dgUtamaYearInput.disabled = false;

  btnResetDgUtama.addEventListener('click', resetProgram);

  btnSelectDgUtama.addEventListener('click', async (e) => {

    if (IsEmpty(dgUtamaYearInput.value)) {
      alertComponent.sendAnAlertOnCatch('Tahun kolom tidak diisi!');
      return false;
    } else {
      yearKPI = parseInt(dgUtamaYearInput.value);
    }
    selectKPIModalForm.innerHTML = null;
    const sendData = new FormData();
    sendData.append('year_kpi', yearKPI);
    const getResponse = await sendViaFetchForm('route.php?act=selectKpi', sendData);

    if (getResponse.response === 'error') {
      alertComponent.sendAnAlertOnCatch(getResponse.alert);
    } else {
      const templateCard = (typeKPI, id_company, name_company, id_department, name_department, index) => `
        <div class="card-header d-flex justify-content-start align-items-center">
          <div>${typeKPI}</div>
          <div class="form-check ms-auto">
            <input class="form-check-input checkbox-select-kpi" type="radio" name="statusKpi" id="statusKpi_${index}" value="${index}">
            <input type="hidden" name="companyKpi[${index}]" id="companyKpi_${index}" value="${id_company}">
            <input type="hidden" name="departmentKpi[${index}]" id="departmentKpi_${index}" value="${id_department}">
            <input type="hidden" name="typeKPI[${index}]" id="typeKPI_${index}" value="${typeKPI}">
          </div>
        </div>
        <div class="card-body">
          <table>
            <tbody>
              <tr>
                <td class="pb-2 white-space-nowrap">Perusahaan</td>
                <td class="pb-2 px-2">:</td>
                <td class="pb-2 w-100">${name_company}</td>
              </tr>
              <tr>
                <td class="pb-2 white-space-nowrap">Department</td>
                <td class="pb-2 px-2">:</td>
                <td class="pb-2 w-100">${name_department}</td>
              </tr>
            </tbody>
          </table>
        </div>
      `;
  
      let index = 1;
      getResponse.forEach(data => {
        const labelCard = document.createElement('label');
        labelCard.classList.add('card', 'my-3', 'text-start');
        labelCard.setAttribute('for', 'statusKpi_' + index);
        labelCard.innerHTML = templateCard(data.status_kpi, data.compkpi_id, data.compkpi_name, data.deptkpi_id, data.deptkpi_name, index);
        selectKPIModalForm.append(labelCard);
        index++;
      });
  
      const elementCheckbox = selectKPIModal.querySelectorAll('.checkbox-select-kpi');
      elementCheckbox.forEach(element => {
        element.addEventListener('change', (e) => {
          const elementCard = e.target.parentElement.parentElement.parentElement;
          const allCard = selectKPIModal.querySelectorAll('label.card');
          allCard.forEach(card => {
            card.classList.remove('selected');
          });
          elementCard.classList.add('selected');
        });
      });
  
      bsModalSelectKPI.show();
    }

  })

  selectKPIModalSave.addEventListener('click', async (e) => {
    const cssSelect = document.querySelectorAll('.checkbox-select-kpi');
    let elementCopy;
    for (const element of cssSelect) {
      if (element.checked) {
        const elementCard = element.parentElement.parentElement.parentElement;
        elementCopy = elementCard.cloneNode(true);
      }
    }
    
    if (IsEmpty(elementCopy)) {
      alertComponent.sendAnAlertOnCatch('Silahkan pilih terlebih dahulu KPI yang ingin diisi!');
      return false;
    } else {

      
      cardChoice.innerHTML = null;
      cardChoice.append(elementCopy);
      elementCopy.checked = true;

      const cardHeader = elementCopy.querySelector('div.card-header');
      cardHeader.classList.add('fw-bold');
  
      dgUtamaYearInput.disabled = true;

      const sendData = new FormData(selectKPIModalForm);
      sendData.append('year_kpi', yearKPI);
      const getData = await sendViaFetchForm('route.php?act=getKpi', sendData);

      const updatedData = {};
      getData.forEach(data => {
        data.target_kpi.forEach(monthData => {
          if (CheckIsObjectEmpty(updatedData) || !updatedData.last_update || monthData.timeRealisasi > updatedData.last_update) {
            updatedData.user_entry = monthData.fullnameRealisasi;
            updatedData.last_update = monthData.timeRealisasi;
          }
        });
      });
      dgUtamaUserEntry.innerText = updatedData.user_entry;
      dgUtamaLastUpdate.innerText = updatedData.last_update;

      for (const dataKpi of getData) {
        const trElem = document.createElement('tr');
        const tdContainer = document.createElement('td');
        const button = document.createElement('button');
        button.classList.add("btn", "rounded", "btn-sm", "btn-setup");
        button.setAttribute("type", "button"); 
        button.innerHTML = `<span class="d-inline-block ps-1">Kustom Formula</span>`;
        button.addEventListener('click', () => {
          // config button kustom formula disini
          console.log('nanih');
        })
        tdContainer.append(button);

        trElem.innerHTML = `
          <td>${dataKpi.name_perspective}</td>
          <td>${dataKpi.text_sobject}</td>
          <td><span style="padding-left: ${(dataKpi.index_kpi_realization.split('.').length - 2) * 12}px;">(${dataKpi.index_kpi_realization}) ${dataKpi.name_kpi_realization}</span></td>
          <td>${dataKpi.define_kpi_realization}</td>
          <td>${dataKpi.control_cek_kpi_realization}</td>
          <td class="text-center">${dataKpi.name_satuan}</td>
          <td class="text-center">${dataKpi.polaritas_kpi_realization.toUpperCase()}</td>
          <td class="text-end">${dataKpi.target_kpicorp ? number_format_big(dataKpi.target_kpicorp, 2, '.', ',') : '-'}</td>
          <td class="text-end">${dataKpi.target_kpibunit ? number_format_big(dataKpi.target_kpibunit, 2, '.', ',') : '-'}</td>
        `;
        
        monthAcro.forEach(element => {
          const filteringArr = dataKpi.target_kpi.filter((objValue) => parseInt(objValue.month) === parseInt(element.number))[0];
          if (filteringArr) {
            trElem.innerHTML += `
              <td class="text-end" data-month="${element.number}">${number_format_big(filteringArr.target, 2, '.', ',')}</td>
            `;
          } else {
            trElem.innerHTML += `
              <td data-month="${element.number}"></td>
            `;
          }
        });
        trElem.prepend(tdContainer);
        idTargetTbody.append(trElem);
      }

      saveProgramKPI = false;
      btnSelectDgUtama.disabled = true;
      await bsModalSelectKPI.hide();

    }
    saveStateSelectKPI = false;
  });

});