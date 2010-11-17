<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminController.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class AdminController extends Zend_Controller_Action
{
  protected $_digestFile;
  
  protected $_isDigestWritable;
  
  protected $_digestUsers;

  protected $_databaseUsers;
  
  public function init()
  {
    // Check if already logged in
    if( !Zend_Registry::get('Zend_Auth')->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    
    // Get digest file name
    $this->_digestFile = $this->view->digestFile =
      APPLICATION_PATH . '/install/config/auth.php';

    // Check if digest is readable/writable
    $this->_isDigestWritable = $this->view->isDigestWritable =
      file_exists($this->_digestFile) ? is_writable($this->_digestFile) : is_writable(dirname($this->_digestFile));

    // Check if we have digest users
    $digestUsers = array();
    if( file_exists($this->_digestFile) && false != ($handle = fopen(APPLICATION_PATH . '/install/config/auth.php', 'r')) ) {
      while( false != ($line = fgets($handle)) ) {
        $line = trim($line);
        $parts = explode(':', $line);
        if( count($parts) != 3 || $parts[2] == 'someMd5' ) continue;
        $digestUsers[] = $parts[0];
      }
    }
    $this->_digestUsers = $this->view->digestUsers = $digestUsers;

    // Check if we have database users
    $databaseUsers = array();
    if( Zend_Registry::isRegistered('Zend_Db') && ($db = Zend_Registry::get('Zend_Db')) instanceof Zend_Db_Adapter_Abstract ) {
      //$db = new Zend_Db_Adapter_Abstract();
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_users')
        ->joinRight('engine4_authorization_levels', '`engine4_authorization_levels`.`level_id` = `engine4_users`.`level_id`')
        ->where('`engine4_authorization_levels`.`flag` = ?', 'superadmin')
        ->where('`engine4_users`.`enabled` = ?', 1)
        ;

      foreach( $select->query()->fetchAll() as $dbUser ) {
        $databaseUsers[] = $dbUser['email'];
      }

      $hasDatabase = true;
    }
    $this->_databaseUsers = $this->view->databaseUsers = $databaseUsers;
  }
  
  public function indexAction()
  {
    $users = array();
    foreach( $this->_digestUsers as $user ) {
      $users[] = array(
        'type' => 'digest',
        'name' => $user,
      );
    }
    foreach( $this->_databaseUsers as $user ) {
      $users[] = array(
        'type' => 'database',
        'name' => $user,
      );
    }
    $this->view->users = $users;
  }

  public function createAction()
  {
    $this->view->form = $form = $this->_getAuthForm(false, 'Create Auth User');

    if( !$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost()) ) {
      $this->_helper->viewRenderer->setNoRender();
      $this->getResponse()->setBody($form->render($this->view));
      return;
    }

    $values = $form->getValues();
    $this->_writeAuthFile('create', $values['username'], $values['password'], 'seiran');
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  public function editAction()
  {
    $username = $this->_getParam('username');
    $this->view->form = $form = $this->_getAuthForm(true, 'Edit Auth User: ' . $username);

    if( !$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost()) ) {
      $form->populate(array(
        'username' => $username,
      ));
      $this->_helper->viewRenderer->setNoRender();
      $this->getResponse()->setBody($form->render($this->view));
      return;
    }

    $values = $form->getValues();
    $this->_writeAuthFile('edit', $values['username'], $values['password'], 'seiran');
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  public function deleteAction()
  {
    $username = $this->_getParam('username');
    $this->view->form = $form = $this->_getAuthForm(true, 'Remove Auth User: ' . $username);

    /*
    if( !$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost()) ) {
      $form->populate(array(
        'username' => $username,
      ));
      $this->_helper->viewRenderer->setNoRender();
      $this->getResponse()->setBody($form->render($this->view));
      return;
    }

    $values = $form->getValues();
    $this->_writeAuthFile('delete', $values['username'], $values['password'], 'seiran');
     */
    $this->_writeAuthFile('delete', $username, null, 'seiran');
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  protected function _writeAuthFile($action, $user = null, $password = null, $realm = 'basic')
  {
    // Check params
    if( !in_array($action, array('create', 'edit', 'delete')) ) {
      throw new Engine_Exception(sprintf('Unknown auth file action: %s', $action));
    }
    if( $action == 'create' && (!$password || !$realm) ) {
      throw new Engine_Exception('missing password or realm');
    }
    if( $action == 'edit' && !$password && !$realm ) {
      throw new Engine_Exception('missing password or realm');
    }

    // Check if exists
    $file = array();
    if( file_exists($this->_digestFile) ) {
      $file = file($this->_digestFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      if( false === $file ) {
        throw new Engine_Exception(sprintf('Unable to open auth file "%s". Please make sure install/config is writable (CHMOD 0777).', 'install/config/auth.php'));
      }
    }

    // Initialize
    if( empty($file) ) {
      $file[] = "<?php die(); ?>";
    } else if( $file[0] != "<?php die(); ?>" ) {
      array_unshift($file, "<?php die(); ?>");
    }

    // Read existing
    $found = false;
    $id = $user . ':' . $realm;
    $idLength = strlen($id);
    foreach( $file as $index => $line ) {
      if( substr($line, 0, $idLength) === $id ) {
        $found = true;
        switch( $action ) {
          case 'create':
            throw new Engine_Exception(sprintf('User "%s" already exists in realm %s.', $user, $realm));
            break;
          case 'edit':
            $file[$index] = $id . ':' . md5($user . ':' . $realm . ':' . $password);
            break;
          case 'delete':
            unset($file[$index]);
            break;
        }
        break;
      }
    }

    if( !$found ) {
      switch( $action ) {
        case 'create':
          $file[] = $id . ':' . md5($user . ':' . $realm . ':' . $password);
          break;
        case 'edit':
          if( !$found ) {
            throw new Engine_Exception(sprintf('User "%s" does not exist in realm %s.', $user, $realm));
          }
          break;
        case 'delete':
          if( !$found ) {
            throw new Engine_Exception(sprintf('User "%s" does not exist in realm %s.', $user, $realm));
          }
          break;
      }
    }
    
    if( !file_put_contents($this->_digestFile, join(PHP_EOL, $file) . PHP_EOL) ) {
      throw new Engine_Exception(sprintf('Unable to write auth file "%s". Please make sure install/config is writable (CHMOD 0777).', 'install/config/auth.php'));
    }
  }

  protected function _joinRecursive($char, $array)
  {
    $str = '';
    foreach( $array as $val ) {
      if( is_array($val) ) {
        $str .= $this->_joinRecursive($char, $val);
      } else {
        $str .= (string) $val;
      }
    }
    return $str;
  }

  protected function _getAuthForm($usernameHidden = false, $formTitle = '')
  {
    return new Engine_Form(array(
      'title' => $formTitle,
      'elements' => array(
        array(
          $usernameHidden ? 'Hidden' : 'Text',
          'username',
          array(
            'label' => 'Username',
            'required' => true,
            'allowEmpty' => false,
          ),
        ),
        array(
          'Password',
          'password',
          array(
            'label' => 'Password',
            'required' => true,
            'allowEmpty' => false,
          ),
        ),
        array(
          'Button',
          'execute',
          array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
          )
        ),
        array(
          'Cancel',
          'cancel',
          array(
            'decorators' => array('ViewHelper'),
            'link' => true,
            'prependText' => ' or ',
            'onclick' => "window.location.href = '" . $this->view->url(array('action' => 'index')) . "';"
          )
        )
      ),
      'displayGroups' => array(
        'buttons' => array('execute', 'cancel'),
      )
    ));
  }
}