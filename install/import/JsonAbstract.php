<?php

abstract class Install_Import_JsonAbstract extends Install_Import_Abstract
{
  protected $_fromFile;

  protected $_fromFileAlternate;

  public function setFromFile($fromFile)
  {
    $this->_fromFile = $fromFile;
    return $this;
  }
  
  public function getFromFile()
  {
    if( null === $this->_fromFile ) {
      throw new Engine_Exception('No from file');
    }
    return $this->_fromFile;
  }

  public function getFromFileAbs()
  {
    $file = $this->getFromPath() . DIRECTORY_SEPARATOR . $this->getFromFile();
    if( !file_exists($file) ) {
      throw new Engine_Exception(sprintf('From file "%s" does not exist', $file));
    }
    return $file;
  }

  public function setFromFileAlternate($fromFileAlternate)
  {
    $this->_fromFileAlternate = $fromFileAlternate;
    return $this;
  }

  public function getFromFileAlternate()
  {
    if( null === $this->_fromFileAlternate ) {
      throw new Engine_Exception('No from file slternate');
    }
    return $this->_fromFileAlternate;
  }

  public function getFromFileAlternateAbs()
  {
    $file = $this->getFromPath() . DIRECTORY_SEPARATOR . $this->getFromFileAlternate();
    if( !file_exists($file) ) {
      throw new Engine_Exception(sprintf('From file "%s" does not exist', $file));
    }
    return $file;
  }

  public function run()
  {
    $this->_message('(START)', 2);
    
    $fromFileAbs = null;
    try {
      $fromFileAbs = $this->getFromFileAbs();
    } catch( Exception $e ) {
      $fromFileAbs = $this->getFromFileAlternateAbs();
    }

    $toDb = $this->getToDb();
    $toTable = $this->getToTable();

    // Check file exists
    if( !file_exists($fromFileAbs) ) {
      $this->_message(sprintf('File "%s" does not exist, skipping import', $fromFileAbs), 0);
      $this->_message('(END)', 2);
      return;
    }

    // Check table exists
    $sql = 'SHOW TABLES LIKE ' . $toDb->quote($toTable);
    $ret = $toDb->query($sql)->fetchColumn();
    if( !$ret ) {
      $this->_message(sprintf('Table "%s" does not exist, skipping import', $toTable), 0);
      $this->_message('(END)', 2);
      return;
    }

    // stats
    $cTotal = 0;
    $cSuccess = 0;
    $cFail = 0;

    // Import
    $fromData = file_get_contents($fromFileAbs);
    $fromData = trim($fromData, " \n\r\t()");

    // Hack to fix bug?
    $fromData = str_replace('}{', '},{', $fromData);
    
    // Debug
    /*
    include_once 'OFC/JSON_Format.php';
    echo "<pre>";
    print_r(json_format($fromData));
    die();
     * 
     */

    // Decode
    $fromData = Zend_Json::decode($fromData);
    if( !is_array($fromData) ) {
      throw new Engine_Exception('Data could not be decoded');
    }
    
    foreach( $fromData as $fromKey => $fromDatum ) {

      $cTotal++;

      try {

        $newData = $this->_translateRow($fromDatum, $fromKey);

        if( false !== $newData ) {
          $toDb->insert($toTable, $newData);
        }

        $cSuccess++;

      } catch( Exception $e ) {

        $message = $e->getMessage();

        //if( !empty($fromData) ) {
        //  $message .= ' | ' . Zend_Json::encode($fromData);
        //}

        if( !empty($newData) ) {
          $message .= ' | ' . Zend_Json::encode($newData);
        }

        $this->_message($message, 0);

        $cFail++;

      }

    }

    $this->_message(sprintf('Total: %d', $cTotal), 1);
    $this->_message(sprintf('Success: %d', $cSuccess), 1);
    $this->_message(sprintf('Failure: %d', $cFail), 1);
    $this->_message('(END)', 2);
  }
}