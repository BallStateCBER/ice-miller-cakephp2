<?php
	header('Content-type: text/html; charset=UTF-8');
	$logged_in = $this->Session->check('Auth.User.id');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>
			<?php if (isset($title_for_layout) && $title_for_layout): ?>
				<?php echo $title_for_layout; ?> -
			<?php endif; ?>
			Ice Miller Articles
		</title>
		<?php echo $this->Html->charset('utf-8'); ?>
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<meta name="language" content="en" />

		<meta property="og:image" content="http://icemiller.cberdata.org/img/logo_for_facebook.jpg" />
		<meta property="og:site_name" content="Ice Miller Articles" />
		<meta property="fb:admins" content="20721049" /><!-- Graham -->
		<meta property="og:locality" content="Muncie" />
		<meta property="og:region" content="IN" />
		<meta property="og:country-name" content="USA" />
		<meta property="og:email" content="cber@bsu.edu" />
		<meta property="og:description" content="" />
		<?php if (isset($facebook_og_meta_tags)): ?>
			<?php foreach ($facebook_og_meta_tags as $tag_name => $tag_val): ?>
				<meta property="og:<?php echo $tag_name; ?>" content="<?php echo htmlentities($tag_val, ENT_COMPAT); ?>" />
			<?php endforeach; ?>
		<?php endif; ?>

		<link rel="icon" type="image/png" href="/img/favicon.png" />
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/fonts/fonts-min.css" />
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/reset/reset-min.css" />
		<link rel="stylesheet" type="text/css" href="/css/main.css" />
		<link rel="stylesheet" type="text/css" href="/shadowbox/shadowbox.css" />
		<?php
			echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/prototype/1.7.0.0/prototype.js');
			echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/scriptaculous/1.8.3/scriptaculous.js');
			echo $this->Html->script('tiny_mce/tiny_mce.js');
			echo $this->Html->script('main.js');
		?>
		<?php echo $scripts_for_layout ?>
	</head>
	<body>
		<div id="flash_messages">
			<?php echo $this->Flash->show(); ?>
			<noscript>
				<ul>
					<li class="error">
						<p>
							<img src="/img/icons/cross-circle.png" />
							You do not have Javascript enabled! Many features of this site will not work properly for you.
						</p>
					</li>
				</ul>
			</noscript>
		</div>
		<div id="header_1">
			<div class="inner">
				<a href="/">
					<img src="/img/logo.jpg" />
				</a>
				<div class="buttons">
					<a href="#"><img src="/img/icons/facebook.png" /></a>
					<a href="#"><img src="/img/icons/twitter.png" /></a>
					<a href="/articles/index.rss" title="Subscribe to RSS feed"><img src="/img/icons/feed.png" /></a>
				</div>
				<div class="nav">
					<ul>
						<li><a href="/">Home</a></li>
						<li><a href="/about">About Us</a></li>
						<li><a href="/contact">Contact</a></li>
						<?php if ($logged_in): ?>
							<li><a href="/logout">Log out</a></li>
						<?php else: ?>
							<li><a href="/register">Register</a></li>
							<li><a href="/login">Log in</a></li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</div>
		<div id="header_2">
			<div class="inner">
				<div class="left">
					<h1>
						Ice Miller Articles
					</h1>
					<p>
						Examining the economy and economic development through the prism of a professional economic developer and academic economist
					</p>
				</div>
				<div class="right">
					<?php
						echo $this->Form->create("Article", array('controller' => 'articles', 'action' => 'search'));
						echo $this->Form->input("query", array('label' => false, 'div' => false));
						echo $this->Form->end(array('label' => 'Search', 'div' => false));
					?>
				</div>
				<br class="clear" />
			</div>
		</div>
		<div id="main">
			<div class="inner">
				<div id="content">
					<?php if (isset($breadcrumbs)): ?>
						<div id="breadcrumbs">
							<?php echo $breadcrumbs ?>
						</div>
					<?php endif; ?>
					<?php echo $content_for_layout ?>
				</div>
				<div id="sidebar">
					<?php if ($logged_in): ?>
						<div>
						 	<h2>User Menu</h2>
						 	<div>
								<?php echo $this->element('user_menu'); ?>
							</div>
						</div>
					<?php endif; ?>
					<div>
						<h2>Authors</h2>
						<div>
							<?php echo $this->element('authors', array('cache' => '+1 hour')); ?>
						</div>
					</div>
					<div>
						<h2><a href="/tags">Top Tags</a></h2>
						<div>
							<?php echo $this->element('tags/cloud', array(
								//'cache' => '+1 hour',
								'min_font_percent' => 80,
								'max_font_percent' => 150,
								'tag_limit' => 10,
								'tag_cloud' => null
							)); ?>
						</div>
					</div>
					<div>
						<h2>Facebook</h2>
						<div style="padding: 0;">
							<iframe src="http://www.facebook.com/plugins/activity.php?site=icemiller.cberdata.org&amp;width=298&amp;height=200&amp;header=false&amp;colorscheme=light&amp;font=verdana&amp;border_color=white&amp;recommendations=true" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:298px; height:200px;" allowTransparency="true"></iframe>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="footer">
			<div class="inner">

			</div>
		</div>
		<?php if (Configure::read('debug') != 0): ?>
			<div>
				<a href="#sql_dump" onclick="$('sql_dump').toggle()">Show SQL dump</a>
			</div>
			<div id="sql_dump" style="display: none;">
				<a name="sql_dump"></a>
				<?php echo $this->element('sql_dump'); ?>
			</div>
		<?php endif; ?>
		<?php echo $this->Js->writeBuffer(); ?>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-32998887-11', 'cberdata.org');
			ga('send', 'pageview');
		</script>
	</body>
</html>