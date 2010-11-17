<?php

class Install_Import_Ning_EventMembership extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-events-local.json';

  protected $_fromFileAlternate = 'ning-events.json';

  protected $_toTable = 'engine4_event_membership';

  protected function  _translateRow(array $data, $key = null)
  {
    if( !isset($data['members']) || !is_array($data['members']) || count($data['members']) < 1 ) {
      return false;
    }

    $eventIdentity = $key + 1;

    foreach( $data['members'] as $memberKey => $memberData ) {
      $memberUserIdentity = $this->getUserMap($memberData['contributorName']);
      $rsvp = 0;
      switch( @$memberData['attendeeStatus'] ) {
        case 'attending':
          $rsvp = 2;
          break;
        case 'maybe':
          $rsvp = 2;
          break;
        default:
          $rsvp = 0;
          break;
      }

      $this->getToDb()->insert($this->getToTable(), array(
        'resource_id' => $eventIdentity,
        'user_id' => $memberUserIdentity,
        'active' => true,
        'resource_approved' => true,
        'user_approved' => true,
        'rsvp' => $rsvp,
      ));
    }
    
    return false;
  }
}