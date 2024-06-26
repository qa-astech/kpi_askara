<?php
// require_once('../notes_form.php');
$idModalAddPlant = 'modalUtama';
$componentAddPlant = <<<COMPONENTADDPLANT
<div class="modal fade" id="{$idModalAddPlant}" tabindex="-1" role="dialog" aria-labelledby="{$idModalAddPlant}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title fw-bold text-white" id="{$idModalAddPlant}Title"></h5>
        <button type="button" class="btn btn-x" data-bs-dismiss="modal" data-bs-target="#{$idModalAddPlant}" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <div class="container">
          $notesForm
          <form enctype="multipart/form-data" id="{$idModalAddPlant}Form">
            <div class="form-group">
              <label for="plant_id">Kode Plan <small class="fw-bold text-danger">(auto)</small></label>
              <input type="text" class="form-control" id="plant_id" aria-describedby="plant_id" placeholder="Masukkan Kode..." disabled>
            </div>
            <div class="form-group">
              <label for="plant_name">Nama Plan <span class="fw-bold text-danger">*</span></label>
              <input type="text" class="form-control" id="plant_name" name="plant_name" aria-describedby="plant_name" placeholder="Masukkan Nama...">
            </div>
            <div class="form-group">
              <label for="plant_alias">Singkatan (Alias) <span class="fw-bold text-danger">#</span></label>
              <input type="text" class="form-control" id="plant_alias" name="plant_alias" aria-describedby="plant_alias" placeholder="Masukkan Alias..." maxlength="10">
              <small class="fst-italic">Tidak boleh lebih dari 10 karakter <span class="fw-bold text-danger">#</span></small>
            </div>
          </form>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-keluar" data-bs-dismiss="modal" data-bs-target="#{$idModalAddPlant}">
          <span class="ps-1">Keluar</span>
        </button>
        <button type="button" class="btn btn-sm btn-save" id="{$idModalAddPlant}BtnSave">
          <span class="ps-1">Simpan</span>
        </button>
      </div>
    </div>
  </div>
</div>
COMPONENTADDPLANT;
?>