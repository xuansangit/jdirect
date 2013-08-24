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
class YooniqueaclViewVariable extends JViewLegacy {
	/**
	 * Yooniqueacl view display method
	 * @return void
	 **/
	 // **********************************************
	function display($tpl = null) {
        jimport('joomla.utilities.date');
        
        JToolBarHelper::title( JText::_( 'Variable' ), 'yooniqueacl' );
 
		//get the data
		$row = &$this->get('Data');
		
		if ($row) { 
			$isNew = ($row->id < 1);
			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			JToolBarHelper::title(   JText::_( 'Variable' ).": <small>".$text."</small>" , 'yooniqueacl' );

			JToolBarHelper::save('save');
			if ($isNew)  {
				JToolBarHelper::cancel();
			} else {
				// for existing items the button is renamed `close`
				JToolBarHelper::cancel( 'cancel', 'Close' );
			}
			
			
		}
		
		$this->assignRef('row', $row);
		$this->assignRef( 'sites', $this->get( 'SitesSelectList' ) );
		$this->assignRef( 'force_integer', $this->get( 'ForceIntegerSelectList' ) );

		parent::display($tpl);

    }
	 // **********************************************

}
// ************************************************************************