<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// set author name
$author->name = $author->name ? $author->name : JText::_('Anonymous');

?>
<li>
	<div id="comment-<?php echo $comment->id; ?>" class="comment <?php if ($author->isJoomlaAdmin()) echo 'comment-byadmin'; ?>">

		<?php if ($params->get('avatar', 0)) : ?>
			<div class="avatar"><?php echo $author->getAvatar(85); ?></div>
		<?php endif; ?>

		<div class="comment-content">
			<div class="comment-head">

				<?php if ($author->url) : ?>
					<h5 class="author"><a href="<?php echo JRoute::_($author->url); ?>" title="<?php echo $author->url; ?>" rel="nofollow"><?php echo $author->name; ?></a></h5>
				<?php else: ?>
					<h5 class="author"><?php echo $author->name; ?></h5>
				<?php endif; ?>

				<div class="meta">
					<?php echo $this->app->html->_('date', $comment->created, $this->app->date->format(JText::_('DATE_FORMAT_COMMENTS')), $this->app->date->getOffset()); ?>
					| <a class="permalink" href="#comment-<?php echo $comment->id; ?>">#</a>
				</div>

			</div>

			<div class="comment-body">

				<div class="content"><p><?php echo $this->app->comment->filterContentOutput($comment->content); ?></p></div>

				<?php if ($comment->state != Comment::STATE_APPROVED) : ?>
					<div class="moderation"><?php echo JText::_('COMMENT_AWAITING_MODERATION'); ?></div>
				<?php endif; ?>

			</div>

			<?php if ($comment->getItem()->isCommentsEnabled()) : ?>
				<div class="reply"><a href="#" rel="nofollow"><?php echo JText::_('Reply'); ?></a></div>
			<?php endif; ?>

		</div>

	</div>

	<ul class="level<?php echo ++$level; ?>">
		<?php
		foreach ($comment->getChildren() as $comment) {
			echo $this->partial('comment', array('level' => $level, 'comment' => $comment, 'author' => $comment->getAuthor(), 'params' => $params));
		}
		?>
	</ul>

</li>