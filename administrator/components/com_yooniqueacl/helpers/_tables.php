<?php

defined('_JEXEC') or die('Restricted Access');

if (class_exists ("YooniqueaclConfigFile")) {

$config = new YooniqueaclConfigFile;

define('TABLE_YOONIQUEACL_CONFIG',    '#__'.$config->dbprefix.'config');
define('TABLE_YOONIQUEACL_ADDONS',    '#__'.$config->dbprefix.'addons');
define('TABLE_YOONIQUEACL_CODES',     '#__'.$config->dbprefix.'codes');
define('TABLE_YOONIQUEACL_G2I',       '#__'.$config->dbprefix.'g2i');
define('TABLE_YOONIQUEACL_GROUPS',    '#__'.$config->dbprefix.'groups');
define('TABLE_YOONIQUEACL_ITEMS',     '#__'.$config->dbprefix.'items');
define('TABLE_YOONIQUEACL_U2G',       '#__'.$config->dbprefix.'u2g');
define('TABLE_YOONIQUEACL_V2I',       '#__'.$config->dbprefix.'v2i');
define('TABLE_YOONIQUEACL_VARIABLES', '#__'.$config->dbprefix.'variables');
}
