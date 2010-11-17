<?php

class Install_Import_Ning_ForumSignatures extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-members-local.json';

  protected $_fromFileAlternate = 'ning-members.json';

  protected $_toTable = 'engine4_forum_signatures';
  
  protected function  _translateRow(array $data, $key = null)
  {
    $userIdentity = $this->getUserMap($data['contributorName']);

    $newData = array();

    $newData['user_id'] = $userIdentity;

    $count = $this->getToDb()->select()
      ->from('engine4_forum_posts', new Zend_Db_Expr('COUNT(*)'))
      ->where('user_id = ?', $userIdentity)
      ->query()
      ->fetchColumn(0)
      ;

    $newData['post_count'] = (int) $count;

    return $newData;
  }
}