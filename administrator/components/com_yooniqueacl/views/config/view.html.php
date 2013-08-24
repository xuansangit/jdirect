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
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class YooniqueaclViewConfig extends JViewLegacy 
{
	/**
	 *
	 * @return void
	 **/
	function display($tpl = null) 
	{

		// check config		
			$config = &YooniqueaclConfig::getInstance();
			$this->assignRef( 'config', $config );

		// set the toolbar and pagetitle
		JToolBarHelper::title( JText::_( 'Configuration' ), 'yooniqueacl' );		
			JToolBarHelper::save( 'save' );
			JToolBarHelper::cancel( 'cancel', JText::_( 'Close' ) );
			
			
        parent::display($tpl);
    }
}