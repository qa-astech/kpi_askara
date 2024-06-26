<?php
// require_once('../notes_form.php');
$idModalAddDepartment = 'modalUtama';
$componentAddDepartment = <<<COMPONENTADDDEPARTMENT
<div class="modal fade" id="{$idModalAddDepartment}" tabindex="-1" role="dialog" aria-labelledby="{$idModalAddDepartment}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title fw-bold text-white" id="{$idModalAddDepartment}Title"></h5>
        <button type="button" class="btn btn-x" data-bs-dismiss="modal" data-bs-target="#{$idModalAddDepartment}" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <div class="container">
          $notesForm
          <form enctype="multipart/form-data" id="{$idModalAddDepartment}Form">
            <div class="form-group">
              <label for="department_id">Kode Departemen <small class="fw-bold text-danger">(auto)</small></label>
              <input type="text" class="form-control" id="department_id" aria-describedby="department_id" placeholder="Masukkan Kode..." disabled>
            </div>
            <div class="form-group">
              <label for="department_name">Nama Departemen <span class="fw-bold text-danger">*</span></label>
              <input type="text" class="form-control" id="department_name" name="department_name" aria-describedby="department_name" placeholder="Masukkan Nama...">
            </div>
            <div class="form-group">
              <label for="department_alias">Singkatan (Alias) <span class="fw-bold text-danger">#</span></label>
              <input type="text" class="form-control" id="department_alias" name="department_alias" aria-describedby="department_alias" placeholder="Masukkan Alias..." maxlength="10">
              <small class="fst-italic">Tidak boleh lebih dari 10 karakter <span class="fw-bold text-danger">#</span></small>
            </div>
          </form>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-keluar" data-bs-dismiss="modal" data-bs-target="#{$idModalAddDepartment}">
          <span class="ps-1">Keluar</span>
        </button>
        <button type="button" class="btn btn-sm btn-save" id="{$idModalAddDepartment}BtnSave">
          <span class="ps-1">Simpan</span>
        </button>
      </div>
    </div>
  </div>
</div>
COMPONENTADDDEPARTMENT;
?>