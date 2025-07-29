=== Magic Functions ===
Contributors: luisdelcid
Donate link: https://luisdelcid.com
Tested up to: 6.8.2
Requires at least: 5.9
Requires PHP: 7.4
Stable tag: 5.7.29
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Magic functions for WordPress plugins and themes.

== Description ==

Magic functions for WordPress plugins and themes.

= For plugins =

Add the following code to the main plugin file:

`add_action('magic_loaded', function(){`
`   // Add your custom theme functions here...`
`});`

= For themes =

Create a new file named `magic-functions.php` and add your custom theme functions there or add the following code to the functions.php file:

`add_action('after_magic_loaded', function(){`
`   // Add your custom theme functions here...`
`});`

Note that `after_magic_loaded` is the **first action hook available to themes**, instead of `after_setup_theme`.

== Changelog ==

To see what’s changed, visit the [GitHub repository &#187;](https://github.com/luisdelcid/magic-functions)
