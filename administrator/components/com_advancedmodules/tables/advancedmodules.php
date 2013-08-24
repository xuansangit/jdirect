<?php
/**
 * Table class: advancedmodules
 *
 * @package         Advanced Module Manager
 * @version         4.7.1
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2013 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class AdvancedModulesTableAdvancedModules extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__advancedmodules', 'moduleid', $db);
	}

	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_advancedmodules.module.' . (int) $this->$k;
	}

	protected function _getAssetTitle()
	{
		if (isset($this->_title))
		{
			return $this->_title;
		}

		return $this->_getAssetName();
	}

	/**
	 * Method to get the parent asset id for the record
	 *
	 * @param   JTable   $table  A JTable object for the asset parent
	 * @param   integer  $id
	 *
	 * @return  integer
	 *
	 * @since   11.1
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;
		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->select('id')
			->from('#__assets')
			->where('name = ' . $db->quote('com_advancedmodules'));

		// Get the asset id from the database.
		$db->setQuery($query);
		if ($result = $db->loadResult())
		{
			$assetId = (int) $result;
		}

		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}
}
