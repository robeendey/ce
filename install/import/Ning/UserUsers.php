<?php

class Install_Import_Ning_UserUsers extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-members-local.json';

  protected $_fromFileAlternate = 'ning-members.json';

  protected $_toTable = 'engine4_users';

  protected $_priority = 1000;

  protected $_staticSalt;

  protected function _initPre()
  {
    // get static salt
    $this->_staticSalt = (string) $this->getToDb()->select()
      ->from('engine4_core_settings', 'value')
      ->where('name = ?', 'core.secret')
      ->limit(1)
      ->query()
      ->fetchColumn(0)
      ;
    if( !$this->_staticSalt ) {
      $this->_staticSalt = 'staticSalt';
    }
  }
  
  protected function  _translateRow(array $data, $key = null)
  {
    // Get user id
    $userIdentity = $this->getUserMap($data['contributorName']);

    // Get username
    $userName = preg_replace('/[^a-zA-Z0-9]/', '', $data['fullName']);
    $hasUserName = true;
    $i = 0;
    do {
      $existingNameId = $this->getToDb()->select()
        ->from('engine4_users', 'user_id')
        ->where('username = ?', $userName . ( $i ? $i : ''))
        ->limit(1)
        ->query()
        ->fetchColumn(0)
        ;
      if( !$existingNameId || $existingNameId == $userIdentity ) {
        $hasUserName = false;
        $userName = $userName . ( $i ? $i : '');
      } else {
        $i++;
      }

    } while( $hasUserName );


    //
    $newData = array();

    $newData['user_id'] = $userIdentity;
    $newData['email'] = $data['email'];
    $newData['displayname'] = $data['fullName'];
    $newData['username'] = $userName;
    $newData['creation_date'] = $this->_translateTime($data['createdDate']);
    $newData['level_id'] = $this->getLevel($data['level']);
    $newData['verified'] = true;
    $newData['enabled'] = ( $data['state'] == 'active' );

    // fill in defaults
    $newData['status'] = '';
    $newData['status_date'] = 'NULL';
    $newData['search'] = true;
    $newData['creation_ip'] = 0;

    $newData['lastlogin_date'] = '0000-00-00 00:00';
    $newData['lastlogin_ip'] = 0;

    $newData['show_profileviewers'] = false;


    // privacy
    try {
      $this->_insertPrivacy('user', $newData['user_id'], 'view');
      $this->_insertPrivacy('user', $newData['user_id'], 'comment');
    } catch( Exception $e ) {
      
    }
    

    // photo
    if( !empty($data['profilePhoto']) ) {
      $info = parse_url($data['profilePhoto']);
      $file = $this->getFromPath() . '/' . $info['path'];

      $file_id = $this->_translatePhoto($file, array(
        'parent_type' => 'user',
        'parent_id' => $userIdentity,
        'user_id' => $userIdentity,
      ));

      if( $file_id ) {
        $newData['photo_id'] = $file_id;
      }
    }

    if( $this->isUpdateUser($userIdentity) ) {
      $this->getToDb()->update($this->getToTable(), $newData, array(
        'user_id = ?' => $userIdentity,
      ));
      return false;
    }


    // Email them a generated password
    if( 'random' == $this->getParam('passwordRegeneration') ) {

      // Make password
      $passwordRaw = $this->_generateRandomPassword();

      $newData['salt'] = (string) rand(1000000, 9999999);
      $newData['password'] = md5( $this->_staticSalt . $passwordRaw . $newData['salt'] );

      // Make email
      $fromAddress = $this->getParam('mailFromAddress');
      $subject = $this->getParam('mailSubject');
      $message = $this->getParam('mailTemplate');

      $search = array(
        '{name}',
        '{siteUrl}',
        '{email}',
        '{password}',
      );
      
      $replace = array(
        $newData['displayname'],
        'http://' . $_SERVER['HTTP_HOST'] . str_replace('\\', '/', dirname(dirname($_SERVER['PHP_SELF']))),
        $newData['email'],
        $passwordRaw,
      );

      list($subject, $message) = str_replace($search, $replace, array($subject, $message));

      // Make mail
      $messageText = strip_tags($message);

      if( $messageText == $message ) {
        $message = nl2br($message);
      }

      $mail = new Zend_Mail();
      $mail
        ->setFrom($fromAddress)
        ->addTo($newData['email'])
        ->setSubject($subject)
        ->setBodyHtml($message)
        ->setBodyText($messageText)
        ;

      try {
        $this->getToDb()->insert('engine4_core_mail', array(
          'type' => 'zend',
          'body' => serialize($mail),
          'priority' => 200,
          'recipient_count' => 1,
          'recipient_total' => 1,
          'creation_time' => new Zend_Db_Expr('NOW()'),
        ));
      } catch( Exception $e ) {
        $this->_error($e);
      }
    }

    return $newData;
  }


  protected function _generateRandomPassword($length = 8, $charlist = null)
  {
    if( !is_int($length) || $length < 1 || $length > 32 ) {
      $length = 8;
    }
    if( !$charlist ) {
      $charlist = 'abcdefghjkmnpqstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // No iolIO10
      //$charlist = 'abcdefghijklmnopqstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    }

    $password = '';
    do {
      $password .= $charlist[rand(0, strlen($charlist)-1)];
    } while( strlen($password) < $length );

    return $password;
  }
}