<?php
$notesForm = <<<NOTESFORM
<div class="alert alert-info" role="alert">
  <h5 class="fw-bold">Catatan</h5>
  <div class="d-flex justify-content-start align-items-center flex-wrap" style="column-gap: 1em;">
    <ul class="mb-0">
      <li><small>Simbol <span class="fw-bold text-danger">*</span> : Data tidak boleh kosong!</small></li>
      <li><small>Simbol <span class="fw-bold text-danger">#</span> : Perhatikan ketentuan kolom!</small></li>
      <li><small>Simbol <span class="fw-bold text-danger">Step - {no}</span> : Data harus diisi secara <span class="fst-italic">step-by-step</span></small></li>
    </ul>
    <ul class="mb-0">
      <li><small><span class="fw-bold text-danger">auto</span> : Data akan terisi otomatis oleh sistem!</small></li>
      <li><small><span class="fw-bold text-danger">unik - {nama}</span> : Kombinasi data harus bersifat unik, dilarang adanya duplikat!</small></li>
    </ul>
  </div>
</div>
NOTESFORM;
?>