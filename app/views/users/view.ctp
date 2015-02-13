<?php
	$user_id = $user['User']['id'];
	$name = $user['User']['name'];
	$email = $user['User']['email'];
	$bio = $user['User']['bio'];
	$picture = $user['User']['picture'];
	$may_edit = $this->Permission->permitted('users', 'edit');
	$may_delete = $this->Permission->permitted('users', 'delete');
?>

<div class="user">
	<?php if ($picture): ?>
		<div class="picture">
			<img src="/img/users/<?php echo $picture ?>" />
		</div>
	<?php endif; ?> 
	<div class="controls">
		<?php if ($own_profile): ?>
			<?php if ($may_edit): ?>
				<?php echo $this->Html->link(
					$this->Html->image('/img/icons/pencil.png').' Edit',
					array('controller' => 'users', 'action' => 'my_account'),
					array('escape' => false)
				); ?>
			<?php endif; ?>
		<?php else: ?>
			<?php if ($may_edit): ?>
				<?php echo $this->Html->link(
					$this->Html->image('/img/icons/pencil.png').' Edit',
					array('controller' => 'users', 'action' => 'edit', $user['User']['id']),
					array('escape' => false)
				); ?>
			<?php endif; ?>
			<?php if ($may_delete): ?>
				&nbsp;
				<?php echo $this->Html->link(
					$this->Html->image('/img/icons/cross.png').' Delete',
					array('controller' => 'users', 'action' => 'delete', $user['User']['id']),
					array('escape' => false),
					'Are you sure that you want to delete this user?'
				); ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<h1 class="page_title">
		<?php echo $name; ?>
	</h1>
	<?php if ($email): ?>
		<p class="email">
			<a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
		</p>
	<?php endif; ?>
	<div class="bio">
		<?php echo $bio; ?>
	</div>
	<?php if (! empty($articles)): ?>
		<div class="articles_list">
			<h2>Articles</h2>
			<?php echo $this->element('articles/list_by_author', compact('articles')); ?>
			<?php 
				if ($article_limit < $article_count) {
					$more_articles = $article_count - $article_limit;
					echo $this->Html->link(
						$more_articles.' more article'.($more_articles > 1 ? 's' : ''),
						array(
							'controller' => 'articles',
							'action' => 'by',
							$user_id 
						)
					);
				} 
			?>
		</div>
	<?php endif; ?>
</div>