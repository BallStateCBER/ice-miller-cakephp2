<?php
$article_title = $article['Article']['title'];
$article_id = $article['Article']['id'];
$article_slug = $article['Article']['slug'];
$published_date = $article['Article']['published_date'];
$article_body = $article['Article']['body'];
$article_body = $text->autoLink($article_body);
$user_name = $article['User']['name'];
$user_id = $article['User']['id'];
$is_published = $article['Article']['is_published'];
$comments_count = count($article['Comment']);
$comments_enabled = $article['Article']['comments_enabled'];
$comments_label = $comments_count.' comment'.($comments_count == 1 ? '' : 's');
$tags = $article['Tag'];
$tag_count = count($article['Tag']);
$facebook_like_url = "http%3A%2F%2Ficemiller.cberdata.org/article/$article_id";
$is_sticky = $article['Article']['sticky'];
?>

<div class="article <?php if ($is_sticky): ?>sticky<?php endif; ?>">
	<div class="header">
		<?php echo $this->element('articles/controls', compact('article')); ?>
		<h2 class="title">
			<?php if (! $is_published): ?>
				<em>Draft:</em>
			<?php endif; ?>
			<?php echo $this->Html->link(
				$article_title, 
				array(
					'controller' => 'articles',
					'action' => 'view',
					'slug' => $article_slug
				)
			); ?>
		</h2>
		<div class="info">
			<?php if ($user_name): ?>
				<?php echo $this->Html->link(
					$user_name,
					array(
						'controller' => 'users',
						'action' => 'view',
						'id' => $user_id
					),
					array(
						'escape' => false,
						'title' => 'View profile'
					)
				); ?>
				&nbsp;&nbsp;|&nbsp;&nbsp;				
			<?php endif; ?>
			<?php echo $this->Html->link(
				date('F j, Y', $time->fromString($published_date)),
				array(
					'controller' => 'articles', 
					'action' => 'dated', 
					'year' => date('Y', $time->fromString($published_date)), 
				),
				array('title' => 'View more articles from '.substr($published_date, 0, 4))
			); ?>
			<?php if ($comments_enabled): ?>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<?php echo $this->Html->link($comments_label, array(
					'controller' => 'articles', 
					'action' => 'view', 
					'id' => $article_id,
					'#' => 'comments'
				)); ?>
			<?php endif; ?>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo $facebook_like_url; ?>&amp;layout=button_count&amp;show_faces=false&amp;width=450&amp;action=like&amp;font=verdana&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:auto; height:21px; vertical-align: bottom;" allowTransparency="true"></iframe>
		</div>
	</div>
	<div class="body">
		<?php 
			echo $this->Text->truncate(
				$article_body, 
				300, 
				array(
					'ending' => "...", 
					'exact' => false,
					'html' => true
				)
			);
		?>
		<?php echo $this->Html->link(
			'read more',
			array(
				'controller' => 'articles',
				'action' => 'view',
				'id' => $article_id
			)
		); ?>
	</div>
	<div class="footer">
		<?php if ($tag_count): ?>
			<div class="tags">
				<?php foreach ($tags as $key => $tag): ?>
					<span class="tag">
						<?php echo $this->Html->link(
							$tag['name'],
							array(
								'controller' => 'articles',
								'action' => 'tagged',
								'id' => $tag['id']
							)
						); ?>
						<?php echo ($key < $tag_count - 1) ? '|' : '' ?>
					</span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>