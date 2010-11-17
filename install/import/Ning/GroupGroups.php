<?php

class Install_Import_Ning_GroupGroups extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-groups-local.json';

  protected $_fromFileAlternate = 'ning-groups.json';

  protected $_toTable = 'engine4_group_groups';

  protected $_priority = 700;

  protected function  _translateRow(array $data, $key = null)
  {
    $userIdentity = $this->getUserMap($data['contributorName']);
    $groupIdentity = $key + 1;
    $this->setGroupMap($data['id'], $groupIdentity);

    $newData = array();

    $newData['group_id'] = $groupIdentity;
    $newData['user_id'] = $userIdentity;
    $newData['title'] = $data['title'];
    $newData['description'] = (string) @$data['description'];
    $newData['search'] = 1;
    $newData['invite'] = ( $data['allowInvitations'] == 'Y' );
    $newData['approval'] = ( @$data['isPrivate'] && @$data['allowInvitationRequests'] == 'Y' );
    $newData['creation_date'] = $this->_translateTime($data['createdDate']);
    $newData['modified_date'] = $this->_translateTime($data['updatedDate']);
    $newData['member_count'] = count($data['members']);
    $newData['view_count'] = 0;

    // privacy
    if( @$data['isPrivate'] ) {
      $this->_insertPrivacy('group', $newData['group_id'], 'view', 'member');
      $this->_insertPrivacy('group', $newData['group_id'], 'comment', 'member');
      $this->_insertPrivacy('group', $newData['group_id'], 'photo', 'member');
    } else {
      $this->_insertPrivacy('group', $newData['group_id'], 'view');
      $this->_insertPrivacy('group', $newData['group_id'], 'comment');
      $this->_insertPrivacy('group', $newData['group_id'], 'photo', 'member');
    }
    
    // photo
    if( !empty($data['iconUrl']) ) {
      $info = parse_url($data['iconUrl']);
      $file = $this->getFromPath() . '/' . $info['path'];

      $file_id = $this->_translatePhoto($file, array(
        'parent_type' => 'group',
        'parent_id' => $groupIdentity,
        'user_id' => $userIdentity,
      ));

      if( $file_id ) {
        $newData['photo_id'] = $file_id;
      }
    }

    return $newData;
  }
}