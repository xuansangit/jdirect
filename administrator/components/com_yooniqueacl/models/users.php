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

class YooniqueaclModelUsers extends JModelLegacy {

	/**
	 * constructor
	 * @return array Array of objects containing the data from the database
	 */	
	function __construct() {
		parent::__construct();
		global $mainframe;
		$mainframe = JFactory::getApplication();
		
		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( 'yooniqueacl.limitstart', 'limitstart', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState( 'limit', $limit );
		$this->setState( 'limitstart', $limitstart );
		$this->setState( 'filter_order', strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "filter_order", 'filter_order', 'id' ) ) ) );
		$this->setState( 'filter_order_Dir', strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "filter_order_Dir", 'filter_order_Dir', 'asc' ) ) ) );

		// set the groupid
		$this->_groupid = strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "group_id", 'group_id', ' ' ) ) );

	}
	
	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery() {
		global $mainframe;
		$mainframe = JFactory::getApplication();
		
		$search				= $mainframe->getUserStateFromRequest( 'search', 'search', '',	'string' );
		$search				= JString::strtolower($search);
	
		$where = array();
			
		if (isset($search)) {
			$where[] = "LOWER(id) LIKE '%" . $this->_db->escape( trim( strtolower( $search ) ) ) . "%'";
			$where[] = "LOWER(name) LIKE '%" . $this->_db->escape( trim( strtolower( $search ) ) ) . "%'";
			$where[] = "LOWER(username) LIKE '%" . $this->_db->escape( trim( strtolower( $search ) ) ) . "%'";
			$where[] = "LOWER(email) LIKE '%" . $this->_db->escape( trim( strtolower( $search ) ) ) . "%'";
		}

		$group_query = "";
		if ( $this->_groupid > '0' ) {
			$group_query = " AND `group_id` = '".$this->_groupid."' ";
		} elseif ( $this->_groupid == '-1' ) {
			$group_query = " AND `group_id` IS NOT NULL ";
		} elseif ( $this->_groupid == '-2' ) {
			$group_query = " AND `group_id` IS NULL ";
		} 

		$filter_order_query = "";
		$filter_order = $this->getState( 'filter_order' );
		$filter_order_Dir = $this->getState( 'filter_order_Dir' );
		switch ($filter_order) {
			case "id":
				$filter_order_query = " ORDER BY u.id ".strtoupper( $filter_order_Dir )." ";
			  break;
			case "name":
				$filter_order_query = " ORDER BY u.name ".strtoupper( $filter_order_Dir )." ";
			  break;
			case "username":
				$filter_order_query = " ORDER BY u.username ".strtoupper( $filter_order_Dir )." ";
			  break;
			case "email":
				$filter_order_query = " ORDER BY u.email ".strtoupper( $filter_order_Dir )." ";
			  break;
			default:
				$filter_order_query = " ORDER BY u.id ASC ";
			  break;
		}

	
		// get the total number of records
		$query = "SELECT DISTINCT(`user_id`), u.* FROM #__users AS u "
		." LEFT JOIN  " . TABLE_YOONIQUEACL_U2G . " ON  " . TABLE_YOONIQUEACL_U2G . ".user_id = u.id "
		." WHERE 1 "
		. $group_query 		
		. (count( $where ) ? "\n HAVING " . implode( ' OR ', $where ) : "")
		. $filter_order_query 
		;
		
		return $query;
	}
	
	/**
	 * Retrieves the messages
	 * @return array Array of objects containing the data from the database
	 */
	function getData() {
		// load the data if it doesn't already exist
		if (empty( $this->_data )) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_data;
	}


	/**
	 * Paginates the data
	 * @return array Array of objects containing the data from the database
	 */
	function getPagination() {
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	/**
	 * Retrieves the count 
	 * @return array Array of objects containing the data from the database
	 */
	function getTotal() {
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Retrieves the value
	 * @return mixed
	 */
	function getField( $fieldname='fieldname', $default='' ) {
		if (empty($this->$fieldname)) {
			global $mainframe;		
			$mainframe = JFactory::getApplication();
			$data = $mainframe->getUserStateFromRequest( $fieldname, $fieldname, $default );
			$data = JString::strtolower( htmlspecialchars( $data ) );
			$this->$fieldname = $data;
		}
		return $this->$fieldname;
	}
	


	/**
	 * Retrieves the Groups
	 * @return array Array of objects containing the data from the database
	 */
	function getGroups() {
		if (empty($this->_groups)) {
			$this->_db->setQuery("SELECT * FROM  " . TABLE_YOONIQUEACL_GROUPS
								." ORDER BY `title` ASC");
			$db_groups = $this->_db->loadObjectList();
	
			$this->_groups = $db_groups;
		
		}

		return $this->_groups;
	}


	/**
	 * Retrieves the Groups
	 * @return array Array of objects containing the data from the database
	 */
	function getGroupsFilterList() {
		if (empty($this->_groupsFilterList)) {
			$db_groups = $this->getGroups();

			$types[] = JHTML::_('select.option', '0', '- '.JText::_( 'Select Group' ).' -' );
			$types[] = JHTML::_('select.option', '-1', '- '.JText::_( 'All Groups' ).' -' );
			$types[] = JHTML::_('select.option', '-2', '- '.JText::_( 'No Groups' ).' -' );
		
			if ($db_groups) {
			  foreach ($db_groups as $dbg) {
				$types[] = JHTML::_('select.option', $dbg->id, htmlspecialchars( $dbg->title ) );
			  }
			}
			$javascript 	= 'onchange="document.adminForm.submit();"';	
			$this->_groupsFilterList = JHTML::_('select.genericlist', $types, "group_id", "size='1' $javascript", "value", "text", $this->_groupid );
		
		}

		return $this->_groupsFilterList;
	}

	/**
	 * 
	 * @return 
	 * @param $msg Object
	 */	
	function enroll_flex( &$msg ) 
	{
		$success = false;
		
		$cids = JRequest :: getVar('cid', array (0), 'post', 'array');
		$flexid = $this->getField( 'flexid' );

		foreach ($cids as $cid)
		{
			$action = YooniqueaclHelper::addToGroup( $cid, $flexid );

			if ($action->error)
			{
				$msg->type = 'notice';
				$msg->message .= ' - '.$action->errorMsg.", ID: {$cid}";
			}				
			
		}
		
		return $success;
	}
	
	/**
	 * 
	 * @return 
	 * @param $msg Object
	 */	
	function withdraw_flex( &$msg ) {
		$success = false;
		
		$cids = JRequest :: getVar('cid', array (0), 'post', 'array');
		$flexid = $this->getField( 'flexid' );

		foreach ($cids as $cid)
		{
			$action = YooniqueaclHelper::removeFromGroup( $cid, $flexid );

			if ($action->error)
			{
				$msg->type = 'notice';
				$msg->message .= ' - '.$action->errorMsg.", ID: {$cid}";
			}				
			
		}
		
		return $success;
	}

	/**
	 * 
	 * @return 
	 * @param $msg Object
	 */	
	function withdraw_all( &$msg ) 
	{
		$success = true;
		// get the database object
		$database = &JFactory::getDBO();
				
		$cids = JRequest :: getVar('cid', array (0), 'post', 'array');

		foreach ($cids as $cid)
		{
			$query = "
				DELETE FROM 
					 " . TABLE_YOONIQUEACL_U2G . "
			    WHERE 
			    	`user_id` = '$cid' 
			";
			$database->setQuery( $query );
			if (!$database->query()) {
				$success = false;
				$msg->type = 'notice';
				$msg->message .= ' - '.JText::_( 'Failed to withdraw user' )." [{$cid}]";			
			}
		
		}
		
		return $success;
	}
	
}
