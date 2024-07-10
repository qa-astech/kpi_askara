import { IsEmpty, resetInputExceptChoice, sendViaFetchForm, AlertElemBS5, ConfirmElemBS5, checkBooleanFromServer } from '../../../third-party/utility-yudhi/utils.js';
$.fn.dataTable.ext.errMode = 'none';

const btnElemDgUtama = `
    <button type="button" id="btnAddDgUtama" class="btn rounded btn-sm btn-add">
      <span class="d-inline-block ps-1">Tambah</span>
    </button>
    <button type="button" id="btnEditDgUtama" class="btn rounded btn-sm btn-edit ms-2">
      <span class="d-inline-block ps-1">Ubah</span>
    </button>
    <button type="button" id="btnDeleteDgUtama" class="btn rounded btn-sm btn-delete ms-2">
      <span class="d-inline-block ps-1">Hapus</span>
    </button>
    <div class="btn-group ms-2" role="group" aria-label="Button Print" id="btnMenuPrintDgUtama">
      <button type="button" class="btn btn-sm btn-print dropdown-toggle" data-bs-toggle="dropdown" data-bs-target="#menuPrintDgUtama" aria-expanded="false">
        <span class="d-inline-block ps-1">Cetak</span>
      </button>
      <ul class="dropdown-menu dropdown-print" id="menuPrintDgUtama">
        <li>
          <a href="../all_report_detail/master_company_pdf.php" target="_blank" class="dropdown-item" id="btnPDFDetailDgUtama">
            <i class="fa-solid fa-file-pdf"></i>
            <span class="d-inline-block ps-1">Detail PDF</span>
          </a>
        </li>
      </ul>
    </div>
    <button type="button" id="btnReloadDgUtama" class="btn rounded btn-sm btn-reload ms-2"></button>
`;
// Notes untuk detail excel
/* <li>
  <button type="button" class="dropdown-item" id="btnExcelDetailDgUtama">
    <i class="fa-solid fa-file-excel"></i>
    <span class="d-inline-block ps-1">Detail Excel</span>
  </button>
</li> */

