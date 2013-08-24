<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.view');

class YooniqueaclViewDbprefix extends JViewLegacy
{
	function display()
	{
		// Set the toolbar title
		JToolBarHelper::title(JText::_('TITLE_DBPREFIX'),'yooniqueacl');
		
		$model = $this->getModel();
		$this->assign('currentPrefix',			$model->getCurrentPrefix());
		$this->assign('newPrefix',					$model->getRandomPrefix(6));

		parent::display();
	}
}