<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$media_position = $params->get('media_position', 'top');

?>

<div class="wk-zoo-item layout-article clearfix">

	<?php if (($media_position == 'top' || $media_position == 'left' || $media_position == 'right') && $this->checkPosition('media')) : ?>
	<div class="media media-<?php echo $media_position; ?>">
		<?php echo $this->renderPosition('media'); ?>
	</div>
	<?php endif; ?>

	<div class="text">
		<?php if ($this->checkPosition('title')) : ?>
		<h2 class="title">
			<?php echo $this->renderPosition('title'); ?>
		</h2>
		<?php endif; ?>

		<?php if ($this->checkPosition('meta')) : ?>
		<p class="meta">
			<?php echo $this->renderPosition('meta', array('style' => 'comma')); ?>
		</p>
		<?php endif; ?>

		<?php if (($media_position == 'middle') && $this->checkPosition('media')) : ?>
		<div class="media media-<?php echo $media_position; ?>">
			<?php echo $this->renderPosition('media'); ?>
		</div>
		<?php endif; ?>

		<?php if ($this->checkPosition('description')) : ?>
		<p class="description">
			<?php 
			$desc = strip_tags($this->renderPosition('description', array('style' => 'block')));
			echo substr($desc, 0, 200).' [...]'; 
			?>
		</p>
		<?php endif; ?>

		<?php if ($this->checkPosition('links')) : ?>
		<p class="links">
			<?php echo $this->renderPosition('links', array('style' => 'pipe')); ?>
		</p>
		<?php endif; ?>
	</div>

	<?php if (($media_position == 'bottom') && $this->checkPosition('media')) : ?>
	<div class="media media-<?php echo $media_position; ?>">
		<?php echo $this->renderPosition('media'); ?>
	</div>
	<?php endif; ?>

	

</div>