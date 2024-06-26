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
          <a href="../all_report_detail/perspektif_pdf.php" target="_blank" class="dropdown-item" id="btnPDFDetailDgUtama">
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
            url: 'route.php?act=getPerspective',
            type: 'POST'
          },
          columns: [
            { data: 'id_perspective' },
            { data: 'name_perspective' },
            { data: 'index_perspective' },
            { data: 'alias_perspective' },
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
          order: [[2, 'asc']],
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

const perspective_id = document.getElementById('perspective_id');
const perspective_name = document.getElementById('perspective_name');
const perspective_index = document.getElementById('perspective_index');
const perspective_alias = document.getElementById('perspective_alias');

let saveModalUtama;
let saveStateModalUtama = false;
let stateIndexPerspective = null;

const resetModalUtama = async () => {
  modalUtama._element.removeEventListener('shown.bs.modal', addModalUtama);
  modalUtama._element.removeEventListener('shown.bs.modal', editModalUtama);
  modalUtamaBtnSave.removeEventListener('click', saveModalUtama);
  alertComponent.alertElem.removeEventListener('shown.bs.modal', closeModalUtama);
  resetInputExceptChoice(modalUtamaForm);
}

const getDataUtama = async () => {
  const sendData = new FormData(modalUtamaForm);
  return sendData;
}

const addModalUtama = async (e) => {
  modalUtamaTitle.innerHTML = 'Tambah Perspektif';
  saveModalUtama = async (e) => {
    modalUtamaBtnSave.disabled = true;
    if (saveStateModalUtama === false) {
      saveStateModalUtama = true;
      try {
        const sendData = await getDataUtama();
        const getResponse = await sendViaFetchForm('route.php?act=addPerspective', sendData);
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
  modalUtamaTitle.innerHTML = 'Ubah Perspektif';
  perspective_name.value = data.name_perspective;
  perspective_index.value = data.index_perspective;
  stateIndexPerspective = data.index_perspective;
  perspective_alias.value = data.alias_perspective;
  perspective_id.value = data.id_perspective;
  saveModalUtama = async (e) => {
    modalUtamaBtnSave.disabled = true;
    if (saveStateModalUtama === false) {
      saveStateModalUtama = true;
      try {
        const sendData = await getDataUtama();
        sendData.append('perspective_id', data.id_perspective);
        const getResponse = await sendViaFetchForm('route.php?act=editPerspective', sendData);
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

document.addEventListener("DOMContentLoaded", () => {
  
  dgUtama.btnReload.addEventListener('click', async () => {
    dgUtama.table.ajax.reload();
  });

  if (accessModule.access_add === 't') {
    dgUtama.btnAdd.addEventListener('click', async () => {
      await resetModalUtama();
      modalUtama._element.addEventListener('shown.bs.modal', addModalUtama);
      modalUtama.show();
    });
  } else {
    dgUtama.btnAdd.disabled = true;
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
  } else {
    dgUtama.btnEdit.disabled = true;
  }

  if (accessModule.access_delete === 't') {
    dgUtama.btnDelete.addEventListener('click', async () => {
      const data = dgUtama.table.row( { selected: true } ).data();
      if (!IsEmpty(data.id_perspective)) {
        confirmComponent.setupconfirm('Hapus Data', 'bg-danger', 'text-white', 'Apakah anda yakin ingin menghapus data tersebut?');
        confirmComponent.btnConfirm.addEventListener('click', async () => {
          if (saveStateModalUtama === false) {
            saveStateModalUtama = true;
            try {
              const sendData = new FormData();
              sendData.append('perspective_id', data.id_perspective);
              const getResponse = await sendViaFetchForm('route.php?act=deletePerspective', sendData);
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
    dgUtama.btnDelete.disabled = true;
  }

  if (accessModule.access_print === 't') {
    // dgUtama.btnExcelDetail.addEventListener('click', async () => {});
    dgUtama.btnPDFDetail.addEventListener('click', async () => {});
  } else {
    dgUtama.btnMenuPrint.disabled = true;
    // dgUtama.btnExcelDetail.disabled = true;
    dgUtama.btnPDFDetail.disabled = true;
  }

  dgUtama.filterTh.forEach((element, index) => {
    for (const eventType of ['keyup', 'change']) {
      element.addEventListener(eventType, (event) => {
        dgUtama.table.column(index).search(event.target.value).draw();
      })
    }
  });

  perspective_index.addEventListener('keyup', (e) => {
    const input = e.target;
    const value = input.value.trim();
    const numericRegex = /^[0-9]*$/;
    if (IsEmpty(value)) {
      input.value = null;
    } else if (!numericRegex.test(value)) {
      input.value = stateIndexPerspective;
    } else {
      stateIndexPerspective = input.value;
    }
  });

  modalUtama._element.addEventListener('hidden.bs.modal', (e) => {
    stateIndexPerspective = null;
  });
  
});