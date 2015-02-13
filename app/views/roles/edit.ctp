<h1 class="page_title">
	Edit Role: <em><?php echo $role['Role']['name']; ?></em>
</h1>

<?php echo $this->Form->create('Role', array('url' => array('controller' => 'roles', 'action' => 'edit'))); ?>
<?php echo $this->Form->input('Permission', array(
	'label' => 'Permissions', 
	'type' => 'select',
	'multiple' => 'checkbox',
	'options' => $permissions,
)); ?>
<?php echo $this->Form->end('Submit'); ?>