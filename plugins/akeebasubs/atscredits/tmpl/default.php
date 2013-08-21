<?php defined('_JEXEC') or die(); ?>
<div class="row-fluid">
	<div class="span6">
		<div class="control-group">
			<label for="params_atscredits_credits" class="control-label">
				<?php echo JText::_('PLG_AKEEBASUBS_ATSCREDITS_CREDITS_TITLE'); ?>
			</label>
			<div class="controls">
				<input type="text" name="params[atscredits_credits]" id="params_atscredits_credits"
					   value="<?php echo isset($level->params->atscredits_credits) ? $level->params->atscredits_credits : 0 ?>"
					   class="input-small"
					   />
				<span class="help-block">
					<?php echo JText::_('PLG_AKEEBASUBS_ATSCREDITS_CREDITS_DESC') ?>
				</span>
			</div>
		</div>
	</div>
</div>
<div class="alert alert-warning">
	<p><?php echo JText::_('PLG_AKEEBASUBS_ATSCREDITS_USAGENOTE'); ?></p>
</div>