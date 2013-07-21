<?php
/**
 * @package watchfulli
 * @copyright Copyright (c) 2012-2013 watchful.li
 */
// No direct access to this file
(defined('_JEXEC') or defined('JPATH_PLATFORM')) or die;

// define our base path
defined('WATCHFULLI_PATH')  or define('WATCHFULLI_PATH', dirname(__FILE__));
defined('WATCHFULLI_ROOT')  or define('WATCHFULLI_ROOT', WATCHFULLI_PATH);

// require helper
require_once WATCHFULLI_PATH . '/classes/watchfulli.php';

// import joomla controller library and get a controller instance
if ('1.5' == Watchfulli::joomla()->RELEASE) {
	// Access check
	$canAdmin = in_array(JFactory::getUser()->gid, array(24, 25));
	// get the controller and task
	require_once WATCHFULLI_ROOT . '/controller.php';
	$controller = new watchfulliController();
	$task = JRequest::getCmd('task');
}
else {
	// Access check.
	$canAdmin = JFactory::getUser()->authorise('core.manage', 'com_watchfulli');
	// get the task from the application
	$task = JFactory::getApplication()->input->get('task');
	// load the proper library and controller
	if (Watchfulli::joomla()->isCompatible('3.0')) {
		jimport('legacy.controller.legacy');
		$controller = JControllerLegacy::getInstance('watchfulli');
	}
	else {
		jimport('joomla.application.component.controller');
		$controller = JController::getInstance('watchfulli');
	}
}

// Acces check
if (!$canAdmin && JFactory::getApplication()->isAdmin()) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Perform the Request task
$controller->execute($task);
 
// Redirect if set by the controller
$controller->redirect();