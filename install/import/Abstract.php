<?php

abstract class Install_Import_Abstract
{
  protected $_fromPath;
  
  protected $_toDb;

  protected $_toPath;

  protected $_toTable;

  protected $_toTableTruncate = true;

  protected $_toTableExists;

  protected $_messages = array();

  protected $_priority = 100;

  protected $_requires = array();

  protected $_verbosity = 1;

  protected $_hasError = false;

  protected $_hasWarning = false;

  protected $_params;

  protected $_log;

  protected $_cache;

  protected $_batchCount;

  protected $_isComplete = false;



  // Special
  
  final public function __construct(array $options)
  {
    $this->setOptions($options);
    $this->init();
  }

  final public function setOptions(array $options)
  {
    foreach( $options as $key => $value ) {
      $method = 'set' . ucfirst($key);
      if( method_exists($this, $method) ) {
        $this->$method($value);
      } else {
        $this->_params[$key] = $value;
      }
    }
    return $this;
  }

  public function __sleep()
  {
    return array(
      '_fromPath', /*'_toDb',*/ '_toPath', '_toTable', '_toTableTruncate',
      '_toTableExists', '_messages', '_priority', '_requires', '_verbosity',
      '_hasError', '_hasWarning', '_params', /*'_log',*/ /*'_cache',*/
      '_batchCount', '_isComplete',
    );
    // Need to reinit _toDb, _log, _cache
  }



  // Options
  
  public function setToDb(Zend_Db_Adapter_Abstract $toDb)
  {
    $this->_toDb = $toDb;
    return $this;
  }

  /**
   * @return Zend_Db_Adapter_Abstract
   */
  public function getToDb()
  {
    if( null ===  $this->_toDb ) {
      throw new Engine_Exception('No to database');
    }
    return $this->_toDb;
  }

  public function setToPath($toPath)
  {
    $this->_toPath = $toPath;
    return $this;
  }

  public function getToPath()
  {
    if( null === $this->_toPath ) {
      throw new Engine_Exception('No to path');
    }
    return $this->_toPath;
  }

  public function setToTable($toTable)
  {
    $this->_toTable = $toTable;
    return $this;
  }

  public function getToTable()
  {
    if( null === $this->_toTable ) {
      throw new Engine_Exception('No to table');
    }
    return $this->_toTable;
  }

  public function getToTableExists()
  {
    if( null === $this->_toTable ) {
      return false;
    }
    if( null === $this->_toTableExists ) {
      $this->_toTableExists = $this->_tableExists($this->getToDb(), $this->getToTable());
    }
    return $this->_toTableExists;
  }

  public function setFromPath($fromPath)
  {
    $this->_fromPath = $fromPath;
    return $this;
  }

  public function getFromPath()
  {
    if( null === $this->_fromPath ) {
      throw new Engine_Exception('No from path');
    }
    return $this->_fromPath;
  }

  public function setPriority($priority = 100)
  {
    $this->_priority = (int) $priority;
    return $this;
  }

  public function getPriority()
  {
    return $this->_priority;
  }

  public function setRequires(array $requires)
  {
    $this->_requires = $requires;
    return $this->_requires;
  }

  public function getRequires()
  {
    return $this->_requires;
  }

  public function setVerbosity($verbosity)
  {
    $this->_verbosity = (integer) $verbosity;
    return $this;
  }

  public function getVerbosity()
  {
    return $this->_verbosity;
  }

  public function setParams(array $params = array())
  {
    $this->_params = $params;
    return $this;
  }

  public function getParams()
  {
    return $this->_params;
  }

  public function getParam($key, $default = null)
  {
    if( array_key_exists($key, (array) $this->_params) ) {
      return $this->_params[$key];
    } else {
      return $default;
    }
  }



  // Messages

  public function setLog(Zend_Log $log)
  {
    $this->_log = $log;
  }

  public function getLog()
  {
    if( null === $this->_log ) {
      $log = new Zend_Log();
      $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/import-version3.log', 'a'));
      $this->_log = $log;
    }
    return $this->_log;
  }

  public function getMessages()
  {
    return $this->_messages;
  }

  public function hasError()
  {
    return $this->_hasError;
  }

  public function hasWarning()
  {
    return $this->_hasWarning;
  }

