<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

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
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/
class YooniqueaclViewUsers extends JViewLegacy {

	/**
	 * 
	 * @param $tpl
	 * @return unknown_type
	 */
	function display($tpl = null) 
	{
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$model = &$this->getModel();
        jimport('joomla.utilities.date');
        
        JToolBarHelper::title( JText::_( 'Users' ), 'yooniqueacl' );
			JToolBarHelper::custom('enroll_flex', "book_add.png", "book_add.png", JText::_( 'Flex' )." +", true);
			JToolBarHelper::custom('withdraw_flex', "book_remove.png", "book_remove.png", JText::_( 'Flex' )." -", true);
			JToolBarHelper::divider();
			JToolBarHelper::custom('withdraw_all', "paste_remove.png", "paste_remove.png", JText::_( 'Withdraw' )." ".JText::_( 'All' ), true);

		//get the data
		$items = &$this->get('Data');
		$this->assignRef('items', $items);
		
		$pagination = $this->get( 'Pagination' );
		$this->assignRef( 'pagination' , $pagination);
		
		$search				= $mainframe->getUserStateFromRequest( 'search', 'search', '',	'string' );
		$search				= JString::strtolower($search);
		$this->assignRef('search', $search);
		
		$this->assignRef( 'filter_order', strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "filter_order", 'filter_order', 'id' ) ) ) );
		$this->assignRef( 'filter_order_Dir', strval( htmlspecialchars( $mainframe->getUserStateFromRequest( "filter_order_Dir", 'filter_order_Dir', 'asc' ) ) ) );

				$this->assignRef( 'groupsFilterList', $this->get( 'GroupsFilterList' ) );
				$this->assignRef( 'groups', $this->get( 'Groups' ) );

		// selectLists
			$selectList_flex = YooniqueaclHelperGroup::getSelectList( 'flexid', $model->getField( 'flexid' ) );
			$this->assignRef('selectList_flex', $selectList_flex );
			
		parent::display($tpl);

    }
	 
}
