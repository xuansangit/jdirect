<?php
/**
 * @package watchfulli
 * @copyright Copyright (c) 2012-2013 watchful.li
 */
// No direct access to this file
defined('WATCHFULLI_PATH') or die;

require_once WATCHFULLI_PATH . '/classes/watchfulli.php';

// create a base class that directly extends the core view
// NOTE: please don't add methods to these unless it's for version compatibility
// if it's J! version agnostic code, put it in WatchfulView below!!!
if (Watchfulli::joomla()->isCompatible('3.0')) {
	jimport('legacy.controller.legacy');
	class WatchfulliSubBaseController extends JControllerLegacy {}
}
else {
	jimport('joomla.application.component.controller');
	class WatchfulliSubBaseController extends JController {}
}

/**
 * Watchful Controller Class
 * 
 * @author jeff
 *
 */
class WatchfulliBaseController extends WatchfulliSubBaseController
{
	
}
