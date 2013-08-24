<?php
/**
 * @package JUGA
 * @link 	http://www.dioscouri.com
 * @license GNU/GPLv2
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

/**
 * Yooniqueacl View
 *
 */
class YooniqueaclViewVariables extends JViewLegacy {
	/**
	 * Yooniqueacl view display method
	 * @return void
	 **/
	 // **********************************************
	function display($tpl = null) {
		global $mainframe;
		$mainframe = JFactory::getApplication();
        jimport('joomla.utilities.date');

		// JHTML::_('stylesheet', 'yooniqueacl.css', 'media/com_yooniqueacl/css/');
		$pagetitle = $this->get( 'PageTitle' );
        JToolBarHelper::title( JText::_( $pagetitle ), 'yooniqueacl' );
			JToolBarHelper::editList( );
			JToolBarHelper::deleteList( JText::_( 'Confirm Delete' ) );
			JToolBarHelper::addnew( );	

        // Get data from the model
        $items =& $this->get( 'Data');
		$pagination = $this->get( 'Pagination' );
		$search = $this->get( 'Search' );
		
		$this->assignRef( 'pagination' , $pagination);
        $this->assignRef( 'items', $items );
        $this->assignRef( 'search', $search );
		$this->assignRef( 'sites', $this->get( 'SitesFilterList' ) );
		$this->assignRef( 'siteOptions', $this->get( 'SiteOptionsFilterList' ) );
		$this->assignRef( 'variables', $this->get( 'VariablesFilterList' ) );
		
		$this->assignRef( 'filter_order', strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "filter_order", 'filter_order', 'title' ) ) ) );
		$this->assignRef( 'filter_order_Dir', strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "filter_order_Dir", 'filter_order_Dir', 'asc' ) ) ) );


        parent::display($tpl);
    }
}
// ************************************************************************
