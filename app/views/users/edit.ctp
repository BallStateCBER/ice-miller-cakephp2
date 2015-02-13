<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'edit')));?>
<?php echo $this->Form->input('name', array('label' => 'Name', 'between' => '<div class="footnote">First and last, please</div>')); ?>
<?php echo $this->Form->input('email', array('label' => 'Email')); ?>
<?php echo $this->Form->input('sex', array('label' => 'Sex', 'options' => array('', 'm' => 'Male', 'f' => 'Female'))); ?>
<?php echo $this->Form->input('bio', array('label' => 'Bio', 'style' => 'height: 300px; width: 400px;', 'between' => '<div class="footnote">ENTER double-spaces. SHIFT + ENTER single-spaces.</div>')); ?>
<fieldset class="change_password">
	<legend>Change Password</legend>
	<?php echo $this->Form->input('new_password', array('label' => 'Password', 'type' => 'password', 'autocomplete' => 'off')); ?>
	<?php echo $this->Form->input('confirm_password', array('type' => 'password', 'autocomplete' => 'off')); ?>
</fieldset>
<?php echo $this->Form->input('id', array('type'=>'hidden')); ?> 
<?php echo $this->Form->end('Submit'); ?>
<?php echo $this->element('tinymce_init'); ?>