<?php
class upload{

  public $nameFile;
  public $tempFile;
  public $typeFile = [];
  public $pkeyFile;
  public $dirFile;
  public $imgSuccess = [];
  public $newNameFile = '';

  public function setNameFile($value){
    $this->nameFile = $value;
    return true;
  }
  public function setTempFile($value){
    $this->tempFile = $value;
    return true;
  }
  public function setTypeFile($value){
    $this->typeFile = $value;
    return true;
  }
  public function setPkeyFile($value){
    $this->pkeyFile = preg_replace("/[^A-Za-z0-9 ]-_/", '-', $value);
    return true;
  }
  public function setDirFile($value){
    $this->dirFile = $value;
    return true;
  }
  public function setImgSuccess($value){
    array_push($this->imgSuccess, $value);
    return true;
  }
  public function destroyUpload() {
    $this->nameFile = null;
    $this->tempFile = null;
    $this->typeFile  = [];
    $this->pkeyFile = null;
    $this->dirFile = null;
    $this->imgSuccess = [];
    $this->newNameFile = '';
  }
  private function checkUpload() {
    // echo $this->nameFile . "\n";
    // echo $this->tempFile . "\n";
    // echo $this->pkeyFile . "\n";
    // echo $this->dirFile . "\n";
    if (
      empty($this->nameFile) ||
      empty($this->tempFile) ||
      empty($this->pkeyFile) ||
      empty($this->dirFile)
    ) {
      $this->revertUpload();
      echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Terjadi kesalahan, data yang diunggah tidak lengkap. Hubungi IT Software!'
        )
      );
      die();
    }
  }
  private function checkFileType() {
    if (!empty($this->typeFile)) {
      $img_ext = strtolower(pathinfo($this->newNameFile, PATHINFO_EXTENSION));
      if( !in_array($img_ext, $this->typeFile) ) {
        $this->revertUpload();
        echo json_encode(
          array(
            'response'=>'error',
            'alert'=>'Ekstensi file tidak diizinkan, unggah file sesuai dengan tipenya!'
          )
        );
        die();
      }
    }
  }
  public function prosesUpload() {
    $this->checkUpload();
    $fileArrName = explode(".", $this->nameFile);
    $this->newNameFile = $this->pkeyFile . "-" . round(microtime(true)) . '.' . end($fileArrName);
    $this->checkFileType();
    $pathUpload = $this->dirFile . $this->newNameFile;
    if ( @move_uploaded_file($this->tempFile, $pathUpload) ){
      $this->setImgSuccess($this->newNameFile);
      return true;
    } else {
      $this->revertUpload();
        echo json_encode(
          array(
            'response'=>'error',
            'alert'=>'Upload gagal, coba lagi beberapa saat!'
          )
        );
        die();
    }
  }
  public function revertUpload() {
    $this->clearImg($this->imgSuccess, $this->dirFile);
  }
  public function clearImg($arrImage, $dir){
    if (!empty($arrImage)) {
      foreach ($arrImage as $value) {
        @unlink( $dir . $value );
      }
    }
  }
  public function clearImgAut($arrImage, $dir, $arrExt){
    if (!empty($arrImage)) {
      foreach ($arrImage as $value) {
        $pic_name = explode(".", $value);
        if ( in_array( end($pic_name), $arrExt ) ) {
          @unlink( $dir . $value );
        }
      }
    }
  }

}
$upload = new upload();
?>