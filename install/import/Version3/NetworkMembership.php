<?php

class Install_Import_Version3_NetworkMembership extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_users';

  protected $_toTable = 'engine4_network_membership';

  protected function _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['resource_id'] = $data['user_subnet_id'];
    $newData['user_id'] = $data['user_id'];
    $newData['active'] = true;
    $newData['resource_approved'] = true;
    $newData['user_approved'] = true;
    
    return $newData;
  }
}

/*
CREATE TABLE `engine4_network_membership` (
  `resource_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL default '0',
  `resource_approved` tinyint(1) NOT NULL default '0',
  `user_approved` tinyint(1) NOT NULL default '0',
  PRIMARY KEY (`resource_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */