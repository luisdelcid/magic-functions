=== Magic Functions ===
Contributors: luisdelcid
Donate link: https://luisdelcid.com/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 5.6
Requires PHP: 5.6
Stable tag: 0.9.3.6
Tags: magic, functions
Tested up to: 6.3.1

Magic Functions

== Description ==

A collection of magic functions for your WordPress plugins and themes.

= For plugins =

Add the following code to the main plugin file:

`add_action('pluigns_loaded', function(){
    if(!did_action('magic_loaded')){
        return;
    }
    // Do your magic here.
});`

= For themes =

Create a new file named `magic-functions.php` and do your magic there or add the following code to the functions.php file:

`if(did_action('magic_loaded')){
    // Do your magic here.
}`

== Changelog ==

Please visit the [GitHub repository](https://github.com/luisdelcid/magic-functions).
