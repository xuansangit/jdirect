<?php
/**
* @package   ZOO Comment
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include css
$zoo->document->addStylesheet('mod_zoocomment:tmpl/list/style.css');

?>

<?php if (count($comments)) : ?>

<section class="zoo-comments-list">

	<?php $i = 0; foreach ($comments as $comment) : ?>

		<?php // set author name
			$author = $comment->getAuthor();
			$author->name = $author->name ? $author->name : JText::_('COM_ZOO_ANONYMOUS');
		?>

		<article class="<?php if ($author->isJoomlaAdmin()) echo 'comment-byadmin'; ?>">

			<?php if ($params->get('show_avatar', 1)) : ?>
			<div class="avatar media-left">
				<?php if ($author->url) : ?><a href="<?php echo $author->url; ?>" title="<?php echo $author->url; ?>" rel="nofollow"><?php endif; ?>
				<?php echo $author->getAvatar($params->get('avatar_size', 50)); ?>
				<?php if ($author->url) : ?></a><?php endif; ?>
			</div>
			<?php endif; ?>

			<?php if ($params->get('show_meta', 1)) : ?>
			<h4 class="title">
				<a class="permalink" href="<?php echo JRoute::_($zoo->route->comment($comment)); ?>"><?php echo $comment->getItem()->name; ?></a>
			</h4>
			<?php endif; ?>

			<?php if ($params->get('show_author', 1)) : ?>
			<p class="author">
				<?php if ($author->url) : ?><a href="<?php echo $author->url; ?>" title="<?php echo $author->url; ?>" rel="nofollow"><?php endif; ?>
				<?php echo $author->name; ?>
				<?php if ($author->url) : ?></a><?php endif; ?>
			</p>
			<?php endif; ?>

			<div class="content"><p><?php echo $zoo->comment->filterContentOutput($zoo->string->truncate($comment->content, $zoo->get('commentsmodule.max_characters'))); ?></p></div>

		</article>

	<?php $i++; endforeach; ?>

</section>

<?php else : ?>
	<?php echo JText::_('COM_ZOO_NO_COMMENTS_FOUND'); ?>
<?php endif;