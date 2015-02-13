<?php echo $this->Form->create('Role', array('url' => array('controller' => 'roles', 'action' => 'add'))); ?>
<?php echo $this->Form->input('name', array('label' => 'Role Name')); ?>
<?php echo $this->Form->end('Submit'); ?>