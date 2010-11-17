<?php

class Install_Import_Version3_UserMigration extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_users';

  protected $_toTable = 'engine4_user_migration';

  protected $_priority = 10000;

  protected $_toTableTruncate = false;

  protected function _initPre()
  {
    $this->_createCompatibilityTables();
  }
  
  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();
    
    $newData['user_id'] = $data['user_id'];
    $newData['user_password'] = $data['user_password'];
    $newData['user_password_method'] = $data['user_password_method'];
    $newData['user_code'] = $data['user_code'];
    
    return $newData;
  }

  protected function _createCompatibilityTables()
  {
    $sql = 'DROP TABLE IF EXISTS `engine4_user_migration`';
    $this->getToDb()->query($sql);

    $sql = <<<EOF
CREATE TABLE IF NOT EXISTS `engine4_user_migration` (
  `user_id` int(11) unsigned NOT NULL,
  `user_password` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `user_password_method` tinyint(1) NOT NULL default '0',
  `user_code` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `user_is_admin` tinyint(1) NOT NULL default '0',
  `user_id_original` int(11) unsigned NULL,
  PRIMARY KEY (`user_id`)
)
EOF;
    $this->getToDb()->query($sql);

    // Add row to the database to flag b/c password handling
    $this->_insertOrUpdate($this->getToDb(), 'engine4_core_settings', array(
      'name' => 'core.compatibility.password',
      'value' => 'import-version-3',
    ), array(
      'value' => 'import-version-3',
    ));
  }
}