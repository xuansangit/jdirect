<?php
/**
* @package		ZOOaksubs
* @author    	ZOOlanders http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class plgSystemZooaksubsInstallerScript
{
	protected $_error;
	protected $_src;
	protected $_target;
	protected $_ext = 'zooaksubs';
	protected $_ext_name = 'ZOOaksubs';
	protected $_lng_prefix = 'PLG_ZOOAKSUBS_SYS';

	/* List of obsolete files and folders */
	protected $_obsolete = array(
		'files'	=> array(
			'plugins/system/zooaksubs/zooaksubs/fields/aksubslevels.php',
			'plugins/system/zooaksubs/zooaksubs/fields/aksubslevels.json.php'
		),
		'folders' => array(
			'plugins/system/zooaksubs/zooaksubs/elements/akeebasubs'
		)
	);

	/**
	 * Called before any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install)
	 * @param   object  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($type, $parent)
	{
		// init vars
		$db = JFactory::getDBO();
		$this->_src = $parent->getParent()->getPath('source'); // tmp folder
		$this->_target = JPATH_ROOT.'/plugins/system/zooaksubs'; // install folder

		// load ZLFW sys language file
		JFactory::getLanguage()->load('plg_system_zlframework.sys', JPATH_ADMINISTRATOR, 'en-GB', true);

		// check dependencies if not uninstalling
		if($type != 'uninstall' && !$this->checkRequirements($parent)){
			Jerror::raiseWarning(null, $this->_error);
			return false;
		}

		if($type == 'update')
		{
			/*
			 * deny updating from v2.x
			 */
			$zooaksubs_manifest = simplexml_load_file(JPATH_ROOT.'/plugins/system/zooaksubs/zooaksubs.xml');

			if($zooaksubs_manifest && version_compare((string)$zooaksubs_manifest->version, 3.0, '<=') ) {
				Jerror::raiseWarning(null, JText::_('PLG_ZOOAKSUBS_SYS_V2UPDATE'));
				return false;
			}

			/* 
			 * when updating we don't wont to override renderer/item folder,
			 * so let's delete the temp folder before install only if it already exists
			 */
			JFolder::exists($this->_target.'/zooaksubs/renderer/item') && 
			JFolder::delete($this->_src.'/zooaksubs/renderer/item');
		}
	}

	/**
	 * Called on installation
	 *
	 * @param   object  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	function install($parent)
	{
		// init vars
		$db = JFactory::getDBO();

        // enable plugin
        $db->setQuery("UPDATE `#__extensions` SET `enabled` = 1 WHERE `type` = 'plugin' AND `element` = '{$this->_ext}' AND `folder` = 'system'");
        $db->query();
    }

    /**
	 * Called on uninstallation
	 *
	 * @param   object  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	function uninstall($parent)
	{
		// init vars
		$db = JFactory::getDBO();

		// drop table
        $db->setQuery('DROP TABLE IF EXISTS `#__akeebasubs_levelitemxref`')->query();
    }

	/**
	 * Called after install
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install)
	 * @param   object  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($type, $parent)
	{
		// init vars
		$db = JFactory::getDBO();
		$release = $parent->get( "manifest" )->version;

		if($type == 'install'){
			echo JText::sprintf('PLG_ZLFRAMEWORK_SYS_INSTALL', $this->_ext_name, $release);
		}

		if($type == 'update'){
			echo JText::sprintf('PLG_ZLFRAMEWORK_SYS_UPDATE', $this->_ext_name, $release);
		}

		// create table
		$db->setQuery('CREATE TABLE IF NOT EXISTS `#__akeebasubs_levelitemxref` ('
			.'`level_id` int(11) NOT NULL UNIQUE,'
			.'`item_id` int(11) NOT NULL UNIQUE,'
			.'PRIMARY KEY (`level_id`,`item_id`),'
			.'KEY `LEVEL_INDEX` (`level_id`),'
			.'KEY `ITEMID_INDEX` (`item_id`)'
			.') ENGINE=MyISAM DEFAULT CHARSET=utf8;');
		$db->query();

		// fix the existing tables
		$db->setQuery('ALTER TABLE `#__akeebasubs_levelitemxref` ADD UNIQUE (`level_id`)')->query();
		$db->setQuery('ALTER TABLE `#__akeebasubs_levelitemxref` ADD UNIQUE (`item_id`)')->query();

		// remove obsolete
		$this->removeObsolete();
	}

	/**
	 * Removes obsolete files and folders
	 * @version 1.1
	 */
	private function removeObsolete()
	{
		// Remove files
		if(!empty($this->_obsolete['files'])) foreach($this->_obsolete['files'] as $file) {
			$f = JPATH_ROOT.'/'.$file;
			if(!JFile::exists($f)) continue;
			JFile::delete($f);
		}

		// Remove folders
		if(!empty($this->_obsolete['folders'])) foreach($this->_obsolete['folders'] as $folder) {
			$f = JPATH_ROOT.'/'.$folder;
			if(!JFolder::exists($f)) continue;
			JFolder::delete($f);
		}
	}

	/**
	 * creates the lang string
	 * @version 1.0
	 *
	 * @return  string
	 */
	protected function langString($string)
	{
		return $this->_lng_prefix.$string;
	}

	/**
	 * check extensions requirements
	 *
	 * @return  boolean  True on success
	 */
	protected function checkRequirements($parent)
	{
		/*
		 * make sure Akeeba Subscription exist, is enabled
		 */
		if (!JFile::exists(JPATH_ADMINISTRATOR.'/components/com_akeebasubs/aaa_akeebasubs.xml')
			|| !JComponentHelper::getComponent('com_akeebasubs', true)->enabled) {
			$this->_error = "ZOOaksubs relies on <a href=\"https://www.akeebabackup.com\" target=\"_blank\">Akeeba Subscriptions</a>, be sure is installed and enabled before retrying the installation.";
			return false;
		}

		// and up to date
		$akeeba_manifest = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_akeebasubs/aaa_akeebasubs.xml');
		$min_release = 2;

		if( version_compare((string)$akeeba_manifest->version, (string)$min_release, '<') ) {
			$this->_error = "Akeeba Subscription v{$min_release} or higher required, please update it and retry the installation.";

			return false;
		}

		return true;
	}
}