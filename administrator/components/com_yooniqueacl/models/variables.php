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
class YooniqueaclModelVariables extends JModelLegacy {
 
	var $_data;
	var $_total;
	var $_pagination;
	var $_pagetitle;
	var $_search;
	var $_variable;
	var $_variables;
	var $_site_option;
	var $_site_options;
	var $_site;
	var $_sites;
	var $_sitesFilterList;

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

		// set the state
		$this->_site = strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "_site", '_site', ' ' ) ) );
		$this->_site_option = strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "_site_option", '_site_option', ' ' ) ) );
		$this->_variable = strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "_variable", '_variable', ' ' ) ) );

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
			case "variable":
				$filter_order_query = " ORDER BY t.variable ".strtoupper( $filter_order_Dir )." ";
			  break;
			case "option":
				$filter_order_query = " ORDER BY t.site_option ".strtoupper( $filter_order_Dir )." ";
			  break;
			case "site":
				$filter_order_query = " ORDER BY t.site ".strtoupper( $filter_order_Dir )." ";
			  break;
			case "forceinteger":
				$filter_order_query = " ORDER BY t.force_integer ".strtoupper( $filter_order_Dir )." ";
			  break;
			default:
				$filter_order_query = " ORDER BY t.id DESC ";
			  break;
		}
		
		if (isset($search)) {
				$where[] = " LOWER(id) LIKE '%" . $database->escape( trim( strtolower( $search ) ) ) . "%'";
				$where[] = " LOWER(variable) LIKE '%" . $database->escape( trim( strtolower( $search ) ) ) . "%'";
				$where[] = " LOWER(site_option) LIKE '%" . $database->escape( trim( strtolower( $search ) ) ) . "%'";
		}

		$site_query = "";
		if ($this->_site) {
				$site_query = " AND LOWER(t.site) LIKE '%" . $database->escape( trim( strtolower( $this->_site ) ) ) . "%' ";
		}
	
		$siteoption_query = "";
		if ($this->_site_option) {
				$siteoption_query = " AND LOWER(t.site_option) LIKE '%" . $database->escape( trim( strtolower( $this->_site_option ) ) ) . "%' ";
		}
	
		$variable_query = "";
		if ($this->_variable) {
				$variable_query = " AND LOWER(t.variable) LIKE '%" . $database->escape( trim( strtolower( $this->_variable ) ) ) . "%' ";
		}
	
	
		// all records		
		$query = "SELECT t.* FROM  " . TABLE_YOONIQUEACL_VARIABLES . " as t "
		. " WHERE 1 "
		. $site_query 
		. $siteoption_query
		. $variable_query
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
		  $this->_pagetitle = JText::_( 'Variables' );
		}

		return $this->_pagetitle;
	}
	
	/**
	 * Retrieves the value
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
	function getVariable() {
		global $mainframe;
		$mainframe = JFactory::getApplication();
		if (empty($this->_variable)) {
			$this->_variable = strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "_variable", '_variable', ' ' ) ) );
		}

		return $this->_variable;
	}

	/**
	 * Retrieves the value
	 * @return array Array of objects containing the data from the database
	 */
	function getVariables() {
		if (empty($this->_variables)) {
			$this->_db->setQuery("SELECT DISTINCT(variable) FROM  " . TABLE_YOONIQUEACL_VARIABLES
						." ORDER BY `variable` ASC");
			$db_data = $this->_db->loadObjectList();
	
			$this->_variables = $db_data;
		
		}

		return $this->_variables;
	}

	/**
	 * Creates a List
	 * @return array Array of objects containing the data from the database
	 */
	function getVariablesFilterList() {
		if (empty($this->_variablesFilterList)) {
			$data = $this->getVariables();

			$types[] = JHTML::_('select.option', '0', '- '.JText::_( 'Select Variable' ).' -' );

			if ($data) {
			  foreach ($data as $d) {
				$types[] = JHTML::_('select.option', htmlspecialchars( $d->variable ), htmlspecialchars( $d->variable ) );
			  }
			}
			$javascript 	= 'onchange="document.adminForm.submit();"';	
			$this->_variablesFilterList = JHTML::_('select.genericlist', $types, "_variable", "size='1' $javascript", "value", "text", $this->_variable );
		
		}

		return $this->_variablesFilterList;
	}

}
// ************************************************************************
