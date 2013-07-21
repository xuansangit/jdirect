<?php
/**
 * @package watchfulli
 * @copyright Copyright (c) 2012-2013 watchful.li
 */
// No direct access to this file
(defined('_JEXEC') or defined('JPATH_PLATFORM')) or die;

/**
 * @name JMonitoringPluginMonitoring 
 * @author jonathan fuchs, comem+  
 * @link  www.comem.ch
 * @version 1.0.0 
 * This class manages JmonitoringPlugin. A JMP is caracterised by a name, a description,
 * some values and some alerts.
 * 
 */
abstract class JMonitoringPluginMonitoring extends JPlugin
{
  public $name;
  public $description;
  public $values;
  public $alerts;
  private $exJMonitoringPluginValues;

  /**
   * Set the name
   * @param String name - the name
   */
  public function setName($name)
  {
      $this->name = $name;
  }
  /**
   * Set the description
   * @param String description - the description
   */
  public function setDescription($description)
  {
      $this->description = $description;
  }

  /**
   * Add a value
   * @param String key - the key
   */
  public function addValue($value)
  {
    if($value !=null) $this->values[] = $value;
  }

  /**
   * Add an alert 
   * @param Alert alert - an alert
   */
  public function addAlert($alert)
  {
    if($alert != null) $this->alerts[] = $alert;
  }

  /**
   *  Create and add a jmonitoring value. Return true if ok.
   * @param type $name
   * @param JMonitoringValue $value
   * @param type $type
   * @param type $unit
   * @return boolean 
   */
  public function createJMonitoringValue($name, $value)
  {
    $createdValue = false;
    $value = new JMonitoringValue($name, $value);
    if($value !=null)
    {
      $this->addValue($value);
      $createdValue = true;
    }
    return $createdValue;
  }
  /**
   * read a JMonitoring value
   * @param type $name
   * @return type value
   */
  public function readJMonitoringValue($name)
  {
    $valueReturned = null;
    if($name != null)
    {
      $values = $this->values;
      if($values != null)
        foreach($values as $value)
          if($value->name == $name) $valueReturned = $value->value;
    }
    return $valueReturned;
  }
  /**
   *  Update existing JMonitoring value. return true if updated.
   * @param type $name
   * @param type $newVal
   * @return boolean 
   */
  public function updateJMonitoringValue($name, $newVal)
  {
    $isUpdated = false;
    if($name != null && $newVal != null)
    {
      $values = $this->values;
      if($values != null)
      foreach($values as $value)
      {
        if($value->name == $name)
        {
            $value->value = $newVal;
            $isUpdated = true;
        }
      }
    }
    return $isUpdated;     
  }
  /**
   * Delete a existing value. Return true if deleted. 
   * @param type $name
   * @return type 
   */
  public function deleteJMonitoringValue($name)
  {
    if($name != null)
    {
      $values = $this->values;
      if($values != null)
      foreach($values as $key=>$value)
      {
        if($value->name == $name)
        {
            unset($this->values[$key]);
            return true;
        }              
      }
    }
    return false;
  }
      
  /**
   * Read (get) an ex JMonitoring value. Return null if non-existent value.
   * @param type $pluginName
   * @param type $valueName
   * @return String
   */
  public function readExJMonitoringValue($exValues)
  {
    if($this->name!=null)
    {
      $exValues = str_replace('0000000000', ' ', $exValues);
      $exValues = unserialize($exValues);
      
      if($exValues != null)
      foreach($exValues as $plugin)
      {
        if ($plugin['name'] == $this->name)
        {
          $this->createJMonitoringValue($plugin['name'], $plugin['value']);
          return $plugin['value'];
        }
      }
    }
    return null;
  }
  
  /**
   * create a JMonitoring alert.
   * @param int $level
   * @param string $message 
   */
  public function createJMonitoringAlert($level, $message)
  {
    $alert = new JMonitoringAlert($level, $message);
    $this->addAlert($alert);
  }
}
/**
 * This class manages JMonitoring Value. A value is composed by a key (the name), a value,
 * a type (int, float or string) and a unit
 * @name JMonitoringValue 
 * @author jonathan fuchs, comem+  
 * @link  www.comem.ch
 * @version 1.0.0 
 */
class JMonitoringValue
{
  public $name;
  public $value;
  
  /**
   *  constructor
   * @param type $name
   * @param type $value
   * @param type $type
   * @param type $unit 
   */
  function JMonitoringValue($name, $value)
  {
    if($name !=null && $value !=null)
    {
        $this->name = $name;
        $this->value = $value;
    }
  }
}
/**
 * This class manages JMonitoringAlert. A JMonitoringAlert has a level and a message.
 * The level is a number. Differents numbers can be used.
 * "1" means that the alert is an information.
 * "2" means that the alert is an error.
 * @name JMonitoringAlert 
 * @author jonathan fuchs, comem+  
 * @link  www.comem.ch
 * @version 1.0.0 
 */
class JMonitoringAlert
{
  public $level;
  public $message;

  /**
   * constructor
   */
  function JMonitoringAlert($level, $message)
  {
      if($level !=null && $message != null)
      {
          $this->level = $level;
          $this->message = $message;
      }
  }
}
