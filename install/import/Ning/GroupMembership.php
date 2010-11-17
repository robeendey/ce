<?php

class Install_Import_Ning_GroupMembership extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-groups-local.json';

  protected $_fromFileAlternate = 'ning-groups.json';

  protected $_toTable = 'engine4_group_membership';

  protected function  _translateRow(array $data, $key = null)
  {
    if( !isset($data['members']) || !is_array($data['members']) || count($data['members']) < 1 ) {
      return false;
    }

    $groupIdentity = $key + 1;

    foreach( $data['members'] as $memberKey => $memberData ) {
      $memberUserIdentity = $this->getUserMap($memberData['contributorName']);
      $this->getToDb()->insert($this->getToTable(), array(
        'resource_id' => $groupIdentity,
        'user_id' => $memberUserIdentity,
        'active' => true,
        'resource_approved' => true,
        'user_approved' => true,
      ));
    }
    
    return false;
  }
}