<?php
/**
 * @package JUGA
 * @link 	http://www.dioscouri.com
 * @license GNU/GPLv2
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class YooniqueaclViewGroup extends JViewLegacy 
{
	/**
	 * 
	 * @param $tpl
	 * @return unknown_type
	 */
	function display($tpl = null) 
	{
		global $mainframe;
		$mainframe = JFactory::getApplication();
        jimport('joomla.utilities.date');
        
        JToolBarHelper::title( JText::_( JText::_( 'Group' ) ), 'yooniqueacl' );
 
		//get the data
		$row = &$this->get('Data');
		
		if ($row) { 
			$isNew = ($row->id < 1);
			$text = $isNew ? JText::_( JText::_( 'New' ) ) : JText::_( JText::_( 'Edit' ) );
			JToolBarHelper::title(   JText::_( 'Group' ).": <small>".$text."</small>" , 'yooniqueacl' );
			JToolBarHelper::save2new('save_new');
			JToolBarHelper::divider();
			JToolBarHelper::save('save');
			if ($isNew)  {
				JToolBarHelper::cancel();
			} else {
				// for existing items the button is renamed `close`
				JToolBarHelper::cancel( 'cancel', JText::_( 'Close' ) );
			}
			
			
		}
		
		$this->assignRef('row', $row);

		parent::display($tpl);

    }

}
