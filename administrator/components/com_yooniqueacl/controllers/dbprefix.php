<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controller');

/**
 * A feature to change the site's database prefix - Controller
 */
class YooniqueaclControllerDbprefix extends YooniqueaclController
{
	
	public final function getThisModel()
	{
		static $prefix = null;
		static $modelName = null;

		if(empty($modelName)) {
			$prefix = $this->getName().'Model';
			$view = JRequest::getCmd('view','dbprefix');
			$modelName = ucfirst($view);
		}

		return $this->getModel($modelName, $prefix);
	}

	function change()
	{
		$prefix = JRequest::getString('prefix','jos_');
		$changeconfigonly = JRequest::getString('changeconfigonly','');
		$model = $this->getThisModel();

		$result = $model->performChanges($prefix,$changeconfigonly);
		$url = 'index.php?option=com_yooniqueacl&view=dbprefix';
		if($result !== true) {
			$this->setRedirect($url, $result, 'error');
		} else {
			$this->setRedirect($url, JText::sprintf('LBL_DBREFIX_OK', $prefix));
		}
		
		$this->redirect();
	}
}
