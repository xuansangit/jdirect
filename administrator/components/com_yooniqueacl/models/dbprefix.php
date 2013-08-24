<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.model');

/**
 * A feature to change the site's database prefix - Model
 */
class YooniqueaclModelDbprefix extends JModelLegacy
{
	
	/**
	 * Returns the currently used database prefix
	 * @return string
	 */
	public function getCurrentPrefix()
	{
		
		$config = new YooniqueaclConfigFile;
		return $config->dbprefix;

	}
	
	/**
	 * Gets a random database prefix of a specified length. For instance, if the requested
	 * length is 3, it will consist of three random letters and an underscore. 
	 * @param $length int The requested alpha portion length of the prefix (3-6)
	 * @return string
	 */
	public function getRandomPrefix($length = 3)
	{
		$validchars = 'abcdefghijklmnopqrstuvwxyz';
		$charslength = strlen($validchars);
		
		if($length < 3) $length = 3;
		if($length > 6) $length = 6;
		
		$prefix = '';
		
		for($i = 0; $i < $length; $i++)
		{
			$rand = rand(0, $charslength - 1);
			$prefix .= substr($validchars, $rand, 1);
		}
		
		$prefix .= '_';
		
		return $prefix;
	}
	
	/**
	 * Validates a prefix. The prefix must be 3-6 lowercase characters followed by
	 * an underscore and must not alrady exist in the current database. It must
	 * also not be jos_ or bak_.
	 * 
	 * @param $prefix string The prefix to check
	 * @return string|bool The validated prefix or false if the prefix is invalid
	 */
	public function validatePrefix($prefix,$changeconfigonly)
	{
		// Check that the prefix is not jos_ or bak_
		if( ($prefix == 'jos_') || ($prefix == 'bak_') ) return false;
		
		$config = new YooniqueaclConfigFile;
		$oldprefix = $config->dbprefix;

		if($prefix == $oldprefix) return false;
		
		// Check the length
		$pLen = strlen($prefix);
		if( ($pLen < 4) || ($pLen > 6) && $prefix <> 'yooniqueacl_') return false;
		
		// Check that the prefix ends with an underscore
		if( substr($prefix,-1) != '_' ) return false;
		
		// Check that the part before the underscore is lowercase letters
		$valid = preg_match('/[\w]/i_', $prefix);
		if($valid === 0) return false;
		
		// Turn the prefix into lowercase
		$prefix = strtolower($prefix);
		
		// Check if the prefix already exists in the database
		$config = JFactory::getConfig();
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$joomlaprefix = $config->get('dbprefix','');
		} else {
			$joomlaprefix = $config->getValue('config.dbprefix','');
		}
		$db = $this->getDBO();
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$dbname = $config->get('db','');
		} else {
			$dbname = $config->getValue('config.db','');
		}
		$sql = "SHOW TABLES WHERE `Tables_in_{$dbname}` like '${joomlaprefix}{$prefix}%'";
		$db->setQuery($sql);
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$existing_tables = $db->loadColumn();
		} else {
			$existing_tables = $db->loadResultArray();
		}

		if($changeconfigonly? !count($existing_tables) : count($existing_tables)) return false;
		
		return $prefix;
	}
	
	/**
	 * Updates the configuration.php file with the given prefix
	 * @param $prefix string The prefix to write to the configuration.php file
	 * @return bool False if writing to the file was not possible
	 */
	public function updateConfiguration($prefix)
	{
//return true;		
		$data = array ('dbprefix' => $prefix);

		$prev = new YooniqueaclConfigFile();
		$prev = JArrayHelper::fromObject($prev);

		$data = array_merge($prev, $data);

		$config = new JRegistry('config');
		$config->loadArray($data);

		jimport('joomla.filesystem.path');
		jimport('joomla.filesystem.file');

		$file = JPATH_ADMINISTRATOR.'/components/com_yooniqueacl/helpers/_configfile.php';

		$configString = $config->toString('PHP', array('class' => 'YooniqueaclConfigFile', 'closingtag' => false));
		if (!JFile::write($file, $configString)) {
			$this->setError(JText::_('COM_CONFIG_ERROR_WRITE_FAILED'));
			return false;
		}

		return true;
	}
	
	/**
	 * Performs the actual schema change
	 * @param $prefix string The new prefix
	 * @return bool False if the schema could not be changed
	 */
	public function changeSchema($prefix)
	{

		$config = JFactory::getConfig();
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$dbname = $config->get('db','');
		} else {
			$dbname = $config->getValue('config.db','');
		}
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$joomlaprefix = $config->get('dbprefix','');
		} else {
			$joomlaprefix = $config->getValue('config.dbprefix','');
		}
		$config = new YooniqueaclConfigFile;
		$oldprefix = $joomlaprefix . $config->dbprefix;
		$newprefix = $joomlaprefix . $prefix;
		
		$db = $this->getDBO();
		$sql = "SHOW TABLES WHERE `Tables_in_{$dbname}` like '{$oldprefix}%'";
		$db->setQuery($sql);
		
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$oldTables = $db->loadColumn();
		} else {
			$oldTables = $db->loadResultArray();
		}
		
		if(empty($oldTables)) return false;

		foreach($oldTables as $table)
		{
			$newTable = $newprefix . substr($table, strlen($oldprefix));
			$sql = "RENAME TABLE `$table` TO `$newTable`";
			$db->setQuery($sql);
			if(!$db->query()) {
				// Something went wrong; I am pulling the plug and hope for the best
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Performs the actual database changes and configuration updates
	 * @param $prefix string The new prefix
	 * @return bool|string True on success, the error message string on failure
	 */
	public function performChanges($prefix, $changeconfigonly)
	{

		// Cache the old prefix
		$config = JFactory::getConfig();
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$oldprefix = $config->get('dbprefix','');
		} else {
			$oldprefix = $config->getValue('config.dbprefix','');
		}
		
		// Validate the prefix
		$prefix = $this->validatePrefix($prefix,$changeconfigonly);
		if($prefix === false) {
			return JText::sprintf('ERR_DBPREFIX_INVALIDPREFIX', $prefix);
		}
		
		// Try to change the configuration.php
		if(!$this->updateConfiguration($prefix)) {
			return JText::_('ERR_DBPREFIX_CANTSAVECONFIGURATION');
		}

		if ($changeconfigonly) return true;
	
		// Try to perform the database changes
		if(!$this->changeSchema($prefix)) {
			// Revert the configuration.php
			$this->updateConfiguration($oldprefix);
			// and return an error string
			return JText::_('ERR_DBPREFIX_COULDNTCHANGESCHEMA');
		}
		
		// All done. Hopefully nothing broke.
		return true;
	}
	

}
