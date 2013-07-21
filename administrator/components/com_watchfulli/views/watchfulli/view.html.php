<?php
/**
 * @package watchfulli
 * @copyright Copyright (c) 2012-2013 watchful.li
 */
// No direct access to this file
defined('WATCHFULLI_PATH') or die;

require_once WATCHFULLI_PATH . '/classes/view.php';
 
/**
 * jmonitoringslave View
 */
class watchfulliViewWatchfulli extends WatchfulliView
{
	/**
	 * HelloWorlds view display method
	 * @return void
	 */
	function display($tpl = null) 
	{ 
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		$this->addToolBar();
		// Display the template
		parent::display($tpl);
	}
         
	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		JHTML::stylesheet( 'icon_jmon.css', 'administrator/components/com_watchfulli/');
    JToolBarHelper::title(JText::_('Watchfulli'), 'icon_jmon');
		JToolBarHelper::preferences('com_watchfulli',$height='200', $width='600');
	}
}