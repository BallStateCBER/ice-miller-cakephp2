<?php
$article_title = $article['Article']['title'];
$article_id = $article['Article']['id'];
$published_date = $article['Article']['published_date'];
$article_body = $article['Article']['body'];

/* Autolinking was turned off for article 419 "The most popular person in Valparaiso is..." and
 * future posts to allow images to be posted without autoLink() screwing up the <img> tags.
 */
if ($article_id < 419) {
	$article_body = $text->autoLink($article_body);
}

$user_name = $article['User']['name'];
$author_id = $article['User']['id'];
$is_published = $article['Article']['is_published'];
$tags = $article['Tag'];
$tag_count = count($article['Tag']);
$comments_enabled = $article['Article']['comments_enabled'];
$comments_label = $comments_count.' comment'.($comments_count == 1 ? '' : 's');
$facebook_like_url = "http%3A%2F%2Ficemiller.cberdata.org/article/$article_id";
?>

<div class="article">
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
					'slug' => $article['Article']['slug']
				)
			); ?>
		</h2>
		<div class="info">
			<?php if ($user_name): ?>
				<a href="/user/<?php echo $author_id; ?>" title="View profile"><?php echo $user_name; ?></a>
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
				<a href="#comments">
					<?php echo $comments_label; ?>
				</a>
			<?php endif; ?>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo $facebook_like_url; ?>&amp;layout=button_count&amp;show_faces=false&amp;width=450&amp;action=like&amp;font=verdana&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:auto; height:21px; vertical-align: bottom;" allowTransparency="true"></iframe>
		</div>
	</div>
	<div class="body">
		<?php echo $article_body; ?>
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

	<?php if ($comments_enabled): ?>
		<div class="comments">
			<?php if ($this->Session->read('Auth.User.id')): ?>
				<div class="new_comment">
					<?php
						echo $this->Ajax->link(
							$this->Html->image('/img/icons/balloon-left.png').' <span>Post a comment</span>',
							array(
								'controller' => 'comments',
								'action' => 'add',
								'parent_id' => null,
								'article_id' => $article_id
							),
							array(
								'update' => 'cr_root',
								'before' => "$('crb').addClassName('loading')",
								'loaded' => "Effect.SlideDown('cr', {
									duration: 5.0,
									beforeStart: function() {
										$('crb').removeClassName('loading');
										$('crb').hide();
										$('crbc').show();
									}
								});",
								'escape' => false
							)
						);
					?>
				</div>
				<h2>Comments</h2>
				<div id="cr_root"></div>
			<?php else: ?>
				<h2>Comments</h2>
				<p class="notification_message">
					Please <a href="/login?back=<?php echo $this->here; ?>">log in</a> to leave a comment.
				</p>
			<?php endif; ?>
			<a name="comments"></a>
			<?php if ($comments_count == 0): ?>
			<?php else: ?>
				<?php foreach ($comments as $key => $comment): ?>
					<?php echo $this->element(
						'articles/comment',
						array(
							'comment' => $comment,
							'shaded' => $key % 2 == 1,
							'root' => true,
							'article_id' => $article_id
						)
					); ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
