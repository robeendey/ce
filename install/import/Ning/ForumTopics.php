<?php

class Install_Import_Ning_ForumTopics extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-discussions-local.json';

  protected $_fromFileAlternate = 'ning-discussions.json';

  protected $_toTable = 'engine4_forum_topics';

  protected function  _translateRow(array $data, $key = null)
  {
    if( !empty($data['groupId']) || empty($data['category']) ) {
      return false;
    }

    $userIdentity = $this->getUserMap($data['contributorName']);
    $topicIdentity = $key + 1;

    // Get forum id
    switch( $data['category'] ) {
      case 'announcements';
        $forum_id = 1;
        break;
      case 'introductions':
        $forum_id = 5;
        break;
      case 'other stuff':
        $forum_id = 4;
        break;
      default:
        $title = ucwords($data['category']);
        $forum_id = $this->getToDb()->select()
          ->from('engine4_forum_forums', array('forum_id'))
          ->where('title = ?', $title)
          ->limit(1)
          ->query()
          ->fetchColumn(0)
          ;
        if( !$forum_id ) {
          // Insert forum
          $this->getToDb()->insert('engine4_forum_forums', array(
            'category_id' => 2,
            'title' => $title,
            'description' => '',
            'creation_date' => $this->_translateTime(strtotime($data['createdDate'])),
            'modified_date' => $this->_translateTime(strtotime($data['updatedDate'])),
          ));
          $forum_id = $this->getToDb()->lastInsertId();
        }
        break;
    }


    
    $newData = array();

    $newData['topic_id'] = $topicIdentity;
    $newData['forum_id'] = $forum_id;
    $newData['user_id'] = $userIdentity;
    $newData['title'] = $data['title'];
    $newData['description'] = $data['description'];
    $newData['creation_date'] = $this->_translateTime(strtotime($data['createdDate']));
    $newData['modified_date'] = $this->_translateTime(strtotime($data['updatedDate']));
    $newData['sticky'] = false;
    $newData['closed'] = false;
    $newData['post_count'] = ( empty($data['comments']) ? 0 : count($data['comments']) ) + 1;

    return $newData;
  }
}