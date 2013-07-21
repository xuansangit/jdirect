<?php
/**
 * @package watchfulli
 * @copyright Copyright (c) 2012-2013 watchful.li
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class watchfulliApps extends JPlugin
{
  public $name;
  public $description;
  public $values;
  public $alerts;
  private $exAppPluginValues;

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
   *  Create and add a App value. Return true if ok.
   * @param type $name
   * @param AppValue $value
   * @param type $type
   * @param type $unit
   * @return boolean 
   */
  public function createAppValue($name, $value)
  {
    $createdValue = false;
    $value = new AppValue($name, $value);
    if($value !=null)
    {
      $this->addValue($value);
      $createdValue = true;
    }
    return $createdValue;
  }
  /**
   * read a App value
   * @param type $name
   * @return type value
   */
  public function readAppValue($name)
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
   *  Update existing App value. return true if updated.
   * @param type $name
   * @param type $newVal
   * @return boolean 
   */
  public function updateAppValue($name, $newVal)
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
  public function deleteAppValue($name)
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
   * Read (get) an ex App value. Return null if non-existent value.
   * @param type $pluginName
   * @param type $valueName
   * @return String
   */
  public function readExAppValue($exValues)
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
          $this->createAppValue($plugin['name'], $plugin['value']);
          return $plugin['value'];
        }
      }
    }
    return null;
  }
  
  /**
   * create a App alert.
   * @param int $level
   * @param string $message 
   */
  public function createAppAlert($level, $message, $parameter1=null, $parameter2=null, $parameter3=null)
  {
    $alert = new AppVariableAlert($level, $message, $parameter1, $parameter2, $parameter3);
    $this->addAlert($alert);
  }
}
/**
 * This class manages App Value. A value is composed by a key (the name), a value,
 * a type (int, float or string) and a unit
 * @name AppValue 
 * @author jonathan fuchs, comem+  
 * @link  www.comem.ch
 * @version 1.0.0 
 */
class AppValue
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
  function AppValue($name, $value)
  {
    if($name !=null && $value !=null)
    {
        $this->name = $name;
        $this->value = $value;
    }
  }
}
/**
 * This class manages AppAlert. A AppAlert has a level and a message and a few parameters.
 * The level is a number. Differents numbers can be used.
 * "1" means that the alert is an information.
 * "2" means that the alert is an error.
 * The parameters will be passed as variables to language strings
 * @name AppAlert 
 * @author jonathan fuchs, comem+  
 * @link  www.comem.ch
 * @version 1.0.0 
 */
class AppAlert
{
  public $level;
  public $message;
  public $parameter1;
  public $parameter2;
  public $parameter3;  

  /**
   * constructor
   */
  function AppAlert($level, $message, $parameter1=null, $parameter2=null, $parameter3=null )
  {
      if($level !=null && $message != null)
      {
        $this->level = $level;
        $this->message = $message;
        $this->parameter1 = $parameter1;
        $this->parameter2 = $parameter2;
        $this->parameter3 = $parameter3;
      }
  }
}
?>
