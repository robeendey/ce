<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminSystemController.php 7457 2010-09-23 05:45:23Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_AdminSystemController extends Core_Controller_Action_Admin
{
  public function init()
  {
    if( defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER ) {
      return $this->_helper->redirector->gotoRoute(array(), 'admin_default', true);
    }
  }

  public function indexAction()
  {
    $this->_redirect('/admin/system/php');
  }

  public function logAction()
  {
    $logPath = APPLICATION_PATH . '/temporary/log';

    // Get all existing log files
    $logFiles = array();
    foreach( scandir($logPath) as $file ) {
      if( strtolower(substr($file, -4)) == '.log' ) {
        $logFiles[] = $file;
      }
    }
    
    // No files
    $this->view->logFiles = $logFiles;
    if( empty($logFiles) ) {
      $this->view->error = 'There are no log files to view.';
      return;
    }

    // Make form
    $labels = array(
      'main.log' => 'Error log',
      'tasks.log' => 'Task scheduler log',
      'translate.log' => 'Language log',
      'video.log' => 'Video encoding log',
    );
    $multiOptions = array_combine($logFiles, $logFiles);
    $labels = array_intersect_key($labels, $multiOptions);
    $multiOptions = array_diff_key($multiOptions, $labels);
    $multiOptions = array_merge($labels, $multiOptions);

    $this->view->formFilter = $formFilter = new Core_Form_Admin_System_Log();
    $formFilter->getElement('file')->addMultiOptions($multiOptions);
    //$formFilter->getElement('file')->setValue(key($logFiles));

    $values = array_merge(array(
      //'file' => current($logFiles),
      'length' => 50,
    ), $this->_getAllParams());
    
    if( $formFilter->isValid($values) ) {
      $values = $formFilter->getValues();
    } else {
      $values = array('length' => 50);
    }

    /*
    if( empty($values['file']) ) {
      $values = array(
        'file' => current($logFiles),
        'length' => 50,
      );
    }
     */
    
    // Make sure param is in existing log files
    $logFile = $values['file'];
    if( empty($logFile) ||
        !in_array($logFile, $logFiles) ||
        !file_exists($logPath . DIRECTORY_SEPARATOR . $logFile) ) {
      $logFile = null;
    }

    // Exit if no valid log file
    if( !$logFile ) {
      $this->view->error = 'Please select a file to view.';
      return;
    }

    // Clear log if requested
    if( $this->getRequest()->isPost() && $this->_getParam('clear', false) ) {
      if( ($fh = fopen($logPath . DIRECTORY_SEPARATOR . $logFile, 'w')) ){
        ftruncate($fh, 0);
        fclose($fh);
      }
      return $this->_helper->redirector->gotoRoute(array());
    }
    
    // Get log length
    $this->view->logFile = $logFile;
    $this->view->logSize = $logSize = filesize($logPath . DIRECTORY_SEPARATOR . $logFile);
    $this->view->logLength = $logLength = $values['length'];
    $this->view->logOffset = $logOffset = $this->_getParam('offset', $logSize);
    

    // Tail the file
    $endOffset = 0;
    try {
      $lines = $this->_tail($logPath . DIRECTORY_SEPARATOR . $logFile, $logLength, $logOffset, true, $endOffset);
    } catch( Exception $e ) {
      $this->view->error = $e->getMessage();
      return;
    }

    $this->view->logText = $lines;
    $this->view->logEndOffset = $endOffset;
  }

  public function logDownloadAction()
  {
    $logPath = APPLICATION_PATH . '/temporary/log';

    // Get all existing log files
    $logFiles = array();
    foreach( scandir($logPath) as $file ) {
      if( strtolower(substr($file, -4)) == '.log' ) {
        $logFiles[] = $file;
      }
    }

    $logFile = $this->_getParam('file');
    if( empty($logFile) ||
        !in_array($logFile, $logFiles) ||
        !file_exists($logPath . DIRECTORY_SEPARATOR . $logFile) ) {
      exit();
    }

    // kill output buffering
    while( ob_get_level() > 0 ) {
      ob_end_clean();
    }
    
    // Send headers
    header('content-disposition: attachment, filename=' . urlencode($logFile));
    header('content-length: ' . filesize($logPath . DIRECTORY_SEPARATOR . $logFile));

    // Open file
    $handle = fopen($logPath . DIRECTORY_SEPARATOR . $logFile, 'r');
    while( '' !== ($str = fread($handle, 1024)) ) {
      echo $str;
    }
    exit();
  }
  
  public function phpAction()
  {
    ob_start();
    phpinfo();
    $source = ob_get_clean();

    preg_match('~<style.+?>(.+?)</style>.+?(<table.+\/table>)~ims', $source, $matches);
    $css = $matches[1];
    $source = $matches[2];

    $css = preg_replace('/[\r\n](.+?{)/iu', "\n#phpinfo \$1", $css);

    //$regex = '/'.preg_quote('<a href="http://www.php.net/">', '/').'.+?'.preg_quote('</a>', '/').'/ims';
    //$source = preg_replace($regex, '', $source);

    // strip images from phpinfo()
    $regex = '/<img .+?>/ims';
    $source = preg_replace($regex, '', $source);
    
    $regex = '/'.preg_quote('<h2>PHP License</h2>', '/').'.+$/ims';
    $source = preg_replace($regex, '', $source);

    $source = str_replace("module_Zend Optimizer", "module_Zend_Optimizer", $source);

    $this->view->style = $css;
    $this->view->content = $source;
  }

  public function apcAction()
  {
    
  }

  protected function _tail($file, $length = 10, $offset = 0, $whence = true, &$endOffset = null)
  {
    // Check stuff
    if( !file_exists($file) ) {
      throw new Exception('File does not exist.');
    }
    if( 0 === ($size = filesize($file)) ) {
      throw new Exception('File is empty.');
    }
    if( !($fh = fopen($file, 'r')) ) {
      throw new Exception('Unable to open file.');
    }

    // Process args
    if( abs($offset) > $size ) {
      throw new Exception('Reached end of file.');
    }
    if( !in_array($whence, array(SEEK_SET, SEEK_END)) ) {
      throw new Exception('Unknown whence.');
    }
    
    // Seek to requested position
    fseek($fh, $offset, SEEK_SET);

    // Read in chunks of 512 bytes
    $position = $offset;
    $break = false;
    $lines = array();
    $chunkSize = 512;
    $buffer = '';

    do {

      // Get next position
      $position += ( $whence ? -$chunkSize : $chunkSize );
      fseek($fh, $position, SEEK_SET);
      
      // Whoops we ran out of stuff
      if( $position < 0 || $position > $size ) {
        $break = true;
        break;
      }

      // Read a chunk
      $chunk = fread($fh, $chunkSize);
      if( $whence ) {
        $buffer = $chunk . $buffer;
      } else {
        $buffer .= $chunk;
      }

      // Parse chunk into lines
      $bufferLines = preg_split('/\r\n?|\n/', $buffer);

      // Put the last (probably incomplete) one back
      if( $whence ) {
        $buffer = array_shift($bufferLines);
      } else {
        $buffer = array_pop($bufferLines);
      }

      // Add to existing lines
      if( $whence ) {
        $lines = array_merge($bufferLines, $lines);
      } else {
        $lines = array_merge($lines, $bufferLines);
      }

      // Are we done?
      if( count($lines) >= $length ) {
        $break = true;
      }
      
    } while( !$break );

    $endOffset = $position;

    // Add remaining length in buffer
    $endOffset += strlen($buffer);
    
    return trim(join(PHP_EOL, $lines), "\n\r");
  }
}