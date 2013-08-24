<?php
/**
 * @package         Advanced Module Manager
 * @version         4.7.1
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2013 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper for parse XML module files
 *
 * @package     Joomla.Administrator
 * @subpackage  com_advancedmodules
 * @since       1.5
 */
class ModulesHelperXML
{
	/**
	 * @since  1.5
	 */
	public function parseXMLModuleFile(&$rows)
	{
		foreach ($rows as $i => $row)
		{
			if ($row->module == '')
			{
				$rows[$i]->name = 'custom';
				$rows[$i]->module = 'custom';
				$rows[$i]->descrip = 'Custom created module, using Module Manager New function';
			}
			else
			{
				$data = JInstaller::parseXMLInstallFile($row->path . '/' . $row->file);

				if ($data['type'] == 'module')
				{
					$rows[$i]->name = $data['name'];
					$rows[$i]->descrip = $data['description'];
				}
			}
		}
	}
}
