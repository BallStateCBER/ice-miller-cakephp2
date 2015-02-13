<?php echo $this->Form->create('Permission', array('url' => array('controller' => 'permissions', 'action' => 'add'))); ?>
<?php echo $this->Form->input('name', array('label' => 'Permission (controller:action, controller:*, or *)')); ?>
<?php echo $this->Form->input('description', array('label' => 'Description (Category: Description of action)')); ?>
<?php echo $this->Form->end('Submit'); ?>