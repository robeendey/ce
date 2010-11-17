<?php

class Install_Import_Ning_ForumPosts extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-discussions-local.json';

  protected $_fromFileAlternate = 'ning-discussions.json';

  protected $_toTable = 'engine4_forum_posts';

  protected function  _translateRow(array $data, $key = null)
  {
    if( !empty($data['groupId']) || empty($data['category']) ) {
      return false;
    }

    $userIdentity = $this->getUserMap($data['contributorName']);
    $topicIdentity = $key + 1;


    // unshift primary post
    $posts = (array) @$data['comments'];
    array_unshift($posts, array(
      'id' => $data['id'],
      'contributorName' => $data['contributorName'],
      'description' => $data['description'],
      'createdDate' => $data['createdDate'],
    ));

    foreach( $posts as $postData ) {
      $postUserIdentity = $this->getUserMap($postData['contributorName']);
      $this->getToDb()->insert($this->getToTable(), array(
        'topic_id' => $topicIdentity,
        'user_id' => $postUserIdentity,
        'body' => $postData['description'],
        'creation_date' => $this->_translateTime($postData['createdDate']),
        'modified_date' => $this->_translateTime($postData['createdDate']),
      ));
      $lastPostId = $this->getToDb()->lastInsertId();
      $lastPosterId = $postUserIdentity;
    }

    // Update last post?
    if( count($posts) > 1 && $lastPostId && $lastPosterId ) {
      $this->getToDb()->update('engine4_forum_topics', array(
        'lastpost_id' => $lastPostId,
        'lastposter_id' => $lastPosterId,
      ), array(
        'topic_id = ?' => $topicIdentity,
      ));

      $forum_id = $this->getToDb()->select()
        ->from('engine4_forum_topics', 'forum_id')
        ->where('topic_id = ?', $topicIdentity)
        ->limit(1)
        ->query()
        ->fetchColumn(0);
      if( $forum_id ) {
        $this->getToDb()->update('engine4_forum_forums', array(
          'lastpost_id' => $lastPostId,
          'lastposter_id' => $lastPosterId,
        ), array(
          'forum_id = ?' => $forum_id,
        ));
      }
    }

    // Update signature?

    return false;
  }
}