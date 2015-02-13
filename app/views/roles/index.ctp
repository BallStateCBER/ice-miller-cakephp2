<h1 class="page_title">
	Roles / Permissions
</h1>

<table class="permissions">
	<thead>
		<tr>
			<th>Role</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tfoot></tfoot>
	<tbody>
		<?php foreach ($roles as $role): ?>
			<tr>
				<th>
					<?php echo $role['Role']['name']; ?>
				</th>
				<td>
					<a href="/roles/edit/<?php echo $role['Role']['id']; ?>">Edit Permissions</a>
					|
					<a href="/roles/delete/<?php echo $role['Role']['id']; ?>">Delete</a>
				</td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<th colspan="2">
				<?php echo $this->Form->create('Role', array('url' => array('controller' => 'roles', 'action' => 'add'))); ?>
				<?php echo $this->Form->input('name', array('label' => 'New Role: ', 'div' => false)); ?>
				<?php echo $this->Form->end(array('label' => 'Add', 'div' => false)); ?>
			</th>
		</tr>
	</tbody>
</table>


<table class="permissions">
	<thead>
		<tr>
			<th colspan="2">Permission</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tfoot></tfoot>
	<tbody>
		<?php foreach ($permissions as $p): ?>
			<tr>
				<th class="description">
					<?php echo $p['Permission']['description']; ?>
				</th>
				<td class="name">		
					(<?php echo $p['Permission']['name']; ?>)
				</td>
				<td>
					<a href="/permissions/delete/<?php echo $p['Permission']['id']; ?>">Delete</a>
				</td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<th colspan="2">
				<?php echo $this->Html->link(
					'Add a new permission',
					array(
						'controller' => 'permissions', 'action' => 'add'
					)
				); ?>
				<?php /* echo $this->Form->create('Permission', array('url' => array('controller' => 'permissions', 'action' => 'add'))); ?>
				<?php echo $this->Form->input('name', array('label' => 'New Permission: ', 'div' => false)); ?>
				<?php echo $this->Form->end(array('label' => 'Add', 'div' => false)); */ ?>
			</th>
		</tr>
	</tbody>
</table>

<table class="permissions">
	<thead>
		<tr>
			<th>User</th>
			<th></th>
		</tr>
	</thead>
	<tfoot></tfoot>
	<tbody>
		<tr>
			<td>
				<?php echo $this->Form->create('Role', array('url' => array('controller' => 'roles', 'action' => 'user'))); ?>
				<?php
					$user_options = array();
					foreach ($users as $user) {
						$user_options[$user['User']['id']] = $user['User']['name'];
					}
				?>
				<?php echo $this->Form->select('user_id', $user_options, null, array('empty' => '')); ?>
			</td>
			<td>
				<?php echo $this->Form->end(array('label' => 'Edit Roles', 'div' => false)); ?>
			</td>
		</tr>
	</tbody>
</table>