<?php
// require_once('../notes_form.php');
$idModalAddPosition = 'modalUtama';
$componentAddPosition = <<<COMPONENTADDPOSITION
<div class="modal fade" id="{$idModalAddPosition}" tabindex="-1" role="dialog" aria-labelledby="{$idModalAddPosition}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title fw-bold text-white" id="{$idModalAddPosition}Title"></h5>
        <button type="button" class="btn btn-x" data-bs-dismiss="modal" data-bs-target="#{$idModalAddPosition}" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <div class="container">
          $notesForm
          <form enctype="multipart/form-data" id="{$idModalAddPosition}Form">
            <div class="form-group">
              <label for="position_id">Kode Posisi <small class="fw-bold text-danger">(auto)</small></label>
              <input type="text" class="form-control" id="position_id" aria-describedby="position_id" placeholder="Masukkan Kode..." disabled>
            </div>
            <div class="form-group">
              <label for="position_name">Nama Posisi <span class="fw-bold text-danger">*</span></label>
              <input type="text" class="form-control" id="position_name" name="position_name" aria-describedby="position_name" placeholder="Masukkan Nama...">
            </div>
          </form>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-keluar" data-bs-dismiss="modal" data-bs-target="#{$idModalAddPosition}">
          <span class="ps-1">Keluar</span>
        </button>
        <button type="button" class="btn btn-sm btn-save" id="{$idModalAddPosition}BtnSave">
          <span class="ps-1">Simpan</span>
        </button>
      </div>
    </div>
  </div>
</div>
COMPONENTADDPOSITION;
?>