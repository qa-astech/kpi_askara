import { IsEmpty, resetInputExceptChoice, sendViaFetchForm, AlertElemBS5, ConfirmElemBS5 } from '../../../third-party/utility-yudhi/utils.js';

const showMenu = document.getElementById('showMenu');
const alertComponent = new AlertElemBS5('alertComponent1');
const accBox = document.querySelectorAll('.chkbx-toggle');

const getUserAccess = async (value) => {
  return new Promise(async (resolve, reject) => {
    const sendData = new FormData();
    sendData.append('nik', value);
    const getResult = await sendViaFetchForm('route.php?act=getUserAccess', sendData);
    if (!IsEmpty(getResult)) {
      resolve(getResult);
    } else {
      reject('Data tidak ditemukan!');
    }
  });
}

const showMenuFun = async (e) => {
  showMenu.innerHTML = null;
  if (e.target.value) {
    await getUserAccess(e.target.value)
    .then(jsonData => {
      for (const dataMenu of jsonData) {
        const divContainer = document.createElement('div');
        divContainer.classList.add('rounded', 'border', 'px-3', 'py-2', 'd-flex', 'justify-content-start', 'align-items-center', 'mb-3');
        divContainer.style.gap = '1rem';
        
        const titleMenu = document.createElement('div');
        titleMenu.innerHTML = `(${dataMenu.index_menu}) ${dataMenu.title_menu}`;
        titleMenu.style.fontSize = '1.15rem';
        const padLeft = (dataMenu.index_menu.split('.').length - 1) * 10;
        titleMenu.style.paddingLeft = padLeft.toString() + 'px';

        const listMenu = document.createElement('div');
        listMenu.classList.add('p-1', 'd-flex', 'flex-wrap', 'justify-content-center', 'align-items-start', 'ms-auto');
        listMenu.style.gap = '1rem';
        listMenu.style.maxWidth = '350px';
        let elementCk = '';
        for (const dataMaccess of dataMenu.menu_akses) {
          elementCk += `
            <div class='text-center'>
              <p class='fst-italic mb-2'>${dataMaccess.name_maccess}</p>
              <input type="checkbox" id="${dataMaccess.code_maccess}" class="chkbx-toggle" value="${dataMaccess.code_maccess}" ${dataMaccess.access_permission === 't' ? 'checked' : ''}>
              <label for="${dataMaccess.code_maccess}"></label>
            </div>
          `;
        }
        listMenu.innerHTML = elementCk;

        divContainer.append(titleMenu);
        divContainer.append(listMenu);
        showMenu.append(divContainer);
      }
    })
    .then(jsonData => {
      const getAllCheck = document.querySelectorAll('.chkbx-toggle');
      getAllCheck.forEach(element => {
        element.addEventListener('change', async (e) => {
          const sendData = new FormData();
          sendData.append('code_maccess', e.target.value);
          sendData.append('check_menu', e.target.checked);
          sendData.append('nik', $('#fullname').select2('data')[0].id );
          await sendViaFetchForm('route.php?act=editUserAccess', sendData);
        })
      });
    })
    .catch(error => {
      const errorMsg = error ?? 'Terjadi kesalahan, Coba beberapa saat lagi!';
      alertComponent.sendAnAlertOnCatch(errorMsg);
      console.error('An error occurred:', error);
    })
  }
}

document.addEventListener("DOMContentLoaded", async () => {
  $('#fullname').select2({
    theme: "bootstrap-5",
    width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
    placeholder: $(this).data('placeholder'),
    dropdownParent: $('#mainDiv'),
    allowClear: true,
    ajax: {
      url: "../users/route.php?act=jsonUsers",
      dataType: 'json',
      method: 'POST',
      delay: 250,
      data: function(params) {
        return {
          q: params.term,
          page: params.page || 1
        };
      },
      processResults: function(data) {
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
  $('#fullname').on('change', showMenuFun);
});