class DgUtama {
  constructor () {
    this.filterTh = [];
    function createTable() {
      return new Promise((resolve, reject) => {
        const table = $('#dgUtama').DataTable({
          autoWidth: true,
          select: {
            info: false,
            style: 'single'
          },
          // processing: true,
          serverSide: true,
          dom:`
            <'row'<'dgUtamaToolbar col-sm-12 col-lg-9 text-start pb-2 pb-lg-0'><'col-sm-12 col-lg-3'f>>
            <'d-block overflow-auto w-100 position-relative rounded-top mt-2'tr>
            <'row pt-2'<'col-sm-12 col-md-8 d-flex justify-content-start align-items-center'li><'col-sm-12 col-md-4'p>>
          `,
          ajax: {
            url: 'route.php?act=getCompany',
            type: 'POST'
          },
          columns: [
            {
              className: 'align-middle dt-control',
              orderable: false,
              data: null,
              defaultContent: '',
            },
            {
              data: 'id_company',
              className: 'align-middle'
            },
            {
              data: 'name_company',
              className: 'align-middle'
            },
            {
              data: 'alias_company',
              className: 'align-middle'
            },
            {
              data: 'stat_group',
              className: 'align-middle text-center',
              render: function ( data, type, row, meta ) {
                return checkBooleanFromServer(data)
                  ? `<i class="fa-solid fa-check text-success"></i>`
                  : `<i class="fa-solid fa-xmark text-danger"></i>`;
              }
            },
            {
              data: 'stat_customer',
              className: 'align-middle text-center',
              render: function ( data, type, row, meta ) {
                return checkBooleanFromServer(data)
                  ? `<i class="fa-solid fa-check text-success"></i>`
                  : `<i class="fa-solid fa-xmark text-danger"></i>`;
              }
            },
            {
              data: 'stat_supplier',
              className: 'align-middle text-center',
              render: function ( data, type, row, meta ) {
                return checkBooleanFromServer(data)
                  ? `<i class="fa-solid fa-check text-success"></i>`
                  : `<i class="fa-solid fa-xmark text-danger"></i>`;
              }
            },
            {
              data: 'logo_perusahaan',
              className: 'align-middle text-center',
              render: function ( data, type, row, meta ) {
                return !IsEmpty(data) ? `<img src="logo/${data}" alt="logo_perusahaan" class="d-inline-block" style="max-width: 100%; max-height: 80px;">` : ``;
              }
            },
            {
              data: 'fullname_entry',
              className: 'align-middle text-center'
            },
            {
              data: 'last_update',
              className: 'align-middle text-center'
            }
          ],
          paging: true,
          pageLength: 10,
          lengthMenu: [ 10, 25, 50, 75, 100 ],
          ordering: true,
          order: [[1, 'asc']],
        });
        resolve(table);
      });
    }
    createTable()
      .then(table => {
        this.table = table;
        const rowToMove = document.getElementById('dgUtamaFilter');
        const thead = rowToMove.parentElement;
        thead.removeChild(rowToMove);
        thead.appendChild(rowToMove);
        $('div.dgUtamaToolbar').html(btnElemDgUtama);
        $("#dgUtama_length label").contents()[2].textContent = "";
      })
      .then(table => {
        const thDetailDgUtama = document.getElementById('thDetailDgUtama');
        thDetailDgUtama.setAttribute('rowspan', '2');
        const dgUtama_length = document.getElementById('dgUtama_length');
        dgUtama_length.classList.add('pe-3');
        const dgUtama_info = document.getElementById('dgUtama_info');
        dgUtama_info.classList.add('pt-0');
        this.btnAdd = document.getElementById('btnAddDgUtama');
        this.btnEdit = document.getElementById('btnEditDgUtama');
        this.btnDelete = document.getElementById('btnDeleteDgUtama');
        this.btnMenuPrint = document.getElementById('btnMenuPrintDgUtama');
        // this.btnExcelDetail = document.getElementById('btnExcelDetailDgUtama');
        this.btnPDFDetail = document.getElementById('btnPDFDetailDgUtama');
        this.btnReload = document.getElementById('btnReloadDgUtama');
      })
      .then(table => {
        const filterDg = document.querySelectorAll('#dgUtamaFilter th');
        filterDg.forEach((element, index, parentArr) => {
          if (element.classList.contains("filterTable")) {
            const title = element.textContent;
            const inputElement = document.createElement('input');
            inputElement.setAttribute('type', 'text');
            inputElement.setAttribute('class', 'form-control form-control-sm');
            inputElement.setAttribute('placeholder', `Saring ${title}`);
            element.innerHTML = '';
            element.appendChild(inputElement);
            this.filterTh[index] = inputElement;
          } else {
            element.innerHTML = '';
          }
        });
      })
  }
}

const btnElemDgDetail = `
    <button type="button" id="btnAddDgDetail" class="btn rounded btn-sm btn-add">
      <span class="d-inline-block ps-1">Tambah</span>
    </button>
    <button type="button" id="btnEditDgDetail" class="btn rounded btn-sm btn-edit ms-2">
      <span class="d-inline-block ps-1">Ubah</span>
    </button>
    <button type="button" id="btnDeleteDgDetail" class="btn rounded btn-sm btn-delete ms-2">
      <span class="d-inline-block ps-1">Hapus</span>
    </button>
    <button type="button" id="btnReloadDgDetail" class="btn rounded btn-sm btn-reload ms-2"></button>
`;

