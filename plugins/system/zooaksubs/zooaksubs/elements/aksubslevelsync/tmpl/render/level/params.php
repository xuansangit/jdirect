<?php
/**
* @package		ZOOaksubs
* @author    	ZOOlanders http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');	
	
	return 
	'{"fields": {
		"layout_wrapper":{
			"type": "fieldset",
			"fields": {
	
				"layout_sep":{
					"type": "separator",
					"text": "Level Layout",
					"big": "1"
				},
				"_param":{
					"type": "select",
					"label": "Param",
					"specific": {
						"options": {
							"Duration":"duration",
							"Price":"price"
						}
					}
				}

			}
		}
	}}';