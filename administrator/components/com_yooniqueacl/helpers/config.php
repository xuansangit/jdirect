<?php
/**
 * @package	Juga
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2010 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class YooniqueaclConfig extends JObject
{
	var $default_ce							= '';
	var $admin_default_ce							= '';
	var $public_yooniqueacl						= '';
	var $default_group_admin				= '';
	var $default_group_site					= '';
	var $super_group						= '';
	
	/**
	 * constructor
	 * @return void
	 */
	function __construct() {
		parent::__construct();
		
		$this->setVariables();
	}

	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery() {

		$query = "SELECT t.* FROM  " . TABLE_YOONIQUEACL_CONFIG . " as t ";
		
		return $query;
	}
	
	/**
	 * Retrieves the data
	 * @return array Array of objects containing the data from the database
	 */
	function getData() {
		// load the data if it doesn't already exist
		if (empty( $this->_data )) {
			$database = &JFactory::getDBO();
			$query = $this->_buildQuery();
			$database->setQuery( $query );
			$this->_data = $database->loadObjectList( );
		}
		
		return $this->_data;
	}

	/**
	 * Set Variables
	 *
	 * @acces	public
	 * @return	object
	 */
	function setVariables() {
		$success = false;
		
		if ( $data = $this->getData() ) {
			for ($i=0; $i<count($data); $i++) {
				$title = $data[$i]->title;
				$value = $data[$i]->value;
				if (isset($title)) {
					$this->$title = $value;
				}
			}
			
			$success = true;
		}
		
		return $success;
	}	

	/**
	 * Get component config
	 *
	 * @acces	public
	 * @return	object
	 */
	function &getInstance() {
		static $instance;

		if (!is_object($instance)) {
			$instance = new YooniqueaclConfig();
		}

		return $instance;
	}

}
