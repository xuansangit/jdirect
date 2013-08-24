<?php
/**
 * @package JUGA
 * @link 	http://www.dioscouri.com
 * @license GNU/GPLv2
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class YooniqueaclViewSiteitems extends JViewLegacy 
{
	
	function display($tpl = null) 
	{
		global $mainframe;
		$mainframe = JFactory::getApplication();
        jimport('joomla.utilities.date');

		switch (JRequest::getCmd( 'layout' )) {
			case "define":
				$pagetitle = JText::_( 'Define' )." ".JText::_( 'Site' )." ".JText::_( 'Item' );
				JToolBarHelper::title( JText::_( $pagetitle ), 'yooniqueacl' );
				JToolBarHelper::custom('switch_groups', "switch_f2.png", "switch_f2.png", JText::_( 'Switch' ), true);
				JToolBarHelper::cancel( 'cancel', JText::_( 'Close' ) );
		
				$this->assignRef( 'item', $this->get( 'Item' ) );		
				$model = new YooniqueaclModelGroups();
		        $this->assignRef( 'items', $model->getData( ) );
				$this->assignRef( 'pagination' , $model->getPagination( ) );
			  break;			  
			default: 
				$pagetitle = $this->get( 'PageTitle' );
				JToolBarHelper::title( JText::_( $pagetitle ), 'yooniqueacl' );
				JToolBarHelper::custom('enroll_flex', "book_add.png", "book_add.png", JText::_( 'Flex' )." +", true);
				JToolBarHelper::custom('withdraw_flex', "book_remove.png", "book_remove.png", JText::_( 'Flex' )." -", true);
				JToolBarHelper::custom('withdraw_all', "paste_remove.png", "paste_remove.png", JText::_( 'Withdraw' )." ".JText::_( 'All' ), true);
				JToolBarHelper::divider( );	
				JToolBarHelper::custom('enroll_ce', "world_add.png", "world_add.png", "CE URL +", true);
				JToolBarHelper::custom('withdraw_ce', "world_remove.png", "world_remove.png", "CE URL -", true);
				JToolBarHelper::custom('publish_ce', "world_ok.png", "world_ok.png", "CE URL ".JText::_( 'Publish' ), true);
				JToolBarHelper::custom('unpublish_ce', "world_close.png", "world_close.png", "CE URL ".JText::_( 'Unpublish' ), true);
				JToolBarHelper::divider( );	
				JToolBarHelper::editList( );
				JToolBarHelper::deleteList( JText::_( 'Confirm Delete' ) );
				JToolBarHelper::addnew( );
					
				// selectLists
					$model = &$this->getModel();
					$selectList_flex = YooniqueaclHelperGroup::getSelectList( 'flexid', $model->getField( 'flexid' ) );
					$this->assignRef('selectList_flex', $selectList_flex );
			
		        $this->assignRef( 'items', $this->get( 'Data') );				
				$this->assignRef( 'pagination' , $this->get( 'Pagination' ) );
				$this->assignRef( 'types', $this->get( 'TypesFilterList' ) );
				$this->assignRef( 'sites', $this->get( 'SitesFilterList' ) );
				$this->assignRef( 'siteOptions', $this->get( 'SiteOptionsFilterList' ) );
				$this->assignRef( 'groupsFilterList', $this->get( 'GroupsFilterList' ) );
				$this->assignRef( 'groups', $this->get( 'Groups' ) );
			  break;
		}		

        // Get data from the model
        $this->assignRef( 'search', $this->get( 'Search' ) );
		$this->assignRef( 'filter_order', strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "filter_order", 'filter_order', 'title' ) ) ) );
		$this->assignRef( 'filter_order_Dir', strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "filter_order_Dir", 'filter_order_Dir', 'asc' ) ) ) );
		
        parent::display($tpl);
    }
}
