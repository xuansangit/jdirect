<?php
/**
 * Main Plugin File
 * Does all the magic!
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

// Include the moduleHelper
jimport('joomla.filesystem.file');
if (JFile::exists(JPATH_PLUGINS . '/system/nnframework/nnframework.php'))
{
	$classes = get_declared_classes();
	if (!in_array('JModuleHelper', $classes) && !in_array('jmodulehelper', $classes))
	{
		require_once JPATH_PLUGINS . '/system/advancedmodules/modulehelper.php';
	}
	JFactory::getApplication()->registerEvent('onRenderModule', 'plgSystemAdvancedModulesRenderModule');
	JFactory::getApplication()->registerEvent('onCreateModuleQuery', 'plgSystemAdvancedModulesCreateModuleQuery');
	JFactory::getApplication()->registerEvent('onPrepareModuleList', 'plgSystemAdvancedModulesPrepareModuleList');
}

/**
 * Plugin that shows active modules in menu item edit view
 */
class plgSystemAdvancedModules extends JPlugin
{
	function __construct(&$subject, $config)
	{
		$this->_pass = 0;
		parent::__construct($subject, $config);
	}

	function onAfterRoute()
	{
		$this->_pass = 0;

		if (JFactory::getApplication()->isSite())
		{
			return;
		}

		// only in html
		if (JFactory::getDocument()->getType() != 'html')
		{
			return;
		}

		// load the admin language file
		$lang = JFactory::getLanguage();
		if ($lang->getTag() != 'en-GB')
		{
			// Loads English language file as fallback (for undefined stuff in other language file)
			$lang->load('com_advancedmodules', JPATH_ADMINISTRATOR, 'en-GB');
		}
		$lang->load('com_advancedmodules', JPATH_ADMINISTRATOR, null, 1);

		// return if NoNumber Framework plugin is not installed
		if (!JFile::exists(JPATH_PLUGINS . '/system/nnframework/nnframework.php'))
		{
			if (JFactory::getApplication()->isAdmin() && JFactory::getApplication()->input->get('option') != 'com_login')
			{
				$msg = JText::_('AMM_NONUMBER_FRAMEWORK_NOT_INSTALLED')
					. ' ' . JText::sprintf('AMM_EXTENSION_CAN_NOT_FUNCTION', JText::_('COM_ADVANCEDMODULES'));
				$mq = JFactory::getApplication()->getMessageQueue();
				foreach ($mq as $m)
				{
					if ($m['message'] == $msg)
					{
						$msg = '';
						break;
					}
				}
				if ($msg)
				{
					JFactory::getApplication()->enqueueMessage($msg, 'error');
				}
			}
			return;
		}

		if (!JFile::exists(JPATH_ADMINISTRATOR . '/components/com_advancedmodules/advancedmodules.php'))
		{
			return;
		}

		$this->_pass = 1;
	}

	/*
	 * Replace links to com_modules with com_advancedmodules
	 */
	function onAfterRender()
	{
		if ($this->_pass)
		{
			if (JFactory::getApplication()->input->get('option') == 'com_modules')
			{
				$config = plgSystemAdvancedModulesConfig();
				if (!$config->show_switch)
				{
					return;
				}
				$body = JResponse::getBody();
				if (JFactory::getApplication()->input->get('view') == 'module')
				{
					$url = JRoute::_('index.php?option=com_advancedmodules&task=module.edit&id=' . (int) JFactory::getApplication()->input->get('id'));
				}
				else
				{
					$url = JRoute::_('index.php?option=com_advancedmodules');
				}
				$link = '<a style="float:right;" href="' . $url . '">' . JText::_('AMM_SWITCH_TO_ADVANCED_MODULES_MANAGER') . '</a>';
				$body = preg_replace('#(<div class="m">\s*)((<\!--.*?-->\s*)*<form)#', '\1' . $link . '<div class="clr"></div>\2', $body);
				$body = preg_replace('#(</form>\s*)((<\!--.*?-->\s*)*<div class="clr"></div>)#', '\1' . $link . '\2', $body);
				JResponse::setBody($body);
			}
			else
			{
				$body = JResponse::getBody();
				$body = preg_replace('#(\?option=com_)(modules[^a-z-_])#', '\1advanced\2', $body);
				$body = str_replace(array('?option=com_advancedmodules&force=1', '?option=com_advancedmodules&amp;force=1'), '?option=com_modules', $body);
				JResponse::setBody($body);
			}
		}
	}
}

