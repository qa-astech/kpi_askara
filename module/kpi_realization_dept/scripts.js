import {
  CheckIsObjectEmpty, IsEmpty, resetInputExceptChoice, sendViaFetchForm, AlertElemBS5, ConfirmElemBS5,
  findSmallest, findSmallestObjectPerspective, number_format, number_format_big
} from '../../../third-party/utility-yudhi/utils.js';

const alertComponent = new AlertElemBS5('alertComponent1');
const confirmComponent = new ConfirmElemBS5('confirmComponent1');
const dgUtamaYearInput = document.getElementById('dgUtamaYearInput');
const dgUtamaUserEntry = document.getElementById('dgUtamaUserEntry');
const dgUtamaLastUpdate = document.getElementById('dgUtamaLastUpdate');
const btnSelectDgUtama = document.getElementById('btnSelectDgUtama');
const btnResetDgUtama = document.getElementById('btnResetDgUtama');
const btnSaveDgUtama = document.getElementById('btnSaveDgUtama');
const dgUtama = document.getElementById('dgUtama');
const beforeBulanHeader = document.getElementById('beforeBulanHeader');
const trHeaderUtama = document.getElementById('trHeaderUtama');
const monthTarget = document.getElementById('monthTarget');
const selectKPIModal = document.getElementById('selectKPIModal');
const selectKPIModalForm = document.getElementById('selectKPIModalForm');
const selectKPIModalSave = document.getElementById('selectKPIModalSave');
const cardChoice = document.getElementById('card-choice');
const idTargetTbody = document.getElementById('idTargetTbody');
const iframeDownload = document.getElementById('iframeDownload');
const formRealization = document.getElementById('formRealization');
const isiNote = document.querySelector('.noteDgUtama > div > div');
const bsModalSelectKPI = new bootstrap.Modal(selectKPIModal);

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
let visibleMonth = [];

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
let typeKPI;
let companyKPI;
let departmentKPI;
let saveStateSelectKPI = false;
let saveProgramKPI = false;
let stateTargetInput = [];

const resetProgram = (e) => {
  cardChoice.innerHTML = null;
  idTargetTbody.innerHTML = null;
  btnSelectDgUtama.disabled = false;
  dgUtamaYearInput.disabled = false;
  $('#dgUtamaDateInput1')[0].disabled = false;
  $('#dgUtamaDateInput2')[0].disabled = false;
  alertComponent.alertElem.removeEventListener('shown.bs.modal', resetProgram);
}

