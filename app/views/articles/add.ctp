<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<?php echo $this->Form->create('Article', array('url' => array('controller' => 'articles', 'action' => 'add'))); ?>
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
	<?php echo $this->Form->input('comments_enabled', array('checked' => true, 'label' => 'Allow comments')); ?>
	<?php echo $this->Form->input('is_published', array('checked' => true, 'label' => 'Publish <span id="delayed_publishing_date"></span>')); ?>
	<?php echo $this->Form->input('sticky', array('label' => 'Make sticky')); ?>
</div>
<?php echo $this->Form->end('Submit'); ?>

<script type="text/javascript">
	/* This is not buffered so it can run before tinymce_init */
	var form_changed = false;
</script>

<?php echo $this->element('tinymce_init'); ?>

<?php $this->Js->buffer("
	toggleDelayPublishing();
	$('ArticlePublishedDateMonth').observe('change', function() {toggleDelayPublishing()});
	$('ArticlePublishedDateDay').observe('change', function() {toggleDelayPublishing()});
	$('ArticlePublishedDateYear').observe('change', function() {toggleDelayPublishing()});
	window.onbeforeunload = function() {
		if (form_changed) {
			return 'Are you sure you want to leave this page? Your article has not been saved.';
		}
	}
	$('ArticleAddForm').observe('submit', function() {
		window.onbeforeunload = null;
	});
"); ?>
