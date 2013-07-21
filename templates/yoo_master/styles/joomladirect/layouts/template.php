<?php
/**
* @package   yoo_master
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// get template configuration
include($this['path']->path('layouts:template.config.php'));
	
?>
<!DOCTYPE HTML>
<html lang="<?php echo $this['config']->get('language'); ?>" dir="<?php echo $this['config']->get('direction'); ?>">

<head>
<link rel="author" href="https://plus.google.com/108506465691203310523/posts" />
<?php echo $this['template']->render('head'); ?>
</head>

<body id="page" class="page <?php echo $this['config']->get('body_classes'); ?>" data-config='<?php echo $this['config']->get('body_config','{}'); ?>'>

	<?php if ($this['modules']->count('absolute')) : ?>
	<div id="absolute">
		<?php echo $this['modules']->render('absolute'); ?>
	</div>
	<?php endif; ?>
	
	<div class="clearfix">

		<header id="header">

			<?php if ($this['modules']->count('toolbar-l + toolbar-r') || $this['config']->get('date')) : ?>
			<div id="toolbar" class="clearfix">
				<div class="wrapper clearfix">

					<?php if ($this['modules']->count('toolbar-l')) : ?>
					<div class="float-left">
					
						<?php echo $this['modules']->render('toolbar-l'); ?>
						
					</div>
					<?php endif; ?>
						
					<?php if ($this['modules']->count('toolbar-r') || $this['config']->get('date')) : ?>
					<div class="float-right">

						<?php if ($this['config']->get('date')) : ?>
						<time datetime="<?php echo $this['config']->get('datetime'); ?>"><?php echo $this['config']->get('actual_date'); ?></time>
						<?php endif; ?>

						<?php echo $this['modules']->render('toolbar-r'); ?>
					</div>
					<?php endif; ?>
				
				</div>
			</div>
			<?php endif; ?>

			<?php if ($this['modules']->count('logo + headerbar')) : ?>	
			<div id="headerbar" class="clearfix">
				<div class="wrapper clearfix">
					<?php if ($this['modules']->count('logo')) : ?>	
					<a id="logo" href="<?php echo $this['config']->get('site_url'); ?>"><?php echo $this['modules']->render('logo'); ?></a>
					<?php endif; ?>
					
					<?php echo $this['modules']->render('headerbar'); ?>
				</div>
			</div>
			<?php endif; ?>

			<?php if ($this['modules']->count('menu + search')) : ?>
			<div id="menubar" class="clearfix">
				<div class="wrapper clearfix">
					<?php if ($this['modules']->count('menu')) : ?>
					<nav id="menu"><?php echo $this['modules']->render('menu'); ?></nav>
					<?php endif; ?>

					<?php if ($this['modules']->count('search')) : ?>
					<div id="search"><?php echo $this['modules']->render('search'); ?></div>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>
		
			<?php if ($this['modules']->count('banner')) : ?>
			<div id="banner"><?php echo $this['modules']->render('banner'); ?></div>
			<?php endif; ?>
		
		</header>

		<?php if ($this['modules']->count('top-a')) : ?>
		<section id="top-a" class="grid-block">
			<div class="wrapper clearfix"><?php echo $this['modules']->render('top-a', array('layout'=>$this['config']->get('top-a'))); ?></div>
		</section>
		<?php endif; ?>
		
		<?php if ($this['modules']->count('top-b')) : ?>
		<section id="top-b" class="grid-block">
			<div class="wrapper clearfix"><?php echo $this['modules']->render('top-b', array('layout'=>$this['config']->get('top-b'))); ?></div>
		</section>
		<?php endif; ?>
		
		<?php if ($this['modules']->count('innertop + innerbottom + sidebar-a + sidebar-b') || $this['config']->get('system_output')) : ?>
		<div id="main" class="grid-block">
			<div class="wrapper clearfix">
				<div id="maininner" class="grid-box">

					<?php if ($this['modules']->count('innertop')) : ?>
					<section id="innertop" class="grid-block"><?php echo $this['modules']->render('innertop', array('layout'=>$this['config']->get('innertop'))); ?></section>
					<?php endif; ?>

					<?php if ($this['modules']->count('breadcrumbs')) : ?>
					<section id="breadcrumbs"><?php echo $this['modules']->render('breadcrumbs'); ?></section>
					<?php endif; ?>

					<?php if ($this['config']->get('system_output')) : ?>
					<section id="content" class="grid-block"><?php echo $this['template']->render('content'); ?></section>
					<?php endif; ?>

					<?php if ($this['modules']->count('innerbottom')) : ?>
					<section id="innerbottom" class="grid-block"><?php echo $this['modules']->render('innerbottom', array('layout'=>$this['config']->get('innerbottom'))); ?></section>
					<?php endif; ?>

				</div>
				<!-- maininner end -->
				
				<?php if ($this['modules']->count('sidebar-a')) : ?>
				<aside id="sidebar-a" class="grid-box"><?php echo $this['modules']->render('sidebar-a', array('layout'=>'stack')); ?></aside>
				<?php endif; ?>
				
				<?php if ($this['modules']->count('sidebar-b')) : ?>
				<aside id="sidebar-b" class="grid-box"><?php echo $this['modules']->render('sidebar-b', array('layout'=>'stack')); ?></aside>
				<?php endif; ?>
			</div>

		</div>
		<?php endif; ?>
		<!-- main end -->

		<?php if ($this['modules']->count('bottom-a')) : ?>
		<section id="bottom-a" class="grid-block">
			<div class="wrapper clearfix"><?php echo $this['modules']->render('bottom-a', array('layout'=>$this['config']->get('bottom-a'))); ?></div>
		</section>
		<?php endif; ?>
		
		<?php if ($this['modules']->count('bottom-b')) : ?>
		<section id="bottom-b" class="grid-block">
			<div class="wrapper clearfix"><?php echo $this['modules']->render('bottom-b', array('layout'=>$this['config']->get('bottom-b'))); ?></div>
		</section>
		<?php endif; ?>
		
		<?php if ($this['modules']->count('footer + debug') || $this['config']->get('warp_branding') || $this['config']->get('totop_scroller')) : ?>
		<footer id="footer">
			<div class="wrapper clearfix">

				<?php if ($this['config']->get('totop_scroller')) : ?>
				<a id="totop-scroller" href="#page"></a>
				<?php endif; ?>

				<?php
					echo $this['modules']->render('footer');
					$this->output('warp_branding');
					echo $this['modules']->render('debug');
				?>

			</div>
		</footer>
		<?php endif; ?>

	</div>
	
	<?php echo $this->render('footer'); ?>

	<script>
		//Truncate teaser text in zoo blog category layout
		(function($){
		var teaser = '.yoo-zoo .rightcontent .pos-content';
		$(teaser).each(function(index){
			var shorter = $(this).text().substring(0,200);
			$(this).replaceWith('<p>' + shorter + ' [...]</p>');
			});
		})(jQuery);
	</script>

	<script type="text/javascript">
		//Add current date to newsletter form submissions
		(function($){
			var currentdate = new Date();
			var date = (currentdate.getMonth()+1) + "/" + currentdate.getDate() + "/" + currentdate.getFullYear();
			$('form input#SubscribeDate').attr('value', date);
		})(jQuery);
	</script>

	<script>
		// Set placeholder in email subscribe fields for IE. Refers to js/jquery.placeholder.js
	   (function($) {
	    $('input').placeholder();
	   })(jQuery);;
  	</script>
</body>
</html>