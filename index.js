import { sendViaFetchForm, AlertElemBS5, ConfirmElemBS5 } from "../third-party/utility-yudhi/utils.js";
import implanChangeRole from "./change_role.js";

const seePass = document.querySelectorAll('.seePass');
const resetPassModal = document.getElementById('resetPassModal');
const formResetPass = document.getElementById('formResetPass');
const old_password = document.getElementById('old_password');
const new_password = document.getElementById('new_password');
const confirm_password = document.getElementById('confirm_password');
const saveResetPass = document.getElementById('saveResetPass');
const detailRoleModal = document.getElementById('detailRoleModal');
const navbarImage1 = document.getElementById('navbarImage1');
const offcanvasNavbar = document.getElementById('offcanvasNavbar');
const navbarImage2 = document.getElementById('navbarImage2');
const nameNavbar = document.getElementById('nameNavbar');
const companyNavbar = document.getElementById('companyNavbar');
const detailInfoBtn = document.getElementById('detailInfoBtn');
const resetPassBtn = document.getElementById('resetPassBtn');
const logoutBtn = document.getElementById('logoutBtn');
const changeRoleBtn = document.getElementById('changeRoleBtn');
const mainContent = document.getElementById('mainContent');
const tabMenu = document.getElementById('tabMenu');
const tabContent = document.getElementById('tabContent');
const tabPanel = document.getElementById('tabPanel');
const roleName = document.getElementById('roleName');
const roleCompany = document.getElementById('roleCompany');
const rolePosition = document.getElementById('rolePosition');
const roleDepartment = document.getElementById('roleDepartment');
const roleSection = document.getElementById('roleSection');
const roleGolongan = document.getElementById('roleGolongan');
const listMenu = document.getElementById('listMenu');

const bsModalReset = new bootstrap.Modal(resetPassModal);
const bsModalDetailRole = new bootstrap.Modal(detailRoleModal);
const alertComponent = new AlertElemBS5('alertInformation');
const confirmComponent = new ConfirmElemBS5('confirmComponent');

let saveStateResetPass = false;

const seePassword = (elem) => {
  const e = elem.target.type !== 'button' ? elem.target.parentElement : elem.target;
  if (e.previousElementSibling.type === 'password') {
    e.firstElementChild.classList.add('fa-eye');
    e.firstElementChild.classList.remove('fa-eye-slash');
    e.previousElementSibling.type = 'text';
  } else {
    e.firstElementChild.classList.remove('fa-eye');
    e.firstElementChild.classList.add('fa-eye-slash');
    e.previousElementSibling.type = 'password';
  }
}

const funSaveResetPass = async () => {
  saveResetPass.disabled = true;
  if (saveStateResetPass === false) {
    saveStateResetPass = true;
    try {
      const sendData = new FormData(formResetPass);
      const getResponse = await sendViaFetchForm('logAttempt.php?act=resetpass', sendData);
      alertComponent.sendAnAlertOnTry(getResponse, () => {
        location.reload();
      });
    } catch (error) {
      alertComponent.sendAnAlertOnCatch(error);
    } finally {
      saveResetPass.disabled = false;
      saveStateResetPass = false;
    }
  }
}

const closeAlertComp = async () => {
  window.open("login.php", "_self");
}

const templateMenu = async (code_menu, icon_menu, title_menu, padLeft = 0) => `
<div class="border-bottom" id="button-${code_menu}">
  <a class="nav-link py-3 menu-navbar-button" style="padding-left: calc(1rem + ${padLeft}px);" href="#">
    <i class="${icon_menu}"></i>
    <span>${title_menu}</span>
  </a>
</div>
`;

const closeAllIframe = async () => {
  const listPanels = tabPanel.querySelectorAll('iframe');
  if (listPanels.length > 0) {
    listPanels.forEach(element => {
      if (!element.classList.contains('d-none')) {
        element.classList.add('d-none');
      }
    });
  }
}

const closeDeselectTabs = async () => {
  const listTabs = tabMenu.querySelectorAll('li > a.active');
  if (listTabs.length > 0) {
    listTabs.forEach(element => {
      element.classList.remove('active');
    });
  }
}

