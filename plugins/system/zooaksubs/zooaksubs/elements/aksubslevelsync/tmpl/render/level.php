<?php
/*
* @package		ZOOaksubs
* @author    	ZOOlanders http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');


	switch ($params->find('layout._param')) {
			
		case 'price':
			echo $level->price;
			break;
		
		case 'duration':
			echo $level->duration;
			break;
			
		default:
			echo $level->title;
	}
	
?>