<?php
/**
 *  @package	akeebasubs
 *  @copyright	Copyright (c)2010-2013 Nicholas K. Dionysopoulos / AkeebaBackup.com
 *  @license	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 *  @version 	$Id$
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// no direct access
defined('_JEXEC') or die('');

// PHP version check
if(defined('PHP_VERSION')) {
	$version = PHP_VERSION;
} elseif(function_exists('phpversion')) {
	$version = phpversion();
} else {
	// No version info. I'll lie and hope for the best.
	$version = '5.0.0';
}
// Old PHP version detected. EJECT! EJECT! EJECT!
if(!version_compare($version, '5.3.0', '>=')) return;

include_once JPATH_ADMINISTRATOR.'/components/com_akeebasubs/version.php';
include_once JPATH_LIBRARIES.'/fof/include.php';
if(!defined('FOF_INCLUDED') || !class_exists('FOFForm', true))
{
	return;
}
require_once JPATH_ADMINISTRATOR.'/components/com_akeebasubs/helpers/format.php';
require_once JPATH_ADMINISTRATOR.'/components/com_akeebasubs/helpers/cparams.php';

$lang = JFactory::getLanguage();
$lang->load('mod_aksubslist',JPATH_SITE,'en-GB',true);
$lang->load('mod_aksubslist',JPATH_SITE,null,true);
$lang->load('com_akeebasubs',JPATH_SITE,'en-GB',true);
$lang->load('com_akeebasubs',JPATH_SITE,null,true);

?>
<div id="mod-aksubslist-<?php echo $module->id?>" class="mod-aksubslist">
<?php if(JFactory::getUser()->guest): ?>
	<span class="akeebasubs-subscriptions-itemized-nosubs">
		<?php echo JText::_('COM_AKEEBASUBS_LEVELS_ITEMIZED_NOSUBS')?>
	</span>
<?php else: ?>
<?php FOFDispatcher::getTmpInstance('com_akeebasubs', 'subscriptions', array(
	'input'		=> array(
		'savestate'	=> 0,
		'option'	=> 'com_akeebasubs',
		'view'		=> 'subscriptions',
		'layout'	=> 'itemized',
		'limit'		=> 0,
		'limitstart'=> 0,
		'paystate'	=> 'C',
		'user_id'	=> JFactory::getUser()->id,
		'task'		=> 'browse'
	)
))->dispatch();
?>
<?php endif; ?>
</div>