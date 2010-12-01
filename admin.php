<?php

	class ICS_Comment_Referrer_Admin
	{
		public static function instance()
		{
			static $self = null;
			if (!$self) {
				$self = new ICS_Comment_Referrer_Admin();
			}

			return $self;
		}

		private function __construct()
		{
			// Will be called during init hook execution
			load_plugin_textdomain('icscr-admin', false, 'ics-comment-referrer/lang/admin');
			add_action('admin_init', array($this, 'admin_init'));
		}

		public function admin_init()
		{
			add_filter('plugin_action_links_ics-comment-referrer/ics-comment-referrer.php', array($this, 'plugin_action_links'));

			add_filter('manage_edit-comments_columns',  array($this, 'manage_edit_comments_columns'));
			add_action('manage_comments_custom_column', array($this, 'manage_comments_custom_column'), 10, 2);
		}

		public function plugin_action_links($links)
		{
			$bugreport = esc_attr('https://bugs.launchpad.net/wp-plugin-comment-referrer/+filebug');
			$support   = esc_attr('https://answers.launchpad.net/wp-plugin-comment-referrer/+addquestion');

			$links[] = '<a href="' . $bugreport . '">' . esc_html__('Report a Bug', 'icscr-admin') . '</a>';
			$links[] = '<a href="' . $support . '">' . esc_html__('Ask a Question', 'icscr-admin') . '</a>';

			return $links;
		}

		public function manage_edit_comments_columns($columns)
		{
			return $columns + array('icscr-referrer' => __('Referrer', 'icscr-admin'));
		}

		public function manage_comments_custom_column($column, $comment_id)
		{
			if ('icscr-referrer' == $column) {
				$referer   = trim(get_comment_meta($comment_id, '_icr_ref', true));
				$jsreferer = trim(get_comment_meta($comment_id, '_icr_jsref', true));

				$ref_tpl   = __('<strong>Referer</strong>: %s<br/>', 'icscr-admin');
				$refjs_tpl = __('<strong>Referer (JavaScript)</strong>: %s<br/>', 'icscr-admin');

				if (empty($referer)) {
					$referer = sprintf($ref_tpl, __('N/A', 'icscr-admin'));
				}
				else {
					$link    = '<a href="' . esc_attr($referer) . '" rel="external" target="_blank">' . esc_html($referer) . '</a>';
					$referer = sprintf($ref_tpl, $link);
				}

				if (empty($jsreferer)) {
					$jsreferer = sprintf($refjs_tpl, __('N/A', 'icscr-admin'));
				}
				else {
					$link      = '<a href="' . esc_attr($jsreferer) . '" rel="external" target="_blank">' . esc_html($jsreferer) . '</a>';
					$jsreferer = sprintf($refjs_tpl, $link);
				}

				echo $referer . $jsreferer;
			}
		}

		private function __clone() {}
	}

?>