<?php
	$article_id = 		$article['Article']['id'];
	$is_published = 	$article['Article']['is_published'];
	$may_edit = 		$this->Permission->permitted('articles', 'edit');
	$may_delete = 		$this->Permission->permitted('articles', 'delete');
	$may_unpublish = 	$this->Permission->permitted('articles', 'unpublish');
	$may_publish = 		$this->Permission->permitted('articles', 'publish');
	$user_id =			$this->Session->read('Auth.User.id');
	$is_author = 		$user_id == $article['Article']['user_id'];
?>
<?php if ($user_id): ?>
	<ul class="controls">
		<?php if ($may_edit || $is_author): ?>
			<li>
				<?php echo $this->Html->link(
					$this->Html->image('/img/icons/pencil.png').' <span>Edit</span>',
					array('controller' => 'articles', 'action' => 'edit', $article_id),
					array('escape' => false)
				); ?>
			</li>
		<?php endif; ?>
		<?php if ($may_delete || $is_author): ?>
			<li>
				<?php echo $this->Html->link(
					$this->Html->image('/img/icons/cross.png').' <span>Delete</span>',
					array('controller' => 'articles', 'action' => 'delete', $article_id),
					array('escape' => false),
					'Are you sure that you want to delete this article?'
				); ?>
			</li>
		<?php endif; ?>
		<?php if ($is_published && ($may_unpublish || $is_author)): ?>
			<li>
				<?php echo $this->Html->link(
					$this->Html->image('/img/icons/newspaper--minus.png').' <span>Move to Drafts</span>',
					array('controller' => 'articles', 'action' => 'unpublish', $article_id),
					array('escape' => false)
				); ?>
			</li>
		<?php elseif (! $is_published && ($may_publish || $is_author)): ?>
			<li>
				<?php echo $this->Html->link(
					$this->Html->image('/img/icons/newspaper--plus.png').' <span>Publish</span>',
					array('controller' => 'articles', 'action' => 'publish', $article_id),
					array('escape' => false)
				); ?>
			</li>
		<?php endif; ?>
	</ul>
<?php endif; ?>