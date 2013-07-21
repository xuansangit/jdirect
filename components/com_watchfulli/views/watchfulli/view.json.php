<?php

/**
 * @package watchfulli
 * @copyright Copyright (c) 2012-2013 watchful.li
 */
// No direct access to this file
defined('WATCHFULLI_PATH') or die;

require_once WATCHFULLI_PATH . '/classes/view.php';
require_once WATCHFULLI_PATH . '/classes/send.php';

/**
 * watchfulliViewWatchfulli
 */
class watchfulliViewWatchfulli extends WatchfulliView {

    function display($tpl = null) {
        @error_reporting(0);
        @ini_set('error_reporting', 0);
        $send = new watchfulliSend();
        if (defined(WATCHFULLI_DEBUG)) {
            print_r($send->getData());
        } else {
            echo Watchfulli::encodedJson($send->getData());
        }
        JFactory::getApplication()->close();
    }

}
