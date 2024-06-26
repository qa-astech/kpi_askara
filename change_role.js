const implanChangeRole = async (changeRoleBtn, sendViaFetchForm, alertComponent) => {
  const changeRoleModal = document.getElementById('changeRoleModal');
  const changeRoleModalForm = document.getElementById('changeRoleModalForm');
  const changeRoleModalSave = document.getElementById('changeRoleModalSave');
  const bsModalChangeRole = new bootstrap.Modal(changeRoleModal);
  let saveStateChangeRole = false;
  
  changeRoleBtn.addEventListener('click', async (e) => {
    changeRoleModalForm.innerHTML = null;
  
    const getUsers = await sendViaFetchForm('logAttempt.php?act=getAllRoles');
    const dataUsers = getUsers.data;
  
    const templateCard = (id_usersetup, nama_user, nama_perusahaan, jabatan, department, divisi, golongan, index) => `<label class="card mb-2" for="${id_usersetup}">
      <div class="card-header d-flex justify-content-start align-items-center">
        <div>Identitas ${index}</div>
        <div class="form-check ms-auto">
          <input class="form-check-input checkbox-change-role" type="radio" name="id_usersetup" id="${id_usersetup}" value="${id_usersetup}">
        </div>
      </div>
      <div class="card-body">
        <table>
          <tbody>
            <tr>
              <td class="pb-2 white-space-nowrap">Nama</td>
              <td class="pb-2 px-2">:</td>
              <td class="pb-2 w-100">${nama_user}</td>
            </tr>
            <tr>
              <td class="pb-2 white-space-nowrap">Perusahaan</td>
              <td class="pb-2 px-2">:</td>
              <td class="pb-2 w-100">${nama_perusahaan}</td>
            </tr>
            <tr>
              <td class="pb-2 white-space-nowrap">Jabatan</td>
              <td class="pb-2 px-2">:</td>
              <td class="pb-2 w-100">${jabatan}</td>
            </tr>
            <tr>
              <td class="pb-2 white-space-nowrap">Department</td>
              <td class="pb-2 px-2">:</td>
              <td class="pb-2 w-100">${department}</td>
            </tr>
            <tr>
              <td class="pb-2 white-space-nowrap">Divisi</td>
              <td class="pb-2 px-2">:</td>
              <td class="pb-2 w-100">${divisi}</td>
            </tr>
            <tr>
              <td class="pb-2 white-space-nowrap">Golongan</td>
              <td class="pb-2 px-2">:</td>
              <td class="pb-2 w-100">${golongan}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </label>`;
    
    let cardList = '';
    let index = 1;
    dataUsers.forEach(data => {
      cardList += templateCard(data.id_usersetup, data.fullname_users, data.name_company, data.name_position, data.name_department, data.id_section, data.golongan, index);
      index++;
    });
    changeRoleModalForm.innerHTML = cardList;
  
    bsModalChangeRole.show();
  });

  changeRoleModal.addEventListener('shown.bs.modal', event => {
    const elementCheckbox = changeRoleModal.querySelectorAll('.checkbox-change-role');
    elementCheckbox.forEach(element => {

      element.addEventListener('change', (e) => {
        const elementCard = e.target.parentElement.parentElement.parentElement;
        const allCard = changeRoleModal.querySelectorAll('label.card');
        allCard.forEach(card => {
          card.classList.remove('selected');
        });
        elementCard.classList.add('selected');
      });

    });
  })
  
  changeRoleModalSave.addEventListener('click', async (e) => {
    changeRoleModalSave.disabled = true;
    if (saveStateChangeRole === false) {
      saveStateChangeRole = true;
      try {
        const sendData = new FormData(changeRoleModalForm);
        const getResponse = await sendViaFetchForm('logAttempt.php?act=changeDept', sendData);
        alertComponent.sendAnAlertOnTry(getResponse, () => {
          location.reload();
        });
      } catch (error) {
        alertComponent.sendAnAlertOnCatch(error);
      } finally {
        changeRoleModalSave.disabled = false;
        saveStateChangeRole = false;
      }
    }
  });
}
export default implanChangeRole;