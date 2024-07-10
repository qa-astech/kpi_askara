import {
  CheckIsObjectEmpty, IsEmpty, resetInputExceptChoice, sendViaFetchForm, AlertElemBS5, ConfirmElemBS5,
  findSmallest, findSmallestObjectPerspective, number_format,
  checkBooleanFromServer
} from '../../../third-party/utility-yudhi/utils.js';

const alertComponent = new AlertElemBS5('alertComponent1');
const confirmComponent = new ConfirmElemBS5('confirmComponent1');
const dgUtamaYearInput = document.getElementById('dgUtamaYearInput');
const dgUtamaUserEntry = document.getElementById('dgUtamaUserEntry');
const dgUtamaLastUpdate = document.getElementById('dgUtamaLastUpdate');
const btnSelectDgUtama = document.getElementById('btnSelectDgUtama');
const btnResetDgUtama = document.getElementById('btnResetDgUtama');
const monthTarget = document.getElementById('monthTarget');
const selectKPIModal = document.getElementById('selectKPIModal');
const selectKPIModalForm = document.getElementById('selectKPIModalForm');
const selectKPIModalSave = document.getElementById('selectKPIModalSave');
const cardChoice = document.getElementById('card-choice');
const idTargetTbody = document.getElementById('idTargetTbody');
const bsModalSelectKPI = new bootstrap.Modal(selectKPIModal);
// const changeEvent = new Event('change');

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

