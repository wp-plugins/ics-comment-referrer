<?php
/*
Plugin Name: ICS Comment Referrer
Plugin URI: https://launchpad.net/wp-plugin-comment-referrer
Description: Adds referrer information to the comment notifications.
Version: 0.1
Author: ICS
Author URI: http://blog.sjinks.pro/
*/

	class ICS_Comment_Referrer
	{
		public static function instance()
		{
			static $self = null;
			if (!$self) {
				$self = new ICS_Comment_Referrer();
			}

			return $self;
		}

		private function __construct()
		{
			add_action('init', array($this, 'init'));
		}

		public function init()
		{
			if (!is_admin()) {
				add_action('comment_form', array($this, 'comment_form'));
				add_action('comment_post', array($this, 'comment_post'));

				load_plugin_textdomain('icscr', false, 'ics-comment-referrer/lang');
			}
			else {
				require_once dirname(__FILE__) . '/admin.php';
				ICS_Comment_Referrer_Admin::instance();
			}
		}

		public function comment_form($post_id)
		{
			$referer = stripslashes(wp_get_referer());
			$referer = apply_filters('icr_get_referer', $referer);
			$hmac    = hash_hmac('sha1', $referer, self::get_salt(), true);
			$value   = base64_encode($hmac . $referer);

			echo '<input type="hidden" name="icr_ref" id="icr_ref" value="' . esc_attr($value) . '"/>';
			add_action('wp_footer', array($this, 'wp_footer'), 1000);
		}

		public function comment_post($comment_id)
		{
			if (isset($_POST['icr_jsref'])) {
				$value = stripslashes($_POST['icr_jsref']);
				update_comment_meta($comment_id, '_icr_jsref', $value);
				add_filter('comment_notification_text', array(__CLASS__, 'add_icrjs_referrer'), 11, 2);
				add_filter('comment_moderation_text',   array(__CLASS__, 'add_icrjs_referrer'), 11, 2);
			}

			if (isset($_POST['icr_ref'])) {
				$value = stripslashes($_POST['icr_ref']);
				$value = base64_decode($value, true);
				if (false !== $value) {
					$hmac    = substr($value, 0, 20);
					$referer = substr($value, 20);
					$verify  = hash_hmac('sha1', $referer, self::get_salt(), true);

					if ($verify == $hmac) {
						update_comment_meta($comment_id, '_icr_ref', $referer);
						add_filter('comment_notification_text', array(__CLASS__, 'add_icr_referrer'), 10, 2);
						add_filter('comment_moderation_text',   array(__CLASS__, 'add_icr_referrer'), 10, 2);
					}
					else {
						do_action('icr_referer_tampered', $comment_id);
					}
				}
			}
			else {
				do_action('icr_no_referer', $comment_id);
			}
		}

		public static function add_icr_referrer($text, $comment_id)
		{
			remove_filter('comment_notification_text', array(__CLASS__, 'add_icr_referrer'), 10, 2);
			remove_filter('comment_moderation_text',   array(__CLASS__, 'add_icr_referrer'), 10, 2);

			$referer = trim(get_comment_meta($comment_id, '_icr_ref', true));
			if (!empty($referer)) {
				$text .= sprintf(__("\nReferrer: %s\n", 'icscr'), $referer);
			}

			return $text;
		}

		public static function add_icrjs_referrer($text, $comment_id)
		{
			remove_filter('comment_notification_text', array(__CLASS__, 'add_icrjs_referrer'), 10, 2);
			remove_filter('comment_moderation_text',   array(__CLASS__, 'add_icrjs_referrer'), 10, 2);

			$referer = trim(get_comment_meta($comment_id, '_icr_jsref', true));
			if (!empty($referer)) {
				$text .= sprintf(__("\nReferrer (JavaScript): %s\n", 'icscr'), $referer);
			}

			return $text;
		}

		public function wp_footer()
		{
?>
<script type="text/javascript">/*<![CDATA[*/
function icr_add_referer()
{
	var d = document, e = d.getElementById('icr_ref');
	if (e) {
		var n = d.createElement('input'); n.type = 'hidden'; n.name = 'icr_jsref'; n.value = d['referrer'];
		e.parentNode.insertBefore(n, e);
	}
}
var i_u = 'undefined';
if (i_u != typeof(jQuery)) {
	jQuery(icr_add_referer);
}
else if (i_u != typeof Prototype && i_u != typeof Prototype.Version) {
	document.observe("dom:loaded", icr_add_referer);
}
else if (window.addEventListener) {
	window.addEventListener('load', icr_add_referer, false);
}
else {
	window.attachEvent('onload', icr_add_referer);
}
/*]]>*/
</script>
<?php
		}

		private static function get_salt()
		{
			global $wp_default_secret_key;

			if (defined('NONCE_SALT') && '' != NONCE_SALT && NONCE_SALT != $wp_default_secret_key) {
				$salt = NONCE_SALT;
			}
			else {
				$salt = get_site_option('nonce_salt');
				if (empty($salt)) {
					$salt = wp_generate_password(64, true, true);
					update_site_option('nonce_salt', $salt);
				}
			}

			return $salt;
		}

		private function __clone() {}
	}

	ICS_Comment_Referrer::instance();
?>