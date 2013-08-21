<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div class="category">
<?php if ($category) : ?>

	<?php $link = $this->app->route->category($category); ?>

	<?php if ($this->params->get('template.show_categories_titles')) : ?>
	<h2 class="title">

		<a href="<?php echo $link; ?>" title="<?php echo $category->name; ?>"><?php echo $category->name; ?></a>

		<?php if ($this->params->get('template.show_categories_item_count')) : ?>
			<span>(<?php echo $category->totalItemCount(); ?>)</span>
		<?php endif; ?>

	</h2>
	<?php endif; ?>

	<?php if ((($image = $category->getImage('content.teaser_image')) && $this->params->get('template.show_categories_images')) || ($this->params->get('template.show_categories_descriptions') && $category->getParams()->get('content.teaser_description'))) : ?>
	<div class="description">

		<?php if ($image && $this->params->get('template.show_categories_images')) : ?>
		<a class="teaser-image <?php echo 'teaser-image-'.$this->params->get('template.categories_images_alignment'); ?>" href="<?php echo $link; ?>" title="<?php echo $category->name; ?>">
			<img src="<?php echo $image['src']; ?>" title="<?php echo $category->name; ?>" alt="<?php echo $category->name; ?>" <?php echo $image['width_height']; ?>/>
		</a>
		<?php endif; ?>

		<?php if ($this->params->get('template.show_categories_descriptions') && $category->getParams()->get('content.teaser_description')) echo $category->getParams()->get('content.teaser_description'); ?>

	</div>
	<?php endif; ?>

	<?php

		// render sub categories
		if ($this->params->get('template.show_sub_categories') && $selected_child_categories = $category->getChildren()) {

			// remove empty categories
			if (!$this->params->get('config.show_empty_categories', false)) {
				$selected_child_categories = array_filter($selected_child_categories, create_function('$category', 'return $category->totalItemCount();'));
			}

			// init vars
			$i       = 0;
			$columns = array();
			$column  = 0;
			$row     = 0;
			$rows    = ceil(count($selected_child_categories) / $this->params->get('template.categories_cols'));

			// create columns
			foreach ($selected_child_categories as $child) {

				if ($this->params->get('template.categories_order')) {
					// order down
					if ($row >= $rows) {
						$column++;
						$row  = 0;
						$rows = ceil((count($selected_child_categories) - $i) / ($this->params->get('template.categories_cols') - $column));
					}
					$row++;
					$i++;
				} else {
					// order across
					$column = $i++ % $this->params->get('template.categories_cols');
				}

				if (!isset($columns[$column])) {
					$columns[$column] = '';
				}

				$link = $this->app->route->category($child);
				$item_count = ($this->params->get('template.show_sub_categories_item_count')) ? ' <span>('.$child->totalItemCount().')</span>' : '';
				$children = '<li><a href="'.$link.'" title="'.$child->name.'">'.$child->name.'</a>'.$item_count.'</li>';

				$columns[$column] .= $children;
			}

			// render columns
			$count = count($columns);
			if ($count) {
				echo '<div class="sub-categories sub-categories-col-'.$count.'">';
				for ($j = 0; $j < $count; $j++) {
					$first = ($j == 0) ? ' first' : null;
					$last  = ($j == $count - 1) ? ' last' : null;
					echo '<ul class="width'.intval(100 / $count).$first.$last.'">'.$columns[$j].'</ul>';
				}
				echo '</div>';
			}

		}

	?>

<?php endif; ?>
</div>