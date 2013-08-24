<?php
/**
 * @package         Advanced Module Manager
 * @version         4.7.1
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2013 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @package        Joomla.Administrator
 * @subpackage     com_advancedmodules
 * @copyright      Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_advancedmodules'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$lang = JFactory::getLanguage();
$lang->load('com_modules', JPATH_ADMINISTRATOR);
if ($lang->getTag() != 'en-GB')
{
	// Loads English language file as fallback (for undefined stuff in other language file)
	$lang->load('com_advancedmodules', JPATH_ADMINISTRATOR, 'en-GB');
}
$lang->load('com_advancedmodules', JPATH_ADMINISTRATOR, null, 1);

jimport('joomla.filesystem.file');

// return if NoNumber Framework plugin is not installed
if (!JFile::exists(JPATH_PLUGINS . '/system/nnframework/nnframework.php'))
{
	JFactory::getApplication()->set('_messageQueue', '');
	$msg = JText::_('AMM_NONUMBER_FRAMEWORK_NOT_INSTALLED')
		. ' ' . JText::sprintf('AMM_EXTENSION_CAN_NOT_FUNCTION', JText::_('COM_ADVANCEDMODULES'));
	JFactory::getApplication()->enqueueMessage($msg, 'error');
	return;
}

// give notice if NoNumber Framework plugin is not enabled
$nnep = JPluginHelper::getPlugin('system', 'nnframework');
if (!isset($nnep->name))
{
	JFactory::getApplication()->set('_messageQueue', '');
	$msg = JText::_('AMM_NONUMBER_FRAMEWORK_NOT_ENABLED')
		. ' ' . JText::sprintf('AMM_EXTENSION_MAY_NOT_FUNCTION', JText::_('COM_ADVANCEDMODULES'));
	JFactory::getApplication()->enqueueMessage($msg, 'notice');
}

// load the NoNumber Framework language file
if ($lang->getTag() != 'en-GB')
{
	// Loads English language file as fallback (for undefined stuff in other language file)
	$lang->load('plg_system_nnframework', JPATH_ADMINISTRATOR, 'en-GB');
}
$lang->load('plg_system_nnframework', JPATH_ADMINISTRATOR, null, 1);

$controller = JControllerLegacy::getInstance('AdvancedModules');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
