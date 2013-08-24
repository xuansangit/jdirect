<?php
/**
 * @version   1.4.17
 * @date      Fri Mar 29 15:34:01 2013 -0700
 * @package   yoonique ACL
 * @author    yoonique[.]net
 * @copyright Copyright (C) 2012 yoonique[.]net and all rights reserved.
 *
 * based on
 *
 * @package	Juga
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2010 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

class YooniqueaclModelConfig extends JModelLegacy 
{

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function save() 
	{
		$success = true;
		$errorMsg = '';

		$config = &YooniqueaclConfig::getInstance();
		$properties = $config->getProperties();
					
		if ($properties) { foreach ($properties as $key => $value ) {
			unset($row);
			$row = $this->getTable( 'config','TableYooniqueacl' );
			$newvalue = JRequest::getVar( $key );
			$post = JRequest::get( 'post' );
			$value_exists = array_key_exists( $key, $post );
			if ( $value_exists && !empty($key) ) 
			{ 
				// proceed if newvalue present in request. prevents overwriting for none existant values.
				$row->load( $key );
				$row->title = $key;
				$row->value = $newvalue;
				if (!$row->store() ) {
					$success = false;
					$errorMsg .= JText::_( "Could not store")." $key - ";	
				}
			}
		} }
		
		$this->setError( $errorMsg );
		
		return $success;
	}

}
