=== G-Forms hCaptcha ===

Tags: gravity, forms, addon, hcaptcha, recaptcha, captcha, anti, spam, bots, block, abuse, invisible
Requires at least: 4.7
Tested up to: 6.0
Requires PHP: 5.6
Stable tag: 1.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A new way to monetize your site traffic with the hCaptcha addon for Gravity Forms.

== Description ==

A new way to monetize your site traffic with the hCaptcha addon for Gravity Forms.

This hCaptcha addon is a drop-in replacement for reCAPTCHA that earns website owners money and help companies get their data labeled while blocking bots and other forms of abuse.

== Installation ==

1. Upload `gf-hcaptcha` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Register on [hCaptcha.com](https://hCaptcha.com/?r=e3d96a76f4c8).
4. Enter your Secret Key and global Site Key in the Forms -> Settings -> hCaptcha menu in WordPress.
5. Drag the hCaptcha field into your form. You can find it under the 'Advanced fields' tab.
6. As optional option you can fill in an overwriting Site Key under the 'Advanced' tab of the field.

=== Frequently Asked Questions ===

Q: Where can I get more information about hCaptcha?  
A: Please see their website at: [hCaptcha.com](https://hCaptcha.com/?r=e3d96a76f4c8)

Q: Can I use hCaptcha on pages where reCAPCTHA is active?
A: Yes you can! You can enable/disable reCAPTCHA compatibility under the fields 'advanced' tab.

Q: Can I use different hCaptcha site keys for each form?
A: Besides the required site key field on the Global Settings page, you can fill in an optional site key for each hCaptcha field which will overrule the global site key.

=== Privacy Notices ===

With the default configuration, this addon does not:

* track users by stealth;
* write any user personal data to the database;
* send any data to external servers;
* use cookies.

Once you activate this plugin, the hCaptcha-answering user's personal data, including their IP address, may be sent to the hCaptcha service.

Please see the hCaptcha privacy policy at: 

* [hCaptcha.com](https://hCaptcha.com/privacy)

== Screenshots ==

1. Fill in your hCaptcha Secret Key and global Site Key

2. Drag the hCaptcha field into your form

3. Choose the styling of your hCaptcha badge

4. Choose for visible or invisible hCaptcha and/or fill in your overwriting hCaptcha Site Key

== Changelog ==

= 1.3.1 =
* Hotfix missing language file

= 1.3.0 =
* Added language selector in the field appearance settings
* Added placeholder image in the form editor to improve visibility
* Added field label visibility option in the field appearance settings
* Removed some parameters from API url and set them as data attribute on the element to improve dynamic options
* Replaced older API url with url from the new documentation
* Fix for confliction with multi-upload fields and forms appearing with css "display:none"
* Tested up to Wordpress 6.0
* Tested up to Gravity Forms 2.6

= 1.2.3 =
* Hotfix for last update where hCaptcha wouldn't validate the required state.

= 1.2.2 =
* Tested up to Wordpress 5.8
* Made the hCaptcha field programmatically required when newly added to a form, so an asterisk will appear in the label

= 1.2.1 =
* Added advanced option to enable/disable reCAPTCHA compatibility, when using hCaptcha and reCAPTCHA both on one page

= 1.2.0 =
* Tested up to Gravity Forms 2.5
* Added addon icon on Gravity Forms settings page
* Minor string fixes for settings page

= 1.1.1 =
* Fixed an issue where hCaptcha widget wouldn't re-init after validation errors or on a multi-page form

= 1.1.0 =
* Added global Site Key field
* Changed old Site Key field to overwriting field for global Site Key
* Added German translations

= 1.0.2 =
* Fixed an issue where multiple forms on same page with invisible hCaptcha caused a JS error

= 1.0.1 =
* Tested upon to Wordpress 5.4

= 1.0.0 =
* Public launch of the Gravity Forms hCaptcha addon