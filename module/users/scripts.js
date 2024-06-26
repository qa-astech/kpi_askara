import { IsEmpty, resetInputExceptChoice, sendViaFetchForm, AlertElemBS5, ConfirmElemBS5 } from '../../../third-party/utility-yudhi/utils.js';
$.fn.dataTable.ext.errMode = 'none';

const btnElemDgUtama = `
    <button type="button" id="btnAddDgUtama" class="btn rounded btn-sm btn-add">
      <span class="d-inline-block ps-1">Tambah</span>
    </button>
    <button type="button" id="btnEditDgUtama" class="btn rounded btn-sm btn-edit ms-2">
      <span class="d-inline-block ps-1">Ubah</span>
    </button>
    <button type="button" id="btnResetDgUtama" class="btn rounded btn-sm btn-reload ms-2">
      <span class="d-inline-block ps-1">Reset Password</span>
    </button>
    <div class="btn-group ms-2" role="group" aria-label="Button Print" id="btnMenuPrintDgUtama">
      <button type="button" class="btn btn-sm btn-print dropdown-toggle" data-bs-toggle="dropdown" data-bs-target="#menuPrintDgUtama" aria-expanded="false">
        <span class="d-inline-block ps-1">Cetak</span>
      </button>
      <ul class="dropdown-menu dropdown-print" id="menuPrintDgUtama">
        <li>
          <a href="../all_report_detail/users_pdf.php" target="_blank" class="dropdown-item" id="btnPDFDetailDgUtama">
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
            url: 'route.php?act=getUsers',
            type: 'POST'
          },
          columns: [
            {
              className: 'dt-control',
              orderable: false,
              data: null,
              defaultContent: '',
            },
            { data: 'nik_users' },
            { data: 'username_users' },
            { data: 'fullname_users' },
            {
              data: 'user_entry',
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
        this.btnResetPass = document.getElementById('btnResetDgUtama');
        // this.btnDelete = document.getElementById('btnDeleteDgUtama');
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
            url: '../users_setup/route.php?act=getUsersSetup',
            type: 'POST',
            data: getData
          },
          columns: [
            { data: 'id_usersetup' },
            { data: 'name_company' },
            { data: 'name_department' },
            { data: 'name_section' },
            {
              data: 'name_position',
              className: 'text-center'
            },
            {
              data: 'name_plant',
              className: 'text-center',
              render: function ( data, type, row, meta ) {
                return data ?? '-';
              }
            },
            {
              data: 'golongan',
              className: 'text-center'
            },
            {
              data: 'status_active',
              className: 'text-center',
              render: function ( data, type, row, meta ) {
                return data === "t" ? `<i class="fa-solid fa-check text-success"></i>` : `<i class="fa-solid fa-xmark text-danger"></i>`;
              }
            },
            {
              data: 'role_utama',
              className: 'text-center',
              render: function ( data, type, row, meta ) {
                return data === "t" ? `<i class="fa-solid fa-check text-success"></i>` : `<i class="fa-solid fa-xmark text-danger"></i>`;
              }
            },
            {
              data: 'user_entry',
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
        // this.btnDelete = document.getElementById('btnDeleteDgDetail');
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

const modalDetail = bootstrap.Modal.getOrCreateInstance('#modalDetail');
const modalDetailBackdrop = document.getElementById('modalDetailBackdrop');
const modalDetailForm = document.getElementById('modalDetailForm');
const modalDetailTitle = document.getElementById('modalDetailTitle');
const modalDetailBtnSave = document.getElementById('modalDetailBtnSave');

const dgDetail = new DgDetail();
const modalDetailView = bootstrap.Modal.getOrCreateInstance('#modalDetailView');
const modalDetailViewNik = document.getElementById('modalDetailViewNik');
const modalDetailViewFullname = document.getElementById('modalDetailViewFullname');

const users_nik = document.getElementById('users_nik');
const users_username = document.getElementById('users_username');
const users_fullname = document.getElementById('users_fullname');
const users_password = document.getElementById('users_password');
const users_confirm_password = document.getElementById('users_confirm_password');

const status_active1 = document.getElementById('status_active1');
const status_active2 = document.getElementById('status_active2');
const users_role_utama = document.getElementById('users_role_utama');
const checkDivCorps = document.getElementById('checkDivCorps');

let saveModalUtama;
let saveStateModalUtama = false;
let saveModalDetail;
let saveStateModalDetail = false;

let state_users_nik = null;
let state_users_username = null;

const clearSelectCompany = (e) => {
  clearSelectSection();
  $('#users_section').val(null).trigger('change');
}
const clearSelectSection = (e) => {
  clearSelectPosition();
  $('#users_position').val(null).trigger('change');
}
const clearSelectPosition = (e) => {
  clearSelectPlant();
  $('#users_plant').val(null).trigger('change');
}
const clearSelectPlant = (e) => {
  $('#users_golongan').val(null).trigger('change');
}

$('#users_company').select2({
  theme: "bootstrap-5",
  width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
  placeholder: $( this ).data( 'placeholder' ),
  dropdownParent: $('#modalDetail'),
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
$('#users_company').on('select2:select', clearSelectCompany);
$('#users_company').on('select2:unselect', clearSelectCompany);
$('#users_company').on('select2:clear', clearSelectCompany);

$('#users_section').select2({
  theme: "bootstrap-5",
  width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
  placeholder: $( this ).data( 'placeholder' ),
  dropdownParent: $('#modalDetail'),
  allowClear: true,
  ajax: {
    url: "../master_company/route.php?act=searchSection",
    dataType: 'json',
    method: 'POST',
    delay: 250,
    data: function (params) {
      return {
        q: params.term,
        page: params.page || 1,
        id_company: $('#users_company').val()
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
  templateSelection: (state) => {
    return state.templateText ?? state.text;
  }
});
$('#users_section').on('select2:select', clearSelectSection);
$('#users_section').on('select2:unselect', clearSelectSection);
$('#users_section').on('select2:clear', clearSelectSection);

$('#users_position').select2({
  theme: "bootstrap-5",
  width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
  placeholder: $( this ).data( 'placeholder' ),
  dropdownParent: $('#modalDetail'),
  allowClear: true,
  ajax: {
    url: "../master_company/route.php?act=searchPosition",
    dataType: 'json',
    method: 'POST',
    delay: 250,
    data: function (params) {
      return {
        q: params.term,
        page: params.page || 1,
        id_company: $('#users_company').val(),
        id_section: $('#users_section').val()
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
$('#users_position').on('select2:select', clearSelectPosition);
$('#users_position').on('select2:unselect', clearSelectPosition);
$('#users_position').on('select2:clear', clearSelectPosition);

$('#users_plant').select2({
  theme: "bootstrap-5",
  width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
  placeholder: $( this ).data( 'placeholder' ),
  dropdownParent: $('#modalDetail'),
  allowClear: true,
  ajax: {
    url: "../master_company/route.php?act=searchPlant",
    dataType: 'json',
    method: 'POST',
    delay: 250,
    data: function (params) {
      return {
        q: params.term,
        page: params.page || 1,
        id_company: $('#users_company').val(),
        id_section: $('#users_section').val(),
        id_position: $('#users_position').val()
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
$('#users_plant').on('select2:select', clearSelectPlant);
$('#users_plant').on('select2:unselect', clearSelectPlant);
$('#users_plant').on('select2:clear', clearSelectPlant);

$('#users_golongan').select2({
  theme: "bootstrap-5",
  width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
  placeholder: $( this ).data( 'placeholder' ),
  dropdownParent: $('#modalDetail'),
  allowClear: true,
  ajax: {
    url: "../master_company/route.php?act=searchGolongan",
    dataType: 'json',
    method: 'POST',
    delay: 250,
    data: function (params) {
      return {
        q: params.term,
        page: params.page || 1,
        id_company: $('#users_company').val(),
        id_section: $('#users_section').val(),
        id_position: $('#users_position').val(),
        id_plant: $('#users_plant').val()
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

$('#users_section_corps').select2({
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
  templateSelection: (state) => {
    return state.templateText ?? state.text;
  }
});
$('#users_position_corps').select2({
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


const resetModalUtama = () => {
  modalUtama._element.removeEventListener('shown.bs.modal', addModalUtama);
  modalUtama._element.removeEventListener('shown.bs.modal', editModalUtama);
  alertComponent.alertElem.removeEventListener('shown.bs.modal', closeModalUtama);
  modalUtamaBtnSave.removeEventListener('click', saveModalUtama);
  resetInputExceptChoice(modalUtamaForm);
  users_password.disabled = false;
  users_confirm_password.disabled = false;
}
const addModalUtama = async (e) => {
  modalUtamaTitle.innerHTML = 'Tambah Pengguna';
  state_users_nik = null;
  state_users_username = null;
  saveModalUtama = async (e) => {
    modalUtamaBtnSave.disabled = true;
    if (saveStateModalUtama === false) {
      saveStateModalUtama = true;
      try {
        const sendData = new FormData(modalUtamaForm);
        const getResponse = await sendViaFetchForm('route.php?act=addUsers', sendData);
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
  state_users_nik = null;
  state_users_username = null;
  const data = dgUtama.table.row( { selected: true } ).data();
  modalUtamaTitle.innerHTML = 'Ubah Pengguna';
  users_nik.value = data.nik_users;
  users_username.value = data.username_users;
  users_fullname.value = data.fullname_users;
  users_password.disabled = true;
  users_confirm_password.disabled = true;
  saveModalUtama = async (e) => {
    modalUtamaBtnSave.disabled = true;
    if (saveStateModalUtama === false) {
      saveStateModalUtama = true;
      try {
        const sendData = new FormData(modalUtamaForm);
        sendData.append('users_nik_before', data.nik_users);
        const getResponse = await sendViaFetchForm('route.php?act=editUsers', sendData);
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

const resetModalDetail = () => {
  modalDetail._element.removeEventListener('shown.bs.modal', addModalDetail);
  modalDetail._element.removeEventListener('shown.bs.modal', editModalDetail);
  alertComponent.alertElem.removeEventListener('shown.bs.modal', closeModalDetail);
  modalDetailBtnSave.removeEventListener('click', saveModalDetail);
  resetInputExceptChoice(modalDetailForm);
  $('#users_company').val(null).trigger('change');
  $('#users_section').val(null).trigger('change');
  $('#users_position').val(null).trigger('change');
  $('#users_plant').val(null).trigger('change');
  $('#users_golongan').val(null).trigger('change');
  status_active1.checked = false;
  status_active2.checked = false;
  users_role_utama.checked = false;
  checkDivCorps.checked = false;
  checkDivCorps.disabled = false;
  changeModeForm(false);
}
const addModalDetail = async (e) => {
  modalDetailTitle.innerHTML = 'Peran Kerja';
  saveModalDetail = async (e) => {
    modalDetailBtnSave.disabled = true;
    if (saveStateModalDetail === false) {
      saveStateModalDetail = true;
      try {
        const sendData = new FormData(modalDetailForm);
        sendData.append('nik', dgDetail.sendingData.nik_users);
        const getResponse = await sendViaFetchForm('../users_setup/route.php?act=addUsersSetup', sendData);
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
  modalDetailTitle.innerHTML = 'Mengubah Peran Kerja';

  changeModeForm(data.status_users === 'corps');
  checkDivCorps.disabled = true;
  if (data.status_users === 'corps') {
    checkDivCorps.checked = true;
    const optionSection = new Option(data.name_section, data.id_section, true, true);
    const dataSection = {};
    dataSection.id = data.id_section;
    dataSection.text = data.name_section;
    $('#users_section_corps').append(optionSection).trigger('change');
    $('#users_section_corps').trigger({
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
    $('#users_position_corps').append(optionPosition).trigger('change');
    $('#users_position_corps').trigger({
      type: 'select2:select',
      params: {
        data: dataPosition
      }
    });

    const users_golongan_corps = document.getElementById('users_golongan_corps');
    users_golongan_corps.value = data.golongan;
  } else {
    checkDivCorps.checked = false;
    // Set value company
    const optionCompany = new Option(data.name_company, data.id_company, true, true);
    const dataCompany = {};
    dataCompany.id = data.id_company;
    dataCompany.text = data.name_company;
    $('#users_company').append(optionCompany).trigger('change');
    $('#users_company').trigger({
      type: 'select2:select',
      params: {
        data: dataCompany
      }
    });
  
    // Set value section
    const optionSection = new Option(data.name_section, data.id_section, true, true);
    const dataSection = {};
    dataSection.id = data.id_section;
    dataSection.text = data.name_section;
    $('#users_section').append(optionSection).trigger('change');
    $('#users_section').trigger({
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
    $('#users_position').append(optionPosition).trigger('change');
    $('#users_position').trigger({
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
    $('#users_plant').append(optionPlant).trigger('change');
    $('#users_plant').trigger({
      type: 'select2:select',
      params: {
        data: dataPlant
      }
    });
  
    // Set value golongan
    const optionGolongan = new Option(data.golongan, data.golongan, true, true);
    const dataGolongan = {};
    dataGolongan.id = data.golongan;
    dataGolongan.text = data.golongan;
    $('#users_golongan').append(optionGolongan).trigger('change');
    $('#users_golongan').trigger({
      type: 'select2:select',
      params: {
        data: dataGolongan
      }
    });
  }
  data.status_active === 't' ? status_active2.checked = true : status_active1.checked = true;
  users_role_utama.checked = data.role_utama === 't';

  saveModalDetail = async (e) => {
    modalDetailBtnSave.disabled = true;
    if (saveStateModalDetail === false) {
      saveStateModalDetail = true;
      try {
        const sendData = new FormData(modalDetailForm);
        sendData.append('nik', dgDetail.sendingData.nik_users);
        sendData.append('id_usersetup', data.id_usersetup);
        const getResponse = await sendViaFetchForm('../users_setup/route.php?act=editUsersSetup', sendData);
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

const changeModeForm = (valDiff = false) => {
  const formDefault = document.querySelectorAll('div.form-default');
  const formCorps = document.querySelectorAll('div.form-corps');
  $("#users_company").prop('disabled', valDiff);
  if (valDiff) {
    formDefault.forEach(element => {
      element.classList.add('d-none');
    });
    formCorps.forEach(element => {
      element.classList.remove('d-none');
    });
  } else {
    formDefault.forEach(element => {
      element.classList.remove('d-none');
    });
    formCorps.forEach(element => {
      element.classList.add('d-none');
    });
  }
}

document.addEventListener("DOMContentLoaded", () => {

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

  checkDivCorps.addEventListener('change', (e) => {
    changeModeForm(e.target.checked);
  })

  if (accessModule.access_add === 't') {
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

  if (accessModule.access_edit === 't') {
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
    dgUtama.btnResetPass.addEventListener('click', async () => {
      const data = dgUtama.table.row( { selected: true } ).data();
      if (!IsEmpty(data.nik_users)) {
        confirmComponent.setupconfirm('Reset Password', 'bg-warning', 'text-dark-emphasis', 'Apakah anda yakin ingin mengatur ulang kata sandi akun tersebut?');
        confirmComponent.btnConfirm.addEventListener('click', async () => {
          if (saveStateModalUtama === false) {
            saveStateModalUtama = true;
            try {
              const sendData = new FormData();
              sendData.append('users_nik', data.nik_users);
              const getResponse = await sendViaFetchForm('route.php?act=resetUsers', sendData);
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
  } else {
    dgUtama.btnEdit.disabled = true;
    dgDetail.btnEdit.disabled = true;
    dgUtama.btnResetPass.disabled = true;
  }

  if (accessModule.access_delete === 't') {
    // dgUtama.btnDelete.addEventListener('click', async () => {
    //   const data = dgUtama.table.row( { selected: true } ).data();
    //   if (!IsEmpty(data.nik_users)) {
    //     confirmComponent.setupconfirm('Hapus Data', 'bg-danger', 'text-white', 'Apakah anda yakin ingin menghapus data tersebut?');
    //     confirmComponent.btnConfirm.addEventListener('click', async () => {
    //       if (saveStateModalUtama === false) {
    //         saveStateModalUtama = true;
    //         try {
    //           const sendData = new FormData();
    //           sendData.append('users_nik', data.nik_users);
    //           const getResponse = await sendViaFetchForm('route.php?act=deleteUsers', sendData);
    //           alertComponent.sendAnAlertOnTry(getResponse, closeDeleteUtama);
    //         } catch (error) {
    //           alertComponent.sendAnAlertOnCatch(error);
    //         } finally {
    //           saveStateModalUtama = false;
    //         }
    //       }
    //     });
    //     confirmComponent.btnCancel.addEventListener('click', () => {
    //       confirmComponent.confirmModal.hide();
    //     });
    //     confirmComponent.confirmModal.show();
    //   } else {
    //     alertComponent.sendAnAlertOnCatch('Pilih data pada tabel terlebih dahulu!');
    //   }
    // });
    // dgDetail.btnDelete.addEventListener('click', async () => {
    //   const data = dgDetail.table.row( { selected: true } ).data();
    //   if (!IsEmpty(data.id_usersetup)) {
    //     confirmComponent.setupconfirm('Hapus Data', 'bg-danger', 'text-white', 'Apakah anda yakin ingin menghapus data tersebut?');
    //     confirmComponent.btnConfirm.addEventListener('click', async () => {
    //       if (saveStateModalDetail === false) {
    //         saveStateModalDetail = true;
    //         try {
    //           const sendData = new FormData();
    //           sendData.append('id_usersetup', data.id_usersetup)
    //           const getResponse = await sendViaFetchForm('../users_setup/route.php?act=deleteUsersSetup', sendData);
    //           alertComponent.sendAnAlertOnTry(getResponse, closeDeleteDetail);
    //         } catch (error) {
    //           alertComponent.sendAnAlertOnCatch(error);
    //         } finally {
    //           saveStateModalDetail = false;
    //         }
    //       }
    //     });
    //     confirmComponent.btnCancel.addEventListener('click', () => {
    //       confirmComponent.confirmModal.hide();
    //     });
    //     confirmComponent.confirmModal.show();
    //   } else {
    //     alertComponent.sendAnAlertOnCatch('Pilih data pada tabel terlebih dahulu!');
    //   }
    // });
  } else {
    // dgUtama.btnDelete.disabled = true;
    // dgDetail.btnDelete.disabled = true;
  }

  if (accessModule.access_print === 't') {
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
      dgDetail.sendingData.nik_users = dataRow.nik_users;
      dgDetail.table.ajax.reload();
      modalDetailViewNik.innerHTML = dataRow.nik_users;
      modalDetailViewFullname.innerHTML = dataRow.fullname_users;
      modalDetailView.show();
    }
  });

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

  // target_kpibunit.addEventListener('focusout', (e) => {
  //   const input = e.target;
  //   const value = input.value.trim().replace(/,/g, "");
  //   const numericDotRegex = /^[0-9.]*$/;
  //   const realValue = number_format_big(value, 2, '.', ',');
  //   if (IsEmpty(value) && parseInt(value) !== 0) {
  //     input.value = null;
  //   } else if (parseInt(value) === 0) {
  //     input.value = 0;
  //   } else if (!numericDotRegex.test(value)) {
  //     input.value = stateTargetInput[index];
  //   } else {
  //     stateTargetInput[index] = realValue;
  //     input.value = realValue;
  //   }
  // });

  users_nik.addEventListener('keyup', (e) => {
    const input = e.target;
    const value = input.value;
    const regexCheck = /^[a-zA-Z0-9]{1,}$/;
    if (regexCheck.test(value)) {
      state_users_nik = value;
    } else {
      if (value && value !== "") {
        input.value = state_users_nik;
      }
    }
  });
  users_username.addEventListener('keyup', (e) => {
    const input = e.target;
    const value = input.value;
    const regexCheck = /^[a-zA-Z0-9 ]{1,}$/;
    if (regexCheck.test(value) && value && value !== "") {
      state_users_username = value;
    } else {
      if (value && value !== "") {
        input.value = state_users_username;
      }
    }
  });

  // target_kpibunit.addEventListener('focusin', (e) => {
  //   const input = e.target;
  //   const value = input.value.trim().replace(/,/g, "");
  //   input.value = value;
  // });

});