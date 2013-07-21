<?php
/**
 * @package watchfulli
 * @copyright Copyright (c) 2012-2013 watchful.li
 */
// No direct access to this file
(defined('_JEXEC') or defined('JPATH_PLATFORM')) or die;

// define our base paths
defined('WATCHFULLI_PATH') or define('WATCHFULLI_PATH', JPATH_ADMINISTRATOR . '/components/com_watchfulli');
defined('WATCHFULLI_ROOT') or define('WATCHFULLI_ROOT', dirname(__FILE__));

// ensure there's no notices or anything
@error_reporting(0);
@ini_set('error_reporting', 0);

// just use admin index, as it does the same thing & is based on the two paths defined above
require_once WATCHFULLI_PATH . '/watchfulli.php';