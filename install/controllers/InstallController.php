<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: InstallController.php 7599 2010-10-07 21:40:23Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class InstallController extends Zend_Controller_Action
{
  protected $_session;
  
  public function init()
  {
    $this->_session = new Zend_Session_Namespace('InstallController');
    
    // Redirect on failed auth
    if( empty($this->_session->inProgress) && (!Zend_Registry::isRegistered('Engine/Installed') || Zend_Registry::get('Engine/Installed')) ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    
    $this->_session->inProgress = true;

    if( empty($this->_session->license) && $this->_getParam('action') != 'license' ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'license'));
    }

    // Add install socialengine title
    $this->view->headTitle()->prepend('Install SocialEngine');
  }
  
  public function licenseAction()
  {
    $this->view->form = $form = new Install_Form_License();

    $form->removeElement('email');

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Verify license
    $values = $form->getValues();
    
    /*
    try {
      $valid = $this->_verifyKeyEmail($values['key'], $values['email']);
    } catch( Exception $e ) {
      $valid = false;
      $error = $e->getMessage();
    }

    if( !$valid && $this->_getParam('force', 0) < 1 ) {
      $form->addError($error);

      // Add a force element
      $form->addElement('Checkbox', 'force', array(
        'label' => 'Install anyway?',
        'description' => 'Do you want to install anyway?',
      ));

      return;
    }

    $values['valid'] = empty($values['force']);
    unset($values['force']);
    */
    
    // Next!
    $this->_session->license = $form->getValues();
    return $this->_helper->redirector->gotoRoute(array('action' => 'sanity'), 'install', true);
  }

  public function sanityAction()
  {
    // We're just going to hard-code it for now :'(
    $this->view->test = $test = new Engine_Sanity(array(
      'basePath' => APPLICATION_PATH,
      'tests' => array(
        // PHP VERSION
        array(
          'type' => 'PhpVersion',
          'name' => 'PHP 5',
          'minVersion' => '5.1.2',
        ),
        // MYSQL ADAPTER
        array(
          'type' => 'Multi',
          'name' => 'MySQL',
          'allForOne' => true,
          'messages' => array(
            'allTestsFailed' => 'Requires one of the following extensions: mysql, mysqli, pdo_mysql',
          ),
          'tests' => array(
            array(
              'type' => 'PhpExtension',
              'extension' => 'mysql',
            ),
            array(
              'type' => 'PhpExtension',
              'extension' => 'mysqli',
            ),
            array(
              'type' => 'PhpExtension',
              'extension' => 'pdo_mysql',
            ),
          ),
        ),
        // APACHE MOD_REWRITE
        array(
          'type' => 'ApacheModule',
          'name' => 'mod_rewrite',
          'module' => 'mod_rewrite',
          'defaultErrorType' => Engine_Sanity::ERROR_NOTICE,
          'messages' => array(
            'noModule' => 'mod_rewrite does not appear to be available. This is OK, but it might prevent you from having search engine-friendly URLs.',
          ),
        ),
        // PHP SAFE_MODE
        array(
          'type' => 'PhpConfig',
          'name' => 'PHP Safe Mode OFF',
          'directive' => 'safe_mode',
          'comparisonMethod' => 'l',
          'comparisonValue' => 1,
          'messages' => array(
            'badValue' => 'PHP Safe Mode is currently ON - please turn it off and try again.',
          ),
        ),
        array(
          'type' => 'PhpConfig',
          'name' => 'PHP Register Globals OFF',
          'directive' => 'register_globals',
          'comparisonMethod' => 'l',
          'comparisonValue' => 1,
          'messages' => array(
            'badValue' => 'PHP Register Globals is currently ON - please turn it off and try again.',
          ),
        ),
        // PHP APC
        array(
          'type' => 'PhpExtension',
          'name' => 'APC',
          'extension' => 'apc',
          'defaultErrorType' => Engine_Sanity::ERROR_NOTICE,
          'messages' => array(
            'noExtension' => 'For optimal performance, we recommend adding the Alternative PHP Cache (APC) extension',
          ),
        ),
        // PHP GD
        array(
          'type' => 'PhpExtension',
          'name' => 'GD',
          'extension' => 'gd',
          'messages' => array(
            'noExtension' => 'The GD Image Library is required for resizing images.',
          ),
        ),
        // PHP MBSTRING
        array(
          'type' => 'PhpExtension',
          'name' => 'Multi-byte String',
          'extension' => 'mbstring',
          'defaultErrorType' => Engine_Sanity::ERROR_NOTICE,
          'messages' => array(
            'noExtension' => 'The Multi-byte String (mbstring) library is required for languages other than English.',
          ),
        ),
        // PHP PCRE
        array(
          'type' => 'PhpExtension',
          'name' => 'PCRE',
          'extension' => 'pcre',
          'messages' => array(
            'noExtension' => 'The Perl-Compatible Regular Expressions extension is required.',
          ),
        ),
        // PHP CURL
        array(
          'type' => 'PhpExtension',
          'name' => 'Curl',
          'extension' => 'curl',
          'messages' => array(
            'noExtension' => 'The Curl extension is required.',
          ),
        ),
        // PHP SESSION
        array(
          'type' => 'PhpExtension',
          'name' => 'Session',
          'extension' => 'session',
          'messages' => array(
            'noExtension' => 'Session support is required.',
          ),
        ),
        //PHP DOMDOCUMENT
        array(
          'type' => 'PhpExtension',
          'name' => 'DOM',
          'extension' => 'dom',
          'defaultErrorType' => Engine_Sanity::ERROR_NOTICE,
          'messages' => array(
            'noExtension' => 'The DOM (Document Object Model) extension is required for RSS feed parsing and link attachments.',
          ),
        ),
        // Public dir perms
        array(
          'type' => 'FilePermission',
          'name' => 'Public Directory Permissions',
          'path' => 'public',
          'value' => 7,
          'recursive' => true,
          'ignoreFiles' => true,
          'messages' => array(
            'insufficientPermissions' => 'Please log in over FTP and set CHMOD 0777 (recursive) on the public/ directory',
          ),
        ),
        // Temporary dir perms
        array(
          'type' => 'Multi',
          'name' => 'Temp Directory Permissions',
          'allForOne' => false,
          'breakOnFailure' => true,
          'messages' => array(
            'oneTestFailed' => 'Please log in over FTP and set CHMOD 0777 (recursive) on the temporary/ directory',
            'someTestsFailed' => 'Please log in over FTP and set CHMOD 0777 (recursive) on the temporary/ directory',
            'allTestsFailed' => 'Please log in over FTP and set CHMOD 0777 (recursive) on the temporary/ directory',
          ),
          'tests' => array(
            array(
              'type' => 'FilePermission',
              'path' => 'temporary',
              'value' => 7,
            ),
            array(
              'type' => 'FilePermission',
              'path' => 'temporary/cache',
              'value' => 7,
              'ignoreMissing' => true,
            ),
            array(
              'type' => 'FilePermission',
              'path' => 'temporary/log',
              'recursive' => true,
              'value' => 7,
              'ignoreMissing' => true,
            ),
            array(
              'type' => 'FilePermission',
              'path' => 'temporary/package',
              'value' => 7,
              'ignoreMissing' => true,
            ),
            array(
              'type' => 'FilePermission',
              'path' => 'temporary/package/archives',
              'value' => 7,
              'ignoreMissing' => true,
            ),
            array(
              'type' => 'FilePermission',
              'path' => 'temporary/package/packages',
              'value' => 7,
              'ignoreMissing' => true,
            ),
            array(
              'type' => 'FilePermission',
              'path' => 'temporary/package/repositories',
              'value' => 7,
              'ignoreMissing' => true,
            ),
            array(
              'type' => 'FilePermission',
              'path' => 'temporary/scaffold',
              'value' => 7,
              'ignoreMissing' => true,
            ),
            array(
              'type' => 'FilePermission',
              'path' => 'temporary/session',
              'value' => 7,
              'ignoreMissing' => true,
            ),
          ),
        ),
        // Settings dir perms
        array(
          'type' => 'FilePermission',
          'name' => 'Settings Directory Permissions',
          'path' => 'application/settings',
          'value' => 7,
          'recursive' => true,
          'messages' => array(
            'insufficientPermissions' => 'Please log in over FTP and set CHMOD 0777 (recursive) on the application/settings/ directory',
          ),
        ),
        // Packages dir perms
        array(
          'type' => 'FilePermission',
          'name' => 'Packages Directory Permissions',
          'path' => 'application/packages',
          'value' => 7,
          'recursive' => true,
          'messages' => array(
            'insufficientPermissions' => 'Please log in over FTP and set CHMOD 0777 (recursive) on the application/packages/ directory',
          ),
        ),
        // deploy auth perms
        array(
          'type' => 'FilePermission',
          'name' => 'Install Permissions',
          'path' => 'install/config/auth.php',
          'value' => 7,
          'recursive' => false,
          'messages' => array(
            'insufficientPermissions' => 'Please log in over FTP and set CHMOD 0777 (recursive) on the install/config/ directory',
          ),
        ),
        // language dir perms
        array(
          'type' => 'FilePermission',
          'name' => 'Language Directory Permissions',
          'path' => 'application/languages',
          'value' => 7,
          'recursive' => true,
          'messages' => array(
            'insufficientPermissions' => 'Please log in over FTP and set CHMOD 0777 (recursive) on the application/languages/ directory',
          ),
        ),
        // theme dir perms
        array(
          'type' => 'FilePermission',
          'name' => 'Theme Directory Permissions',
          'path' => 'application/themes',
          'value' => 7,
          'recursive' => true,
          'messages' => array(
            'insufficientPermissions' => 'Please log in over FTP and set CHMOD 0777 (recursive) on the application/themes/ directory',
          ),
        ),
      ),
    ));
    $test->run();

    // Check error levels
    $maxFileErrorLevel = 0;
    $maxOtherErrorLevel = 0;
    foreach( $test->getTests() as $stest ) {
      $isFile = false;
      if( $stest->getType() == 'FilePermission' ) {
        $isFile = true;
      } else if( $stest->getType() == 'Multi' ) {
        $isFile = true;
        foreach( $stest->getTests() as $stest2 ) {
          if( $stest2->getType() !== 'FilePermission' ) {
            $isFile = false;
          }
        }
      }
      
      if( $isFile ) {
        $maxFileErrorLevel = max($maxFileErrorLevel, $stest->getMaxErrorLevel());
      } else {
        $maxOtherErrorLevel = max($maxOtherErrorLevel, $stest->getMaxErrorLevel());
      }
    }

    $this->view->maxErrorLevel = $maxErrorLevel = max($maxFileErrorLevel, $maxOtherErrorLevel);
    $this->view->maxFileErrorLevel = $maxFileErrorLevel;
    $this->view->maxOtherErrorLevel = $maxOtherErrorLevel;

    $this->_session->sanity = array(
      'maxErrorLevel' => $maxErrorLevel,
      'maxFileErrorLevel' => $maxFileErrorLevel,
      'maxOtherErrorLevel' => $maxOtherErrorLevel,
    );

    $this->view->force = (bool) $this->_getParam('force');
  }
  
  public function vfsAction()
  {
    $adapterType = $this->_getParam('adapter');
    if( null === $adapterType ) {
      $adapterType = $this->_session->vfsAdapter;
    }
    $previousAdapterType = $this->_getParam('previousAdapter');

    $this->view->form = $form = new Install_Form_VfsInfo(array(
      'adapterType' => $adapterType,
    ));
    
    $form->setTitle(null)->setDescription(null);

    if( !$this->getRequest()->isPost() || $adapterType != $previousAdapterType ) {
      if( !$adapterType ) {
        // Ignore
      } else if( $adapterType == @$this->_session->vfsAdapter ) {
        // Load from session
        $form->populate(array(
          'adapter' => $adapterType,
          'config'  => $this->_session->vfsConfig
        ));
      } else {
        $form->populate(array(
          'adapter' => $adapterType,
        ));
      }
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $values = $form->getValues();
    $vfsAdapter = $values['adapter'];
    $vfsConfig = $values['config'];

    // Try to load adapter
    try {
      $vfs = Engine_Vfs::factory($vfsAdapter, $vfsConfig);
    } catch( Exception $e ) {
      $form->addError('Connection error: ' . $e->getMessage());
      return;
    }

    // Try to connect (getResource will force connect)
    try {
      $vfs->getResource();
    } catch( Exception $e ) {
      $form->addError('Connection error: ' . $e->getMessage());
      return;
    }

    // Search for target
    $path = null;
    if( !empty($vfsConfig['search']) && $vfsConfig['search'] !== '0' ) {
      $path = $vfs->findJailedPath($vfsConfig['path'], APPLICATION_PATH . '/install/ftp_search_will_look_for_this_file');
      if( !$path ) {
        $form->addError('Your installation could not be found. Please double check your connection info and starting path. Your starting path needs to be your SocialEngine path, or a parent directory of it.');
        return;
      }
      $path = dirname(dirname($path));
    } else {
      $path = $vfsConfig['path'];
    }

    // Verify path
    if( !$vfs->exists($path . '/install/ftp_search_will_look_for_this_file') ) {
      $form->addError('Specified path is not a SocialEngine install directory.');
      return;
    }

    // Save config
    $vfsConfig['path'] = $values['config']['path'] = $path;

    $vfs->changeDirectory($path);
    
    $this->_session->vfsInstance = $vfs;
    $this->_session->vfsAdapter = $vfsAdapter;
    $this->_session->vfsConfig = $vfsConfig;

    // Save for later
    $vfsSettingsData = array(
      'adapter' => $vfsAdapter,
      'config' => array_diff_key($vfsConfig, array(
        'password' => null,
        'location' => null,
        'search' => null,
      )),
    );
    @file_put_contents(APPLICATION_PATH . '/install/config/vfs.php', '<?php return ' . var_export($vfsSettingsData, true) . '?>');

    // Redirect to next step
    return $this->_helper->redirector->gotoRoute(array('action' => 'perms'));
  }

  public function permsAction()
  {
    // Leave if not ready
    if( empty($this->_session->vfsInstance) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'vfs'));
    }

    $vfs = $this->_session->vfsInstance;
    
    // Chmod temporary
    //$vfs = new Engine_Vfs_Adapter_System($config);
    
    $paths = array(
      'temporary' => 0777,
      'public' => 0777,
      'application/languages' => 0777,
      'application/packages' => 0777,
      'application/themes' => 0777,
      'application/settings' => 0777,
      'install/config' => 0777,
    );

    $errors = array();
    foreach( $paths as $path => $mode ) {
      try {
        $vfs->mode($path, $mode, true);
      } catch( Exception $e ) {
        $errors[] = $e->getMessage();
      }
    }
    $this->view->errors = $errors;
  }

  public function dbInfoAction()
  {
    $this->view->form = $form = new Install_Form_DbInfo();

    // Make session
    if( $this->_getParam('clear') ) {
      $this->_session->mysql = array();
    }

    // Check post
    if( $this->getRequest()->isPost() ) {
      if( $form->isValid($this->getRequest()->getPost()) ) {
        $this->_session->mysql = $form->getValues();
      } else {
        return;
      }
    }

    if( empty($this->_session->mysql) ) {
      return;
    }

    // Validate mysql options
    try {
      $config = $this->dbFormToConfig($this->_session->mysql);

      // Add some special magic
      if( $config['adapter'] == 'mysqli' ) {
        $config['params']['driver_options'] = array(
          'MYSQLI_OPT_CONNECT_TIMEOUT' => '2',
        );
      } else if( $config['adapter'] == 'pdo_mysql' ) {
        $config['params']['driver_options'] = array(
          Zend_Db::ATTR_TIMEOUT => '2',
        );
      }

      // Connect!
      $adapter = Zend_Db::factory($config['adapter'], $config['params']);
      $adapter->getServerVersion();
      
    } catch( Exception $e ) {
      $form->addError('Adapter Error: ' . $e->getMessage());

      if( APPLICATION_ENV == 'development' ) {
        echo $e;
      }

      return;
    }

    // Next!
    return $this->_helper->redirector->gotoRoute(array('action' => 'db-sanity'), 'install', true);
  }

  public function dbSanityAction()
  {
    // Leave if not ready
    if( empty($this->_session->mysql) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'db-info'));
    }

    // Connect
    try {
      
      $config = $this->dbFormToConfig($this->_session->mysql);
      $adapter = Zend_Db::factory($config['adapter'], $config['params']);
      $adapter->getServerVersion();

    } catch( Exception $e ) {

      $this->view->error = 'Adapter Error: ' . $e->getMessage();
      if( APPLICATION_ENV == 'development' ) {
        echo $e;
      }
      return;
      
    }

    // Run sanity
    $this->view->test = $test = new Engine_Sanity(array(
      'tests' => array(
        array(
          'type' => 'MysqlServer',
          'name' => 'MySQL 4.1',
          'adapter' => $adapter,
          'minVersion' => '4.1',
        ),
        array(
          'type' => 'MysqlEngine',
          'name' => 'MySQL InnoDB Storage Engine',
          'adapter' => $adapter,
          'engine' => 'innodb',
        )
      )
    ));
    $test->run();
    $this->view->maxErrorLevel = $maxErrorLevel = $test->getMaxErrorLevel();

    $this->_session->db_sanity = array(
      'maxErrorLevel' => $maxErrorLevel,
    );
  }

  public function dbCreateAction()
  {
    // Leave if not ready
    if( empty($this->_session->mysql) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'db-info'));
    }

    $this->view->code = 0;

    // Connect again
    try {
      $config = $this->dbFormToConfig($this->_session->mysql);

      // Connect!
      $adapter = Zend_Db::factory($config['adapter'], $config['params']);
      $adapter->getServerVersion();

    } catch( Exception $e ) {
      $this->view->code = 1;
      $this->view->error = 'Adapter Error: ' . $e->getMessage();
      return;
    }

    // Check if database config already exists
    $configFile = APPLICATION_PATH . '/application/settings/database.php';
    if( file_exists($configFile) && !($this->_getParam('force', 0) >= 1) ) {
      $this->view->code = 2;
      $this->view->error = 'We found an existing database configuration file. Do you want to overwrite it?';
      return;
    }

    // Put database.php into place
    if( !file_exists($configFile) || ($this->_getParam('force', 0) >= 1) ) {
      $contents = '';
      $contents .= '<?php defined(\'_ENGINE\') or die(\'Access Denied\'); ';
      $contents .= 'return ' . var_export($config, true);
      $contents .= '; ?>';

      if( !@file_put_contents($configFile, $contents) && !($this->_getParam('force', 0) >= 2) ) {
        $this->view->code = 3;
        $this->view->error = 'Your existing database configuration file is not writeable. Please login to your server via FTP and set full permissions (CHMOD 0777) on /application/settings/database.php, then refresh this page.';
        return;
      }
    }

    // Create database shtuff
    $files = array(
      APPLICATION_PATH . '/application/modules/Core/settings/my.sql',
      APPLICATION_PATH . '/application/modules/Activity/settings/my.sql',
      APPLICATION_PATH . '/application/modules/Authorization/settings/my.sql',
      APPLICATION_PATH . '/application/modules/User/settings/my.sql',
      APPLICATION_PATH . '/application/modules/Messages/settings/my.sql',
      APPLICATION_PATH . '/application/modules/Network/settings/my.sql',
      APPLICATION_PATH . '/application/modules/Invite/settings/my.sql',
      APPLICATION_PATH . '/application/modules/Fields/settings/my.sql',
      APPLICATION_PATH . '/application/modules/Storage/settings/my.sql',
      APPLICATION_PATH . '/application/modules/Announcement/settings/my.sql',
    );

    try {
      
      foreach( $files as $file ) {
        $sql = file_get_contents($file);
        if( !$sql ) {
          throw new Engine_Exception('Unable to read sql file');
        }
        $queries = Engine_Package_Utilities::sqlSplit($sql);
        foreach( $queries as $query ) {
          $adapter->query($query);
        }
      }
      
    } catch( Exception $e ) {
      $this->view->error = $e->getMessage();
      return;
    }


    // Update some other stuff
    $settingsTable = new Zend_Db_Table(array(
      'db' => $adapter,
      'name' => 'engine4_core_settings',
    ));
    
    // Generate new secret key
    $row = $settingsTable->find('core.secret')->current();
    if( null === $row ) {
      $row = $settingsTable->createRow();
      $row->name = 'core.secret';
    }
    if( $row->value == 'staticSalt' || $row->value == 'NULL' || !$row->value ) {
      $row->value = sha1(time() . php_uname() . dirname(__FILE__) . rand(1000000, 9000000));
      $row->save();
    }

    // Save key
    $row = $settingsTable->find('core.license.key')->current();
    if( null === $row ) {
      $row = $settingsTable->createRow();
      $row->name = 'core.license.key';
    }
    $row->value = $this->_session->license['key'];
    $row->save();

    // Save stats
    $row = $settingsTable->find('core.license.statistics')->current();
    if( null === $row ) {
      $row = $settingsTable->createRow();
      $row->name = 'core.license.statistics';
    }
    $row->value = $this->_session->license['statistics'];
    $row->save();

    // Save email
    if( !empty($this->_session->license->email) ) {
      $row = $settingsTable->find('core.license.email')->current();
      if( null === $row ) {
        $row = $settingsTable->createRow();
        $row->name = 'core.license.email';
      }
      if (isset($this->_session->license['email'])) {
        $row->value = $this->_session->license['email'];
      }
      $row->save();
    }

    // Save creation date
    $row = $settingsTable->find('core.site.creation')->current();
    if( null === $row ) {
      $row = $settingsTable->createRow();
      $row->name = 'core.site.creation';
    }
    $row->value = date('Y-m-d H:i:s');
    $row->save();




    // Certain stuff goes here (DO NOT REMOVE THIS LINE!)



    
    // Ok we're done
    $this->view->status = 1;
  }

  public function accountAction()
  {
    // Leave if not ready
    if( empty($this->_session->mysql) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'db-info'));
    }
    
    $this->view->form = $form = new Install_Form_Account();

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Check passwords match
    $values = $form->getValues();
    if( $values['password'] != $values['password_conf'] ) {
      $form->addError('Passwords must match.');
      return;
    }

    // Create account

    // Connect again
    try {
      $config = $this->dbFormToConfig($this->_session->mysql);

      // Connect!
      $adapter = Zend_Db::factory($config['adapter'], $config['params']);
      $adapter->getServerVersion();

    } catch( Exception $e ) {
      $form->addError('Adapter Error: ' . $e->getMessage());
      //$this->view->code = 1;
      //$this->view->error = 'Adapter Error: ' . $e->getMessage();
      return;
    }

    try {
    
      // Preprocess
      $settingsTable = new Zend_Db_Table(array(
        'db' => $adapter,
        'name' => 'engine4_core_settings',
      ));
      $usersTable = new Zend_Db_Table(array(
        'db' => $adapter,
        'name' => 'engine4_users',
      ));
      $levelTable = new Zend_Db_Table(array(
        'db' => $adapter,
        'name' => 'engine4_authorization_levels',
      ));

      // Get static salt
      $staticSalt = $settingsTable->find('core.secret')->current();
      if( is_object($staticSalt) ) {
        $staticSalt = $staticSalt->value;
      } else if( !is_string($staticSalt) ) {
        $staticSalt = '';
      }

      // Get superadmin level
      $superAdminLevel = $levelTable->fetchRow($levelTable->select()->where('flag = ?', 'superadmin'));
      if( is_object($superAdminLevel) ) {
        $superAdminLevel = $superAdminLevel->level_id;
      } else {
        $superAdminLevel = 1;
      }

      // Temporarily save pw
      $originalPassword = $values['password'];

      // Adjust values
      $values['salt'] = (string) rand(1000000, 9999999);
      $values['password'] = md5( $staticSalt . $values['password'] . $values['salt'] );
      $values['level_id'] = $superAdminLevel;
      $values['enabled'] = 1;
      $values['verified'] = 1;
      $values['creation_date'] = date('Y-m-d H:i:s');
      $values['creation_ip'] = ip2long($_SERVER['REMOTE_ADDR']);
      $values['displayname'] = $values['username'];

      // Try to write info to config/auth.php
      if( !$this->_writeAuthToFile($values['email'], 'seiran', $originalPassword) ) {
        throw new Exception('Unable to write Auth to File');
      }

      // Insert
      $row = $usersTable->createRow();
      $row->setFromArray($values);
      $row->save();

      // First Signup Increment
      // Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.creations');

      // Validate password
      if( $row->password != md5($staticSalt . $originalPassword .  $row->salt) ) {
        throw new Engine_Exception('Error creating password');
      }

      // Log the user into the intaller
      $auth = Zend_Registry::get('Zend_Auth');
      $auth->getStorage()->write($row->user_id);

      // Try to log the user into socialengine
      // Note: nasty hack
      try {
        $mainSessionName = 'PHPSESSID';
        if( empty($_COOKIE[$mainSessionName]) ) {
          $mainSessionId = md5(mt_rand(0, time()) . serialize($_SERVER));
          setcookie($mainSessionName, $mainSessionId, null,
              dirname($this->view->baseUrl()), $_SERVER['HTTP_HOST'], false, false);
        } else {
          $mainSessionId = $_COOKIE[$mainSessionName];
        }

        $adapter->insert('engine4_core_session', array(
          'id' => $mainSessionId,
          'modified' => time(),
          'lifetime' => 86400,
          'data' => 'Zend_Auth|' . serialize(array(
            'storage' => $row->user_id,
          )),
        ));
      } catch( Exception $e ) {
        // Silence
        if( APPLICATION_ENV == 'development' ) {
          echo $e->__toString();
        }
      }


      
      // Update some other stuff
      $settingsTable = new Zend_Db_Table(array(
        'db' => $adapter,
        'name' => 'engine4_core_settings',
      ));

      // Save site name
      $row = $settingsTable->find('core.general.site.title')->current();
      if( null === $row ) {
        $row = $settingsTable->createRow();
        $row->name = 'core.general.site.title';
      }
      $row->value = $values['site_title'];
      $row->save();

      
      // Save email
      $row = $settingsTable->find('core.license.email')->current();
      if( null === $row ) {
        $row = $settingsTable->createRow();
        $row->name = 'core.license.email';
      }
      if( $row->value != 'email@domain.com' ) {
        $row->value = $values['email'];
        $row->save();
      }
      
    } catch( Exception $e ) {
      $form->addError('Error: ' . $e->getMessage());
      return;
    }
    
    // Redirect if successful
    return $this->_helper->redirector->gotoRoute(array('action' => 'complete'));
  }


  public function completeAction()
  {
    // Clear all session data
    $this->_session->unsetAll();
  }





  
  // Utility

  public function dbFormToConfig($formValues)
  {
    $adapter = $formValues['adapter'];
    unset($formValues['adapter']);
    $params = $formValues;
    
    return $this->mergeOptions(array(
      'adapter' => null,
      'params' => array(
        'host' => "localhost",
        'username' => null,
        'password' => null,
        'dbname' => null,
        'charset' => 'UTF8',
        'adapterNamespace' => ( $adapter == 'mysql' ? 'Engine_Db_Adapter' : 'Zend_Db_Adapter' ),
      ),
      'isDefaultTableAdapter' => true,
      'tablePrefix' => "engine4_",
      'tableAdapterClass' => "Engine_Db_Table",
    ), array(
      'adapter' => $adapter,
      'params' => $params,
    ));
  }

  public function mergeOptions(array $array1, $array2 = null)
  {
      if (is_array($array2)) {
          foreach ($array2 as $key => $val) {
              if (is_array($array2[$key])) {
                  $array1[$key] = (array_key_exists($key, $array1) && is_array($array1[$key]))
                                ? $this->mergeOptions($array1[$key], $array2[$key])
                                : $array2[$key];
              } else {
                  $array1[$key] = $val;
              }
          }
      }
      return $array1;
  }

  protected function _writeAuthToFile($user, $realm, $password)
  {
    // Try using normal fs op
    if( $this->_htpasswd(APPLICATION_PATH . '/install/config/auth.php', $user, $realm, $password) ) {
      return true;
    }

    // Try using ftp
    if( !empty($this->_session->ftp) && !empty($this->_session->ftp['target']) ) {
      try {
        $ftp = Engine_Package_Utilities::ftpFactory($this->_session->ftp);
        $rfile = $this->_session->ftp['target'] . 'install/config/auth.php';
        $tmpfile = tempnam('/tmp', md5(time() . rand(0, 1000000)));
        //chmod($tmpfile, 0777);
        $ret = $ftp->get($rfile, $tmpfile, true);
        if( $ftp->isError($ret) ) {
          throw new Engine_Exception($ret->getMessage());
        }
        if( !$this->_htpasswd($tmpfile, $user, $realm, $password) ) {
          throw new Engine_Exception('Unable to write to tmpfile');
        }
        $ret = $ftp->put($tmpfile, $rfile, true);
        if( $ftp->isError($ret) ) {
          // Try to chmod + write + unchmod
          $ret2 = $ftp->chmod($rfile, '0777');
          if( $ftp->isError($ret2) ) {
            throw new Engine_Exception($ret2->getMessage());
          }
          $ret2 = $ftp->put($tmpfile, $rfile, true);
          if( $ftp->isError($ret2) ) {
            throw new Engine_Exception($ret2->getMessage());
          }
          $ret2 = $ftp->chmod($rfile, '0755');
          if( $ftp->isError($ret2) ) {
            throw new Engine_Exception($ret2->getMessage());
          }
        }
        
      } catch( Exception $e ) {
        throw $e;
      }
    }

    throw new Engine_Exception('Unable to write to auth file');
  }

  protected function _htpasswd($file, $user, $realm, $password)
  {
    $newLine = $user . ':' . $realm . ':' . md5($user . ':' . $realm . ':' . $password);

    // Read file
    $lines = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if( !$lines ) {
      return false;
    }

    // Search for existing
    $found = false;
    $userRealm = $user . ':' . $realm;
    foreach( $lines as $index => $line ) {
      if( $line == $newLine ) {
        // Same password
        return true;
      } else if( substr($line, 0, strlen($userRealm)) == $userRealm ) {
        // Different password
        if( !$found ) {
          $lines[$index] = $newLine;
          $found = true;
        } else {
          unset($lines[$index]); // Prevent multiple user-realm combos
        }
      }
    }

    if( !$found ) {
      $lines[] = $newLine;
    }

    if( !@file_put_contents($file, join("\n", $lines)) ) {
      return false;
    }

    return true;
  }

  protected function _verifyKeyEmail($key, $email) {
    // No curl
    if( !function_exists('curl_init') ) {
      throw new Exception('Curl is not available.', 100);
    }

    // Request
    $url = base64_decode(str_rot13('nUE0pQbiY3q3ql5mo2AcLJkyozqcozHhozI0Y3WyoJ90MI92MKWcMaxhpTuj'));
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('mode' => 'json', 'email' => $email, 'key' => $key));
    $data = curl_exec($ch);

    if( !$data || !($data = Zend_Json::decode($data)) || !is_array($data) || count($data) != 2 ) {
      throw new Exception('Invalid data returned', 101);
    }

    list($code, $message) = $data;

    switch( (int) $code ) {
      case 0:
      case 1:
      case 3:
      default:
        throw new Exception($message, 110 + (int) $code);
        break;
      case 2:
        break;
    }

    return true;
  }
}