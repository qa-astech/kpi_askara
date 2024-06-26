<?php
// Create a new zip object
class ZippingFile extends ZipArchive {

  private $folder = '';
  private $listFiles = array();

  // Setup folder
  public function setFolder($value){
    $this->folder = $value;
  }

  // Array of file names you want to add to zip
  public function setListFile($value){
    $this->listFiles = $value;
  }

  public function processZipping($nameZip){
    if (empty($this->folder) || empty($this->listFiles)) {
      echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Harap atur folder dan dokumennya terlebih dahulu!'
        )
      );
      die();
    }

    try {
      // Create a temp file & open it
      $tmp_file = tempnam($this->folder, '');

      // Open the zip file
      $this->open($tmp_file, ZipArchive::CREATE);

      // Loop through each file
      foreach($this->listFiles as $file){
        // Download file
        $download_file = file_get_contents($this->folder . $file);
    
        // Add it to the zip
        $this->addFromString(basename($file), $download_file);
      }

      // Close the zip file
      $this->close();

      // Send the file to the browser as a download
      header("Content-disposition: attachment; filename=$nameZip.zip");
      header("Content-type: application/zip");
      readfile($tmp_file);
    } catch (Exception $e) {
      echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Proses zipping error, kontak IT Software!'
        )
      );
      die();
    }
  }

  public function zipFilesAndDownload($nameZip){
    if (empty($this->folder) || empty($this->listFiles)) {
      echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Harap atur folder dan dokumennya terlebih dahulu!'
        )
      );
      die();
    }

    try {
      $nameOfZip = $nameZip . '.zip';
      if(file_exists($nameOfZip)) {
        unlink($nameOfZip);
      }
    
      //create the file and throw the error if unsuccessful
      if ($this->open($nameOfZip, ZIPARCHIVE::CREATE ) !== TRUE) {
        die("Cannot open < $nameOfZip >\n");
      }
      
      //add each files of $file_name array to archive
      foreach($this->listFiles as $files) {
        $this->addFile($this->folder . $files, $files);
      }
      $this->close();
    
      //then send the headers to force download the zip file
      header("Content-type: application/zip"); 
      header("Content-Disposition: attachment; filename=$nameOfZip");
      header("Content-length: " . filesize($nameOfZip));
      header("Pragma: no-cache"); 
      header("Expires: 0"); 
      readfile($nameOfZip);
      exit;

    } catch (Exception $e) {
      echo json_encode(
        array(
          'response'=>'error',
          'alert'=>'Proses zipping error, kontak IT Software!'
        )
      );
      die();
    }
  }
  
}
$zipping = new ZippingFile();
?>
