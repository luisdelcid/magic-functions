=== Magic Functions ===
Contributors: luisdelcid
Donate link: https://luisdelcid.com
Tags: magic, functions
Tested up to: 6.8.1
Requires at least: 5.6
Requires PHP: 5.6
Stable tag: 5.5.13.6
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A collection of magic functions for WordPress plugins and themes.

== Description ==

A collection of magic functions for WordPress plugins and themes.

= For plugins =

Add the following code to the main plugin file:

`add_action('magic_loaded', function(){`
`   'do your magic here'`
`});`

= For themes =

Create a new file named `magic-functions.php` and do your magic there or add the following code to the functions.php file:

`add_action('after_magic_loaded', function(){`
`   'do your magic here'`
`});`

Note that `after_magic_loaded` is the **first action hook available to themes**, instead of `after_setup_theme`.

== Changelog ==

To see what’s changed, visit the [GitHub repository](https://github.com/luisdelcid/magic-functions).
