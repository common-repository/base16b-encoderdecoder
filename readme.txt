=== Plugin Name ===
Contributors: henchan, MrAndersonMD
Donate link: http://base16.org
Tags: encoding, base64
Requires at least: 2.0.2
Tested up to: 2.8.3
Stable tag: 0.1.3

Encode and Decode a section of a post that has been marked up with the custom HTML tag 
<base16b>content to be encoded</base16b> 

== Description ==

This plugin is mainly a copy from MrAndersonMD's plugin base64-encoderdecoder (version 0.8.5).
It differs in the base encoding used. The original uses (PHP's implmentation of) base64. 
This plugin deploys base16b, an encoding algorithm utilising the higher Unicode planes .
The algorithm is specified at http://base16b.org

A demonstration of this plugin can be found at :[Base16.org](http://base16b.org/blog/2009/08/playing-with-base16b/ "Base16b.org")

== Installation ==

To install the plugin and get it working:
1. Copy all the source directory and all its contents to the `/wp-content/plugins/` directory on the wordpress server
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Browse the Settings. Make any necessary changes.
Alternatively  (recommended) : 
1. Use the Wordpress admin screen to Plugins => Add New
2. Search for base16b
3. Install, activate and change Settings

== Changelog ==

= 0.1.1 =
* Original version

= 0.1.2 =
* Minor descriptive changes in the readme

= 0.1.3 =
* Fixed broken encode button after last version

