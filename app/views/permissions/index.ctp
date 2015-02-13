<style>
	table.permissions {}
	table.permissions tbody {border: 1px solid black;}
	table.permissions thead th {border-bottom: 1px solid black; font-size: 12px; text-align: center; text-transform: uppercase;}
	table.permissions tbody th {border-bottom: 1px dotted black; padding: 5px 10px;}
	table.permissions tbody td {border-bottom: 1px dotted black; padding: 5px 10px;}
</style>
<div style="float: right;">
Users | Permissions
</div>
<h1 class="page_title">
	Permissions
</h1>
<table class="permissions">
	<thead>
		<tr>
			<th>Permission</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tfoot></tfoot>
	<tbody>
		<tr>
			<th>
				<a href="/permissions/add">New permission</a>
			</th>
			<td>
				&nbsp;
			</td>
		</tr>
		<?php foreach ($permissions as $permission): ?>
			<tr>
				<th>
					<?php echo $permission['Permission']['name']; ?>
				</th>
				<td>
					<a href="/permissions/edit/<?php echo $permission['Permission']['id']; ?>">Edit</a>
					|
					<a href="/permissions/delete/<?php echo $permission['Permission']['id']; ?>">Delete</a>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>