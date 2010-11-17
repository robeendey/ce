<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Performance.php 7250 2010-09-01 07:42:35Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Form_Admin_Settings_Performance extends Engine_Form
{
  public function init()
  {
    // Set form attributes
    $this->setTitle('Performance & Caching');
    $this->setDescription(strtoupper(get_class($this) . '_description'));

    if (APPLICATION_ENV != 'production') {
      $this->addError('Note: your site is currently not in production mode, so caching will be disabled regardless of these settings.');
    }

    $this->addElement('Radio', 'enable', array(
      'label' => 'Use Cache?',
      'description' => strtoupper(get_class($this) . '_enable_description'),
      'required' => true,
      'multiOptions' => array(
        1 => 'Yes, enable caching.',
        0 => 'No, do not enable caching.',
      ),
    ));

    $this->addElement('Text', 'lifetime', array(
      'label' => 'Cache Lifetime',
      'description' => strtoupper(get_class($this) . '_lifetime_description'),
      'size' => 5,
      'maxlength' => 4,
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('Int'),
      ),
    ));

    $this->addElement('Radio', 'type', array(
      'label' => 'Caching Feature',
      'description' => strtoupper(get_class($this) . '_type_description'),
      'required' => true,
      'allowEmpty' => false,
      'multiOptions' => array(
        'File'      => 'File-based',
        'Memcached' => 'Memcache',
        'Apc'       => 'APC',
        'Xcache'    => 'Xcache',
      ),//Zend_Cache::$standardBackends,
      'onclick' => 'updateFields();',
    ));

    $this->addElement('Text', 'file_path', array(
      'label' => 'File-based Cache Directory',
      'description' => strtoupper(get_class($this) . '_file_path_description'),
    ));

    $this->addElement('Checkbox', 'file_locking', array(
      'label' => 'File locking?',
    ));

    $this->addElement('Text', 'memcache_host', array(
      'label' => 'Memcache Host',
      'description' => 'Can be a domain name, hostname, or an IP address (recommended)',
    ));

    $this->addElement('Text', 'memcache_port', array(
      'label' => 'Memcache Port',
    ));

    $this->addElement('Checkbox', 'memcache_compression', array(
      'label' => 'Memcache compression?',
      'title' => 'Title?',
      'description' => 'Compression will decrease the amount of memory used, however will increase processor usage.',
    ));


    $this->addElement('Text', 'xcache_username', array(
      'label' => 'Xcache Username',
    ));

    $this->addElement('Text', 'xcache_password', array(
      'label' => 'Xcache Password',
    ));

    $this->addElement('Checkbox', 'flush', array(
      'label' => 'Flush cache?',
    ));
    
    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }

  public function populate($current_cache=array()) {

    $enabled = true;
    if (isset($current_cache['frontend']['core']['caching']))
      $enabled = $current_cache['frontend']['core']['caching'];
    $this->getElement('enable')->setValue($enabled);

    $backend = Engine_Cache::getDefaultBackend();
    if (isset($current_cache['backend'])) {
      $backend = array_keys($current_cache['backend']);
      $backend = $backend[0];
    }
    $this->getElement('type')->setValue($backend);

    $file_path = $current_cache['default_file_path'];
    if (isset($current_cache['backend']['File']['cache_dir']))
      $file_path = $current_cache['backend']['File']['cache_dir'];
    $this->getElement('file_path')->setValue( $file_path );

    $file_locking = 1;
    if (isset($current_cache['backend']['File']['file_locking']))
      $file_locking = $current_cache['backend']['File']['file_locking'];
    $this->getElement('file_locking')->setValue( $file_locking );

    $lifetime = 300; // 5 minutes
    if (isset($current_cache['frontend']['core']['options']['lifetime']))
      $lifetime = $current_cache['frontend']['core']['options']['lifetime'];
    $this->getElement('lifetime')->setValue($lifetime);

    $memcache_host = '127.0.0.1';
    $memcache_port = '11211';
    if (isset($current_cache['backend']['Memcache']['servers'][0]['host']))
      $memcache_host = $current_cache['backend']['Memcache']['servers'][0]['host'];
    if (isset($current_cache['backend']['Memcache']['servers'][0]['port']))
      $memcache_port = $current_cache['backend']['Memcache']['servers'][0]['port'];
    $this->getElement('memcache_host')->setValue($memcache_host);
    $this->getElement('memcache_port')->setValue($memcache_port);


  }
}