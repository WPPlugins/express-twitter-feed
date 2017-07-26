=== Express Twitter Feed ===
Contributors: pacecreative
Tags: twitter, feed, quick, easy, express, oauth
Requires at least: 4.7
Tested up to: 4.7.3
Stable tag: trunk
License: GPLv2 or later

== Description ==

**The simplest way to add a twitter feed to your WordPress site.**

Express Twitter Feed is free, easy and reliable, featuring integrated Twitter authorization and easy-to-use shortcodes that make setup a breeze. And with its minimal styling, customizable colours and layout options, it can be tailored to suit any style or design

= Features: =

* Use shortcodes to display the feed on any page or post.
* List either a single user’s tweets, or a hashtag search.
* Quick and easy – one of the shortest setups of any Twitter feed plugin.
* Authorize with Twitter through our integrated login, or by manually supplying API tokens.
* Customizable tweet layout - show or hide any part of your displayed tweets.
* Integrated tweet action buttons.
* Minimally styled – by default the feed will inherit styles from your site’s theme with no extra work.
* Built with customization in mind – semantic HTML and BEM-syntax classes make it effortless to restyle the feed with CSS.


= Looking for more? We’re constantly adding features to make our plug in even better. =

**Check out these upcoming additions:**

* Shortcode options for greater flexibility.
* Function API for developers.
* Tweet caching system.
* Advanced configuration options.
* More styling options.
* Custom tweet layout templates.
* CSS/JS insertion fields.

== Installation ==

1. Install the plugin via the WordPress Plugin Directory, or by uploading the `express-twitter-feed` widget to your site's `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in your WordPress Admin.
3. Navigate to the Express Twitter Feed configuration page through the 'Settings' menu.
4. Authenticate with Twitter, using one of two methods:
  1. (Recommended) Click the "Log in and authentica automatically via Twitter" button. This will take you to a secure Twitter page that allows you to supply your login information. You will be directed back to the configuration page after logging in, and your authentication info will be saved automatically.
  2. If you have [Twitter auth tokens](https://dev.twitter.com/oauth/overview/application-owner-access-tokens) you would like to supply, click the manual entry checkbox, copy in your tokens, and click "Save Settings".
5. Select either a user timeline or hashtag for your feed to display.
6. Select the number of tweets you would like your feed to display.
7. (Optional) Navigate to the 'Display Options' tab for colour and layout configuration options.
8. Place the shortcode `[express-twitter-feed]` anywhere in your post or page content you would like the feed to appear.

== Changelog ==

= 0.2.1 =
* Fixed PHP notice.
* Removed redundant function.

= 0.2 =
* Initial release version.
