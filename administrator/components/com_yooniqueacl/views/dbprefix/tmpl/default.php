<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');
?>
<div class="disclaimer">
	<p><?php echo JText::_('LBL_DBREFIX_INTRO'); ?></p>
</div>

<form name="adminForm" action="index.php" action="post">
	<input type="hidden" name="option" value="com_yooniqueacl" />
	<input type="hidden" name="controller" value="dbprefix" />
	<input type="hidden" name="task" value="change" />
	
	<div class="editform-row">
		<label for="oldprefix"><?php echo JText::_('LBL_DBREFIX_OLDPREFIX') ?></label>
		<input type="text" name="oldprefix" disabled="disabled" value="<?php echo $this->currentPrefix ?>" size="10" />
	</div>
	
	<div class="editform-row">
		<label for="prefix"><?php echo JText::_('LBL_DBREFIX_NEWPREFIX') ?></label>
		<input type="text" name="prefix" value="<?php echo $this->newPrefix ?>" size="10" /><br/>
	</div>

	<div class="editform-row">
		<label for="changeconfigonly"><?php echo JText::_('LBL_DBREFIX_CONFIGONLY') ?></label>
		<input type="checkbox" name="changeconfigonly" value="Yes" size="10" /><br/>
	</div>
	
	<br/>
	<input type="submit" value="<?php echo JText::_('LBL_DBREFIX_CHANGE') ?>" />
</form>
