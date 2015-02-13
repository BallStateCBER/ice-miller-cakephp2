<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<?php if (isset($success)): ?>
	<p>
		Message sent. Thank you for your feedback!
	</p>
<?php elseif (isset($error)): ?>
	<p>
		<?php echo $error; ?>
	</p>
<?php else: ?>
	<?php echo $javascript->link('http://api.recaptcha.net/js/recaptcha_ajax.js'); ?>
	<?php echo $this->Form->create('message', array('url' => array('controller' => 'pages', 'action' => 'contact'))); ?>
	<?php echo $this->Form->input('category', array('label' => 'Category', 'options' => $categories)); ?>
	<?php echo $this->Form->input('name'); ?>
	<?php echo $this->Form->input('email'); ?>
	<?php echo $this->Form->input('body', array('label' => 'Message', 'type' => 'textarea', 'style' => 'width: 400px; height: 200px;')); ?>
	<?php echo $this->element('recaptcha_input'); ?>
	<?php echo $this->Form->end('Send'); ?>
<?php endif; ?>