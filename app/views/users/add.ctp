<h1 class="page_title">Register New User</h1>
<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'register')));?>
<?php echo $this->Form->input('name', array('label' => 'Name', 'between' => '<div class="footnote">First and last, please</div>')); ?>
<?php echo $this->Form->input('email', array('label' => 'Email')); ?>
<?php echo $this->Form->input('sex', array('label' => 'Sex', 'options' => array('', 'm' => 'Male', 'f' => 'Female'))); ?>
<?php echo $this->Form->input('bio', array('label' => 'Bio', 'style' => 'height: 300px; width: 400px;', 'between' => '<div class="footnote">ENTER double-spaces. SHIFT + ENTER single-spaces.</div>')); ?>
<?php echo $this->Form->input('new_password', array('label' => 'Password', 'type' => 'password', 'value' => '')); ?>
<?php echo $this->Form->input('confirm_password', array('type' => 'password', 'value' => '')); ?>
<?php echo $this->element('recaptcha_input'); ?>
<?php echo $this->Form->end('Add'); ?>
<?php echo $this->element('tinymce_init'); ?>