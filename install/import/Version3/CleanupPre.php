<?php

class Install_Import_Version3_CleanupPre extends Install_Import_Version3_Abstract
{
  protected $_toTableTruncate = false;
  
  protected $_priority = 20000;

  protected function _run()
  {
    // Truncate engine4_authorization_allow
    $this->_message('Truncating privacy tables', 2);
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_authorization_allow'));

    // Truncate engine4_activity_actions engine4_activity_stream engine4_activity_actionsettings engine4_activity_attachments
    $this->_message('Truncating activity tables', 2);
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_activity_actions'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_activity_actionsettings'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_activity_attachments'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_activity_stream'));

    // Truncate engine4_activity_notifications
    $this->_message('Truncating notifications tables', 2);
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_activity_notifications'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_activity_notificationsettings'));

    // Truncate engine4_core_comments engine4_activity_comments engine4_activity_likes engine4_core_likes engine4_core_status
    $this->_message('Truncating comment tables', 2);
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_activity_comments'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_activity_likes'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_core_comments'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_core_likes'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_core_status'));

    // Truncate engine4_core_geotags engine4_core_tagmaps engine4_core_tags
    $this->_message('Truncating tag tables', 2);
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_core_geotags'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_core_tagmaps'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_core_tags'));

    // Truncate engine4_core_auth engine4_core_session
    $this->_message('Truncating session tables', 2);
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_core_auth'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_core_session'));

    // Truncate engine4_core_search
    $this->_message('Truncating search tables', 2);
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_core_search'));

    // Truncate engine4_core_mail engine4_core_mailrecipients
    $this->_message('Truncating mail queue tables', 2);
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_core_mail'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_core_mailrecipients'));

    // Truncate engine4_core_links
    $this->_message('Truncating links tables', 2);
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_core_links'));

    // Truncate engine4_storage_chunks engine4_storage_files
    $this->_message('Truncating storage system tables', 2);
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_storage_chunks'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_storage_files'));

    // Truncate engine4_user_facebook engine4_user_forgot engine4_user_listitems engine4_user_lists engine4_user_online engine4_user_verify
    $this->_message('Truncating user tables', 2);
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_user_facebook'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_user_forgot'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_user_listitems'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_user_lists'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_user_online'));
    $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_user_verify'));

    // Truncate engine4_event_posts engine4_event_topics
    if( $this->_tableExists($this->getToDb(), 'engine4_event_events') ) {
      $this->_message('Truncating event tables', 2);
      $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_event_posts'));
      $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_event_topics'));
    }

    // Truncate engine4_event_posts engine4_event_topics
    if( $this->_tableExists($this->getToDb(), 'engine4_group_groups') ) {
      $this->_message('Truncating group tables', 2);
      $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_group_lists'));
      $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_group_listitems'));
    }
  }

  protected function  _translateRow(array $data, $key = null)
  {
    return false;
  }
}