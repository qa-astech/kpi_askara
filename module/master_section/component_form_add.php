<?php
// require_once('../notes_form.php');
$idModalAddDivisi = 'modalDivisi';
$componentAddDivisi = <<<COMPONENTADDDIVISI
<div class="modal fade" id="{$idModalAddDivisi}" tabindex="-1" role="dialog" aria-labelledby="{$idModalAddDivisi}" aria-hidden="true" style="z-index: 2005;">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title fw-bold text-white" id="{$idModalAddDivisi}Title"></h5>
        <button type="button" class="btn btn-x" data-bs-dismiss="modal" data-bs-target="#{$idModalAddDivisi}" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <div class="container">
          $notesForm
          <form enctype="multipart/form-data" id="{$idModalAddDivisi}Form">
            <div class="form-group">
              <label for="section_id" class="d-block">Kode Divisi <small class="font-weight-bold text-danger">(auto)</small></label>
              <input type="text" class="form-control" id="section_id" aria-describedby="section_id" placeholder="Masukkan Kode..." disabled>
            </div>
            <div class="form-group">
              <label for="section_name">Nama Divisi <span class="font-weight-bold text-danger">*</span></label>
              <input type="text" class="form-control" id="section_name" name="section_name" aria-describedby="section_name" placeholder="Masukkan nama...">
            </div>
          </form>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-keluar" data-bs-dismiss="modal" data-bs-target="#{$idModalAddDivisi}">
          <span class="ps-1">Keluar</span>
        </button>
        <button type="button" class="btn btn-sm btn-save" id="{$idModalAddDivisi}BtnSave">
          <span class="ps-1">Simpan</span>
        </button>
      </div>
    </div>
  </div>
</div>
COMPONENTADDDIVISI;
?>