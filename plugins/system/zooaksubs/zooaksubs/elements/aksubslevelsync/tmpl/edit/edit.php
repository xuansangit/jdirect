<?php
/**
* @package		ZOOaksubs
* @author    	ZOOlanders http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
	
// set default
$default = $this->config->get('default', 0);
if ($default != '' && $this->_item != null && $this->_item->id == 0) {
	$this->set('value', $default);
}

$level_id = $this->app->aksubs->getRelatedLevel($this->_item->id)

?>

<div id="<?php echo $this->identifier; ?>">

	<div class="row radio">
        <?php echo $this->app->html->_('select.booleanlist', $this->getControlName('value'), '', $this->get('value', $default)); ?>
    </div>

	<?php if ($level_id) : ?>
    <div class="row">
        <?php echo JText::_('PLG_ZOOAKSUBS_SYNCED_WITH_LEVEL').' <a target="_blank" href="index.php?option=com_akeebasubs&view=level&id='.$level_id.'">'.$level_id.'</a>'; ?>
    </div>
	<?php endif; ?>
	
</div>
