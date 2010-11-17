<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Entity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Abstract.php 7533 2010-10-02 09:42:49Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Engine
 * @package    Engine_Entity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Engine_Entity_Table_Abstract extends Engine_Db_Table
{
  protected $_entities = array();

  public function  __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getEntity($identity)
  {
    if( !array_key_exists($identity, $this->_entities) ) {
      $this->_entities[$identity] = $this->find($identity)->current();
    }

    return $this->_entities[$identity];
  }

  public function getEntities(array $identities)
  {
    $fetch = array();
    foreach( $identities as $identity ) {
      if( !array_key_exists($identity, $this->_entities) ) {
        $fetch[] = $identity;
      }
    }
    
    foreach( $this->find($fetch) as $entity ) {
      $this->_entities[$entity->getIdentity()] = $entity;
    }

    $entities = array();
    foreach( $identities as $identity ) {
      if( !empty($this->_entities[$identity]) ) {
        $entities[] = $this->_entities[$identity];
      }
    }

    return $entities;
  }
}