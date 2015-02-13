<h1 class="page_title">
	Articles by 
	<?php echo $this->Html->link(
		$user_name,
		array(
			'controller' => 'users',
			'action' => 'view',
			'id' => $user_id
		)
	); ?>
</h1>
<div class="articles_list">
	<?php echo $this->element('articles/list_by_author', compact('articles')); ?>
</div>