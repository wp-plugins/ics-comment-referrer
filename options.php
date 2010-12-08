<div class="wrap">
	<div class="icon32" id="icon-options-general"><br/></div>
	<h2><?php echo $GLOBALS['title']; ?></h2>

	<form action="<?php echo admin_url('options.php'); ?>" method="post">
		<?php do_settings_sections('icscr'); ?>

		<p class="submit">
			<input type="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'icscr-admin'); ?>"/>
			<?php settings_fields('icscr'); ?>
		</p>
	</form>
</div>