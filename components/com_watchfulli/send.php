<?php
/**
 * @package watchfulli
 * @copyright Copyright (c) 2012-2013 watchful.li
 */
if(isset($_GET['testUrl'])) die("<~ok~>"); //Just for testing without process the script
if(isset($_GET['debug'])) {
    define('WATCHFULLI_DEBUG',1);
    $debug = new stdClass();
    $debug->time['1. Start'] = time();
}
$_POST['option'] = 'com_watchfulli';
$_POST['view']   = 'watchfulli';
$_POST['format'] = 'json';
// Set flag that this is a parent file.
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', str_replace(DS.'components'.DS.'com_watchfulli','',dirname(__FILE__)));
if (file_exists(JPATH_BASE.DS.'defines.php')) include JPATH_BASE.DS.'defines.php';
if (!defined('_JDEFINES')) require JPATH_BASE.DS.'includes'.DS.'defines.php';
require JPATH_BASE.DS.'includes'.DS.'framework.php';
defined('WATCHFULLI_PATH') or define('WATCHFULLI_PATH', JPATH_ADMINISTRATOR . '/components/com_watchfulli');
defined('WATCHFULLI_ROOT') or define('WATCHFULLI_ROOT', dirname(__FILE__));

// Instantiate the application.
$mainframe = JFactory::getApplication('site');

require_once JPATH_ADMINISTRATOR . '/components/com_watchfulli/classes/send.php';
$send = new watchfulliSend();
if (defined('WATCHFULLI_DEBUG')) $debug->time['2. Before watchfulliSend::getData'] = time();
$data = $send->getData();
if (defined('WATCHFULLI_DEBUG')) $debug->time['3. End'] = time();
$data['debug'] = $debug;
echo json_encode($data);
$mainframe->close();