// ModuleHelper methods
function plgSystemAdvancedModulesRenderModule(&$module)
{
	$client = JFactory::getApplication()->getClientId();

	// return false if is not frontend
	if ($client != 0)
	{
		return false;
	}

	$config = plgSystemAdvancedModulesConfig();

	// return false if show_hideempty is off in main config
	if (!$config->show_hideempty)
	{
		return false;
	}

	// return false if hideempty is off in module params
	if (!isset($module->adv_params) || !isset($module->adv_params->hideempty) || !$module->adv_params->hideempty)
	{
		return false;
	}

	$trimmed_content = trim($module->content);
	// return true if module is empty
	if ($trimmed_content == '')
	{
		// return true will prevent the module from outputting html
		return true;
	}

	// remove html and hidden whitespace
	$trimmed_content = str_replace(chr(194) . chr(160), ' ', $trimmed_content);
	$trimmed_content = str_replace(array('&nbsp;', '&#160;'), ' ', $trimmed_content);
	// remove comment tags
	$trimmed_content = preg_replace('#<\!--.*?-->#si', '', $trimmed_content);
	// remove all closing tags
	$trimmed_content = preg_replace('#</[^>]+>#si', '', $trimmed_content);
	// remove tags to be ignored
	$tags = 'p|div|span|strong|b|em|i|ul|font|br|h[0-9]|fieldset|label|ul|ol|li|table|thead|tbody|tfoot|tr|th|td|form';
	$s = '#<(' . $tags . ')([^a-z0-9>][^>]*)?>#si';
	if (@preg_match($s . 'u', $trimmed_content))
	{
		$s .= 'u';
	}
	if (preg_match($s, $trimmed_content))
	{
		$trimmed_content = preg_replace($s, '', $trimmed_content);
	}
	// return true if module is empty
	if (trim($trimmed_content) == '')
	{
		// return true will prevent the module from outputting html
		return true;
	}
	return false;
}

function &plgSystemAdvancedModulesConfig()
{
	static $instance;
	if (!is_object($instance))
	{
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters = NNParameters::getInstance();
		$instance = $parameters->getComponentParams('advancedmodules');
	}
	return $instance;
}

function plgSystemAdvancedModulesCreateModuleQuery(&$query)
{
	$client = JFactory::getApplication()->getClientId();

	if ($client == 0)
	{
		foreach ($query as $type => $strings)
		{
			foreach ($strings as $i => $string)
			{
				if ($type == 'select')
				{
					$query->{$type}[$i] = str_replace(', mm.menuid', '', $string);
				}
				else if (!(strpos($string, 'mm.') === false) || !(strpos($string, 'm.publish_') === false))
				{
					unset($query->{$type}[$i]);
				}
			}
		}
		$query->select[] = 'am.params as adv_params, 0 as menuid, m.publish_up, m.publish_down';
		$query->join[] = '#__advancedmodules as am ON am.moduleid = m.id';
		$query->order = array('m.ordering, m.id');
	}
}