let getMonthData;
const getJsonMonthData = async () => {
  return new Promise(function (resolve, reject) {
      let xhr = new XMLHttpRequest();
      xhr.open('GET', '../../json/month.json', true);
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

let yearKPI;
let monthFromKPI;
let monthToKPI;
let saveStateSelectKPI = false;

const resetProgram = (e) => {
  cardChoice.innerHTML = null;
  idTargetTbody.innerHTML = null;
  btnSelectDgUtama.disabled = false;
  dgUtamaYearInput.disabled = false;
  $('#dgUtamaDateInput1')[0].disabled = false;
  $('#dgUtamaDateInput2')[0].disabled = false;
  alertComponent.alertElem.removeEventListener('shown.bs.modal', resetProgram);
}

document.addEventListener("DOMContentLoaded", async () => {
  
  // const sendDataMonthData = new URLSearchParams();
  // getMonthData = await sendViaFetchForm('../../json/month.json', sendDataMonthData);
  getMonthData = await getJsonMonthData();

  $('#dgUtamaDateInput1').select2({
    theme: "bootstrap-5",
    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    placeholder: $( this ).data( 'placeholder' ),
    allowClear: true,
    data: getMonthData,
  });

  $('#dgUtamaDateInput2').select2({
    theme: "bootstrap-5",
    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    placeholder: $( this ).data( 'placeholder' ),
    allowClear: true,
    data: getMonthData,
  });

  btnResetDgUtama.addEventListener('click', resetProgram);

  btnSelectDgUtama.addEventListener('click', async (e) => {

    if (IsEmpty(dgUtamaYearInput.value)) {
      alertComponent.sendAnAlertOnCatch('Tahun kolom tidak diisi!');
      return false;
    } else {
      yearKPI = parseInt(dgUtamaYearInput.value);
    }

    if (IsEmpty($('#dgUtamaDateInput1').val()) || IsEmpty($('#dgUtamaDateInput2').val())) {
      alertComponent.sendAnAlertOnCatch('Filter bulan kosong, periksa kembali!');
      return false;
    } else {
      monthFromKPI = parseInt($('#dgUtamaDateInput1').val());
      monthToKPI = parseInt($('#dgUtamaDateInput2').val());
      if (monthFromKPI > monthToKPI) {
        alertComponent.sendAnAlertOnCatch('Mohon masukan filter bulan dengan benar!');
        return false;
      }
    }

    selectKPIModalForm.innerHTML = null;
    const sendData = new FormData();
    sendData.append('year_kpi', yearKPI);
    sendData.append('monthFrom_kpi', monthFromKPI);
    sendData.append('monthTo_kpi', monthToKPI);
    const getResponse = await sendViaFetchForm('route.php?act=selectKpiLockUnlock', sendData);

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
        labelCard.classList.add('card', 'text-start', 'mb-3');
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

    if (!saveStateSelectKPI) {
      saveStateSelectKPI = true;
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

        const tdMonth = document.querySelectorAll('.tdMonth');
        for (const element of tdMonth) {
          if(!element.classList.contains('d-none')) element.classList.add('d-none');
        }

        for (const element of tdMonth) {
          if (element.dataset.month >= monthFromKPI && element.dataset.month <= monthToKPI) {
            element.classList.remove('d-none');
          }
        }
        
        cardChoice.innerHTML = null;
        cardChoice.append(elementCopy);
        elementCopy.checked = true;
  
        const cardHeader = elementCopy.querySelector('div.card-header');
        cardHeader.classList.add('fw-bold');
    
        dgUtamaYearInput.disabled = true;
        $('#dgUtamaDateInput1')[0].disabled = true;
        $('#dgUtamaDateInput2')[0].disabled = true;
  
        const sendData = new FormData(selectKPIModalForm);
        sendData.append('year_kpi', yearKPI);
        sendData.append('monthFrom_kpi', monthFromKPI);
        sendData.append('monthTo_kpi', monthToKPI);
        const getData = await sendViaFetchForm('route.php?act=getKpiLockUnlock', sendData);

        const updatedData = {};
        getData.forEach(data => {
          if (CheckIsObjectEmpty(updatedData) || data.last_update > updatedData.last_update) {
            updatedData.user_entry = data.user_entry;
            updatedData.last_update = data.last_update;
          }
        });
        dgUtamaUserEntry.innerText = updatedData.user_entry;
        dgUtamaLastUpdate.innerText = updatedData.last_update;
  
        for (const dataKpi of getData) {
          const trElem = document.createElement('tr');
          trElem.innerHTML = `
            <td>${dataKpi.id_realization}</td>
            <td>${dataKpi.name_perspective}</td>
            <td>${dataKpi.text_sobject}</td>
            <td><span style="padding-left: ${(dataKpi.index_kpi_realization.split('.').length - 2) * 12}px;">(${dataKpi.index_kpi_realization}) ${dataKpi.name_kpi_realization}</span></td>
            <td>${dataKpi.define_kpi_realization}</td>
            <td>${dataKpi.control_cek_kpi_realization}</td>
          `;
          monthAcro.forEach(element => {
            const filteringArr = dataKpi.target_kpi.filter((objValue) => objValue.month === element.number)[0];
            if (filteringArr) {
              console.log(filteringArr);
              trElem.innerHTML += `
                <td data-month="${element.number}">
                  <div class="d-flex justify-content-center align-items-center">
                    <input type="checkbox" id="${filteringArr.idTargetMonth}" class="chkbx-toggle" data-tbl-target="${filteringArr.tblTarget}" value="${filteringArr.idTargetMonth}" ${checkBooleanFromServer(filteringArr.lockStatus) ? 'checked' : ''}>
                    <label for="${filteringArr.idTargetMonth}"></label>
                  </div>
                </td>
              `;
            } else {
              trElem.innerHTML += `
                <td data-month="${element.number}">
                  <div class="d-flex justify-content-center align-items-center">
                    <input type="checkbox" class="chkbx-toggle" disabled>
                    <label></label>
                  </div>
                </td>
              `;
            }
          });
          idTargetTbody.append(trElem);
        }
        
        btnSelectDgUtama.disabled = true;
        await bsModalSelectKPI.hide();
      }
      saveStateSelectKPI = false;
    }
  })
  
  let testUp = 0;
  let headerItemBulanElem = '';
  monthAcro.forEach(element => {
    headerItemBulanElem += `
      <th class="align-middle text-center tdMonth" data-month="${element.number}" style="border-left: 1px solid var(--bs-danger)">${element.full}</th>
    `;
    testUp++;
  });
  monthTarget.innerHTML = headerItemBulanElem;

  dgUtamaYearInput.value = null;
  $('#dgUtamaDateInput1').val(1).trigger('change');
  $('#dgUtamaDateInput2').val(12).trigger('change');
  dgUtamaYearInput.disabled = false;
  $('#dgUtamaDateInput1')[0].disabled = false;
  $('#dgUtamaDateInput2')[0].disabled = false;

  selectKPIModal.addEventListener('hidden.bs.modal', (e) => {
    const getAllCheck = document.querySelectorAll('.chkbx-toggle');
    getAllCheck.forEach(element => {
      element.addEventListener('change', async (e) => {
        const sendData = new FormData();
        sendData.append('code_maccess', e.target.value);
        sendData.append('check_menu', e.target.checked);
        sendData.append('target_table', e.target.dataset.tblTarget);
        await sendViaFetchForm('route.php?act=sendKpiLockUnlock', sendData);
      })
    });
  });

});