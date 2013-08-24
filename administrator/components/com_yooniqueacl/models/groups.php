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
class YooniqueaclModelGroups extends JModelLegacy {

	var $_data;
	var $_total;
	var $_pagination;
	var $_pagetitle;
	var $_search;	

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
		$this->setState( 'filter_order', strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "filter_order", 'filter_order', 'id' ) ) ) );
		$this->setState( 'filter_order_Dir', strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "filter_order_Dir", 'filter_order_Dir', 'asc' ) ) ) );

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
		switch ($filter_order) {
			case "id":
				$filter_order_query = " ORDER BY t.id ".strtoupper( $filter_order_Dir )." ";
			  break;
			case "title":
				$filter_order_query = " ORDER BY t.title ".strtoupper( $filter_order_Dir )." ";
			  break;
			case "description":
				$filter_order_query = " ORDER BY t.description ".strtoupper( $filter_order_Dir )." ";
			  break;
			default:
				$filter_order_query = " ORDER BY t.id DESC ";
			  break;
		}
		
		if (isset($search)) {
				$where[] = " LOWER(id) LIKE '%" . $database->escape( trim( strtolower( $search ) ) ) . "%'";
				$where[] = " LOWER(title) LIKE '%" . $database->escape( trim( strtolower( $search ) ) ) . "%'";
				$where[] = " LOWER(description) LIKE '%" . $database->escape( trim( strtolower( $search ) ) ) . "%'";
		}
	
	
		// all records		
		$query = "SELECT t.* FROM  " . TABLE_YOONIQUEACL_GROUPS . " as t "
		. " WHERE 1 "
		. (count( $where ) ? "\n HAVING " . implode( ' OR ', $where ) : "")
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
	 * Retrieves the page title
	 * @return array Array of objects containing the data from the database
	 */
	function getPageTitle() {
		if (empty($this->_pagetitle)) {
		  $this->_pagetitle = JText::_( 'Groups' );
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
		
}
// ************************************************************************
