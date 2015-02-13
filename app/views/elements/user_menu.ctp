<?php
$logged_in = $session->check('Auth.User.id');
$has_access = array();

/* The following array ($has_access[$category][$permission] = $boolean) is used
 * so that it can be easily determined whether or not any actions are permitted within
 * a given category. */ 
$has_access['articles']['articles:add'] = $this->Permission->permitted('articles', 'add');
$has_access['admin']['roles:index'] = $this->Permission->permitted('roles', 'index');
$has_access['admin']['pages:clear_cache'] = $this->Permission->permitted('pages', 'clear_cache');
$has_access['admin']['articles:reindex'] = $this->Permission->permitted('articles', 'reindex');
?>
<?php if ($logged_in): ?>
	Logged in as <?php echo $session->read('Auth.User.name'); ?>
	<ul id="user_menu">
		<li>
			<span class="fake_link">Account</span>
			<ul>
				<li><a href="/profile">My profile</a></li>
				<li><a href="/users/logout">Log out</a></li>
			</ul>
		</li>
		
		<?php if (in_array(true, $has_access['articles'])): ?>
			<li>
				<span class="fake_link">Articles</span>
				<ul>
					<?php if ($has_access['articles']['articles:add']): ?>
						<li><a href="/articles/add">Submit New Article</a></li>
						<li><a href="/articles/mine">My Published Articles</a></li>
						<li><a href="/articles/drafts">My Drafts</a></li>
					<?php endif; ?>
				</ul>
			</li>
		<?php endif; ?>
		
		<?php if (in_array(true, $has_access['admin'])): ?>
			<li>
				<span class="fake_link">Admin</span>
				<ul>
					<?php if ($has_access['admin']['roles:index']): ?>
						<li><a href="/roles">Roles / Permissions</a></li>
					<?php endif; ?>
					<?php if ($has_access['admin']['pages:clear_cache']): ?>
						<li><a href="/pages/clear_cache">Clear System Cache</a></li>
					<?php endif; ?>
					<?php if ($has_access['admin']['articles:reindex']): ?>
						<li><a href="/articles/reindex">Reindex Article Search Index</a></li>
					<?php endif; ?>
				</ul>
			</li>
		<?php endif; ?>
	</ul>
<?php endif; ?>
<script type="text/javascript">
	var user_menu = $('user_menu');
	$$('#user_menu ul').each(function(ul){
		ul.hide();
	});
	$$('#user_menu span').each(function(span){
		span.onclick = function() {
			span.next('ul').toggle();
		};
	});
</script>