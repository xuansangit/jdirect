<?php
/*
 * @version   2.1.1 Sat Apr 21 19:16:52 2012 -0700
 * @package   yoonique zoo plugin for JoomSEF
 * @author    yoonique[.]net
 * @copyright Copyright (C) yoonique[.]net and all rights reserved.
 * @license   http://www.gnu.org/licenses/gpl.html
 */


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class SefExt_com_zoo extends SefExt {

	var $params;
	var $dosef;

	function create(&$uri) {
		$vars = $uri->getQuery(true);
		extract($vars);
		$title = array();

		if(JVersion::isCompatible('2.6.0')) {
			jexit('JoomSEF Zoo plugin: Only Joomla 2.5 is supported');
		}
		$nonSefVars = array();

		require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

		if (!defined('ZOO_SEF_ALPHAINDEX')) {
			if ($this->params->get('ZOO_SEF_ALPHAINDEX_SHOW') == null) {
				echo "<h1> PLEASE SAVE THE ZOO PLUGIN SETTINGS AT LEAST ONCE!</h1>";
				return $uri;
			}
			if ($this->params->get('ignoreSource') <> 0) {
				echo "<h1> PLEASE SET 'ignore multiple sources' TO NO IN THE ZOO PLUGIN SETTINGS!</h1>";
				return $uri;
			}
			$pluginParams = $this->params;
		}

		$zooapp = App::getInstance('zoo');

		include dirname(__FILE__).DS.'com_zoo_yoonique_sef.php';

		if (!$dosef)
			$nonSefVars = $vars;


		unset($task);

		$newUri = $uri;
		if (count($title) > 0)
			$newUri = JoomSEF::_sefGetLocation($uri, $title, @$task, @$limit, @$limitstart, @$lang, $nonSefVars);
		return $newUri;
	}

}

function shRemoveFromGETVarsList($dummy) {return;} // donothing

?>