class DgDetail {
  constructor () {
    this.filterTh = [];
    this.sendingData = {};
    const getData = (d) => {
      return  $.extend(d, this.sendingData);
    }
    function createTable() {
      return new Promise((resolve, reject) => {
        const table = $('#dgDetail').DataTable({
          autoWidth: true,
          select: {
            info: false,
            style: 'single'
          },
          // processing: true,
          serverSide: true,
          dom:`
            <'row'<'dgDetailToolbar col-sm-12 col-lg-9 text-start pb-2 pb-lg-0'><'col-sm-12 col-lg-3'f>>
            <'d-block overflow-auto w-100 position-relative rounded-top mt-2'tr>
            <'row pt-2'<'col-sm-12 col-md-8 d-flex justify-content-start align-items-center'li><'col-sm-12 col-md-4'p>>
          `,
          ajax: {
            url: '../detail_company/route.php?act=getDetailCompany',
            type: 'POST',
            data: getData
          },
          columns: [
            { data: 'id_det_company' },
            { data: 'name_department' },
            { data: 'name_section' },
            { data: 'name_position' },
            { data: 'name_plant' },
            { data: 'golongan' },
            {
              data: 'fullname_entry',
              className: 'text-center'
            },
            {
              data: 'last_update',
              className: 'text-center'
            }
          ],
          paging: true,
          pageLength: 10,
          lengthMenu: [ 10, 25, 50, 75, 100 ],
          ordering: true,
          order: [[1, 'asc']],
        });
        resolve(table);
      });
    }
    createTable()
      .then(table => {
        this.table = table;
        const rowToMove = document.getElementById('dgDetailFilter');
        const thead = rowToMove.parentElement;
        thead.removeChild(rowToMove);
        thead.appendChild(rowToMove);
        $('div.dgDetailToolbar').html(btnElemDgDetail);
        $("#dgDetail_length label").contents()[2].textContent = "";
      })
      .then(table => {
        const dgDetail_length = document.getElementById('dgDetail_length');
        dgDetail_length.classList.add('pe-3');
        const dgDetail_info = document.getElementById('dgDetail_info');
        dgDetail_info.classList.add('pt-0');
        this.btnAdd = document.getElementById('btnAddDgDetail');
        this.btnEdit = document.getElementById('btnEditDgDetail');
        this.btnDelete = document.getElementById('btnDeleteDgDetail');
        this.btnReload = document.getElementById('btnReloadDgDetail');
      })
      .then(table => {
        const filterDg = document.querySelectorAll('#dgDetailFilter th');
        filterDg.forEach((element, index, parentArr) => {
          if (element.classList.contains("filterTable")) {
            const title = element.textContent;
            const inputElement = document.createElement('input');
            inputElement.setAttribute('type', 'text');
            inputElement.setAttribute('class', 'form-control form-control-sm');
            inputElement.setAttribute('placeholder', `Saring ${title}`);
            element.innerHTML = '';
            element.appendChild(inputElement);
            this.filterTh[index] = inputElement;
          } else {
            element.innerHTML = '';
          }
        });
      })
  }
}

const accessModule = {
  'access_add':'t',
  'access_edit':'t',
  'access_delete':'t',
  'access_print':'t',
};

const dgUtama = new DgUtama();
const alertComponent = new AlertElemBS5('alertComponent1');
const confirmComponent = new ConfirmElemBS5('confirmComponent1');
const modalUtama = bootstrap.Modal.getOrCreateInstance('#modalUtama');
const modalUtamaForm = document.getElementById('modalUtamaForm');
const modalUtamaTitle = document.getElementById('modalUtamaTitle');
const modalUtamaBtnSave = document.getElementById('modalUtamaBtnSave');
const modalUtamaImageChange = document.getElementById('modalUtamaImageChange');
const modalUtamaImageView = document.getElementById('modalUtamaImageView');
const image_change1 = document.getElementById('image_change1');
const image_change2 = document.getElementById('image_change2');
const image_change3 = document.getElementById('image_change3');

const modalDetail = bootstrap.Modal.getOrCreateInstance('#modalDetail');
const modalDetailBackdrop = document.getElementById('modalDetailBackdrop');
const modalDetailForm = document.getElementById('modalDetailForm');
const modalDetailTitle = document.getElementById('modalDetailTitle');
const modalDetailBtnSave = document.getElementById('modalDetailBtnSave');

