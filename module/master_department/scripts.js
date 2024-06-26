import { IsEmpty, resetInputExceptChoice, sendViaFetchForm, AlertElemBS5, ConfirmElemBS5 } from '../../../third-party/utility-yudhi/utils.js';
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
          <a href="../all_report_detail/master_department_pdf.php" target="_blank" class="dropdown-item" id="btnPDFDetailDgUtama">
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
            url: 'route.php?act=getDepartment',
            type: 'POST'
          },
          columns: [
            {
              className: 'dt-control',
              orderable: false,
              data: null,
              defaultContent: '',
            },
            { data: 'id_department' },
            { data: 'name_department' },
            { data: 'alias_department' },
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
          }
        });
      })
  }
}

const btnElemDgDivisi = `
    <button type="button" id="btnAddDgDivisi" class="btn rounded btn-sm btn-add">
      <span class="d-inline-block ps-1">Tambah</span>
    </button>
    <button type="button" id="btnEditDgDivisi" class="btn rounded btn-sm btn-edit ms-2">
      <span class="d-inline-block ps-1">Ubah</span>
    </button>
    <button type="button" id="btnDeleteDgDivisi" class="btn rounded btn-sm btn-delete ms-2">
      <span class="d-inline-block ps-1">Hapus</span>
    </button>
    <button type="button" id="btnReloadDgDivisi" class="btn rounded btn-sm btn-reload ms-2"></button>
`;

