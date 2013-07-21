<?php
/**
 * SEF component for Joomla!
 * 
 * @package   JoomSEF
 * @version   4.4.1
 * @author    ARTIO s.r.o., http://www.artio.net
 * @copyright Copyright (C) 2013 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class SEFViewURLs extends SEFView
{
	function display($tpl = null)
	{
		JToolBarHelper::title( JText::_('COM_SEF_JOOMSEF'), 'url-delete.png' );
		
		JToolBarHelper::back('Back', 'index.php?option=com_sef');
		
		// Get data from the model
		$count		= & $this->get( 'Count' );
		if( $count == 0 ) {
		    $msg = JText::_('COM_SEF_NO_RECORDS_FOUND');
		}
		elseif( $count == 1 ) {
		    $msg = JText::_('COM_SEF_WARNING_DELETE') . ' ' . $count . ' ' . JText::_('COM_SEF_RECORD');
		}
		else {
		    $msg = JText::_('COM_SEF_WARNING_DELETE') . ' ' . $count . ' ' . JText::_('COM_SEF_RECORDS');
		}
		$this->assignRef( 'count', $count );
		$this->assignRef( 'msg', $msg );

		parent::display($tpl);
	}
}