const dgDetail = new DgDetail();
const modalDetailView = bootstrap.Modal.getOrCreateInstance('#modalDetailView');
const modalDetailViewIdCompany = document.getElementById('modalDetailViewIdCompany');
const modalDetailViewNameCompany = document.getElementById('modalDetailViewNameCompany');

const company_id = document.getElementById('company_id');
const company_name = document.getElementById('company_name');
const company_alias = document.getElementById('company_alias');
const stat_group = document.getElementById('stat_group');
const stat_customer = document.getElementById('stat_customer');
const stat_supplier = document.getElementById('stat_supplier');
const logo_perusahaan = document.getElementById('logo_perusahaan');

const det_company_id = document.getElementById('det_company_id');
const golongan = document.getElementById('golongan');

$('#section_id').select2({
  theme: "bootstrap-5",
  width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
  placeholder: $( this ).data( 'placeholder' ),
  dropdownParent: $('#modalDetail'),
  allowClear: true,
  ajax: {
    url: "../master_section/route.php?act=jsonSection",
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

$('#position_id').select2({
  theme: "bootstrap-5",
  width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
  placeholder: $( this ).data( 'placeholder' ),
  dropdownParent: $('#modalDetail'),
  allowClear: true,
  ajax: {
    url: "../master_position/route.php?act=jsonPosition",
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

$('#plant_id').select2({
  theme: "bootstrap-5",
  width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
  placeholder: $( this ).data( 'placeholder' ),
  dropdownParent: $('#modalDetail'),
  allowClear: true,
  ajax: {
    url: "../master_plant/route.php?act=jsonPlant",
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

let saveModalUtama;
let saveStateModalUtama = false;
const resetModalUtama = () => {
  modalUtama._element.removeEventListener('shown.bs.modal', addModalUtama);
  modalUtama._element.removeEventListener('shown.bs.modal', editModalUtama);
  alertComponent.alertElem.removeEventListener('shown.bs.modal', closeModalUtama);
  modalUtamaBtnSave.removeEventListener('click', saveModalUtama);
  resetInputExceptChoice(modalUtamaForm);
}
const getDataUtama = async () => {
  const sendData = new FormData(modalUtamaForm);
  return sendData;
}
const addModalUtama = async (e) => {
  modalUtamaTitle.innerHTML = 'Tambah Perusahaan';
  if (!modalUtamaImageChange.classList.contains('d-none')) modalUtamaImageChange.classList.add('d-none');
  logo_perusahaan.classList.remove('d-none');
  stat_group.checked = true;
  stat_customer.checked = true;
  stat_supplier.checked = true;
  modalUtamaImageView.src = '';
  saveModalUtama = async (e) => {
    modalUtamaBtnSave.disabled = true;
    if (saveStateModalUtama === false) {
      saveStateModalUtama = true;
      try {
        const sendData = await getDataUtama();
        const getResponse = await sendViaFetchForm('route.php?act=addCompany', sendData);
        alertComponent.sendAnAlertOnTry(getResponse, closeModalUtama);
      } catch (error) {
        alertComponent.sendAnAlertOnCatch(error);
      } finally {
        modalUtamaBtnSave.disabled = false;
        saveStateModalUtama = false;
      }
    }
  }
  modalUtamaBtnSave.addEventListener('click', saveModalUtama);
}
const editModalUtama = async (e) => {
  const data = dgUtama.table.row( { selected: true } ).data();
  modalUtamaTitle.innerHTML = 'Ubah Perusahaan';
  modalUtamaImageChange.classList.remove('d-none');
  if (!logo_perusahaan.classList.contains('d-none')) logo_perusahaan.classList.add('d-none');
  company_id.value = data.id_company;
  company_name.value = data.name_company;
  company_alias.value = data.alias_company;

  stat_group.checked = checkBooleanFromServer(data.stat_group);
  stat_customer.checked = checkBooleanFromServer(data.stat_customer);
  stat_supplier.checked = checkBooleanFromServer(data.stat_supplier);

  image_change1.checked = true;
  modalUtamaImageView.src = 'logo/' + data.logo_perusahaan;
  saveModalUtama = async (e) => {
    modalUtamaBtnSave.disabled = true;
    if (saveStateModalUtama === false) {
      saveStateModalUtama = true;
      try {
        const sendData = await getDataUtama();
        sendData.append('company_id', data.id_company);
        const getResponse = await sendViaFetchForm('route.php?act=editCompany', sendData);
        alertComponent.sendAnAlertOnTry(getResponse, closeModalUtama);
      } catch (error) {
        alertComponent.sendAnAlertOnCatch(error);
      } finally {
        modalUtamaBtnSave.disabled = false;
        saveStateModalUtama = false;
      }
    }
  }
  modalUtamaBtnSave.addEventListener('click', saveModalUtama);
}
const closeModalUtama = async () => {
  modalUtama.hide();
  dgUtama.table.ajax.reload();
}
const closeDeleteUtama = async () => {
  confirmComponent.confirmModal.hide();
  dgUtama.table.ajax.reload();
}

let saveModalDetail;
let saveStateModalDetail = false;
const resetModalDetail = () => {
  modalDetail._element.removeEventListener('shown.bs.modal', addModalDetail);
  modalDetail._element.removeEventListener('shown.bs.modal', editModalDetail);
  alertComponent.alertElem.removeEventListener('shown.bs.modal', closeModalDetail);
  modalDetailBtnSave.removeEventListener('click', saveModalDetail);
  resetInputExceptChoice(modalDetailForm);
  $('#section_id').val(null).trigger('change');
  $('#position_id').val(null).trigger('change');
  $('#plant_id').val(null).trigger('change');
}
const getDataDetail = async () => {
  const sendData = new FormData(modalDetailForm);
  return sendData;
}
const addModalDetail = async (e) => {
  modalDetailTitle.innerHTML = 'Tambah Struktur Baru';
  saveModalDetail = async (e) => {
    modalDetailBtnSave.disabled = true;
    if (saveStateModalDetail === false) {
      saveStateModalDetail = true;
      try {
        const sendData = await getDataDetail();
        sendData.append('company_id', dgDetail.sendingData.id_company);
        const getResponse = await sendViaFetchForm('../detail_company/route.php?act=addDetailCompany', sendData);
        alertComponent.sendAnAlertOnTry(getResponse, closeModalDetail);
      } catch (error) {
        alertComponent.sendAnAlertOnCatch(error);
      } finally {
        modalDetailBtnSave.disabled = false;
        saveStateModalDetail = false;
      }
    }
  }
  modalDetailBtnSave.addEventListener('click', saveModalDetail);
}
const editModalDetail = async (e) => {
  const data = dgDetail.table.row( { selected: true } ).data();
  modalDetailTitle.innerHTML = 'Ubah Struktur';
  det_company_id.value = data.id_det_company;
  // Set value section
  const optionSection = new Option(data.name_section, data.id_section, true, true);
  const dataSection = {};
  dataSection.id = data.id_section;
  dataSection.text = data.name_section;
  $('#section_id').append(optionSection).trigger('change');
  $('#section_id').trigger({
    type: 'select2:select',
    params: {
      data: dataSection
    }
  });
  // Set value position
  const optionPosition = new Option(data.name_position, data.id_position, true, true);
  const dataPosition = {};
  dataPosition.id = data.id_position;
  dataPosition.text = data.name_position;
  $('#position_id').append(optionPosition).trigger('change');
  $('#position_id').trigger({
    type: 'select2:select',
    params: {
      data: dataPosition
    }
  });
  // Set value plant
  const optionPlant = new Option(data.name_plant, data.id_plant, true, true);
  const dataPlant = {};
  dataPlant.id = data.id_plant;
  dataPlant.text = data.name_plant;
  $('#plant_id').append(optionPlant).trigger('change');
  $('#plant_id').trigger({
    type: 'select2:select',
    params: {
      data: dataPlant
    }
  });
  golongan.value = data.golongan;
  saveModalDetail = async (e) => {
    modalDetailBtnSave.disabled = true;
    if (saveStateModalDetail === false) {
      saveStateModalDetail = true;
      try {
        const sendData = await getDataDetail();
        sendData.append('det_company_id', data.id_det_company);
        sendData.append('company_id', dgDetail.sendingData.id_company);
        const getResponse = await sendViaFetchForm('../detail_company/route.php?act=editDetailCompany', sendData);
        alertComponent.sendAnAlertOnTry(getResponse, closeModalDetail);
      } catch (error) {
        alertComponent.sendAnAlertOnCatch(error);
      } finally {
        modalDetailBtnSave.disabled = false;
        saveStateModalDetail = false;
      }
    }
  }
  modalDetailBtnSave.addEventListener('click', saveModalDetail);
}
const closeModalDetail = async () => {
  modalDetail.hide();
  dgDetail.table.ajax.reload();
}
const closeDeleteDetail = async () => {
  confirmComponent.confirmModal.hide();
  dgDetail.table.ajax.reload();
}

const changeOptionViewModalUtama = (e) => {
  if (image_change3.checked || image_change1.checked) {
    if (!logo_perusahaan.classList.contains('d-none')) logo_perusahaan.classList.add('d-none');
  } else {
    logo_perusahaan.classList.remove('d-none');
  }
}

document.addEventListener("DOMContentLoaded", () => {

  image_change1.addEventListener('change', changeOptionViewModalUtama);
  image_change2.addEventListener('change', changeOptionViewModalUtama);
  image_change3.addEventListener('change', changeOptionViewModalUtama);

  dgUtama.btnReload.addEventListener('click', async () => {
    dgUtama.table.ajax.reload();
  });
  dgDetail.btnReload.addEventListener('click', async () => {
    dgDetail.table.ajax.reload();
  });

  modalDetail._element.addEventListener('show.bs.modal', () => {
    if (!modalDetailBackdrop.classList.contains('show')) modalDetailBackdrop.classList.add('show');
    modalDetailBackdrop.classList.remove('d-none');
  });
  modalDetail._element.addEventListener('hide.bs.modal', () => {
    modalDetailBackdrop.classList.remove('show');
    if (!modalDetailBackdrop.classList.contains('d-none')) modalDetailBackdrop.classList.add('d-none');
  });

  if (checkBooleanFromServer(accessModule.access_add)) {
    dgUtama.btnAdd.addEventListener('click', async () => {
      await resetModalUtama();
      modalUtama._element.addEventListener('shown.bs.modal', addModalUtama);
      modalUtama.show();
    });
    dgDetail.btnAdd.addEventListener('click', async () => {
      await resetModalDetail();
      modalDetail._element.addEventListener('shown.bs.modal', addModalDetail);
      modalDetail.show();
    });
  } else {
    dgUtama.btnAdd.disabled = true;
    dgDetail.btnAdd.disabled = true;
  }

  if (checkBooleanFromServer(accessModule.access_edit)) {
    dgUtama.btnEdit.addEventListener('click', async () => {
      const data = dgUtama.table.row( { selected: true } ).data();
      if (!IsEmpty(data)) {
        await resetModalUtama();
        modalUtama._element.addEventListener('shown.bs.modal', editModalUtama);
        modalUtama.show();
      } else {
        alertComponent.sendAnAlertOnCatch('Pilih data pada tabel terlebih dahulu!');
      }
    });
    dgDetail.btnEdit.addEventListener('click', async () => {
      const data = dgDetail.table.row( { selected: true } ).data();
      if (!IsEmpty(data)) {
        await resetModalDetail();
        modalDetail._element.addEventListener('shown.bs.modal', editModalDetail);
        modalDetail.show();
      } else {
        alertComponent.sendAnAlertOnCatch('Pilih data pada tabel terlebih dahulu!');
      }
    });
  } else {
    dgUtama.btnEdit.disabled = true;
    dgDetail.btnEdit.disabled = true;
  }

  if (checkBooleanFromServer(accessModule.access_delete)) {
    dgUtama.btnDelete.addEventListener('click', async () => {
      const data = dgUtama.table.row( { selected: true } ).data();
      if (!IsEmpty(data.id_company)) {
        confirmComponent.setupconfirm('Hapus Data', 'bg-danger', 'text-white', 'Apakah anda yakin ingin menghapus data tersebut?');
        confirmComponent.btnConfirm.addEventListener('click', async () => {
          if (saveStateModalUtama === false) {
            saveStateModalUtama = true;
            try {
              const sendData = new FormData();
              sendData.append('company_id', data.id_company);
              const getResponse = await sendViaFetchForm('route.php?act=deleteCompany', sendData);
              alertComponent.sendAnAlertOnTry(getResponse, closeDeleteUtama);
            } catch (error) {
              alertComponent.sendAnAlertOnCatch(error);
            } finally {
              saveStateModalUtama = false;
            }
          }
        });
        confirmComponent.btnCancel.addEventListener('click', () => {
          confirmComponent.confirmModal.hide();
        });
        confirmComponent.confirmModal.show();
      } else {
        alertComponent.sendAnAlertOnCatch('Pilih data pada tabel terlebih dahulu!');
      }
    });
    dgDetail.btnDelete.addEventListener('click', async () => {
      const data = dgDetail.table.row( { selected: true } ).data();
      if (!IsEmpty(data.id_det_company)) {
        confirmComponent.setupconfirm('Hapus Data', 'bg-danger', 'text-white', 'Apakah anda yakin ingin menghapus data tersebut?');
        confirmComponent.btnConfirm.addEventListener('click', async () => {
          if (saveStateModalDetail === false) {
            saveStateModalDetail = true;
            try {
              const sendData = new FormData();
              sendData.append('det_company_id', data.id_det_company);
              const getResponse = await sendViaFetchForm('../detail_company/route.php?act=deleteDetailCompany', sendData);
              alertComponent.sendAnAlertOnTry(getResponse, closeDeleteDetail);
            } catch (error) {
              alertComponent.sendAnAlertOnCatch(error);
            } finally {
              saveStateModalDetail = false;
            }
          }
        });
        confirmComponent.btnCancel.addEventListener('click', () => {
          confirmComponent.confirmModal.hide();
        });
        confirmComponent.confirmModal.show();
      } else {
        alertComponent.sendAnAlertOnCatch('Pilih data pada tabel terlebih dahulu!');
      }
    });
  } else {
    dgUtama.btnDelete.disabled = true;
    dgDetail.btnDelete.disabled = true;
  }

  if (checkBooleanFromServer(accessModule.access_print)) {
    // dgUtama.btnExcelDetail.addEventListener('click', async () => {});
    dgUtama.btnPDFDetail.addEventListener('click', async () => {});
  } else {
    dgUtama.btnMenuPrint.disabled = true;
    // dgUtama.btnExcelDetail.disabled = true;
    dgUtama.btnPDFDetail.disabled = true;
  }


  dgUtama.table.table().node().addEventListener('click', (e) => {
    if (e.target.tagName.toLowerCase() === 'td' && e.target.classList.contains('dt-control')) {
      const trElem = e.target.parentElement;
      const getRow = dgUtama.table.row(trElem);
      const dataRow = getRow.data();
      // const objKey = Object.keys(getRow.data());
      // const objValue = Object.values(getRow.data());
      dgDetail.sendingData.id_company = dataRow.id_company;
      dgDetail.table.ajax.reload();
      modalDetailViewIdCompany.innerHTML = dataRow.id_company;
      modalDetailViewNameCompany.innerHTML = dataRow.name_company;
      modalDetailView.show();
    }
  })

  dgUtama.filterTh.forEach((element, index) => {
    for (const eventType of ['keyup', 'change']) {
      element.addEventListener(eventType, (event) => {
        dgUtama.table.column(index + 1).search(event.target.value).draw();
      })
    }
  });

  dgDetail.filterTh.forEach((element, index) => {
    for (const eventType of ['keyup', 'change']) {
      element.addEventListener(eventType, (event) => {
        dgDetail.table.column(index).search(event.target.value).draw();
      })
    }
  });
  
});