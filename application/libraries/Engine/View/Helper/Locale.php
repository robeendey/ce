<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Locale.php 7543 2010-10-04 07:06:51Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Engine_View_Helper_Locale extends Zend_View_Helper_Abstract
{
  /**
   * The current locale
   * 
   * @var Zend_Locale
   */
  protected $_locale;

  /**
   * Accessor
   * 
   * @return Engine_View_Helper_Locale
   */
  public function locale()
  {
    return $this;
  }
  
  /**
   * Magic caller
   * 
   * @param string $method
   * @param array $args
   * @return mixed
   */
  public function __call($method, array $args)
  {
    $locale = $this->getLocale();
    $r = new ReflectionMethod($locale, $method);
    return $r->invokeArgs($locale, $args);
  }

  /**
   * Set the current locale
   * 
   * @param string|Zend_Locale $locale
   * @return Engine_View_Helper_Locale
   */
  public function setLocale($locale)
  {
    if( is_string($locale) )
    {
      $locale = new Zend_Locale($locale);
    }

    if( !$locale instanceof Zend_Locale )
    {
      throw new Zend_View_Exception('Not passed locale object or valid locale string');
    }

    $this->_locale = $locale;
    return $this;
  }

  /**
   * Get the current locale. Defaults to locale in registry
   * 
   * @return Zend_Locale
   */
  public function getLocale()
  {
    if( null === $this->_locale )
    {
      $this->_locale = Zend_Registry::get('Locale');
    }

    return $this->_locale;
  }

  public function getTimezone()
  {
    return Zend_Registry::get('timezone');
  }

  /**
   * Format a number according to locale and currency
   * @param  integer|float  $number
   * @return string
   * @see Zend_Currency::toCurrency()
   */
  public function toCurrency($value, $currency, $options = array())
  {
    $options = array_merge(array(
      'locale' => $this->getLocale(),
      'display' => 2,
      'precision' => 2,
    ), $options);

    // Doesn't like locales w/o regions
    if( is_object($options['locale']) ) {
      $locale = $options['locale']->__toString();
    } else {
      $locale = (string) $options['locale'];
    }
    if( strlen($locale) < 5 ) {
      $locale = Zend_Locale::getBrowser();
      if( is_array($locale) ) {
        foreach( $locale as $browserLocale => $q ) {
          if( strlen($browserLocale) >= 5 ) {
            $locale = $browserLocale;
            break;
          }
        }
      }
      if( !$locale || strlen($locale) < 5 ) {
        $locale = 'en_US';
      }
    }
    unset($options['locale']);
    
    $currency = new Zend_Currency($currency, $locale);
    return $currency->toCurrency($value, $options);
  }
  
  /**
   * Format a number according to locale
   * @param mixed $number
   * @see Zend_Locale_Format::toNumber()
   */
  public function toNumber($number, $options = array())
  {
    $options = array_merge(array(
      'locale' => $this->getLocale()
    ), $options);
    
    return Zend_Locale_Format::toNumber($number, $options);
  }

  public function toTime($time, $options = array())
  {
    /*
    $options = array_merge(array(
      'locale' => $this->getLocale()
    ), $options);
    $format = Zend_Locale_Format::getTimeFormat($this->getLocale());
    return date($format, $time);
     *
     */
    if( is_numeric($time) ) {
      $time = new Zend_Date($time);
    } else if( is_string($time) ) {
      $time = new Zend_Date(strtotime($time));
    } else if( !($time instanceof Zend_Date) ) {
      return false;
    }

    $time->setTimezone( Zend_Registry::get('timezone') );

    $options = array_merge(array(
      'locale' => $this->getLocale(),
      'size' => 'short',
      'type' => 'time',
    ), $options);

    if( !($time instanceof Zend_Date) ) {
      throw new Exception('Not a valid date');
    }

    if( empty($options['format']) ) {
      $options['format'] = Zend_Locale_Data::getContent($options['locale'], $options['type'], $options['size']);
    }

    return $time->toString($options['format'], $this->getLocale());
  }

  public function toDate($date, $options = array())
  {
    if( is_numeric($date) ) {
      $date = new Zend_Date($date);
    } else if( is_string($date) ) {
      $date = new Zend_Date(strtotime($date));
    } else if( !($date instanceof Zend_Date) ) {
      return false;
    }

    $date->setTimezone( Zend_Registry::get('timezone') );

    $options = array_merge(array(
      'locale' => $this->getLocale(),
      'size' => 'short',
    ), $options);

    if( !($date instanceof Zend_Date) ) {
      throw new Exception('Not a valid date');
    }

    $format = Zend_Locale_Data::getContent($options['locale'], 'date', $options['size']);
    return $date->toString($format, $this->getLocale());
  }

  public function toDateTime($datetime, $options = array())
  {
    $datetime = $this->_checkDateTime($datetime, @$options['timezone']);
    if( !$datetime ) {
      return false;
    }
    unset($options['timezone']);

    $options = array_merge(array(
      'locale' => $this->getLocale(),
      'size' => 'long',
    ), $options);
    
    $format = Zend_Locale_Data::getContent($options['locale'], 'datetime', $options['size']);
    return $datetime->toString($format, $this->getLocale());
  }

  public function toDateTimeInterval($start, $end, $options = array())
  {
    $start = $this->_checkDateTime($start, @$options['timezone']);
    $end = $this->_checkDateTime($end, @$options['timezone']);
    if( !$start || !$end ) {
      return false;
    }
    unset($options['timezone']);

    $options = array_merge(array(
      'locale' => $this->getLocale(),
      //'size' => 'long',
      'format' => 'MEd',
    ), $options);

    $options['locale'] = 'ja';

    $format = Zend_Locale_Data::getContent($options['locale'], 'dateinterval', $options['format']);
    var_dump(Zend_Locale_Format::getDate($start, array(
      'format' => $format)));die();


    if( preg_match('/^(.+?)(\s*[–-～]\s*)(.+?)$/iu', $format, $matches) ) {
      var_dump($matches);die();
    } else {
      // Sigh
      echo 'zzz';
    }


    var_dump($format);
    var_dump($start->toString($format, $this->getLocale()));
    die();
    
  }

  protected function _checkDateTime($datetime, $timezone = null)
  {
    if( is_numeric($datetime) ) {
      $datetime = new Zend_Date($datetime);
    } else if( is_string($datetime) ) {
      $datetime = new Zend_Date(strtotime($datetime));
    } else if( !($datetime instanceof Zend_Date) ) {
      return false;
    }
    
    if( !($datetime instanceof Zend_Date) ) {
      throw new Engine_Exception('Not a valid date');
    }

    if( null === $timezone && Zend_Registry::isRegistered('timezone') ) {
      $timezone = Zend_Registry::get('timezone');
    }
    
    $datetime->setTimezone($timezone);
    
    return $datetime;
  }
}