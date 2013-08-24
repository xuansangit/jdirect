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
 
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

/**
 * Yooniqueacl Model
 *
 */
class YooniqueaclModelVariable extends JModelLegacy {

	/**
	 * constructor
	 * @return array Array of objects containing the data from the database
	 */	
	function __construct() {
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}

	/**
	 * Method to set the identifier
	 *
	 * @access	public
	 * @param	int identifier
	 * @return	void
	 */
	function setId($id) {
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	
	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery() {
		// grab record
		$query = "SELECT r.* FROM  " . TABLE_YOONIQUEACL_VARIABLES . " as r "
		. " WHERE `id` = '".$this->_id."' "
		;
		
		return $query;
	}
	
	/**
	 * Retrieves the data
	 * @return object containing the record/data from the database
	 */
	function getData() {
		// load the data if it doesn't already exist
		if (empty( $this->_data )) {
			$row = $this->getTable( 'variable', 'TableYooniqueacl' );
			$row->load( $this->_id );
			$this->_data = $row;
		}
		return $this->_data;
	}

	/**
	 * Creates a List
	 * @return array Array of objects containing the data from the database
	 */
	function getSitesSelectList() {
		if (empty($this->_sitesSelectList)) {

			$types[] = JHTML::_('select.option', '', '- '.JText::_( 'Select Site' ).' -' );
			$types[] = JHTML::_('select.option', 'site', ''.JText::_( 'Front-end' ).'' );
			$types[] = JHTML::_('select.option', 'administrator', ''.JText::_( 'Admin-side' ).'' );
		
			$this->_sitesSelectList = JHTML::_('select.genericlist', $types, "site", "size='1' ", "value", "text", $this->_data->site );
		
		}

		return $this->_sitesSelectList;
	}

	/**
	 * Creates a List
	 * @return array Array of objects containing the data from the database
	 */
	function getForceIntegerSelectList() {
		if (empty($this->_forceintegerSelectList)) {
		
			$this->_forceintegerSelectList = JHTML::_('select.booleanlist', 'force_integer', 'class="inputbox"', $this->_data->force_integer );
		
		}

		return $this->_forceintegerSelectList;
	}
	 
	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function save() {

		$database = &JFactory::getDBO();

    	// $data = JRequest::get( 'post' );
		$site_option 		= strval ( htmlspecialchars ( JArrayHelper::getValue( $_POST, "site_option" ) ) );
		$variable 			= strval ( htmlspecialchars ( JArrayHelper::getValue( $_POST, "variable" ) ) );

		// invalid entries
		if ( !$site_option || !$variable ) {
        	$this->setError( JText::_( 'Invalid Entries' ));
        	return false;
		}	
	
		$row = $this->getTable( 'variable', 'TableYooniqueacl' );
		$row->bind( JRequest::get('POST') );

		$row->site = 'site';

		// Store the entry to the database
    	if (!$row->store()) {
        	$this->setError($database->getErrorMsg());
        	return false;
    	}
		
		return $row;
		
	}
	
	/**
	 * Method to delete a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delete( $id ) {

		$database = &JFactory::getDBO();

		$row = $this->getTable( 'variable', 'TableYooniqueacl' );
		// load the row from the db table
		$row->load( (int)$id );
		
		if ( $row->id <= 0 ) {
			return false;
		}
		
		if ( $row->delete( $id ) ) {
			return $id;
		}

	}	
}
