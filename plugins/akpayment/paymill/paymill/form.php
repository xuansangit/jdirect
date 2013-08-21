<?php defined('_JEXEC') or die(); ?>

<h3><?php echo JText::_('PLG_AKPAYMENT_PAYMILL_FORM_HEADER') ?></h3>

<div id="payment-errors" class="alert alert-error" style="display: none;"></div>
<?php
/*
 * 2013-01-31 nicholas: I moved thos fields outside the form because we MUST
 * NOT submit the credit card information back to the site. The whole point of
 * using the bridge JS to get a token is exactly that. Not having the CC info
 * reach our server allows us to do transactions even from servers which are not
 * certified for PCI Compliance. This is a matter of transaction security.
 */
?>
<div class="form-horizontal">
	<div class="control-group" id="control-group-card-holder">
		<label for="card-holder" class="control-label" style="width:190px; margin-right:20px;">
			<?php echo JText::_('PLG_AKPAYMENT_PAYMILL_FORM_CARDHOLDER') ?>
		</label>
		<div class="controls">
			<input type="text" name="card-holder" id="card-holder" class="input-large" value="<?php echo $data->carholder ?>" />
		</div>
	</div>
	<div class="control-group" id="control-group-card-number">
		<label for="card-number" class="control-label" style="width:190px; margin-right:20px;">
			<?php echo JText::_('PLG_AKPAYMENT_PAYMILL_FORM_CC') ?>
		</label>
		<div class="controls">
			<input type="text" name="card-number" id="card-number" class="input-large" />
		</div>
	</div>
	<div class="control-group" id="control-group-card-expiry">
		<label for="card-expiry" class="control-label" style="width:190px; margin-right:20px;">
			<?php echo JText::_('PLG_AKPAYMENT_PAYMILL_FORM_EXPDATE') ?>
		</label>
		<div class="controls">
			<?php echo $this->selectMonth() ?><span> / </span><?php echo $this->selectYear() ?>
		</div>
	</div>
	<div class="control-group" id="control-group-card-cvc">
		<label for="card-cvc" class="control-label" style="width:190px; margin-right:20px;">
			<?php echo JText::_('PLG_AKPAYMENT_PAYMILL_FORM_CVC') ?>
		</label>
		<div class="controls">
			<input type="text" name="card-cvc" id="card-cvc" class="input-mini" />
		</div>
	</div>
</div>

<form id="payment-form" action="<?php echo $data->url ?>" method="post" class="form form-horizontal">
	<input type="hidden" name="currency" id="currency" value="<?php echo $data->currency ?>" />
	<input type="hidden" name="amount" id="amount" value="<?php echo $data->amount ?>" />
	<input type="hidden" name="description" id="description" value="<?php echo $data->description ?>" />
	<input type="hidden" name="token" id="token" />
	<div class="control-group">
		<label for="pay" class="control-label" style="width:190px; margin-right:20px;">
		</label>
		<div class="controls">
			<input type="submit" id="payment-button" class="btn" value="<?php echo JText::_('PLG_AKPAYMENT_PAYMILL_FORM_PAYBUTTON') ?>" />
		</div>
	</div>
</form>