class DgDivisi {
  constructor () {
    this.filterTh = [];
    this.dataDept = {};
    const getData = (d) => {
      return  $.extend(d, this.dataDept);
    }
    function createTable() {
      return new Promise((resolve, reject) => {
        const table = $('#dgDivisi').DataTable({
          autoWidth: true,
          select: {
            info: false,
            style: 'single'
          },
          // processing: true,
          serverSide: true,
          dom:`
            <'row'<'dgDivisiToolbar col-sm-12 col-lg-9 text-start pb-2 pb-lg-0'><'col-sm-12 col-lg-3'f>>
            <'d-block overflow-auto w-100 position-relative rounded-top mt-2'tr>
            <'row pt-2'<'col-sm-12 col-md-8 d-flex justify-content-start align-items-center'li><'col-sm-12 col-md-4'p>>
          `,
          ajax: {
            url: '../master_section/route.php?act=getSection',
            type: 'POST',
            data: getData
          },
          columns: [
            { data: 'id_section' },
            { data: 'name_section' },
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
        const rowToMove = document.getElementById('dgDivisiFilter');
        const thead = rowToMove.parentElement;
        thead.removeChild(rowToMove);
        thead.appendChild(rowToMove);
        $('div.dgDivisiToolbar').html(btnElemDgDivisi);
        $("#dgDivisi_length label").contents()[2].textContent = "";
      })
      .then(table => {
        const dgDivisi_length = document.getElementById('dgDivisi_length');
        dgDivisi_length.classList.add('pe-3');
        const dgDivisi_info = document.getElementById('dgDivisi_info');
        dgDivisi_info.classList.add('pt-0');
        this.btnAdd = document.getElementById('btnAddDgDivisi');
        this.btnEdit = document.getElementById('btnEditDgDivisi');
        this.btnDelete = document.getElementById('btnDeleteDgDivisi');
        this.btnReload = document.getElementById('btnReloadDgDivisi');
      })
      .then(table => {
        const filterDg = document.querySelectorAll('#dgDivisiFilter th');
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

const dgUtama = new DgUtama();
const alertComponent = new AlertElemBS5('alertComponent1');
const confirmComponent = new ConfirmElemBS5('confirmComponent1');
const modalUtama = bootstrap.Modal.getOrCreateInstance('#modalUtama');
const modalUtamaForm = document.getElementById('modalUtamaForm');
const modalUtamaTitle = document.getElementById('modalUtamaTitle');
const modalUtamaBtnSave = document.getElementById('modalUtamaBtnSave');

const modalDivisi = bootstrap.Modal.getOrCreateInstance('#modalDivisi');
const modalDivisiBackdrop = document.getElementById('modalDivisiBackdrop');
const modalDivisiForm = document.getElementById('modalDivisiForm');
const modalDivisiTitle = document.getElementById('modalDivisiTitle');
const modalDivisiBtnSave = document.getElementById('modalDivisiBtnSave');

const dgDivisi = new DgDivisi();
const modalDivisiView = bootstrap.Modal.getOrCreateInstance('#modalDivisiView');
const modalDivisiViewIdDepartment = document.getElementById('modalDivisiViewIdDepartment');
const modalDivisiViewNameDepartment = document.getElementById('modalDivisiViewNameDepartment');
const modalDivisiViewAliasDepartment = document.getElementById('modalDivisiViewAliasDepartment');

const department_id = document.getElementById('department_id');
const department_name = document.getElementById('department_name');
const department_alias = document.getElementById('department_alias');

const section_id = document.getElementById('section_id');
const section_name = document.getElementById('section_name');

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
  modalUtamaTitle.innerHTML = 'Tambah Departemen';
  saveModalUtama = async (e) => {
    modalUtamaBtnSave.disabled = true;
    if (saveStateModalUtama === false) {
      saveStateModalUtama = true;
      try {
        const sendData = await getDataUtama();
        const getResponse = await sendViaFetchForm('route.php?act=addDepartment', sendData);
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
  modalUtamaTitle.innerHTML = 'Ubah Departemen';
  department_id.value = data.id_department;
  department_name.value = data.name_department;
  department_alias.value = data.alias_department;
  saveModalUtama = async (e) => {
    modalUtamaBtnSave.disabled = true;
    if (saveStateModalUtama === false) {
      saveStateModalUtama = true;
      try {
        const sendData = await getDataUtama();
        sendData.append('department_id', data.id_department);
        const getResponse = await sendViaFetchForm('route.php?act=editDepartment', sendData);
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

let saveModalDivisi;
let saveStateModalDivisi = false;
const resetModalDivisi = () => {
  modalDivisi._element.removeEventListener('shown.bs.modal', addModalDivisi);
  modalDivisi._element.removeEventListener('shown.bs.modal', editModalDivisi);
  alertComponent.alertElem.removeEventListener('shown.bs.modal', closeModalDivisi);
  modalDivisiBtnSave.removeEventListener('click', saveModalDivisi);
  resetInputExceptChoice(modalDivisiForm);
}
const getDataDivisi = async () => {
  const sendData = new FormData(modalDivisiForm);
  return sendData;
}
const addModalDivisi = async (e) => {
  modalDivisiTitle.innerHTML = 'Tambah Divisi';
  saveModalDivisi = async (e) => {
    modalDivisiBtnSave.disabled = true;
    if (saveStateModalDivisi === false) {
      saveStateModalDivisi = true;
      try {
        const sendData = await getDataDivisi();
        sendData.append('department_id', dgDivisi.dataDept.id_department);
        const getResponse = await sendViaFetchForm('../master_section/route.php?act=addSection', sendData);
        alertComponent.sendAnAlertOnTry(getResponse, closeModalDivisi);
      } catch (error) {
        alertComponent.sendAnAlertOnCatch(error);
      } finally {
        modalDivisiBtnSave.disabled = false;
        saveStateModalDivisi = false;
      }
    }
  }
  modalDivisiBtnSave.addEventListener('click', saveModalDivisi);
}
const editModalDivisi = async (e) => {
  const data = dgDivisi.table.row( { selected: true } ).data();
  modalDivisiTitle.innerHTML = 'Ubah Divisi';
  section_id.value = data.id_section;
  section_name.value = data.name_section;
  saveModalDivisi = async (e) => {
    modalDivisiBtnSave.disabled = true;
    if (saveStateModalDivisi === false) {
      saveStateModalDivisi = true;
      try {
        const sendData = await getDataDivisi();
        sendData.append('section_id', data.id_section);
        sendData.append('department_id', dgDivisi.dataDept.id_department);
        const getResponse = await sendViaFetchForm('../master_section/route.php?act=editSection', sendData);
        alertComponent.sendAnAlertOnTry(getResponse, closeModalDivisi);
      } catch (error) {
        alertComponent.sendAnAlertOnCatch(error);
      } finally {
        modalDivisiBtnSave.disabled = false;
        saveStateModalDivisi = false;
      }
    }
  }
  modalDivisiBtnSave.addEventListener('click', saveModalDivisi);
}
const closeModalDivisi = async () => {
  modalDivisi.hide();
  dgDivisi.table.ajax.reload();
}
const closeDeleteDivisi = async () => {
  confirmComponent.confirmModal.hide();
  dgDivisi.table.ajax.reload();
}


document.addEventListener("DOMContentLoaded", () => {
  dgUtama.btnReload.addEventListener('click', async () => {
    dgUtama.table.ajax.reload();
  });
  dgDivisi.btnReload.addEventListener('click', async () => {
    dgDivisi.table.ajax.reload();
  });

  modalDivisi._element.addEventListener('show.bs.modal', () => {
    modalDivisiBackdrop.classList.add('show');
    modalDivisiBackdrop.classList.remove('d-none');
  });
  modalDivisi._element.addEventListener('hide.bs.modal', () => {
    modalDivisiBackdrop.classList.remove('show');
    modalDivisiBackdrop.classList.add('d-none');
  });

  if (accessModule.access_add === 't') {
    dgUtama.btnAdd.addEventListener('click', async () => {
      await resetModalUtama();
      modalUtama._element.addEventListener('shown.bs.modal', addModalUtama);
      modalUtama.show();
    });
    dgDivisi.btnAdd.addEventListener('click', async () => {
      await resetModalDivisi();
      modalDivisi._element.addEventListener('shown.bs.modal', addModalDivisi);
      modalDivisi.show();
    });
  } else {
    dgUtama.btnAdd.disabled = true;
    dgDivisi.btnAdd.disabled = true;
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
    dgDivisi.btnEdit.addEventListener('click', async () => {
      const data = dgDivisi.table.row( { selected: true } ).data();
      if (!IsEmpty(data)) {
        await resetModalDivisi();
        modalDivisi._element.addEventListener('shown.bs.modal', editModalDivisi);
        modalDivisi.show();
      } else {
        alertComponent.sendAnAlertOnCatch('Pilih data pada tabel terlebih dahulu!');
      }
    });
  } else {
    dgUtama.btnEdit.disabled = true;
    dgDivisi.btnEdit.disabled = true;
  }

  if (accessModule.access_delete === 't') {
    dgUtama.btnDelete.addEventListener('click', async () => {
      const data = dgUtama.table.row( { selected: true } ).data();
      if (!IsEmpty(data.id_department)) {
        confirmComponent.setupconfirm('Hapus Data', 'bg-danger', 'text-white', 'Apakah anda yakin ingin menghapus data tersebut?');
        confirmComponent.btnConfirm.addEventListener('click', async () => {
          if (saveStateModalUtama === false) {
            saveStateModalUtama = true;
            try {
              const sendData = new FormData();
              sendData.append('department_id', data.id_department);
              const getResponse = await sendViaFetchForm('route.php?act=deleteDepartment', sendData);
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
    dgDivisi.btnDelete.addEventListener('click', async () => {
      const data = dgDivisi.table.row( { selected: true } ).data();
      if (!IsEmpty(data.id_section)) {
        confirmComponent.setupconfirm('Hapus Data', 'bg-danger', 'text-white', 'Apakah anda yakin ingin menghapus data tersebut?');
        confirmComponent.btnConfirm.addEventListener('click', async () => {
          if (saveStateModalDivisi === false) {
            saveStateModalDivisi = true;
            try {
              const sendData = new FormData();
              sendData.append('section_id', data.id_section);
              const getResponse = await sendViaFetchForm('../master_section/route.php?act=deleteSection', sendData);
              alertComponent.sendAnAlertOnTry(getResponse, closeDeleteDivisi);
            } catch (error) {
              alertComponent.sendAnAlertOnCatch(error);
            } finally {
              saveStateModalDivisi = false;
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
    dgDivisi.btnDelete.disabled = true;
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
      dgDivisi.dataDept.id_department = dataRow.id_department;
      dgDivisi.table.ajax.reload();
      modalDivisiViewIdDepartment.innerHTML = dataRow.id_department;
      modalDivisiViewNameDepartment.innerHTML = dataRow.name_department;
      modalDivisiViewAliasDepartment.innerHTML = dataRow.alias_department;
      modalDivisiView.show();
    }
  })

  dgUtama.filterTh.forEach((element, index) => {
    for (const eventType of ['keyup', 'change']) {
      element.addEventListener(eventType, (event) => {
        dgUtama.table.column(index + 1).search(event.target.value).draw();
      })
    }
  });

  dgDivisi.filterTh.forEach((element, index) => {
    for (const eventType of ['keyup', 'change']) {
      element.addEventListener(eventType, (event) => {
        dgDivisi.table.column(index).search(event.target.value).draw();
      })
    }
  });
});