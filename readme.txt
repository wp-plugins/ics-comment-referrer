=== ICS Comment Referrer ===
Contributors: vladimir_kolesnikov
Donate link: http://blog.sjinks.pro/
Tags: comments, referrer, referer, notification
Requires at least: 2.9
Tested up to: 3.2
Stable tag: trunk

Adds the referrering link from which a commenting user came from to comment notifications and Comments screen. WARNING: PHP 5 only.

== Description ==

Adds the referrering link from which a commenting user came from to comment notifications and Comments screen in the Admin panel of your site.

This is an extended version of Donncha's [Comment Referrers](http://wordpress.org/extend/plugins/comment-referrers/) plugin

ICS Comment Referrer:

* uses both server (HTTP Referer header) and client (works if JavaScript enabled) referrer identification;
* cryptographically protects the server-generated referrer field;
* adds referring links to comment notifications;
* shows referring links in Admin Panel » Comments.

== Installation ==

1. Upload the whole `ics-comment-referrer` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. That's all.

== Frequently Asked Questions ==

No questions yet, be the first to ask.

Please use [Launchpad](https://answers.launchpad.net/wp-plugin-comment-referrer/+addquestion) to ask a question or for support.

== Screenshots ==

1. Sample screen shot.

== Changelog ==

= 0.2 =
* Option to mark comments with missing or tampered referrer as spam

= 0.1 =
* The first public release

== Upgrade Notice ==

None yet.

== Contributors ==
* [Vladimir Kolesnikov](http://blog.sjinks.pro/)
* [Ricardo A. Hermosilla Carrillo](https://launchpad.net/~rahermosillac)
* [Pierre Slamich](https://launchpad.net/~pierre-slamich)
* [zeugma](https://launchpad.net/~sunder67)
* [Mauricio Peñaloza S.](https://launchpad.net/~elkan76)

== Bug Reports ==

Please use [Launchpad](https://bugs.launchpad.net/wp-plugin-comment-referrer/+filebug) to report any bugs.
