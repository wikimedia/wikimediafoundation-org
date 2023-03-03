# User Switching

Stable tag: 1.7.0  
Requires at least: 3.7  
Tested up to: 6.0  
Requires PHP: 5.3  
License: GPL v2 or later  
Tags: users, user switching, fast user switching, multisite, woocommerce, buddypress, bbpress  
Contributors: johnbillion  
Donate link: https://github.com/sponsors/johnbillion

![](.wordpress-org/banner-1544x500.png)

Instant switching between user accounts in WordPress.

[![](https://img.shields.io/badge/ethical-open%20source-4baaaa.svg?style=flat-square)](#ethical-open-source)
[![](https://img.shields.io/wordpress/plugin/installs/user-switching?style=flat-square)](https://wordpress.org/plugins/user-switching/)
[![](https://img.shields.io/github/workflow/status/johnbillion/user-switching/Test/develop?style=flat-square)](https://github.com/johnbillion/user-switching/actions)

## Description

This plugin allows you to quickly swap between user accounts in WordPress at the click of a button. You'll be instantly logged out and logged in as your desired user. This is handy for testing environments, for helping customers on WooCommerce sites, or for any site where administrators need to switch between multiple accounts.

### Features

 * Switch user: Instantly switch to any user account from the *Users* screen.
 * Switch back: Instantly switch back to your originating account.
 * Switch off: Log out of your account but retain the ability to instantly switch back in again.
 * Compatible with Multisite, WooCommerce, BuddyPress, bbPress, and most two-factor authentication plugins.

### Security

 * Only users with the ability to edit other users can switch user accounts. By default this is only Administrators on single site installations, and Super Admins on Multisite installations.
 * Passwords are not (and cannot be) revealed.
 * Uses the cookie authentication system in WordPress when remembering the account(s) you've switched from and when switching back.
 * Implements the nonce security system in WordPress, meaning only those who intend to switch users can switch.
 * Full support for user session validation where appropriate.
 * Full support for administration over SSL (if applicable).

### Usage

 1. Visit the *Users* menu in WordPress and you'll see a *Switch To* link in the list of action links for each user.
 2. Click this and you will immediately switch into that user account.
 3. You can switch back to your originating account via the *Switch back* link on each dashboard screen or in your profile menu in the WordPress toolbar.

See the [FAQ](https://wordpress.org/plugins/user-switching/faq/) for information about the *Switch Off* feature.

### Other Plugins

I maintain several other plugins for developers. Check them out:

* [Query Monitor](https://wordpress.org/plugins/query-monitor/) is the developer tools panel for WordPress
* [WP Crontrol](https://wordpress.org/plugins/wp-crontrol/) lets you view and control what's happening in the WP-Cron system

### Privacy Statement

User Switching makes use of browser cookies in order to allow users to switch to another account. Its cookies operate using the same mechanism as the authentication cookies in WordPress core, which means their values contain the user's `user_login` field in plain text which should be treated as potentially personally identifiable information (PII) for privacy and regulatory reasons (GDPR, CCPA, etc). The names of the cookies are:

* `wordpress_user_sw_{COOKIEHASH}`
* `wordpress_user_sw_secure_{COOKIEHASH}`
* `wordpress_user_sw_olduser_{COOKIEHASH}`

User Switching does not send data to any third party, nor does it include any third party resources, nor will it ever do so.

See also the FAQ for some questions relating to privacy and safety when switching between users.

## Screenshots

1. The *Switch To* link on the Users screen  
   ![The Switch To link on the Users screen](.wordpress-org/screenshot-1.png)
2. The *Switch To* link on a user's profile  
   ![The Switch To link on a user's profile](.wordpress-org/screenshot-2.png)

## Frequently Asked Questions

### Does this plugin work with PHP 8?

Yes, it's actively tested and working up to PHP 8.1.

### What does "Switch off" mean?

Switching off logs you out of your account but retains your user ID in an authentication cookie so you can switch straight back without having to log in again manually. It's akin to switching to no user, and being able to switch back.

The *Switch Off* link can be found in your profile menu in the WordPress toolbar. Once you've switched off you'll see a *Switch back* link on the Log In screen and in the footer of your site.

### Does this plugin work with WordPress Multisite?

Yes, and you'll also be able to switch users from the Users screen in Network Admin.

### Does this plugin work with WooCommerce?

Yes, and you'll also be able to switch users from various WooCommerce administration screens.

### Does this plugin work with BuddyPress?

Yes, and you'll also be able to switch users from member profile screens and the member listing screen.

### Does this plugin work with bbPress?

Yes, and you'll also be able to switch users from member profile screens.

### Does this plugin work if my site is using a two-factor authentication plugin?

Yes, mostly.

One exception I'm aware of is [Duo Security](https://wordpress.org/plugins/duo-wordpress/). If you're using this plugin, you should install the [User Switching for Duo Security](https://github.com/johnbillion/user-switching-duo-security) add-on plugin which will prevent the two-factor authentication prompt from appearing when you switch between users.

### What capability does a user need in order to switch accounts?

A user needs the `edit_users` capability in order to switch user accounts. By default only Administrators have this capability, and with Multisite enabled only Super Admins have this capability.

### Can the ability to switch accounts be granted to other users or roles?

Yes. The `switch_users` meta capability can be explicitly granted to a user or a role to allow them to switch users regardless of whether or not they have the `edit_users` capability. For practical purposes, the user or role will also need the `list_users` capability so they can access the Users menu in the WordPress admin area.

~~~php
add_filter( 'user_has_cap', function( $allcaps, $caps, $args, $user ) {
	if ( 'switch_to_user' === $args[0] ) {
		if ( my_condition( $user ) ) {
			$allcaps['switch_users'] = true;
		}
	}
	return $allcaps;
}, 9, 4 );
~~~

Note that this needs to happen before User Switching's own capability filtering, hence the priority of `9`.

### Can the ability to switch accounts be denied from users?

Yes. User capabilities in WordPress can be set to `false` to deny them from a user. Denying the `switch_users` capability prevents the user from switching users, even if they have the `edit_users` capability.

~~~php
add_filter( 'user_has_cap', function( $allcaps, $caps, $args, $user ) {
	if ( 'switch_to_user' === $args[0] ) {
		if ( my_condition( $user ) ) {
			$allcaps['switch_users'] = false;
		}
	}
	return $allcaps;
}, 9, 4 );
~~~

Note that this needs to happen before User Switching's own capability filtering, hence the priority of `9`.

### Can I add a custom "Switch To" link to my own plugin or theme?

Yes. Use the `user_switching::maybe_switch_url()` method for this. It takes care of authentication and returns a nonce-protected URL for the current user to switch into the provided user account.

~~~php
if ( method_exists( 'user_switching', 'maybe_switch_url' ) ) {
	$url = user_switching::maybe_switch_url( $target_user );
	if ( $url ) {
		printf(
			'<a href="%1$s">Switch to %2$s</a>',
			esc_url( $url ),
			esc_html( $target_user->display_name )
		);
	}
}
~~~

This link also works for switching back to the original user, but if you want an explicit link for this you can use the following code:

~~~php
if ( method_exists( 'user_switching', 'get_old_user' ) ) {
	$old_user = user_switching::get_old_user();
	if ( $old_user ) {
		printf(
			'<a href="%1$s">Switch back to %2$s</a>',
			esc_url( user_switching::switch_back_url( $old_user ) ),
			esc_html( $old_user->display_name )
		);
	}
}
~~~

### Can I determine whether the current user switched into their account?

Yes. Use the `current_user_switched()` function for this.

~~~php
if ( function_exists( 'current_user_switched' ) ) {
	$switched_user = current_user_switched();
	if ( $switched_user ) {
		// User is logged in and has switched into their account.
		// $switched_user is the WP_User object for their originating user.
	}
}
~~~

### Does this plugin allow a user to frame another user for an action?

Potentially yes, but User Switching includes some safety protections for this and there are further precautions you can take as a site administrator:

* User Switching stores the ID of the originating user in the new session for the user they switch to. Although this session does not persist by default when they subsequently switch back, there will be a record of this ID if your MySQL server has query logging enabled.
* User Switching stores the login name of the originating user in an authentication cookie (see the Privacy Statement for more information). If your server access logs store cookie data, there will be a record of this login name (along with the IP address) for each access request.
* You can install an audit trail plugin such as Simple History, WP Activity Log, or Stream, all of which have built-in support for User Switching and all of which log an entry when a user switches into another account.
* User Switching triggers an action when a user switches account, switches off, or switches back (see below). You can use these actions to perform additional logging for safety purposes depending on your requirements.

One or more of the above should allow you to correlate an action with the originating user when a user switches account, should you need to.

Bear in mind that even without the User Switching plugin in use, any user who has the ability to edit another user can still frame another user for an action by, for example, changing their password and manually logging into that account. If you are concerned about users abusing others, you should take great care when granting users administrative rights.

### Can regular admins on Multisite installations switch accounts?

No. This can be enabled though by installing the [User Switching for Regular Admins](https://github.com/johnbillion/user-switching-for-regular-admins) plugin.

### Can I switch users directly from the admin toolbar?

Yes, there's a third party add-on plugin for this: [Admin Bar User Switching](https://wordpress.org/plugins/admin-bar-user-switching/).

### Are any plugin actions called when a user switches account?

Yes. When a user switches to another account, the `switch_to_user` hook is called:

~~~php
/**
 * Fires when a user switches to another user account.
 *
 * @since 0.6.0
 * @since 1.4.0 The `$new_token` and `$old_token` parameters were added.
 *
 * @param int    $user_id     The ID of the user being switched to.
 * @param int    $old_user_id The ID of the user being switched from.
 * @param string $new_token   The token of the session of the user being switched to. Can be an empty string
 *                            or a token for a session that may or may not still be valid.
 * @param string $old_token   The token of the session of the user being switched from.
 */
do_action( 'switch_to_user', $user_id, $old_user_id, $new_token, $old_token );
~~~

When a user switches back to their originating account, the `switch_back_user` hook is called:

~~~php
/**
 * Fires when a user switches back to their originating account.
 *
 * @since 0.6.0
 * @since 1.4.0 The `$new_token` and `$old_token` parameters were added.
 *
 * @param int       $user_id     The ID of the user being switched back to.
 * @param int|false $old_user_id The ID of the user being switched from, or false if the user is switching back
 *                               after having been switched off.
 * @param string    $new_token   The token of the session of the user being switched to. Can be an empty string
 *                               or a token for a session that may or may not still be valid.
 * @param string    $old_token   The token of the session of the user being switched from.
 */
do_action( 'switch_back_user', $user_id, $old_user_id, $new_token, $old_token );
~~~

When a user switches off, the `switch_off_user` hook is called:

~~~php
/**
 * Fires when a user switches off.
 *
 * @since 0.6.0
 * @since 1.4.0 The `$old_token` parameter was added.
 *
 * @param int    $old_user_id The ID of the user switching off.
 * @param string $old_token   The token of the session of the user switching off.
 */
do_action( 'switch_off_user', $old_user_id, $old_token );
~~~

In addition, User Switching respects the following filters from WordPress core when appropriate:

* `login_redirect` when switching to another user.
* `logout_redirect` when switching off.

### Do you accept donations?

[I am accepting sponsorships via the GitHub Sponsors program](https://github.com/sponsors/johnbillion) and any support you can give will help me maintain this plugin and keep it free for everyone.

## Changelog ##

### 1.7.0 ###

* Redirect to the current post, term, user, or comment being edited when switching off
* Clean up some user-facing messages
* Apply basic styling to the Switch Back link that appears in the footer
* Use a better placement for the Switch To menu on bbPress profiles
* Use a more appropriate HTTP response code if switching off fails
* Exclude `.editorconfig` from dist ZIP

### 1.6.0 ###

* Add a 'Switch To' link to the order screen in WooCommerce
* Add a 'Switch back' link to the My Account screen and the login screen in WooCommerce

### 1.5.8 ###

* Avoid a fatal if the `interim-login` query parameter is present on a page other than wp-login.php.

### 1.5.7 ###

* Fix some issues that could lead to PHP errors given a malformed cookie.
* Fix documentation.


### 1.5.6 ###

* Add a class to the table row on the user edit screen.
* Updated docs.

### 1.5.5 ###

* Added the `user_switching_in_footer` filter to disable output in footer on front end.
* Documentation additions and improvements.

### 1.5.4 ###

* Fix a cookie issue caused by Jetpack 8.1.1 which prevented switching back to the original user.

### 1.5.3 ###

*  Remove usage of a method that's been deprecated in WordPress 5.3

### 1.5.2 ###

* Set the correct `lang` attribute on User Switching's admin notice.
* Move the WooCommerce session forgetting to an action callback so it can be unhooked if necessary.


### 1.5.1 ###

  * Add appropriate HTTP response codes to the error states.
  * Display User Switching's messages in the original user's locale.
  * Increase the priority of the hook that sets up the cookie constants. See #40.
  * Don't attempt to output the 'Switch To' link on author archives when the queried object isn't a user. See #39.


### 1.5.0 ###

* Add support for forgetting WooCommerce sessions when switching between users. Requires WooCommerce 3.6+.


### 1.4.2 ###

* Don't attempt to add the `Switch To` link to the admin toolbar when viewing an author archive in the admin area. This prevents a fatal error occurring when filtering custom post type listing screens by authors in the admin area.

### 1.4.1 ###

* Add a `Switch To` link to the Edit User admin toolbar menu when viewing an author archive.
* Add a `Switch back` link to the Edit User admin toolbar menu when viewing an author archive and you're already switched.

### 1.4.0 ###

* Add support for user session retention, reuse, and destruction when switching to and back from other user accounts.
* Add support for the `switch_users` meta capability for fine grained control over the ability to switch user accounts.
* More code and documentation quality improvements.

### 1.3.1 ###

* Add support for the `X-Redirect-By` header in WordPress 5.0.
* Allow User Switching's admin notices to be dismissed.
* Introduce a privacy statement.


### 1.3.0 ###

* Update the BuddyPress compatibility.
* Various code and inline docs improvements.


### 1.2.0 ###

* Improve the Switch Back functionality when the interim login window is shown.
* Always show the `Switch Back` link in the Meta widget if it's present.


### 1.1.0 ###

* Introduce a `user_switching_switched_message` filter to allow customisation of the message displayed to switched users in the admin area.
* Switch to safe redirects for extra paranoid hardening.
* Docblock improvements.
* Coding standards improvements.

### 1.0.9 ###

- Remove the bundled languages in favour of language packs from translate.wordpress.org.


### 1.0.8 ###

- Chinese (Taiwan) and Czech translations.
- Updated Dutch, Spanish, Hebrew, and German translations.
- Add an ID attribute to the links that User Switching outputs on the WordPress login screen, BuddyPress screens, and bbPress screens.
- Avoid a deprecated argument notice when the `user-actions` admin toolbar node has been removed.


### 1.0.7 ###

- Azerbaijani, Danish, and Bosnian translations.
- Add back the 'User Switching' heading on the user profile screen.
- Correct the value passed to the `$old_user_id` parameter of the `switch_back_user` hook when a user has been switched off. This should be boolean `false` rather than `0`.
- Docblocks for actions and filters.
- More code standards tweaks.


### 1.0.6 ###

- Correct the values passed to the `switch_back_user` action when a user switches back.
- More code standards tweaks.


### 1.0.5 ###

- Norwegian translation by Per Søderlind.
- Code standards tweaks.


### 1.0.4 ###

- Support for the new `logout_redirect` and `removable_query_args` filters in WordPress 4.2.


### 1.0.3 ###

- Croation translation by Ante Sepic.
- Avoid PHP notices caused by other plugins which erroneously use boolean `true` as a capability.


### 1.0.2 ###

- Turkish translation by Abdullah Pazarbasi.
- Romanian translation by ArianServ.
- Dutch translation by Thom.
- Greek translation by evigiannakou.
- Bulgarian translation by Petya Raykovska.
- Finnish translation by Sami Keijonen.
- Italian translation by Alessandro Curci and Alessandro Tesoro.
- Updated Arabic, Spanish, German, and Polish translations.


### 1.0.1 ###

- Shorten the names of User Switching's cookies to avoid problems with Suhosin's over-zealous default rules.
- Add backwards compatibility for the deprecated `OLDUSER_COOKIE` constant.


### 1.0 ###

- Security hardening for sites that use HTTPS in the admin area and HTTP on the front end.
- Add an extra auth check before the nonce verification.
- Pretty icon next to the switch back links.


### 0.9 ###

- Minor fixes for the `login_redirect` filter.
- Increase the specificity of the `switch_to_old_user` and `switch_off` nonces.


### 0.8.9 ###

- French translation by Fx Bénard.
- Hebrew translation by Rami Y.
- Indonesian translation by Eko Ikhyar.
- Portuguese translation by Raphael Mendonça.

