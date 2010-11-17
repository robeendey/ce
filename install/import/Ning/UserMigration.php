<?php

class Install_Import_Ning_UserMigration extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-members-local.json';

  protected $_fromFileAlternate = 'ning-members.json';

  protected $_toTable = 'engine4_user_migration';

  protected $_priority = 2000;

  protected function _initPre()
  {
    try {
      $this->_createCompatibilityTables();
    } catch( Exception $e ) {
      $this->_error($e);
    }
  }
  
  protected function  _translateRow(array $data, $key = null)
  {
    // Check if it already exists in migration table
    $userIdentity = null;
    try {
      $userIdentity = $this->getUserMap($data['contributorName']);
    } catch( Exception $e ) {
      
    }
    if( $userIdentity ) {
      return false;
    }

    // Check if email already exists in users table
    $existingUserIdentity = $this->getToDb()->select()
      ->from('engine4_users', array('user_id', 'email'))
      ->where('email = ?', $data['email'])
      ->limit(1)
      ->query()
      ->fetchColumn(0)
      ;

    // Check if they exist in the migration table
    $existingMigrationUser = null;
    if( $existingUserIdentity ) {
      $existingMigrationUser = $this->getToDb()->select()
        ->from('engine4_user_migration')
        ->where('user_id = ?', $existingUserIdentity)
        ->limit(1)
        ->query()
        ->fetch()
        ;
        
      $this->setUpdateUser($existingUserIdentity);
    }

    if( !$existingUserIdentity || !$existingMigrationUser ) {

      $this->getToDb()->insert('engine4_user_migration', array(
        'user_id' => $existingUserIdentity,
        'email' => $data['email'],
        'user_contributor' => $data['contributorName'],
      ));

      $user_id = $this->getToDb()->lastInsertId();
      $this->setUserMap($data['contributorName'], $user_id);

    } else if( empty($existingMigrationUser['user_contributor'])
            || $existingMigrationUser['user_contributor'] == $data['contributorName'] ) {

      $this->getToDb()->update('engine4_user_migration', array(
        'user_contributor' => $data['contributorName'],
      ), array(
        'user_id = ?' => $existingUserIdentity,
      ));
      $this->setUserMap($data['contributorName'], $existingUserIdentity);

    } else {
      throw new Engine_Exception(sprintf('User (%d/%s) exists in migration %d', $existingUserIdentity, $data['email'], $existingMigrationUser['user_id']));
    }

    return false;
  }

  protected function _createCompatibilityTables()
  {
    $sql = <<<EOF
CREATE TABLE IF NOT EXISTS `engine4_user_migration` (
  `user_id` int(11) unsigned NOT NULL auto_increment,
  `user_contributor` varchar(50) collate utf8_unicode_ci NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY (`user_contributor`),
  UNIQUE KEY (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
EOF;
    $this->getToDb()->query($sql);

    // Select previously imported users
    $stmt = $this->getToDb()->select()
      ->from('engine4_user_migration')
      ->query();

    while( false != ($row = $stmt->fetch()) ) {
      $this->setUserMap($row['user_contributor'], $row['user_id']);
    }

    // Import users that have already been created
    $stmt = $this->getToDb()->select()
      ->from('engine4_users')
      ->query();

    $users = array();
    while( false != ($row = $stmt->fetch()) ) {
      $users[$row['user_id']] = $row['email'];
    }

    foreach( $users as $user_id => $email ) {
      $this->setUpdateUser($user_id);
      $this->getToDb()->insert('engine4_user_migration', array(
        'user_id' => $user_id,
        'email' => $email,
      ));
    }
  }
}