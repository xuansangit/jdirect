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
class YooniqueaclModelSiteitems extends JModelLegacy {
	
	/**
	 * constructor
	 * @return array Array of objects containing the data from the database
	 */	
	function __construct() {
		parent::__construct();
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$config = JFactory::getConfig();
		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( 'yooniqueacl.limitstart', 'limitstart', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState( 'limit', $limit );
		$this->setState( 'limitstart', $limitstart );		
		$this->setState( 'filter_order', strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "filter_order", 'filter_order', 'title' ) ) ) );
		$this->setState( 'filter_order_Dir', strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "filter_order_Dir", 'filter_order_Dir', 'asc' ) ) ) );
		// set the state
		$this->_site = strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "_site", '_site', ' ' ) ) );
		$this->_site_option = strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "_site_option", '_site_option', ' ' ) ) );
		$this->_group = strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "_group", '_group', ' ' ) ) );
		$this->_type = strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "_type", '_type', ' ' ) ) );
		
	}
	
	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery() {
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$database = &JFactory::getDBO();
		// ************************************** //
		//	RECORDS
		// ************************************** //
		$where = array();
		$lists = array();

		$search				= $mainframe->getUserStateFromRequest( 'search', 'search', '',	'string' );
		$search				= JString::strtolower($search);
						
		$filter_order_query = "";
		$filter_order = $this->getState( 'filter_order' );
		$filter_order_Dir = $this->getState( 'filter_order_Dir' );
		$filter_order_query = "";
		switch ($filter_order) {
			case "id":
				$filter_order_query = " ORDER BY i.id ".strtoupper( $filter_order_Dir )." ";
			  break;
			case "site":
				$filter_order_query = " ORDER BY i.site ".strtoupper( $filter_order_Dir ).", i.title ASC ";
			  break;
			case "title":
				$filter_order_query = " ORDER BY i.title ".strtoupper( $filter_order_Dir )." ";
			  break;
			case "option":
				$filter_order_query = " ORDER BY i.site_option ".strtoupper( $filter_order_Dir ).", i.title ASC ";
			  break;
			case "includeitem":
				$filter_order_query = " ORDER BY i.item_exclude ".strtoupper( $filter_order_Dir ).", i.title ASC ";
			  break;
			case "typeid":
				$filter_order_query = " ORDER BY i.type_id ".strtoupper( $filter_order_Dir ).", i.title ASC ";
			  break;
			case "errorurl":
				$filter_order_query = " ORDER BY i.error_url ".strtoupper( $filter_order_Dir ).", i.title ASC ";
			  break;
			case "created":
				$filter_order_query = " ORDER BY i.created_datetime ".strtoupper( $filter_order_Dir ).", i.title ASC ";
			  break;
			default:
				$filter_order_query = " ORDER BY i.title ".strtoupper( $filter_order_Dir )." ";
			  break;
		}
	
		$group_query = "";
		if ( $this->_group > '0' ) {
			$group_query = " AND g2i.group_id = '$this->_group' ";
		} elseif ( $this->_group == '-1' ) {
			$group_query = " AND g2i.group_id IS NOT NULL ";
		} elseif ( $this->_group == '-2' ) {
			$group_query = " AND g2i.group_id IS NULL ";
		}
		
		$group_join = "";
		if (!empty($group_query))
		{
			$group_join = " LEFT JOIN  " . TABLE_YOONIQUEACL_G2I . " AS g2i ON i.id = g2i.item_id ";
		}
	
		$type_query = "";
		if ($this->_type) {
			if ( ($this->_type == 'cont') || ($this->_type == 'com') || ($this->_type == 'mod') ) {
				$type_query = " AND LOWER(i.type) LIKE '%" . $database->escape( trim( strtolower( $this->_type ) ) ) . "%' ";
			} 
		}
	
		$site_query = "";
		if ($this->_site) {
				$site_query = " AND LOWER(i.site) LIKE '%" . $database->escape( trim( strtolower( $this->_site ) ) ) . "%' ";
		}
	
		$siteoption_query = "";
		if ($this->_site_option) {
				$siteoption_query = " AND LOWER(i.site_option) LIKE '%" . $database->escape( trim( strtolower( $this->_site_option ) ) ) . "%' ";
		}
	
		
		if ($search) {
			$where[] = " LOWER(i.title) LIKE '%" . $database->escape( trim( strtolower( $search ) ) ) . "%' ";
			$where[] = " LOWER(i.site_option) LIKE '%" . $database->escape( trim( strtolower( $search ) ) ) . "%' ";
			$where[] = " LOWER(i.query) LIKE '%" . $database->escape( trim( strtolower( $search ) ) ) . "%' ";
		}
		$content_query = "";

		// get the total number of records
		$query = "SELECT i.* "
		. " FROM  " . TABLE_YOONIQUEACL_ITEMS . " AS i "
		. $group_join
		. " WHERE 1 "
		. $content_query
		. $type_query
		. $site_query
		. $group_query
		. $siteoption_query
		. (count( $where ) ? "\n HAVING " . implode( ' OR ', $where ) : "")
		// . " GROUP BY i.id "
	    . $filter_order_query
		;
		return $query;
	}
	
	/**
	 * Retrieves the data
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
	 * Retrieves the page title
	 * @return array Array of objects containing the data from the database
	 */
	function getPageTitle() {
		if (empty($this->_pagetitle)) {
		  $this->_pagetitle = JText::_( 'Items' );
		}

		return $this->_pagetitle;
	}
	
	/**
	 * Retrieves the search
	 * @return array Array of objects containing the data from the database
	 */
	function getSearch() {
		global $mainframe;
		$mainframe = JFactory::getApplication();
		if (empty($this->_search)) {
			$search				= $mainframe->getUserStateFromRequest( 'search', 'search', '',	'string' );
			$search				= JString::strtolower($search);
			$this->_search 		= $search;
		}

		return $this->_search;
	}

	/**
	 * Retrieves the item
	 * @return array Array of objects containing the data from the database
	 */
	function getItem() {
		$database = &JFactory::getDBO();
		$id = intval( JRequest::getVar( 'id', "", "request" ) );

		if (intval($id) == 0) {
			return false;
		}
		
		$query = "SELECT t.* FROM  " . TABLE_YOONIQUEACL_ITEMS . " as t "
		. " WHERE `id` = '".$id."' "
		;
		$database->setQuery($query);
		$row = $database->loadObject();

		return $row;
	}

	/**
	 * Retrieves the value
	 * @return array Array of objects containing the data from the database
	 */
	function getType() {
		global $mainframe;
		$mainframe = JFactory::getApplication();
		if (empty($this->_type)) {
			$this->_type = strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "_type", '_type', ' ' ) ) );
		}

		return $this->_type;
	}

	/**
	 * Creates a List
	 * @return array Array of objects containing the data from the database
	 */
	function getTypesFilterList() {
		if (empty($this->_typesFilterList)) {

			$types[] = JHTML::_('select.option', '', '- '.JText::_( 'Select Type' ).' -' );
			$types[] = JHTML::_('select.option', 'cont', ''.JText::_( 'Content' ).'' );
			$types[] = JHTML::_('select.option', 'com', ''.JText::_( 'Component' ).'' );
		
			$javascript 	= 'onchange="document.adminForm.submit();"';	
			$this->_typesFilterList = JHTML::_('select.genericlist', $types, "_type", "size='1' $javascript", "value", "text", $this->_type );
		
		}

		return $this->_typesFilterList;
	}

	/**
	 * Retrieves the value
	 * @return array Array of objects containing the data from the database
	 */
	function getSite() {
		global $mainframe;
		$mainframe = JFactory::getApplication();
		if (empty($this->_site)) {
			$this->_site = strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "_site", '_site', ' ' ) ) );
		}

		return $this->_site;
	}

	/**
	 * Creates a List
	 * @return array Array of objects containing the data from the database
	 */
	function getSitesFilterList() {
		if (empty($this->_sitesFilterList)) {

			$types[] = JHTML::_('select.option', '', '- '.JText::_( 'Select Site' ).' -' );
			$types[] = JHTML::_('select.option', 'site', ''.JText::_( 'Frontend' ).'' );
			$types[] = JHTML::_('select.option', 'administrator', ''.JText::_( 'Adminside' ).'' );
		
			$javascript 	= 'onchange="document.adminForm.submit();"';	
			$this->_sitesFilterList = JHTML::_('select.genericlist', $types, "_site", "size='1' $javascript", "value", "text", $this->_site );
		
		}

		return $this->_sitesFilterList;
	}

	/**
	 * Retrieves the value
	 * @return array Array of objects containing the data from the database
	 */
	function getSiteOption() {
		global $mainframe;
		$mainframe = JFactory::getApplication();
		if (empty($this->_site_option)) {
			$this->_site_option = strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "_site_option", '_site_option', ' ' ) ) );
		}

		return $this->_site_option;
	}

	/**
	 * Retrieves the value
	 * @return array Array of objects containing the data from the database
	 */
	function getSiteOptions() {
		if (empty($this->_site_options)) {
			$this->_db->setQuery("SELECT DISTINCT(site_option) FROM  " . TABLE_YOONIQUEACL_VARIABLES
						." ORDER BY `site_option` ASC");
			$db_data = $this->_db->loadObjectList();
	
			$this->_site_options = $db_data;
		
		}

		return $this->_site_options;
	}

	/**
	 * Creates a List
	 * @return array Array of objects containing the data from the database
	 */
	function getSiteOptionsFilterList() {
		if (empty($this->_siteOptionsFilterList)) {
			$data = $this->getSiteOptions();

			$types[] = JHTML::_('select.option', '0', '- '.JText::_( 'Select Option' ).' -' );

			if ($data) {
			  foreach ($data as $d) {
				$types[] = JHTML::_('select.option', htmlspecialchars( $d->site_option ), htmlspecialchars( $d->site_option ) );
			  }
			}
			$javascript 	= 'onchange="document.adminForm.submit();"';	
			$this->_siteOptionsFilterList = JHTML::_('select.genericlist', $types, "_site_option", "size='1' $javascript", "value", "text", $this->_site_option );
		
		}

		return $this->_siteOptionsFilterList;
	}
	
	/**
	 * Retrieves the value
	 * @return array Array of objects containing the data from the database
	 */
	function getGroups() {
		if (empty($this->_groups)) {
			$this->_db->setQuery("SELECT * FROM  " . TABLE_YOONIQUEACL_GROUPS
								." ORDER BY `title` ASC");
			$data = $this->_db->loadObjectList();
	
			$this->_groups = $data;
		
		}

		return $this->_groups;
	}

	/**
	 * Retrieves the value
	 * @return array Array of objects containing the data from the database
	 */
	function getGroupsFilterList() {
		if (empty($this->_groupsFilterList)) {
			$data = $this->getGroups();

			$types[] = JHTML::_('select.option', '0', '- '.JText::_( 'Select Group' ).' -' );
			$types[] = JHTML::_('select.option', '-1', '- '.JText::_( 'All Groups' ).' -' );
			$types[] = JHTML::_('select.option', '-2', '- '.JText::_( 'No Groups' ).' -' );
		
			if ($data) {
			  foreach ($data as $d) {
				$types[] = JHTML::_('select.option', $d->id, htmlspecialchars( $d->title ) );
			  }
			}
			$javascript 	= 'onchange="document.adminForm.submit();"';	
			$this->_groupsFilterList = JHTML::_('select.genericlist', $types, "_group", "size='1' $javascript", "value", "text", $this->_group );
		
		}

		return $this->_groupsFilterList;
	}


	
	/**
	 * Adds Item to Group
	 * @return array Array of objects containing the data from the database
	 */
	function addItemToGroup( $itemid, $groupid ) {
		global $mainframe;
		$mainframe = JFactory::getApplication();
		
		// if not already a member of the group
		// add to group
		$this->_db->setQuery("SELECT `group_id` FROM  " . TABLE_YOONIQUEACL_G2I
							." WHERE `group_id` = '$groupid'"
							." AND `item_id` = '$itemid' ");
		$already = $this->_db->loadResult();

		if (($already != $groupid) && $itemid && $groupid) {
		  $query = "INSERT INTO  " . TABLE_YOONIQUEACL_G2I
			."\n SET `item_id` = '$itemid', "
			."\n `group_id` = '$groupid' ";
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		
		return true;		

	}	

	/**
	 * Removes Item from Group
	 * @return array Array of objects containing the data from the database
	 */
	function removeItemFromGroup( $itemid, $groupid ) {
		global $mainframe;
		$mainframe = JFactory::getApplication();
		
		// if already a member of the group
		// remove from group
		$this->_db->setQuery("DELETE FROM  " . TABLE_YOONIQUEACL_G2I
							." WHERE `group_id` = '$groupid'"
							." AND `item_id` = '$itemid' ");
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		
		return true;		

	}
	

	/**
	 * switchItemToGroup
	 * @return array Array of objects containing the data from the database
	 */
	function switchItemToGroup( $itemid, $groupid ) {
		global $mainframe;
		$mainframe = JFactory::getApplication();

		if (!($itemid && $groupid)) {
			return false;
		}
		
		// if not already a member of the group
		// add to group
		$this->_db->setQuery("SELECT `group_id` FROM  " . TABLE_YOONIQUEACL_G2I
							." WHERE `group_id` = '$groupid'"
							." AND `item_id` = '$itemid' ");
		$already = $this->_db->loadResult();

		if (($already == $groupid)) {
			// remove
			return $this->removeItemFromGroup( $itemid, $groupid );
		} else {
			// add
			return $this->addItemToGroup( $itemid, $groupid );
		}

	}		

	/**
	 * Adds CE to Item
	 * @return array Array of objects containing the data from the database
	 */
	function addCEToItem( $itemid, $val ) {
		global $mainframe;
		$mainframe = JFactory::getApplication();
		
		// if not already 
		// add to 
		$this->_db->setQuery(" UPDATE  " . TABLE_YOONIQUEACL_ITEMS
							." SET `error_url` = ".$this->_db->Quote( $val )." "
							." WHERE `id` = '".$itemid."' ");
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		return true;		

	}		

	/**
	 * Removes CE from Item
	 * @return array Array of objects containing the data from the database
	 */
	function removeCEFromItem( $itemid ) {
		global $mainframe;
		$mainframe = JFactory::getApplication();
		
		// if already a member of the group
		// remove from group
		$this->_db->setQuery(" UPDATE  " . TABLE_YOONIQUEACL_ITEMS
							." SET `error_url` = '' "
							." WHERE `id` = '$itemid' ");
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		
		return true;		

	}		

	/**
	 * publishCE
	 * @return array Array of objects containing the data from the database
	 */
	function publishCE( $itemid, $val ) {
		global $mainframe;
		$mainframe = JFactory::getApplication();
		
		// if not already 
		// add to 
		$this->_db->setQuery(" UPDATE  " . TABLE_YOONIQUEACL_ITEMS
							." SET `error_url_published` = '".$val."' "
							." WHERE `id` = '$itemid' ");
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		return true;		

	}		


	/**
	 * 
	 * @return 
	 * @param $msg Object
	 */	
	function enroll_flex( &$msg ) {
		$success = false;
		
		$cids = JRequest :: getVar('cid', array (0), 'post', 'array');
		$flexid = $this->getField( 'flexid' );

		foreach ($cids as $cid)
		{
			$action = YooniqueaclHelperItem::addToGroup( $cid, $flexid );

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
			$action = YooniqueaclHelperItem::removeFromGroup( $cid, $flexid );

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
					 " . TABLE_YOONIQUEACL_G2I . "
			    WHERE 
			    	`item_id` = '$cid' 
			";
			$database->setQuery( $query );
			if (!$database->query()) {
				$success = false;
				$msg->type = 'notice';
				$msg->message .= ' - '.JText::_( 'Failed to withdraw item' )." [{$cid}]";			
			}
		
		}
		
		return $success;
	}
	
}
