<?php if (isset($require_captcha)): ?>
	<?php if ($require_captcha): ?>
		<?php $recaptcha->display_form('echo'); ?>
		<?php if (isset($recaptcha_error)): ?>
			<div class="input">
				<div class="error-message">
					<?php echo $recaptcha_error ?>
				</div>
			</div>
		<?php endif; ?>		
	<?php else: ?>
		<!-- recaptcha not required here (value: <?php echo $require_captcha ?>) -->
	<?php endif; ?>
<?php else: ?>
	<!-- recaptcha requirement not specified here -->
<?php endif; ?>