  protected function _message($message, $verbosity = 1)
  {
    $this->_log($message, Zend_Log::INFO);
    
    if( $verbosity <= $this->getVerbosity() ) {
      if( $message instanceof Exception ) {
        $message = $message->getMessage();
      }
      $this->_messages[] = get_class($this) . ': ' . $message;
    }
    
    return $this;
  }

  protected function _warning($message, $verbosity = 0)
  {
    $this->_log($message, Zend_Log::WARN);
    
    if( $verbosity <= $this->getVerbosity() ) {
      if( $message instanceof Exception ) {
        $message = $message->getMessage();
      }
      $this->_hasWarning = true;
      $this->_messages[] = get_class($this) . ': Warning: ' . $message;

      // Send an email
      if( false != ($email = $this->getParam('email')) && in_array('warning', (array) $this->getParam('emailOptions')) ) {
        try {
          $now = gmdate('c', time());
          $mail = new Zend_Mail();
          $mail
            ->setFrom('no-reply@' . $_SERVER['HTTP_HOST'])
            ->addTo($email)
            ->setSubject('SocialEngine Version 4 Migration Progress for ' . $_SERVER['HTTP_HOST'])
            ->setBodyText("Hello,

This is a SocialEngine 4 migration status report.

Server: {$_SERVER['HTTP_HOST']}
Time: {$now}

Message: A warning has occurred. {$message}

Regards,
Your Server")
          ;
        } catch( Exception $e ) {
          // Silence
        }
      }
    }

    return $this;
  }

  protected function _error($message, $verbosity = 0)
  {
    $this->_log($message, Zend_Log::ERR);
    
    if( $verbosity <= $this->getVerbosity() ) {
      if( $message instanceof Exception ) {
        $message = $message->getMessage();
      }
      $this->_hasError = true;
      $this->_messages[] = get_class($this) . ': Error: ' . $message;
    
      // Send an email
      if( false != ($email = $this->getParam('email')) && in_array('error', (array) $this->getParam('emailOptions')) ) {
        try {
          $now = gmdate('c', time());
          $mail = new Zend_Mail();
          $mail
            ->setFrom('no-reply@' . $_SERVER['HTTP_HOST'])
            ->addTo($email)
            ->setSubject('SocialEngine Version 4 Migration Progress for ' . $_SERVER['HTTP_HOST'])
            ->setBodyText("Hello,

This is a SocialEngine 4 migration status report.

Server: {$_SERVER['HTTP_HOST']}
Time: {$now}

Message: An error has occurred. {$message}

Regards,
Your Server")
          ;
        } catch( Exception $e ) {
          // Silence
        }
      }
    }

    return $this;
  }

  protected function _log($message, $level = Zend_Log::INFO)
  {
    if( $message instanceof Exception ) {
      $message = /*( $this->getVerbosity() >= 3 ?*/ $message->__toString() /* : $message->getMessage() )*/ ;
    }
    $this->getLog()->log(get_class($this) . ' - ' . $message, $level);
  }



  // Cache

  public function setCache(Zend_Cache_Core $cache)
  {
    $this->_cache = $cache;
    return $this;
  }

  /**
   * @return Zend_Cache_Core
   */
  public function getCache()
  {
    return $this->_cache;
  }

  public function setBatchCount($batchCount)
  {
    $this->_batchCount = (int) $batchCount;
    return $this;
  }

  public function getBatchCount()
  {
    return $this->_batchCount;
  }

  public function isComplete()
  {
    return (bool) $this->_isComplete;
  }

  

  // Custom

  public function init()
  {
    $this->_initPre();
    $this->_init();
    $this->_initPost();
  }

  abstract public function run();

  protected function _initPre() {}

  protected function _init() {}

  protected function _initPost() {}

  protected function _runPre() {}

  protected function _runPost() {}

  protected function _translateRow(array $data, $key = null) { return false; }



  // Utility

  protected function _translateTime($time)
  {
    if( is_int($time) || is_numeric($time) ) {
      // okay
    } else if( is_string($time) ) {
      $time = strtotime($time);
    } else {
      //return null;
      $time = 0; // _translateTime must alwasy return a valid date
    }

    return gmdate('Y-m-d H:i:s', $time);
  }

  protected function _translateFile($file, array $params, $checkForThumb = true)
  {
    if( !file_exists($file) ) {
      return false;
    }

    // Prepare data
    $row = array();

    if( !empty($params['parent_file_id']) && !empty($params['type']) ) {
      $row['parent_file_id'] = @$params['parent_file_id'];
      $row['type'] = @$params['type'];
    }
    $row['parent_type'] = $params['parent_type'];
    $row['parent_id'] = $params['parent_id'];
    $row['user_id'] = @$params['user_id'];
    $row['creation_date'] = $row['modified_date'] = $this->_translateTime(filemtime($file));
    $row['storage_type'] = 'local';
    $row['name'] = basename($file);
    $row['extension'] = ltrim(strrchr($file, '.'), '.');
    $row['mime_major'] = 'unknown';
    $row['mime_minor'] = 'unknown';
    $row['size'] = filesize($file);
    $row['hash'] = md5_file($file);
    $row['storage_path'] = $file; // temporary

    // Insert row
    //try {
      $this->getToDb()->insert('engine4_storage_files', $row);
      $file_id = $this->getToDb()->lastInsertId();
    //} catch( Exception $e ) {
    //  die($e->__toString());
    //}

    // Make storage path
    extract($row);

    $subdir1 = ( (int) $parent_id + 999999 - ( ( (int) $parent_id - 1 ) % 1000000) );
    $subdir2 = ( (int) $parent_id + 999    - ( ( (int) $parent_id - 1 ) % 1000   ) );

    $row['storage_path'] =   // extended by default
      'public' . '/'
      . strtolower($parent_type) . '/'
      . $subdir1 . '/'
      . $subdir2 . '/'
      . $parent_id . '/'
      . $file_id . '.'
      . strtolower($extension);

    $fullTargetPath = $this->getToPath() . DIRECTORY_SEPARATOR . $row['storage_path'];

    // Make dir
    if( !is_dir(dirname($fullTargetPath)) ) {
      if( !mkdir(dirname($fullTargetPath), 0777, true) ) {
        throw new Engine_Exception('Unable to make directory (' . $fullTargetPath . ') for file ' . $file);
      }
    }

    // Copy file
    $cpr = copy($file, $fullTargetPath);
    if( !$cpr ) {
      $this->getToDb()->delete('engine4_storage_files', array(
        'file_id' => $file_id
      ));
      throw new Engine_Exception('Unable to store file ' . $file);
    }

    // Update storage path
    $this->getToDb()->update('engine4_storage_files', array(
      'storage_path' => $row['storage_path'],
    ), array(
      'file_id = ?' => $file_id,
    ));

    // Shall we try to do the thumbnail?
    if( $checkForThumb && stripos($file, 'thumb') === false ) {
      $noExtName = rtrim(substr($file, 0, strrpos($file, '.')), '.');
      $thumbFile = $noExtName . '_thumb.' . $row['extension'];
      if( !file_exists($thumbFile) ) {
        $thumbFile = $noExtName . '_thumb.jpg';
        if( !file_exists($thumbFile) ) {
          $thumbFile = $noExtName . '_thumb.gif';
          if( !file_exists($thumbFile) ) {
            $thumbFile = null;
          }
        }
      }

      if( $thumbFile ) {
        $thumbParams = $params;
        $thumbParams['parent_file_id'] = $file_id;
        $thumbParams['type'] = 'thumb.normal';

        try {
          $this->_translateFile($thumbFile, $thumbParams);
        } catch( Exception $e ) {

        }
      }
    }

    return $file_id;
  }

  protected function _translatePhoto($file, array $params)
  {
    if( !file_exists($file) ) {
      return false;
    }

    $name = basename($file);
    $temporaryDirectory = $this->_sys_get_temp_dir();

    $mainFile = $temporaryDirectory . '/m_' . $name;
    $profileFile = $temporaryDirectory . '/p_' . $name;
    $normalFile = $temporaryDirectory . '/n_' . $name;
    $squareFile = $temporaryDirectory . '/s_' . $name;

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($mainFile)
      ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($profileFile)
      ->destroy();

    // Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($normalFile)
      ->destroy();

    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($squareFile)
      ->destroy();

    // Store
    
    $file_id = $this->_translateFile($mainFile, $params, false);

    if( !$file_id ) return false;

    $this->_translateFile($profileFile, array_merge($params, array(
      'parent_file_id' => $file_id,
      'type' => 'thumb.profile',
    )), false);

    $this->_translateFile($normalFile, array_merge($params, array(
      'parent_file_id' => $file_id,
      'type' => 'thumb.normal',
    )), false);

    $this->_translateFile($squareFile, array_merge($params, array(
      'parent_file_id' => $file_id,
      'type' => 'thumb.icon',
    )), false);
    
    // Remove temp files
    @unlink($mainFile);
    @unlink($profileFile);
    @unlink($normalFile);
    @unlink($$squareFile);

    return $file_id;
  }
  
  protected function _sys_get_temp_dir()
  {
    return APPLICATION_PATH . '/temporary';
    if( !function_exists('sys_get_temp_dir') ) {
      function sys_get_temp_dir() {
        if( false != ($temp = getenv('TMP')) ) return $temp;
        if( false != ($temp = getenv('TEMP')) ) return $temp;
        if( false != ($temp = getenv('TMPDIR')) ) return $temp;
        $temp = tempnam(__FILE__, '');
        if( file_exists($temp) ) {
          unlink($temp);
          return dirname($temp);
        }
        return null;
      }
    }

    return sys_get_temp_dir();
  }

  protected function _unserialize($string)
  {
    $data = unserialize($string);
    if( !$data || !is_array($data) ) {
      $data = $this->_mb_unserialize($string);
      if( !$data || !is_array($data) ) {
        throw new Engine_Exception('Unable to unserialize string');
      }
    }
    return $data;
  }

  protected function _mb_unserialize($serial_str)
  {
    $out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str);
    return unserialize($out);
  }

  protected function _truncateTable(Zend_Db_Adapter_Abstract $db, $table)
  {
    try {
      $this->_message('Truncating table: ' . $table, 2);
      $this->getToDb()->query('TRUNCATE TABLE ' . $db->quoteIdentifier($table));
    } catch( Exception $e ) {
      return false;
    }

    return true;
  }
  
  protected function _truncateToTable()
  {
    if( $this->_toTable ) {
      try {
        return $this->_truncateTable($this->getToDb(), $this->getToTable());
      } catch( Exception $e ) {
        return false;
      }
    }
    return false;
  }

  protected function _insertOrUpdate(Zend_Db_Adapter_Abstract $db, $table, $insert, $update)
  {
    list($sql, $bind) = $this->_generateInsert($db, $table, $insert);
    $sql .= ' ON DUPLICATE KEY UPDATE ';

    $i = 0;
    foreach( $update as $col => $val ) {
      if( $i > 0 ) {
        $sql .= ', ';
      }

      $sql .= $db->quoteIdentifier($col, true) . '=';

      if ($val instanceof Zend_Db_Expr) {
          $sql .= $val->__toString();
      } else {
          $sql .= '?';
          $bind[] = $val;
      }

      $i++;
    }

    // execute the statement and return the number of affected rows
    $stmt = $db->query($sql, array_values($bind));
    $result = $stmt->rowCount();
    return $result;
  }

  protected function _generateInsert(Zend_Db_Adapter_Abstract $db, $table, $bind)
  {
    // extract and quote col names from the array keys
    $cols = array();
    $vals = array();
    foreach ($bind as $col => $val) {
        $cols[] = $db->quoteIdentifier($col, true);
        if ($val instanceof Zend_Db_Expr) {
            $vals[] = $val->__toString();
            unset($bind[$col]);
        } else {
            $vals[] = '?';
        }
    }

    // build the statement
    $sql = "INSERT INTO "
         . $db->quoteIdentifier($table, true)
         . ' (' . implode(', ', $cols) . ') '
         . 'VALUES (' . implode(', ', $vals) . ')';


    return array($sql, array_values($bind));
  }

  protected function _tableExists(Zend_Db_Adapter_Abstract $db, $table)
  {
    if( is_array($table) ) {
      $exists = true;
      foreach( $table as $singleTable ) {
        $exists &= $this->_tableExists($db, $singleTable);
      }
      return $exists;
    }

    $sql = 'SHOW TABLES LIKE ' . $db->quote($table);
    $ret = $db->query($sql)->fetchColumn();
    if( !$ret ) {
      return false;
    } else {
      return true;
    }
  }
}