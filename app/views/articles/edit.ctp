<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>
<?php echo $this->Form->create('Article', array('url' => array('controller' => 'articles', 'action' => 'edit'))); ?>
<?php echo $this->Form->input('title', array('label' => 'Title', 'style' => 'width: 400px;')); ?>
<?php echo $this->Form->input('published_date', array(
	'label' => 'Published Date',
	'dateFormat' => 'MDY',
	'timeFormat' => 'none',
	'minYear' => date('Y') - 5,
	'maxYear' => date('Y')
)); ?>
<?php echo $this->Form->input('body', array('label' => 'Body', 'style' => 'height: 300px; width: 100%;', 'between' => '<div class="footnote">ENTER double-spaces. SHIFT + ENTER single-spaces.</div>')); ?>
<?php echo $this->element('tags/tag_editing', compact('available_tags', 'selected_tags')); ?>
<div class="options">
	<?php echo $this->Form->input('comments_enabled', array('label' => 'Allow comments')); ?>
	<?php echo $this->Form->input('is_published', array('label' => 'Publish')); ?>
	<?php echo $this->Form->input('sticky', array('label' => 'Make sticky')); ?>
</div>
<?php echo $this->Form->end('Submit'); ?>
<?php echo $this->element('tinymce_init'); ?>