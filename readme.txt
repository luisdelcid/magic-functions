=== Magic Functions ===
Contributors: luisdelcid
Donate link: https://luisdelcid.com
Tags: magic, functions
Tested up to: 6.6.1
Requires PHP: 5.6
Stable tag: 0.8.14
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A collection of magic functions for WordPress, plugins and themes.

== Description ==

A collection of magic functions for WordPress, plugins and themes.

= For plugins =

Add the following code to the main plugin file:

`add_action('plugins_loaded', function(){`
`. if(did_action('magic_loaded')){`
`.   'do your magic here'`
`. }`
`});`

= For themes =

Create a new file named `magic-functions.php` and do your magic there or add the following code to the functions.php file:

`if(did_action('magic_loaded')){ 'do your magic here' }`

== Changelog ==

To see what’s changed, visit the [GitHub repository](https://github.com/luisdelcid/magic-functions).