const implanHeader = async () => {
  const tdMonth = document.querySelectorAll('.tdMonth');
  tdMonth.forEach(element => {
    element.remove();
  });

  const headerBulanElem = [];
  visibleMonth.forEach(element => {
    const elemImplan = document.createElement('th');
    elemImplan.classList.add('align-middle', 'text-center', 'tdMonth');
    elemImplan.setAttribute('colspan', '6');
    elemImplan.dataset.month = element.number;
    elemImplan.innerText = element.full;
    elemImplan.style.borderLeft = "1px solid var(--bs-danger)";
    elemImplan.style.borderRight = "1px solid var(--bs-danger)";
    headerBulanElem.push(elemImplan);
  });
  beforeBulanHeader.after(...headerBulanElem);

  let headerItemBulanElem = '';
  visibleMonth.forEach(element => {
    headerItemBulanElem += `
      <th class="align-middle text-center tdMonth" data-month="${element.number}" style="border-left: 1px solid var(--bs-danger)">Target</th>
      <th class="align-middle text-center tdMonth" data-month="${element.number}">Realisasi</th>
      <th class="align-middle text-center tdMonth" data-month="${element.number}">Indikator Pencapaian</th>
      <th class="align-middle text-center tdMonth" data-month="${element.number}">Bukti File</th>
      <th class="align-middle text-center tdMonth" data-month="${element.number}">Diinput Oleh</th>
      <th class="align-middle text-center tdMonth" data-month="${element.number}" style="border-right: 1px solid var(--bs-danger)">Waktu Penginputan</th>
    `;
  });
  monthTarget.innerHTML = headerItemBulanElem;
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
    const getResponse = await sendViaFetchForm('route.php?act=selectKpiRealization', sendData);

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

    const date1 = parseInt($('#dgUtamaDateInput1').select2('data')[0].id);
    const date2 = parseInt($('#dgUtamaDateInput2').select2('data')[0].id);
    visibleMonth = monthAcro.filter((monthObj) => parseInt(monthObj.number) >= date1 && parseInt(monthObj.number) <= date2);

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
  //     const tdMonth = document.querySelectorAll('.tdMonth');
  //     for (const element of tdMonth) {
  //       if(!element.classList.contains('d-none')) element.classList.add('d-none');
  //     }
  //     for (const element of tdMonth) {
  //       if (element.dataset.month >= monthFromKPI && element.dataset.month <= monthToKPI) {
  //         element.classList.remove('d-none');
  //       }
  //     }

      await implanHeader();
      
      const btnForSaving = document.createElement('button');
      btnForSaving.classList.add('btn', 'rounded', 'btn-sm', 'btn-save');
      const spanForSaving = document.createElement('span');
      spanForSaving.classList.add('d-inline-block', 'ps-1')
      spanForSaving.innerText = 'Simpan KPI';
      btnForSaving.append(spanForSaving);
      
      cardChoice.innerHTML = null;
      cardChoice.append(elementCopy);
      cardChoice.append(btnForSaving);
      elementCopy.checked = true;

      btnForSaving.addEventListener('click', async (e) => {
        btnForSaving.disabled = true;
        if (saveProgramKPI === false) {
          saveProgramKPI = true;
          try {
            const sendData = new FormData(formRealization);
            const getData = await sendViaFetchForm('route.php?act=sendKpiRealization', sendData);
            alertComponent.sendAnAlertOnTry(getData, resetProgram);
          } catch (error) {
            alertComponent.sendAnAlertOnCatch(error);
          } finally {
            btnForSaving.disabled = false;
            saveProgramKPI = false;
          }
        }
      });

      const cardHeader = elementCopy.querySelector('div.card-header');
      cardHeader.classList.add('fw-bold');
  
      dgUtamaYearInput.disabled = true;
      $('#dgUtamaDateInput1')[0].disabled = true;
      $('#dgUtamaDateInput2')[0].disabled = true;

      const sendData = new FormData(selectKPIModalForm);
      sendData.append('year_kpi', yearKPI);
      sendData.append('monthFrom_kpi', monthFromKPI);
      sendData.append('monthTo_kpi', monthToKPI);
      const getData = await sendViaFetchForm('route.php?act=getKpiRealization', sendData);

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
          <td class="text-end">${dataKpi.target_kpicorp ? number_format_big(dataKpi.target_kpicorp, 2, '.', ',') : '-'}</td>
          <td class="text-end">${dataKpi.target_kpibunit ? number_format_big(dataKpi.target_kpibunit, 2, '.', ',') : '-'}</td>
          <td class="text-center">${dataKpi.name_satuan}</td>
          <td class="text-center">${dataKpi.name_formula}</td>
          <td class="text-center" style="border-right: 1px solid var(--bs-danger)">${dataKpi.polaritas_kpi_realization.toUpperCase()}</td>
        `;
        
        visibleMonth.forEach(element => {
          const filteringArr = dataKpi.target_kpi.filter((objValue) => objValue.month === element.number)[0];
          if (filteringArr) {
            if (filteringArr.lockStatus === 'f') {
              trElem.innerHTML += `
                <input type="hidden" name="type_kpi[${filteringArr.idTargetMonth}]" value="${filteringArr.typeTarget}">
                <td class="bg-danger-subtle text-end" data-month="${element.number}" style="border-left: 1px solid var(--bs-danger)">${number_format_big(filteringArr.target, 2, '.', ',')}</td>
                <td class="bg-danger-subtle" data-month="${element.number}">
                  <input
                    type="text"
                    class="form-control realisasi_kpi"
                    name="realisasi_kpi[${filteringArr.idTargetMonth}]"
                    style="width: 180px;"
                    placeholder="Realisasi..."
                    data-id-month="${filteringArr.idTargetMonth}"
                  >
                </td>
                <td class="bg-danger-subtle" data-month="${element.number}"></td>
                <td class="bg-danger-subtle" data-month="${element.number}">
                  <div class="d-flex flex-column">
                    ${filteringArr.fileRealisasi ? `<button type="button" class="btn rounded btn-sm btn-zip" data-month="${element.number}"><span class="ps-2">Unduh File Sebelumnya</span></button>` : ''}
                    <input type="file" class="form-control file_kpi" name="file_kpi[${filteringArr.idTargetMonth}][]" style="width: 300px;" multiple>
                  </div>
                </td>
                <td class="bg-danger-subtle" data-month="${element.number}"></td>
                <td class="bg-danger-subtle" data-month="${element.number}" style="border-right: 1px solid var(--bs-danger)"></td>
              `;
            } else {
              trElem.innerHTML += `
                <td class="bg-danger-subtle text-end" data-month="${element.number}" style="border-left: 1px solid var(--bs-danger)">${number_format_big(filteringArr.target, 2, '.', ',')}</td>
                <td class="bg-danger-subtle" data-month="${element.number}">${filteringArr.realisasi ? number_format_big(filteringArr.realisasi, 2, '.', ',') : ''}</td>
                <td class="bg-danger-subtle" data-month="${element.number}"></td>
                <td class="bg-danger-subtle" data-month="${element.number}">
                  ${filteringArr.fileRealisasi ? `<button type="button" class="btn rounded btn-sm btn-zip" data-month="${element.number}"><span class="ps-2">Unduh File</span></button>` : ''}
                </td>
                <td class="bg-danger-subtle" data-month="${element.number}">${filteringArr.fullnameRealisasi ?? ''}</td>
                <td class="bg-danger-subtle" data-month="${element.number}" style="border-right: 1px solid var(--bs-danger)">${filteringArr.timeRealisasi ?? ''}</td>
              `;
            }
          } else {
            trElem.innerHTML += `
              <td data-month="${element.number}" style="border-left: 1px solid var(--bs-danger)"></td>
              <td data-month="${element.number}"></td>
              <td data-month="${element.number}"></td>
              <td data-month="${element.number}"></td>
              <td data-month="${element.number}"></td>
              <td data-month="${element.number}" style="border-right: 1px solid var(--bs-danger)"></td>
            `;
          }
        });

        idTargetTbody.append(trElem);
        const allButtonZip = trElem.querySelectorAll('.btn-zip');
        for (const buttonZip of allButtonZip) {
          buttonZip.addEventListener('click', async (e) => {
            const regexSpace = / /g;
            const regexUnderScore = /_/g;
            const targetKpi = dataKpi.target_kpi.filter((value) => {
              return value.month === buttonZip.dataset.month;
            });
            const tipeKPI = dataKpi.status_kpi.replace(regexUnderScore, '-').toUpperCase();
            const companyKPI = dataKpi.compkpi_name.replace('.', '').trim().replace(regexSpace, '-').toUpperCase();
            const departmentKPI = dataKpi.deptkpi_name.trim().replace(regexSpace, '-').toUpperCase();
            const monthKPI = monthAcro.filter((value) => {
              return parseInt(value.number) === parseInt(buttonZip.dataset.month);
            });
            const sendDataFile = new URLSearchParams();
            sendDataFile.append('file', targetKpi[0].fileRealisasi);
            sendDataFile.append('nama_file', `${tipeKPI}_${companyKPI}_${departmentKPI}_${monthKPI[0].full.toUpperCase()}`);
            iframeDownload.setAttribute('src', 'route.php?act=getFileKpiRealization&' + sendDataFile.toString());
          });
        }

      }
      
      const target_input = document.querySelectorAll('.realisasi_kpi');
      target_input.forEach(elementTarget => {
        stateTargetInput[elementTarget.dataset.idMonth] = null;
        elementTarget.addEventListener('focusout', (e) => {
          const input = e.target;
          const value = input.value.trim().replace(/,/g, "");
          const numericDotRegex = /^[0-9.]*$/;
          const realValue = number_format_big(value, 2, '.', ',');
          if (IsEmpty(value) && parseInt(value) !== 0) {
            input.value = null;
          } else if (parseInt(value) === 0) {
            input.value = 0;
          } else if (!numericDotRegex.test(value)) {
            input.value = stateTargetInput[elementTarget.dataset.idMonth];
          } else {
            stateTargetInput[elementTarget.dataset.idMonth] = realValue;
            input.value = realValue;
          }
        });
        elementTarget.addEventListener('focusin', (e) => {
          const input = e.target;
          const value = input.value.trim().replace(/,/g, "");
          input.value = value;
        });

        // const filteringArr = dataKpi.target_kpi.filter((objValue) => objValue.month === element.number)[0];
        // if (filteringArr) {
        //   if (filteringArr.lockStatus === 'f') {}
        // }

        let valueTarget = null;
        for (const dataKpi of getData) {
          const getTarget = dataKpi.target_kpi.filter((objValue) => objValue.idTargetMonth === elementTarget.dataset.idMonth)[0];
          if (getTarget) {
            valueTarget = getTarget.realisasi;
          }
        }

        elementTarget.value = valueTarget ? number_format_big(valueTarget, 2, '.', ',') : '';
        // value="${filteringArr.realisasi ? number_format_big(filteringArr.realisasi, 2, '.', ',') : null}"

      });

      saveProgramKPI = false;
      btnSelectDgUtama.disabled = true;
      await bsModalSelectKPI.hide();
    }
    saveStateSelectKPI = false;
  });

  dgUtamaYearInput.value = null;
  $('#dgUtamaDateInput1').val(1).trigger('change');
  $('#dgUtamaDateInput2').val(12).trigger('change');
  dgUtamaYearInput.disabled = false;
  $('#dgUtamaDateInput1')[0].disabled = false;
  $('#dgUtamaDateInput2')[0].disabled = false;

  const createNewNote = document.createElement('ul');
  createNewNote.classList.add('mb-0');
  createNewNote.innerHTML = `
    <li>
      <small>Terkait dengan <span class="fw-bold text-danger">BUKTI FILE</span>, jika anda mengupdatenya maka <span class="fw-bold text-danger">file sebelumnya akan terhapus!</span> Jadi berhati - hatilah 🤗 </small>
    </li>
    <li>
      <small>Pembatasan pengunggahan <span class="fw-bold text-danger">BUKTI FILE</span> dibatasi <span class="fw-bold text-danger">200MB</span>, jika terlalu banyak file silahkan untuk update satu persatu demi kenyamanan</small>
    </li>
    <li>
      <small><span class="fw-bold text-danger">BUKTI FILE</span> tipe data yang diizinkan : <span class="fw-bold">'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'xls', 'xlsx', 'word', 'ppt', 'pptx', 'doc', 'docs', 'docx'</span></small>
    </li>
  `;
  isiNote.append(createNewNote);

});