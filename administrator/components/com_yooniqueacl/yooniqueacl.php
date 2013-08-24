<?php
/**
 * @version   1.4.17
 * @date      Fri Mar 29 15:34:01 2013 -0700
 * @package   yoonique ACL
 * @author    yoonique[.]net
 * @copyright Copyright (C) 2012 yoonique[.]net and all rights reserved.
 *
 * based on
 *
 * @package	Juga
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2010 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

// include stylesheet and script
JHTML::stylesheet('media/com_yooniqueacl/css/yooniqueacl.css');
if(version_compare(JVERSION, '3.0', '<')) 
	JHTML::script('media/com_yooniqueacl/js/jquery-1.9.1.min.js');

// Require the base controller
require_once( JPATH_COMPONENT_ADMINISTRATOR.'/controller.php' );

// Require Helpers
jimport( 'joomla.filesystem.folder' );
$helpersPath = JPATH_ADMINISTRATOR.'/components/com_yooniqueacl/helpers';
$helperFiles = JFolder::files($helpersPath, '\.php$', false, true);
if (count($helperFiles) > 0) {
	//iterate through the helper files
	foreach ($helperFiles as $helperFile) {
		require_once($helperFile);
	}
}

if($controller = JRequest::getWord('controller')) {
    $path = JPATH_COMPONENT_ADMINISTRATOR.'/controllers/'.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

// Create the controller
$classname    = 'YooniqueaclController'.$controller;
$controller   = new $classname( );

// load the plugins
JPluginHelper::importPlugin( 'yooniqueacl' );

// Perform the requested task
$controller->execute( JRequest::getCmd( 'task' ) );

// Redirect if set by the controller
$controller->redirect();

