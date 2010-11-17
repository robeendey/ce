<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Statistics.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Model_DbTable_Statistics extends Engine_Db_Table
{
  public function increment($type, $value = 1, $time = null)
  {
    // Check args
    if( $value === 0 ) {
      return $this;
    }
    
    if( !is_numeric($value) ) {
      throw new Engine_Exception('statistics can only handle numeric values');
    }
    
    if( null === $time ) {
      $time = time();
    }

    // Check db
    $periodValue = gmdate('Y-m-d', $time);
    //$periodValue = gmdate('Y-m-d H:i:s');

    $sign = ( $value > 0 ? '+' : '-' );
    $absValue = abs($value);

    $updateCount = $this->update(array(
      'value' => new Zend_Db_Expr('value ' . $sign . ' ' . $this->getAdapter()->quote($absValue)),
    ), array(
      'type = ?' => $type,
      'date = ?' => $periodValue,
    ));
    
    if( $updateCount < 1 ) {
      try {
        $this->insert(array(
          'value' => $value,
          'type' => $type,
          'date' => $periodValue,
        ));
      } catch( Exception $e ) {
        // Meh, just ignore
        //throw $e;
      }
    }

    return $this;
  }

  public function getTotal($type, $start = null, $end = null)
  {
    $select = new Zend_Db_Select($this->getAdapter());
    $select
      ->from($this->info('name'), 'SUM(value) as sum')
      ->where('type = ?', $type)
      ;

    // Can pass "today" into start
    switch( $start ) {
      case 'day':
        $start = mktime(0, 0, 0, gmdate("n"), gmdate("j"), gmdate("Y"));
        $end = mktime(0, 0, 0, gmdate("n"), gmdate("j") + 1, gmdate("Y"));
        break;
      case 'week':
        $start = mktime(0, 0, 0, gmdate("n"), gmdate("j") - gmdate('N') + 1, gmdate("Y"));
        $end = mktime(0, 0, 0, gmdate("n"), gmdate("j") - gmdate('N') + 1 + 7, gmdate("Y"));
        break;
      case 'month':
        $start = mktime(0, 0, 0, gmdate("n"), gmdate("j"), gmdate("Y"));
        $end = mktime(0, 0, 0, gmdate("n") + 1, gmdate("j"), gmdate("Y"));
        break;
      case 'year':
        $start = mktime(0, 0, 0, gmdate("n"), gmdate("j"), gmdate("Y"));
        $end = mktime(0, 0, 0, gmdate("n"), gmdate("j"), gmdate("Y") + 1);
        break;
    }

    if( null !== $start ) {
      $select->where('date >= ?', gmdate('Y-m-d', $start));
    }

    if( null !== $end ) {
      $select->where('date < ?', gmdate('Y-m-d', $end));
    }

    $data = $select->query()->fetch();

    if( !isset($data['sum']) ) {
      return 0;
    }

    return $data['sum'];
  }
}