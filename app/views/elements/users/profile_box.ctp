<?php
	$name = $user['name'];
	$email = '<a href="mailto:'.$user['email'].'" class="email">'.$user['email'].'</a>';
	$bio = $user['bio'];
	$picture = $user['picture'];
?>
<div class="profile_box">
	<div class="picture">
		<?php if ($picture): ?>
			<img src="/img/users/<?php echo $picture ?>" />
		<?php endif; ?> 
	</div>
	<p class="about">
		About the Author
	</p>
	<h2 class="name">
		<?php echo $name ?>
		<?php echo $email; ?>
	</h2>
	
	<div class="bio">
		<?php echo $bio; ?>
	</div>
</div>