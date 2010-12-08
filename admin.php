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
			add_action('admin_menu', array($this, 'admin_menu'));
		}

		public function admin_init()
		{
			add_filter('plugin_action_links_ics-comment-referrer/ics-comment-referrer.php', array($this, 'plugin_action_links'));

			add_filter('manage_edit-comments_columns',  array($this, 'manage_edit_comments_columns'));
			add_action('manage_comments_custom_column', array($this, 'manage_comments_custom_column'), 10, 2);

			register_setting('icscr', 'icscr_options', array($this, 'validate'));
			add_settings_section('general', __('General Options', 'icscr-admin'), array(__CLASS__, 'null_func'), 'icscr');
			add_settings_field('nrs', __('Mark comments with no referrer as spam', 'icscr-admin'), array($this, 'checkbox'), 'icscr', 'general', array('icscr_options', 'nrs', 1, 0));
			add_settings_field('trs', __('Mark comments with tampered referrer as spam', 'icscr-admin'), array($this, 'checkbox'), 'icscr', 'general', array('icscr_options', 'trs', 1, 0));
		}

		public function validate(array $input)
		{
			$input['nrs'] = empty($input['nrs']) ? 0 : 1;
			$input['trs'] = empty($input['trs']) ? 0 : 1;
			return $input;
		}

		public static function null_func() {}

		public function checkbox(array $args)
		{
			list($option, $field, $value, $default) = $args;
			$v = get_option($option, array());
			$v = (is_array($v) && isset($v[$field])) ? $v[$field] : $default;
			echo '<input type="checkbox" name="' . esc_attr($option) . '[' . esc_attr($field) . ']" value="' . esc_attr($value) . '"' . checked($v, $value, false) . '/>';
		}

		public function admin_menu()
		{
			add_options_page(__('ICS Comment Referrer', 'icscr-admin'), __('ICS Comment Referrer', 'icscr-admin'), 'manage_options', 'ics-comment-referrer/options.php');
		}

		public function plugin_action_links($links)
		{
			$settings  = esc_attr(admin_url('options-general.php?page=ics-comment-referrer/options.php'));
			$bugreport = esc_attr('https://bugs.launchpad.net/wp-plugin-comment-referrer/+filebug');
			$support   = esc_attr('https://answers.launchpad.net/wp-plugin-comment-referrer/+addquestion');

			$links[] = '<a href="' . $settings . '">' . esc_html__('Settings', 'icscr-admin') . '</a>';
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