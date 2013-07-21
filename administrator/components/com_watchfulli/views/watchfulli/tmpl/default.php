<?php
/**
 * @package watchfulli
 * @copyright Copyright (c) 2012-2013 watchful.li
 */
// No direct access to this file
defined('WATCHFULLI_PATH') or die;

$mainframe = JFactory::getApplication();
?>
<h3><?php echo JText::_('COM_JMONITORINGSLAVE_AUTHENTIFICATION'); ?></h3>
<p><?php echo JText::_('COM_JMONITORINGSLAVE_SECRET_KEY'); ?>:
<input readonly="readonly" type="text" style="width:250px;" size="55" value="<?php echo md5('watch'.$mainframe->getCfg('secret').'fulli'); ?>" /></p>