function plgSystemAdvancedModulesPrepareModuleList(&$modules)
{
	$client = JFactory::getApplication()->getClientId();

	if ($client == 0)
	{
		jimport('joomla.filesystem.file');

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters = NNParameters::getInstance();

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/assignments.php';
		$assignments = new NNFrameworkAssignmentsHelper;

		$xmlfile_assignments = JPATH_ADMINISTRATOR . '/components/com_advancedmodules/assignments.xml';

		$config = plgSystemAdvancedModulesConfig();

		// set params for all loaded modules first
		// and make it an associated array (array id = module id)
		$new_modules = array();
		require_once JPATH_ADMINISTRATOR . '/components/com_advancedmodules/models/module.php';
		$model = new AdvancedModulesModelModule;
		foreach ($modules as $id => $module)
		{
			if (!isset($module->adv_params))
			{
				$module->adv_params = plgSystemAdvancedModulesGetAdvancedParams($id);
			}
			$registry = new JRegistry;
			if (strpos($module->adv_params, '"assignto_menuitems"') === false)
			{
				$module->adv_params = $model->initAssignments($module->id, $module, $module->adv_params);
				$registry->loadArray($module->adv_params);
			}
			else
			{
				$registry->loadString($module->adv_params);
			}
			$module->adv_params = $registry->toObject();
			$module->adv_params = $parameters->getParams($module->adv_params, $xmlfile_assignments);
			$new_modules[$module->id] = $module;
		}
		$modules = $new_modules;
		unset($new_modules);

		foreach ($modules as $id => $module)
		{
			if ($module->adv_params === 0)
			{
				continue;
			}

			$module->reverse = 0;

			if (!isset($module->published))
			{
				$module->published = 0;
			}
			// Check if module should mirror another modules assignment settings
			if ($module->published)
			{
				$count = 0;
				while ($count++ < 10
					&& isset($module->adv_params->mirror_module)
					&& $module->adv_params->mirror_module
					&& isset($module->adv_params->mirror_moduleid)
					&& $module->adv_params->mirror_moduleid
				)
				{
					$mirror_moduleid = (int) $module->adv_params->mirror_moduleid;
					$module->reverse = ($module->adv_params->mirror_module == 2);
					if ($mirror_moduleid)
					{
						if ($mirror_moduleid == $id)
						{
							$empty = new stdClass;
							$module->adv_params = $parameters->getParams($empty, $xmlfile_assignments);
						}
						else
						{
							if (isset($modules[$mirror_moduleid]))
							{
								if (!isset($modules[$mirror_moduleid]->adv_param))
								{
									$modules[$mirror_moduleid]->adv_param = plgSystemAdvancedModulesGetAdvancedParams($mirror_moduleid);
									$modules[$mirror_moduleid]->adv_param = $parameters->getParams($modules[$mirror_moduleid]->adv_param, $xmlfile_assignments);
								}
								$module->adv_params = $modules[$mirror_moduleid]->adv_params;
							}
							else
							{
								$module->adv_params = plgSystemAdvancedModulesGetAdvancedParams($mirror_moduleid);
								$module->adv_params = $parameters->getParams($module->adv_params, $xmlfile_assignments);
							}
						}
					}
				}
			}

			if ($module->published)
			{
				if (!$config->show_assignto_homepage)
				{
					$module->adv_params->assignto_homepage = 0;
				}
				if (!$config->show_assignto_usergrouplevels)
				{
					$module->adv_params->assignto_usergrouplevels = 0;
				}
				if (!$config->show_assignto_date)
				{
					$module->adv_params->assignto_date = 0;
				}
				if (!$config->show_assignto_languages)
				{
					$module->adv_params->assignto_languages = 0;
				}
				if (!$config->show_assignto_templates)
				{
					$module->adv_params->assignto_templates = 0;
				}
				if (!$config->show_assignto_urls)
				{
					$module->adv_params->assignto_urls = 0;
				}
				if (!$config->show_assignto_os)
				{
					$module->adv_params->assignto_os = 0;
				}
				if (!$config->show_assignto_browsers)
				{
					$module->adv_params->assignto_browsers = 0;
				}
				if (!$config->show_assignto_components)
				{
					$module->adv_params->assignto_components = 0;
				}
				if (!$config->show_assignto_content)
				{
					$module->adv_params->assignto_contentpagetypes = 0;
					$module->adv_params->assignto_cats = 0;
					$module->adv_params->assignto_articles = 0;
				}

				$ass = $assignments->getAssignmentsFromParams($module->adv_params);
				$pass = $assignments->passAll($ass, $module->adv_params->match_method);

				if (!$pass)
				{
					$module->published = 0;
				}

				if ($module->reverse)
				{
					$module->published = $module->published ? 0 : 1;
				}
			}

			$modules[$id] = $module;
		}
	}
}

function plgSystemAdvancedModulesGetAdvancedParams($id)
{
	$db = JFactory::getDBO();
	$query = $db->getQuery(true)
		->select('a.params')
		->from('#__advancedmodules AS a')
		->where('a.moduleid = ' . (int) $id);
	$db->setQuery($query);
	return $db->loadResult();
}