const checkIframe = async (id) => {
  const listPanels = tabPanel.querySelectorAll('iframe');
  if (listPanels.length > 0) {
    for (const element of listPanels) {
      if (element.dataset.id === id) {
        return element;
      }
    }
    return false;
  } else {
    return false;
  }
}

const checkTabs = async (id) => {
  const listTabs = tabMenu.querySelectorAll('li > a');
  if (listTabs.length > 0) {
    for (const element of listTabs) {
      if (element.dataset.id === id) {
        return element;
      }
    }
    return false;
  } else {
    return false;
  }
}

const openIframe = async (element, dataMenu) => {
  if (element) {
    element.classList.remove('d-none');
  } else {
    const createIframe = document.createElement('iframe');
    createIframe.setAttribute('src', dataMenu.link_menu);
    createIframe.setAttribute('frameborder', 0);
    createIframe.classList.add('w-100', 'h-100');
    createIframe.dataset.id = dataMenu.code_menu;
    tabPanel.append(createIframe);
  }
}

const openTab = async (elementTab, dataMenu) => {
  if (elementTab) {
    elementTab.classList.add('active');
  } else {
    const liElem = document.createElement('li');
    liElem.setAttribute('role', 'presentation');
    liElem.classList.add('nav-item');

    const spanElem = document.createElement('span');
    spanElem.innerHTML = dataMenu.title_menu;

    const iElem = document.createElement('i');
    iElem.classList.add('fa-solid', 'fa-xmark', 'text-end');
    iElem.addEventListener('click', async (e) => {
      e.stopImmediatePropagation();
      if (liElem) {
        liElem.remove();
      }
      const elementIframe = await checkIframe(dataMenu.code_menu);
      if (elementIframe) {
        elementIframe.remove();
      }
    });

    const aElem = document.createElement('a');
    aElem.setAttribute('aria-current', 'page');
    aElem.setAttribute('href', '#');
    aElem.classList.add('nav-link', 'active');
    aElem.dataset.id = dataMenu.code_menu;
    aElem.addEventListener('click', async (e) => {
      e.preventDefault();
      await closeAllIframe();
      await closeDeselectTabs();
      aElem.classList.add('active');
      const elementIframe = await checkIframe(dataMenu.code_menu);
      await openIframe(elementIframe, dataMenu);
    });
    aElem.append(spanElem);
    aElem.append(iElem);

    liElem.append(aElem);
    tabMenu.append(liElem);
  }
}

