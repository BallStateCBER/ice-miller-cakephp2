<?php
	$is_ajax = isset($this->params['isAjax']) && $this->params['isAjax'];
?>

<?php if (! $is_ajax): ?>
	<h1 class="page_title">
		<?php echo $title_for_layout; ?>
	</h1>
<?php endif; ?>

<?php echo $this->Form->create('Comment', array('url' => array('controller' => 'comments', 'action' => 'add'))); ?>

<?php echo $this->Form->input('article_id', array('type' => 'hidden', 'value' => $article_id)); ?>

<?php if (isset($parent_id)): ?>
	<?php echo $this->Form->input('parent_id', array('type' => 'hidden', 'value' => $parent_id)); ?>
<?php endif; ?>

<?php echo $this->Form->input('body', array('label' => false, 'style' => 'width: 100%;', 'type' => 'textarea')); ?>

<?php echo $this->element('recaptcha_input'); ?>

<?php if ($is_ajax): ?>
	<?php echo $this->Js->submit(
		'Post Comment', 
		array(
			'url' => array(
				'controller' => 'comments', 
				'action' => 'add',
				'parent_id' => $parent_id,
				'article_id' => $article_id,
				'ajax' => true
			), 
			'update' => "#cr$parent_id",
			'buffer' => false,
			'complete' => "$('crbc$parent_id').hide();",
			'evalScripts' => true
		)
	); ?>
	<?php echo $this->Form->end(); ?>
<?php else: ?>
	<?php echo $this->Form->end('Post Comment'); ?>
<?php endif; ?>
