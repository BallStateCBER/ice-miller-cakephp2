<?php
	$password_error = isset($password_incorrect) ? '<div class="error-message">Incorrect password entered</div>' : '';
	$email_error = isset($email_not_found) ? '<div class="error-message">Email address not found</div>' : '';
?>
<h1 class="page_title">
	Log in
</h1>
<?php 
	echo $this->Form->create('User', array('action' => 'login'));
	echo $this->Form->input('email', array('after' => $email_error));
	echo $this->Form->input('password', array('after' => $password_error));
	echo $this->Form->input('auto_login', array(
		'type' => 'checkbox', 
		'label' => array('text' => ' Log me in automatically', 'style' => 'display: inline;'),
		'checked' => true
	)); 
	if (isset($_GET['back'])) { // To where you once belonged
		echo $this->Form->input('back', array('type' => 'hidden', 'value' => $_GET['back']));	
	}
	echo $this->Form->end('Login');
?>