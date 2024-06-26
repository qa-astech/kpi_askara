import { sendViaFetchForm, AlertElemBS5 } from "../third-party/utility-yudhi/utils.js";

const alertShow = new AlertElemBS5('alertYudhi');
const btnSave = document.querySelector('#submitForm');
const formDepan = document.querySelector('#formDepan');
const InputUsername = document.querySelector('#username');
const InputPassword = document.querySelector('#password');

const goToHome = () => {
  window.open("index.php", "_self");
}
const saveFunc = async () => {
  const SendData = new FormData(formDepan);
  sendViaFetchForm('logAttempt.php?act=login', SendData).then(data => {
    alertShow.sendAnAlertOnTry(data, () => {
      if (data.response === 'success') {
        goToHome();
      }
    });
  }).catch(error => {
    alertShow.sendAnAlertOnCatch(error);
  })
}

document.addEventListener("DOMContentLoaded", async () => {

  const getUsers = await sendViaFetchForm('logAttempt.php?act=getSessionSimple');
  if (getUsers.response !== 'error') {
    const dataUsers = getUsers.data;
    if (Object.keys(dataUsers).length > 0) {
      goToHome();
    }
  }

  InputUsername.value = null;
  InputPassword.value = null;
  btnSave.addEventListener('click', saveFunc);
  InputUsername.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.which === 13) {
      saveFunc();
    }
  });
  InputPassword.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.which === 13) {
      saveFunc();
    }
  });
})