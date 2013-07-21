<?php
/**
 * @package watchfulli
 * @copyright Copyright (c) 2012-2013 watchful.li
 */
if(isset($_GET['testUrl'])) die("<~ok~>"); //Just for testing without process the script
// Set flag that this is a parent file.
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', str_replace(DS.'components'.DS.'com_watchfulli','',dirname(__FILE__)));
if (file_exists(JPATH_BASE.DS.'defines.php')) include_once JPATH_BASE.DS.'defines.php';
if (!defined('_JDEFINES')) require_once JPATH_BASE.DS.'includes'.DS.'defines.php';
require_once JPATH_BASE.DS.'includes'.DS.'framework.php';
defined('WATCHFULLI_PATH') or define('WATCHFULLI_PATH', JPATH_ADMINISTRATOR . '/components/com_watchfulli');
defined('WATCHFULLI_ROOT') or define('WATCHFULLI_ROOT', dirname(__FILE__));

// Instantiate the application.
$mainframe = JFactory::getApplication('site');

require_once JPATH_ADMINISTRATOR . '/components/com_watchfulli/classes/actions.php';

$action = new watchfulliActions();

$function = JRequest::getCmd('task',false);
if ($function && method_exists($action, $function)) {
  $action->$function();
}
$mainframe->close();