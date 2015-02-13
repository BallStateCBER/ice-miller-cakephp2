<h1 class="page_title">
	<?php echo $user_name; ?>'s Roles
</h1>
<style>
	#RoleUserForm .checkbox label {display: inline; margin-left: 10px;}
</style>

<?php echo $this->Form->create('Role', array('url' => array('controller' => 'roles', 'action' => 'user', $user_id))); ?>

<?php if (! empty($user_roles)): ?>
	<?php echo $this->Form->input('remove_these_roles', array(
		'type' => 'select',
		'multiple' => 'checkbox',
		'options' => $user_roles
	));	?>
<?php endif; ?>
<?php if (! empty($roles_not_assigned)): ?>
	<?php echo $this->Form->input('add_this_role', array(
		'type' => 'select',
		'options' => $roles_not_assigned,
		'empty' => ''
	)); ?>
<?php endif; ?>
<?php echo $this->Form->end(array('label' => 'Submit', 'div' => false)); ?>