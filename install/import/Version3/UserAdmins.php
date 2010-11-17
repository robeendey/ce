<?php

class Install_Import_Version3_UserAdmins extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_admins';

  protected $_toTable = 'engine4_users';

  protected $_priority = 50;

  protected $_toTableTruncate = false; // Should already have been done by users

  protected function  _translateRow(array $data, $key = null)
  {
    // Check for user with same email
    $userIdentity = $this->getFromDb()
      ->select()
      ->from('se_users', 'user_id')
      ->where('user_email = ?', $data['admin_email'])
      ->limit(1)
      ->query()
      ->fetchColumn(0)
      ;

    // Already a user, update the level id
    if( $userIdentity ) {
      $this->getToDb()->update('engine4_users', array(
        'level_id' => 1,
      ), array(
        'user_id = ?' => $userIdentity,
      ));
      return true;
    }

    // Not yet a user, insert
    $newData = array();
    $newData['email'] = $data['admin_email'];
    $newData['username'] = $data['admin_username'];
    $newData['displayname'] = (string) @$data['admin_name'];
    $newData['search'] = 1;
    $newData['level_id'] = 1;
    $newData['enabled'] = 1;
    $newData['verified'] = 1;
    $newData['show_profileviewers'] = 0;

    $this->getToDb()->insert('engine4_users', $newData);
    $userIdentity = $this->getToDb()->lastInsertId();
    
    // privacy
    try {
      $this->_insertPrivacy('user', $userIdentity, 'view', $this->_translatePrivacy(32));
      $this->_insertPrivacy('user', $userIdentity, 'comment', $this->_translatePrivacy(32));
    } catch( Exception $e ) {
      $this->_error('Problem adding privacy options for object id ' . $data['admin_id'] . ' : ' . $e->getMessage());
    }
    

    // Also insert into migration table
    $this->getToDb()->insert('engine4_user_migration', array(
      'user_id' => $userIdentity,
      'user_password' => $data['admin_password'],
      'user_password_method' => $data['admin_password_method'],
      'user_code' => $data['admin_code'],
      'user_is_admin' => 1,
    ));
    

    return true;
  }
}


/*
CREATE TABLE IF NOT EXISTS `se_admins` (
  `admin_id` int(9) NOT NULL auto_increment,
  `admin_username` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `admin_password` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `admin_password_method` tinyint(1) NOT NULL default '0',
  `admin_code` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  `admin_name` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `admin_email` varchar(70) collate utf8_unicode_ci NOT NULL default '',
  `admin_enabled` tinyint(1) NOT NULL default '1',
  `admin_language_id` smallint(3) NOT NULL default '1',
  `admin_lostpassword_code` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `admin_lostpassword_time` int(14) NOT NULL default '0',
  PRIMARY KEY  (`admin_id`),
  UNIQUE KEY `UNIQUE` (`admin_username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT INTO `se_admins` (`admin_id`, `admin_username`, `admin_password`, `admin_password_method`, `admin_code`, `admin_name`, `admin_email`, `admin_enabled`, `admin_language_id`, `admin_lostpassword_code`, `admin_lostpassword_time`) VALUES
(1, 'admin', 'this is an md5', 1, 'this is a salt', 'Administrator', 'email@domain.com', 1, 1, '', 0);
 * 
 */