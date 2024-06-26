<?php
$changeRole = <<<CHANGEROLE
<div class="modal fade" id="changeRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Pilih Role</h4>
        <button type="button" class="btn-close me-1" data-bs-dismiss="modal" data-bs-target="#changeRoleModal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="changeRoleModalForm" enctype="multipart/form-data" method="post">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" data-bs-target="#changeRoleModal"><i class="fa-solid fa-xmark pe-1"></i> Batal</button>
        <button type="button" class="btn btn-sm btn-primary" id="changeRoleModalSave"><i class="fa-solid fa-floppy-disk pe-1"></i> Ubah Role</button>
      </div>
    </div>
  </div>
</div>
CHANGEROLE;
?>