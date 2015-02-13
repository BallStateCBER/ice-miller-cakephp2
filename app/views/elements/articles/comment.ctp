<?php
	$comment_id = $comment['Comment']['id'];
	if (! isset($shaded)) $shaded = false;
	$has_children = isset($comment['children']) && ! empty($comment['children']);
?>
<div class="comment<?php if ($shaded): ?> shaded<?php endif; ?><?php if ($root): ?> root<?php endif; ?>">
	<a name="comment<?php echo $comment_id; ?>"></a>
	<div class="info">
		<?php echo $this->Html->link(
			$comment['User']['name'],
			array('controller' => 'users', 'action' => 'view', 'id' => $comment['User']['id'])
		); ?>
		<?php if ($root): ?>
			said on
		<?php else: ?>
			replied on
		<?php endif; ?>
		<?php
			$timestamp = $this->Time->fromString($comment['Comment']['created']); 
			echo $this->Html->link(
				date('F j', $timestamp).'<sup>'.date('S', $timestamp).'</sup>'.date(', \'y | g:ia', $timestamp),
				array('controller' => 'articles', 'action' => 'view', 'id' => $article_id, '#' => "comment$comment_id"),
				array('escape' => false)
			);
		?>
	</div>
	
	<div class="action_buttons">
		<?php if ($this->Permission->permitted('comments', 'add')): ?>
			<?php 
				echo $this->Js->link(
					'Reply',
					array(
						'controller' => 'comments', 
						'action' => 'add', 
						'parent_id' => $comment_id,
						'article_id' => $article_id
					),
					array(
						'update' => 'cr'.$comment_id, 
						'before' => "$('crb$comment_id').addClassName('loading')",
						'complete' => "Effect.SlideDown('cr$comment_id', {
							duration: 0.3, 
							beforeStart: function() {
								$('cra$comment_id').show();
								$('crb$comment_id').removeClassName('loading');
								$('crb$comment_id').hide();
								$('crbc$comment_id').show();
							}
						});",
						'error' => 'alert(\'There was an error. Please refresh the page and try again.\')',
						'escape' => false,
						'id' => "crb$comment_id",
						'evalScripts' => true
					)
				);
			?>
			<span class="fake_link" id="crbc<?php echo $comment_id; ?>" style="display: none;">Cancel Reply</span>
			<script type="text/javascript">
				$('crbc<?php echo $comment_id; ?>').observe('click', function (event) {
					var form_area = $('cr<?php echo $comment_id; ?>');
					Effect.SlideUp(form_area, {
						duration: 0.3,
						afterFinish: function() {
							form_area.update();
						}
					});
					$('crbc<?php echo $comment_id; ?>').hide();
					$('crb<?php echo $comment_id; ?>').show();
					<?php if (! $has_children): ?>
						$('cra<?php echo $comment_id; ?>').hide();
					<?php endif; ?>
				});
			</script>
		<?php endif; ?>
		<?php if ($this->Permission->permitted('comments', 'delete')): ?>
			<?php echo $this->Html->link(
				'Delete',
				array(
					'controller' => 'comments',
					'action' => 'delete',
					'comment_id' => $comment_id,
					'article_id' => $article_id
				),
				array(),
				$has_children ? 
					'Are you sure you want to delete this comment? Because it has replies, its content will just be changed to "(this comment has been deleted)".' : 
					'Are you sure you want to delete this comment?'
			); ?>
		<?php endif; ?>
	</div>
	
	<div class="body">
		<p>
			<?php echo $this->Text->autoLink(
				nl2br(
					Sanitize::html($comment['Comment']['body'])
				)
			); ?>
		</p>
	</div>
	
	<div class="children">
		<?php echo $this->Html->image(
			'/img/icons/arrow-turn-000-left_gray.png', 
			array(
				'alt' => 'Reply', 
				'class' => 'reply',
				'id' => "cra$comment_id",
				'style' => (! $has_children) ? 'display: none;' : ''
			)
		); ?>
		
		<div id="cr<?php echo $comment_id; ?>"></div>
		
		<?php if ($has_children): ?>
			<?php foreach ($comment['children'] as $child): ?>
				<?php echo $this->element(
					'articles/comment', 
					array(
						'comment' => $child, 
						'shaded' => false, 
						'root' => false,
						'article_id' => $article_id
					)
				); ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	
</div>
<br class="clear" />