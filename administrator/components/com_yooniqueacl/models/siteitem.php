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

class YooniqueaclModelSiteitem extends JModelLegacy {
 
	var $_data;
	var $_user; 

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
		$query = "SELECT r.* FROM  " . TABLE_YOONIQUEACL_ITEMS . " as r "
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
			$row = $this->getTable( 'item', 'TableYooniqueacl' );
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

			$types[] = JHTML::_('select.option', '', '- '. JText::_( 'Select Site' ).' -' );
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
	function getTypesSelectList() {
		if (empty($this->_typesSelectList)) {

			$types[] = JHTML::_('select.option', '', '- '.JText::_( 'Select Type' ).' -' );
			$types[] = JHTML::_('select.option', 'cont', ''.JText::_( 'Content' ).'' );
			$types[] = JHTML::_('select.option', 'com', ''.JText::_( 'Component' ).'' );
			
			$this->_typesSelectList = JHTML::_('select.genericlist', $types, "type", "size='1' ", "value", "text", $this->_data->type );
		
		}

		return $this->_typesSelectList;
	}
	 
	/**
	 * Creates a List
	 * @return array Array of objects containing the data from the database
	 */
	function getErrorURLPublishedSelectList() {
		if (empty($this->_errorURLPublishedSelectList)) {		
			$this->_errorURLPublishedSelectList = JHTML::_('select.booleanlist', 'error_url_published', 'class="inputbox"', $this->_data->error_url_published );
		}

		return $this->_errorURLPublishedSelectList;
	}

	/**
	 * Creates a List
	 * @return array Array of objects containing the data from the database
	 */
	function getExcludedSelectList() {
		if (empty($this->_excludedSelectList)) {
			$this->_excludedSelectList = JHTML::_('select.booleanlist', 'item_exclude', 'class="inputbox"', $this->_data->item_exclude );
		}

		return $this->_excludedSelectList;
	}

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function save() {

		$database = &JFactory::getDBO();
	
		$row = $this->getTable( 'item', 'TableYooniqueacl' );
		$row->bind( JRequest::get('POST') );		

		$row->site = 'site';

		// invalid entries
		if ( !$row->site ) {
        	$this->setError( JText::_( 'Invalid Entries - Missing Site' ) );
        	return false;
		}	

		if ( !$row->query ) {
        	$this->setError( JText::_( 'Invalid Entries - Missing Query' ) );
        	return false;
		}
		
		if ( !$row->site_option ) {
        	$this->setError( JText::_( 'Invalid Entries - Missing Option' ) );
        	return false;
		}
		
		if (!$row->created_datetime) {
			$row->created_datetime = gmdate('Y-m-d h:i:s');
		}
		
		// check if site item exists already for this query & site
			// if one exists AND it has a different ID than the one we're storing, fail & notify of attempted duplication
			$item = YooniqueaclHelperItem::getItem( $row->query, null, $row->site );
			if ( isset($item->id) && $item->id != $row->id ) {
	        	$this->setError( JText::_( 'A Site Item already exists with this Query' )." - ID: {$item->id}" );
	        	return false;
			}
		
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

		$row = $this->getTable( 'item', 'TableYooniqueacl' );
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
