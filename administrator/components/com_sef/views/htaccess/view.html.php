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

class SEFViewHtaccess extends SEFView
{
    function display($tpl = null)
    {
	    JToolBarHelper::title('JoomSEF - ' . JText::_('COM_SEF_HTACCESS_EDITOR'), 'edit.png');
	    JToolBarHelper::back('COM_SEF_BACK', 'index.php?option=com_sef');
	    
	    parent::display();
    }
    
}
?>