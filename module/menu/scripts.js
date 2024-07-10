import { AlertElemBS5, ConfirmElemBS5, IsEmpty, checkBooleanFromServer, resetInputExceptChoice, sendViaFetchForm } from '../../../third-party/utility-yudhi/utils.js';
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
    <button type="button" id="btnReloadDgUtama" class="btn rounded btn-sm btn-reload ms-2"></button>
`;

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
          // dom:`
          //   <'row'<'dgUtamaToolbar col-sm-12 col-lg-9 text-start pb-2 pb-lg-0'><'col-sm-12 col-lg-3'f>>
          //   <'d-block overflow-auto w-100 position-relative rounded-top mt-2'tr>
          //   <'row pt-2'<'col-sm-12 col-md-8 d-flex justify-content-start align-items-center'li><'col-sm-12 col-md-4'p>>
          // `,
          dom:`
            <'dgUtamaToolbar text-start pb-2 pb-lg-0'>
            <'d-block overflow-auto w-100 position-relative rounded-top mt-2'tr>
          `,
          ajax: {
            url: 'route.php?act=getMenu',
            type: 'POST'
          },
          columns: [
            {
              className: 'dt-control',
              // orderable: false,
              data: null,
              defaultContent: '',
            },
            {
              data: 'code_menu',
              // orderable: false,
            },
            {
              data: 'title_menu',
              render: function ( data, type, row, meta ) {
                return !IsEmpty(data) ? `
                  <span style="padding-left: ${(row.index_menu.split('.').length - 1) * 8}px;">(${row.index_menu}) ${data}</span>
                ` : '';
              },
              // orderable: false,
            },
            {
              className: 'text-center',
              data: 'icon_menu',
              render: function ( data, type, row, meta ) {
                return !IsEmpty(data) ? `<i class="${data}"></i>` : '';
              },
              // orderable: false,
            },
            {
              data: 'link_menu',
              // orderable: false,
            },
            {
              data: 'fullname_entry',
              className: 'text-center',
              // orderable: false,
            },
            {
              data: 'last_update',
              className: 'text-center',
              // orderable: false,
            }
          ],
          paging: false,
          // pageLength: 10,
          // lengthMenu: [ 10, 25, 50, 75, 100 ],
          ordering: false,
          order: [[1, 'asc']],
        });
        resolve(table);
      });
    }
    createTable()
      .then(table => {
        this.table = table;
        // const rowToMove = document.getElementById('dgUtamaFilter');
        // const thead = rowToMove.parentElement;
        // thead.removeChild(rowToMove);
        // thead.appendChild(rowToMove);
        $('div.dgUtamaToolbar').html(btnElemDgUtama);
        // $("#dgUtama_length label").contents()[2].textContent = "";
      })
      .then(table => {
        // const thDetailDgUtama = document.getElementById('thDetailDgUtama');
        // thDetailDgUtama.setAttribute('rowspan', '2');
        // const dgUtama_length = document.getElementById('dgUtama_length');
        // dgUtama_length.classList.add('pe-3');
        // const dgUtama_info = document.getElementById('dgUtama_info');
        // dgUtama_info.classList.add('pt-0');
        this.btnAdd = document.getElementById('btnAddDgUtama');
        this.btnEdit = document.getElementById('btnEditDgUtama');
        this.btnDelete = document.getElementById('btnDeleteDgUtama');
        this.btnReload = document.getElementById('btnReloadDgUtama');
      })
      .then(table => {
        // const filterDg = document.querySelectorAll('#dgUtamaFilter th');
        // filterDg.forEach((element, index, parentArr) => {
        //   if (element.classList.contains("filterTable")) {
        //     const title = element.textContent;
        //     const inputElement = document.createElement('input');
        //     inputElement.setAttribute('type', 'text');
        //     inputElement.setAttribute('class', 'form-control form-control-sm');
        //     inputElement.setAttribute('placeholder', `Saring ${title}`);
        //     element.innerHTML = '';
        //     element.appendChild(inputElement);
        //     this.filterTh[index] = inputElement;
        //   }
        // });
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
    this.dataDept = {};
    const getData = (d) => {
      return  $.extend(d, this.dataDept);
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
            url: '../menu_access/route.php?act=getMenuAccess&code_menu='+getData.code_menu,
            type: 'POST',
            data: getData
          },
          columns: [
            { data: 'code_maccess' },
            { data: 'name_maccess' },
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

const inputEvent = new Event('input');

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
const modalDetailViewKodeMenu = document.getElementById('modalDetailViewKodeMenu');
const modalDetailViewNameMenu = document.getElementById('modalDetailViewNameMenu');
const modalDetailViewIndexMenu = document.getElementById('modalDetailViewIndexMenu');

const code_menu = document.getElementById('code_menu');
const title_menu = document.getElementById('title_menu');
const index_menu = document.getElementById('index_menu');
const status_menu = document.getElementById('status_menu');
const turunan_akhir = document.getElementById('turunan_akhir');
const turunan_akhir_label = document.getElementById('turunan_akhir_label');
const link_menu = document.getElementById('link_menu');

const code_maccess = document.getElementById('code_maccess');
const name_maccess = document.getElementById('name_maccess');

let stateIndexMenu = true;

let saveModalUtama;
let saveStateModalUtama = false;
const resetModalUtama = () => {
  modalUtama._element.removeEventListener('shown.bs.modal', addModalUtama);
  modalUtama._element.removeEventListener('shown.bs.modal', editModalUtama);
  alertComponent.alertElem.removeEventListener('shown.bs.modal', closeModalUtama);
  modalUtamaBtnSave.removeEventListener('click', saveModalUtama);
  resetInputExceptChoice(modalUtamaForm);
  link_menu.disabled = true;
  turunan_akhir.checked = false;
  $('#icon_menu').val(null).trigger('change');
}
const getDataUtama = async () => {
  const sendData = new FormData(modalUtamaForm);
  return sendData;
}
const addModalUtama = async (e) => {
  modalUtamaTitle.innerHTML = 'Tambah Menu';
  saveModalUtama = async (e) => {
    modalUtamaBtnSave.disabled = true;
    if (saveStateModalUtama === false) {
      saveStateModalUtama = true;
      try {
        const sendData = await getDataUtama();
        const getResponse = await sendViaFetchForm('route.php?act=addMenu', sendData);
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
  modalUtamaTitle.innerHTML = 'Ubah Menu';
  code_menu.value = data.code_menu;
  title_menu.value = data.title_menu;
  index_menu.value = data.index_menu;
  // status_menu.value = data.status_menu;
  if (!IsEmpty(data.link_menu)) {
    turunan_akhir.checked = true;
    link_menu.value = data.link_menu;
    link_menu.disabled = false;
  }
  index_menu.dispatchEvent(inputEvent);
  $('#icon_menu').val(data.icon_menu).trigger('change');
  saveModalUtama = async (e) => {
    modalUtamaBtnSave.disabled = true;
    if (saveStateModalUtama === false) {
      saveStateModalUtama = true;
      try {
        const sendData = await getDataUtama();
        sendData.append('code_menu', data.code_menu);
        const getResponse = await sendViaFetchForm('route.php?act=editMenu', sendData);
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
}
const getDataDetail = async () => {
  const sendData = new FormData(modalDetailForm);
  return sendData;
}
const addModalDetail = async (e) => {
  modalDetailTitle.innerHTML = 'Tambah Detail';
  saveModalDetail = async (e) => {
    modalDetailBtnSave.disabled = true;
    if (saveStateModalDetail === false) {
      saveStateModalDetail = true;
      try {
        const sendData = await getDataDetail();
        sendData.append('code_menu', dgDetail.dataDept.code_menu);
        const getResponse = await sendViaFetchForm('../menu_access/route.php?act=addMenuAccess', sendData);
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
  modalDetailTitle.innerHTML = 'Ubah Detail';
  code_maccess.value = data.code_maccess;
  name_maccess.value = data.name_maccess;
  saveModalDetail = async (e) => {
    modalDetailBtnSave.disabled = true;
    if (saveStateModalDetail === false) {
      saveStateModalDetail = true;
      try {
        const sendData = await getDataDetail();
        sendData.append('code_maccess', data.code_maccess);
        sendData.append('code_menu', dgDetail.dataDept.code_menu);
        const getResponse = await sendViaFetchForm('../menu_access/route.php?act=editMenuAccess', sendData);
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

const templateSelectIcon = (state) => {
  const divElem = document.createElement('div');
  const iconElem = document.createElement('i');
  const spanElem = document.createElement('span');
  iconElem.setAttribute('class', state.id);
  spanElem.innerHTML = state.text;
  spanElem.setAttribute('class', 'ps-2');
  divElem.appendChild(iconElem);
  divElem.appendChild(spanElem);
  return !state.id ? state.text : divElem;
}
const createSelectIcon = async (data) => {
  $('#icon_menu').select2({
    theme: "bootstrap-5",
    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    placeholder: $( this ).data( 'placeholder' ),
    dropdownParent: $('#modalUtama'),
    allowClear: true,
    data: data,
    templateResult: templateSelectIcon,
    templateSelection: templateSelectIcon
  });
}

document.addEventListener("DOMContentLoaded", async () => {

  const sendDataIcon = new URLSearchParams();
  sendDataIcon.append('act', 'listSelect');
  const getResponseIcon = await sendViaFetchForm('../../../third-party/fontawesome-6/json_check.php?act=listSelect', sendDataIcon);
  await createSelectIcon(getResponseIcon.results);

  turunan_akhir.addEventListener('change', async (e) => {
    e.target.checked ? link_menu.disabled = false : link_menu.disabled = true;
  });

  // point yudhi
  index_menu.addEventListener('input', async (e) => {
    let value = e.target.value;
    let newValue = value.replace(/[^\d.]/g, '');
    e.target.value = newValue;
    if (!newValue.includes('.')) {
      status_menu.value = 'Induk Utama';
    } else if (newValue.slice(-1) == '.') {
      status_menu.value = '-- Tidak Valid --';
    } else if (stateIndexMenu) {
      stateIndexMenu = false;
      setTimeout(async () => {
        value = index_menu.value;
        newValue = value.replace(/[^\d.]/g, '');
        const sendData = new URLSearchParams();
        sendData.append('q', newValue);
        const getResponse = await sendViaFetchForm('route.php?act=searchIndexMenu', sendData);
        status_menu.value = getResponse.status;
        stateIndexMenu = true;
      }, 250);
    }
  })

  dgUtama.btnReload.addEventListener('click', async () => {
    dgUtama.table.ajax.reload();
  });
  dgDetail.btnReload.addEventListener('click', async () => {
    dgDetail.table.ajax.reload();
  });

  modalDetail._element.addEventListener('show.bs.modal', () => {
    modalDetailBackdrop.classList.add('show');
    modalDetailBackdrop.classList.remove('d-none');
  });
  modalDetail._element.addEventListener('hide.bs.modal', () => {
    modalDetailBackdrop.classList.remove('show');
    modalDetailBackdrop.classList.add('d-none');
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
      if (!IsEmpty(data.code_menu)) {
        confirmComponent.setupconfirm('Hapus Data', 'bg-danger', 'text-white', 'Apakah anda yakin ingin menghapus data tersebut?');
        confirmComponent.btnConfirm.addEventListener('click', async () => {
          if (saveStateModalUtama === false) {
            saveStateModalUtama = true;
            try {
              const sendData = new FormData();
              sendData.append('code_menu', data.code_menu);
              const getResponse = await sendViaFetchForm('route.php?act=deleteMenu', sendData);
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
      if (!IsEmpty(data.code_maccess)) {
        confirmComponent.setupconfirm('Hapus Data', 'bg-danger', 'text-white', 'Apakah anda yakin ingin menghapus data tersebut?');
        confirmComponent.btnConfirm.addEventListener('click', async () => {
          if (saveStateModalDetail === false) {
            saveStateModalDetail = true;
            try {
              const sendData = new FormData();
              sendData.append('code_maccess', data.code_maccess);
              const getResponse = await sendViaFetchForm('../menu_access/route.php?act=deleteMenuAccess', sendData);
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
  } else {
  }


  dgUtama.table.table().node().addEventListener('click', (e) => {
    if (e.target.tagName.toLowerCase() === 'td' && e.target.classList.contains('dt-control')) {
      const trElem = e.target.parentElement;
      const getRow = dgUtama.table.row(trElem);
      const dataRow = getRow.data();
      // const objKey = Object.keys(getRow.data());
      // const objValue = Object.values(getRow.data());
      dgDetail.dataDept.code_menu = dataRow.code_menu;
      dgDetail.table.ajax.reload();
      modalDetailViewKodeMenu.innerHTML = dataRow.code_menu;
      modalDetailViewNameMenu.innerHTML = dataRow.title_menu;
      modalDetailViewIndexMenu.innerHTML = dataRow.index_menu;
      modalDetailView.show();
    }
  })
});