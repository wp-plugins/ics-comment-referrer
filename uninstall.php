<?php

	if (defined('WP_UNINSTALL_PLUGIN') && WP_UNINSTALL_PLUGIN) {
		delete_metadata('comment', false, '_icr_jsref', '', true);
		delete_metadata('comment', false, '_icr_ref', '', true);
		delete_option('icscr_options');
	}

?>