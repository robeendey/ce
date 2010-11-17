<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminIndexController.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_AdminIndexController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    
  }

  public function changeEnvironmentModeAction()
  {
    if ($this->getRequest()->isPost() && $this->_getParam('environment_mode', false)) {
      $global_settings_file = APPLICATION_PATH . '/application/settings/general.php';
      if( file_exists($global_settings_file) ) {
        $g = include $global_settings_file;
      } else {
        $g = array();
      }
      
      if (!is_writable($global_settings_file)) {
        // not writable; can we delete and re-create?
        if (is_writable(dirname($global_settings_file))) {
          @rename($global_settings_file, $global_settings_file.'_backup.php');
          @touch($global_settings_file);
          @chmod($global_settings_file, 0666);
          if (!file_exists($global_settings_file) || !is_writable($global_settings_file)) {
            @rename($global_settings_file, $global_settings_file.'_delete.php');
            @rename($global_settings_file.'_backup.php', $global_settings_file);
            @unlink($global_settings_file.'_delete.php');
          }
        }
        if (!is_writable($global_settings_file)) {
          $this->view->success = false;
          $this->view->error   = 'Unable to write to settings file; please CHMOD 666 the file /application/settings/general.php, then try again.';
          return;
        } else {
          // it worked; continue.
        }
      }

      if ($this->_getParam('environment_mode') != @$g['environment_mode']) {
        $g['environment_mode'] = $this->_getParam('environment_mode');
        $file_contents  = "<?php defined('_ENGINE') or die('Access Denied'); return ";
        $file_contents .= var_export($g, true);
        $file_contents .= "; ?>";
        $this->view->success = @file_put_contents($global_settings_file, $file_contents);
        
        // clear scaffold, just to be sure
        Core_Model_DbTable_Themes::clearScaffoldCache();
        return;
      } else {
        $this->view->message = 'No change necessary';
        $this->view->success = true; // no change
      }
    }
    $this->view->success = false;
    
  }
}