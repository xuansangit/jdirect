<?php
defined('_JEXEC') or die();

define('AKEEBASUBS_VERSION', '3.2.0');
define('AKEEBASUBS_DATE', '2013-06-28');
define('AKEEBASUBS_PRO', '0');
if(version_compare(JVERSION, '3.0', 'ge')) {
	define('AKEEBASUBS_VERSIONHASH', md5(AKEEBASUBS_VERSION.AKEEBASUBS_DATE.JFactory::getConfig()->get('secret','')));
} else {
	define('AKEEBASUBS_VERSIONHASH', md5(AKEEBASUBS_VERSION.AKEEBASUBS_DATE.JFactory::getConfig()->getValue('secret','')));
}
?>