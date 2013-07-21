<?php
/**
* @package   ZOO
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @changed   yoonique[.]net
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include assets css
$this->app->document->addStylesheet('elements:download/assets/css/download.css');

	switch ($display) {
		case 'download_limit':
			$download_limit = ($download_limit) ? $download_limit : '-';
			echo $download_limit;
			break;		
		
		case 'filesize':
			echo $size;
			break;
			
		case 'filehits':
			echo $hits;
			break;
			
		case 'buttonlink':
			if ($access_level) {
				$downloadtext = JText::_('Download');
			} else {
				$downloadtext = 'Login Required';
			}
			if ($limit_reached) {
				echo '<a class="yoo-zoo element-download-button" href="javascript:alert(\''.JText::_('Download limit reached').'\');" title="'.JText::_('Download limit reached').'"><span><span>'.$downloadtext.'</span></span></a>';
			} else {
				echo '<a class="yoo-zoo element-download-button" href="'.JRoute::_($download_link).'" title="'.$download_name.'"><span><span>'.$downloadtext.'</span></span></a>';
			}
			break;

		case 'imagelink':
			if ($limit_reached) {
				echo '<div class="yoo-zoo element-download-type element-download-type-'.$filetype.'" title="'.JText::_('Download limit reached').'"></div>';
			} else {
				echo '<a class="yoo-zoo element-download-type element-download-type-'.$filetype.'" href="'.JRoute::_($download_link).'" title="'.$download_name.'"></a>';
			}
			break;
			
		default:
			if ($limit_reached) {
				echo JText::_('Download limit reached');
			} else {
				echo '<a href="'.JRoute::_($download_link).'" title="'.$download_name.'">'.$download_name.'</a>';
			}
}
