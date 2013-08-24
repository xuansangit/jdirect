<?php
/**
 * NoNumber Framework Helper File: Assignments: Templates
 *
 * @package         NoNumber Framework
 * @version         13.8.4
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2013 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * Assignments: Templates
 */
class NNFrameworkAssignmentsTemplates
{
	function passTemplates(&$parent, &$params, $selection = array(), $assignment = 'all')
	{
		$template = JFactory::getApplication()->getTemplate();
		return $parent->passSimple($template, $selection, $assignment, 1);
	}
}
