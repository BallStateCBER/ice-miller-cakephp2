<h1 class="page_title">Register New User</h1>
<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'register')));?>
<?php echo $this->Form->input('name', array('label' => 'Name', 'between' => '<div class="footnote">First and last, please</div>')); ?>
<?php echo $this->Form->input('email', array('label' => 'Email', 'between' => '<div class="footnote">This is what you will use to log in.</div>')); ?>
<?php echo $this->Form->input('sex', array('label' => 'Sex', 'options' => array('', 'm' => 'Male', 'f' => 'Female'), 'between' => '<div class="footnote">Optional; Allows us to use the correct pronoun when referring to you.</div>')); ?>
<?php echo $this->Form->input('bio', array('label' => 'Bio', 'style' => 'height: 300px; width: 400px;')); ?>
<?php echo $this->Form->input('new_password', array('label' => 'Password', 'type' => 'password', 'value' => '')); ?>
<?php echo $this->Form->input('confirm_password', array('type' => 'password', 'value' => '')); ?>
<?php echo $this->element('recaptcha_input'); ?>
<?php echo $this->Form->end('Register'); ?>