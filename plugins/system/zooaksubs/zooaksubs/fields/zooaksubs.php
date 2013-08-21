<?php
/*
* @package		ZOOaksubs
* @author    	ZOOlanders http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

	// load assets
	$this->app->document->addStylesheet('zooaksubs:assets/zooaksubs.css');

	// init vars
	$control_name 		= $control_name.'[zooaksubs]';
	$element 			= $parent->element;
	$config  			= $element->config;
	$params  			= $this->app->parameterform->convertParams($parent);
	$node_atr 			= (array)$node->attributes();
	$type 	 			= $this->app->zlfield->application->getType($this->app->zlfield->type);
	$json_path			= 'zooaksubs:fields/zooaksubs.json.php';
	$enviroment   		= $this->app->zlfield->getTheEnviroment();

	// check if the field was loaded and used
	$ajaxLoading = $params->find('zooaksubs._evaluate') || $params->find('zooaksubs._itemoveride') ? !1 : 1;

	// set arguments
	$ajaxargs = array(
		'element_type' => $element->getElementType(),
		'element_id' => $element->identifier,
		'group' => $this->app->zlfield->application->getGroup(),
		'node' => $node_atr,
		'control_name' => $control_name,
		'json_path' => $json_path,
		'enviroment' => $enviroment
	);
	$arguments = array('element' => $element, 'node' => $node_atr['@attributes']);

	// get field content or leave empty for ajax loading
	$parseJSONargs = '';
	if (!$ajaxLoading) {
		$json = include($this->app->path->path($json_path));
		$parseJSONargs = array($json, $control_name, array(), '', false, $arguments);
	}

	// render
	echo $this->app->zlfield->render($parseJSONargs, false, JText::_('PLG_ZOOAKSUBS_AKSUBS_EVALUATION'), $ajaxargs, 'zooaksubs', $ajaxLoading);

?>