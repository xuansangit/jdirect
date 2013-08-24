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
class YooniqueaclModelGroup extends JModelLegacy {
 
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
		$query = "SELECT r.* FROM  " . TABLE_YOONIQUEACL_GROUPS . " as r "
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
			$row = $this->getTable( 'group', 'TableYooniqueacl' );
			$row->load( $this->_id );
			$this->_data = $row;
		}
		return $this->_data;
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
		$title 		= strval ( htmlspecialchars ( JArrayHelper::getValue( $_POST, "title" ) ) );
		$description 	= strval ( htmlspecialchars ( JArrayHelper::getValue( $_POST, "description" ) ) );

		// invalid entries
		if ( !$title ) {
        	$this->setError( JText::_( 'Invalid Entries - Missing Title' ) );
        	return false;
		}	
	
		$row = $this->getTable( 'group', 'TableYooniqueacl' );
		$row->bind( JRequest::get('POST') );
		$row->description = JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWRAW);

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

		$row = $this->getTable( 'group', 'TableYooniqueacl' );
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
