<?php
// require_once('../notes_form.php');
$idModalAddCompany = 'modalUtama';
$componentAddCompany = <<<COMPONENTADDCOMPANY
<div class="modal fade" id="{$idModalAddCompany}" tabindex="-1" role="dialog" aria-labelledby="{$idModalAddCompany}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title fw-bold text-white" id="{$idModalAddCompany}Title"></h5>
        <button type="button" class="btn btn-x" data-bs-dismiss="modal" data-bs-target="#{$idModalAddCompany}" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <div class="container">
          $notesForm
          <form enctype="multipart/form-data" id="{$idModalAddCompany}Form">
            <div class="form-group">
              <label for="company_id">Kode Perusahaan <small class="fw-bold text-danger">(auto)</small></label>
              <input type="text" class="form-control" id="company_id" aria-describedby="company_id" placeholder="Masukkan Kode..." disabled>
            </div>
            <div class="form-group">
              <label for="company_name">Nama Perusahaan <span class="fw-bold text-danger">*</span></label>
              <input type="text" class="form-control" id="company_name" name="company_name" aria-describedby="company_name" placeholder="Masukkan Nama...">
            </div>
            <div class="form-group">
              <label for="company_alias">Singkatan (Alias) <span class="fw-bold text-danger">#</span></label>
              <input type="text" class="form-control" id="company_alias" name="company_alias" aria-describedby="company_alias" placeholder="Masukkan Alias..." maxlength="10">
              <small class="fst-italic">Tidak boleh lebih dari 10 karakter <span class="fw-bold text-danger">#</span></small>
            </div>
            <div class="form-group">
              <span class="d-inline-block">Status</span>
              <div class="d-flex justify-content-center align-items-center" style="gap: 1rem;">
                <label for="stat_group" class="form-check">
                  <input type="checkbox" name="stat_group" id="stat_group" class="form-check-input" value="true">
                  <span class="form-check-label">Grup?</span>
                </label>
                <label for="stat_customer" class="form-check">
                  <input type="checkbox" name="stat_customer" id="stat_customer" class="form-check-input" value="true">
                  <span class="form-check-label">Customer?</span>
                </label>
                <label for="stat_supplier" class="form-check">
                  <input type="checkbox" name="stat_supplier" id="stat_supplier" class="form-check-input" value="true">
                  <span class="form-check-label">Supplier?</span>
                </label>
              </div>
            </div>
            <div class="form-group">
              <label for="logo_perusahaan">Logo Perusahaan</label>
              <div id="{$idModalAddCompany}ImageChange" class='text-center'>
                <img id="{$idModalAddCompany}ImageView" src="" alt="-- Tidak ada gambar --" class="d-inline-block" style="max-width: 100%; max-height: 125px;">
                <div class="d-flex justify-content-center align-items-center py-3" style="gap: 1rem;">
                  <label for="image_change1" class="form-check">
                    <input type="radio" name="image_change" id="image_change1" class="form-check-input" value="stay">
                    <span class="form-check-label">Tidak diubah</span>
                  </label>
                  <label for="image_change2" class="form-check">
                    <input type="radio" name="image_change" id="image_change2" class="form-check-input" value="change">
                    <span class="form-check-label">Diubah</span>
                  </label>
                  <label for="image_change3" class="form-check">
                    <input type="radio" name="image_change" id="image_change3" class="form-check-input" value="delete">
                    <span class="form-check-label">Hapus</span>
                  </label>
                </div>
              </div>
              <input type="file" name="logo_perusahaan" id="logo_perusahaan" class="d-block w-100">
            </div>
          </form>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-keluar" data-bs-dismiss="modal" data-bs-target="#{$idModalAddCompany}">
          <span class="ps-1">Keluar</span>
        </button>
        <button type="button" class="btn btn-sm btn-save" id="{$idModalAddCompany}BtnSave">
          <span class="ps-1">Simpan</span>
        </button>
      </div>
    </div>
  </div>
</div>
COMPONENTADDCOMPANY;
?>