document.addEventListener("DOMContentLoaded", async () => {

  const getUsers = await sendViaFetchForm('logAttempt.php?act=getSessionSimple');
  let dataUsers = {};
  if (getUsers.response === 'error') {
    await closeAlertComp();
  } else {
    dataUsers = getUsers.data;
  }

  let menuHtml = '';
  const getMenu = await sendViaFetchForm('module/user_access/route.php?act=getMenuAccess');
  if (getMenu.length > 0) {
    for (const dataMenu of getMenu) {
      const str = dataMenu.index_menu;
      const dotCount = (str.split('.').length - 1);
      menuHtml += await templateMenu(dataMenu.code_menu, dataMenu.icon_menu, dataMenu.title_menu, (dotCount * 12));
    }
    listMenu.innerHTML = menuHtml;

    // onClick child
    for (const dataMenu of getMenu) {
      const divMenu = document.getElementById('button-' + dataMenu.code_menu);
      const button = divMenu.querySelector('a');
      const getChild = getMenu.filter((dataChild) => dataChild.indexParent === dataMenu.index_menu);

      if (getChild.length > 0) {
        button.classList.add('folder-button-nav-right', 'position-relative', 'nav-link-folder');
        button.style.paddingRight = '2.65rem';

        const clearClassFolderButton = () => {
          button.classList.remove('folder-button-nav-right', 'folder-button-nav-down');
        }
        button.addEventListener('click', (e) => {
          e.preventDefault();
          for (const dataChild of getChild) {
            const divMenuChild = document.getElementById('button-' + dataChild.code_menu);
            divMenuChild.classList.contains('d-none') ? divMenuChild.classList.remove('d-none') : divMenuChild.classList.add('d-none')
          }
          if (button.classList.contains('folder-button-nav-right')) {
            clearClassFolderButton();
            button.classList.add('folder-button-nav-down');
          } else {
            clearClassFolderButton();
            button.classList.add('folder-button-nav-right');
          }
        })
        for (const dataChild of getChild) {
          const divMenuChild = document.getElementById('button-' + dataChild.code_menu);
          const linkMenuChild = divMenuChild.querySelector('a');
          linkMenuChild.classList.add('nav-link-child')
        }
      }
    }

    // onClick iframe
    for (const dataMenu of getMenu) {
      if (dataMenu.link_menu) {
        const divMenu = document.getElementById('button-' + dataMenu.code_menu);
        const button = divMenu.querySelector('a');
        button.addEventListener('click', async (e) => {
          e.preventDefault();
          await closeAllIframe();
          await closeDeselectTabs();
          const elementIframe = await checkIframe(dataMenu.code_menu);
          const elementTabs = await checkTabs(dataMenu.code_menu);
          await openIframe(elementIframe, dataMenu);
          await openTab(elementTabs, dataMenu);
        })
      }
    }

    // close child
    const closeChild = getMenu.filter((dataChild) => dataChild.indexParent !== null);
    if (closeChild.length > 0) {
      for (const dataMenu of closeChild) {
        const divMenu = document.getElementById('button-' + dataMenu.code_menu);
        divMenu.classList.add('d-none');
      }
    }
  }

  nameNavbar.innerText = dataUsers.username_users;
  companyNavbar.innerText = dataUsers.name_company;
  roleName.innerText = dataUsers.fullname_users;
  roleCompany.innerText = dataUsers.name_company;
  rolePosition.innerText = dataUsers.name_position;
  roleDepartment.innerText = dataUsers.name_department;
  roleSection.innerText = dataUsers.name_section;
  roleGolongan.innerText = dataUsers.golongan;

  await implanChangeRole(changeRoleBtn, sendViaFetchForm, alertComponent);

  resetPassBtn.addEventListener('click', (e) => {
    $("#resetPassModal").find("input[type=number], textarea, input[type=text], input[type=date], input[type=password], input[type=file]").val(null);
    $("#resetPassModal").find("input[type=checkbox]").prop("checked", false);
    bsModalReset.show();
    seePass.forEach((elem) => {
      elem.firstElementChild.classList.remove('fa-eye');
      elem.firstElementChild.classList.add('fa-eye-slash');
      elem.previousElementSibling.type = 'password';
    });
  });

  logoutBtn.addEventListener('click', (e) => {
    confirmComponent.setupconfirm('Keluar Akun', 'bg-danger', 'text-white', 'Anda yakin ingin keluar dari website ini?');
    alertComponent.alertElem.removeEventListener('hidden.bs.modal', closeAlertComp);
    confirmComponent.btnConfirm.addEventListener('click', async () => {
      try {
        const getResponse = await sendViaFetchForm('logAttempt.php?act=logout');
        alertComponent.sendAnAlertOnTry(getResponse, closeAlertComp);
      } catch (error) {
        alertComponent.sendAnAlertOnCatch(error);
      }
    });
    confirmComponent.btnCancel.addEventListener('click', () => {
      confirmComponent.confirmModal.hide();
    });
    confirmComponent.confirmModal.show();
  });

  detailInfoBtn.addEventListener('click', (e) => {
    bsModalDetailRole.show();
  });
  
  // window.addEventListener('hashchange', async () => {
  //   await app.renderPage();
  // });
  // window.addEventListener('load', async () => {
  //   await app.renderPage();
  // });

  seePass.forEach((elem) => {
    elem.addEventListener('click', seePassword );
  });

  saveResetPass.addEventListener('click', funSaveResetPass);
  old_password.addEventListener('keyup', async (e) => {
    if (e.key === 'Enter' || e.which === 13) {
      await funSaveResetPass();
    }
  })
  new_password.addEventListener('keyup', async (e) => {
    if (e.key === 'Enter' || e.which === 13) {
      await funSaveResetPass();
    }
  })
  confirm_password.addEventListener('keyup', async (e) => {
    if (e.key === 'Enter' || e.which === 13) {
      await funSaveResetPass();
    }
  })

  // const sendData = new FormData();
  // await sendViaFetchForm('module/kpi_lock_unlock/route.php?act=updateLockCutOff', sendData);

})