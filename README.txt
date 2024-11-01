=== WordPress Plugins Compare Diff ===
Contributors: lehelm
Donate link: https://www.paypal.me/lehelmatyus?locale.x=en_US
Tags: comments, spam
Requires at least: 3.8
Tested up to: 5.9.2
Requires PHP: 5.6
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Compare the list of plugins installed on any two websites.

== Description ==

Compare the list of plugins on any two WordPress websites hosted on any platform.

== Features ==

- Creates an admin interface that provides a JSON text containing the list of all plugins installed on your website.
- Provides a graphical diff interface where you can compare the list of plugins on the two websites.
- Highlights if plugins on the other website have been removed or added compared to your current website
- Highlights differences between the versions of the plugins installed on the two websites.
- Highlights differences in which plugins are activated on the two websites.

== How To Use ==

- The plugin has to be installed on both websites.
- The plugin provides a text field with JSON text that can be copied from the other website into yours
- Once the JSON is copied it generates a friendly Diff User interface that highlights the differences

== WordPress Website Plugins Compare ==

Developers usually have multiple instances running of the same website on different environments such as their local machines, development or test servers as well as production environments.

As development goes on a project or as support requests get fulfilled the plugins installed on these various environments could get out of sync.

Version control systems such as Git are great but they were designed to track differences between files not lists of installed plugins and their state in the Database.

This plugin does not rely on version control systems and since it exports the list a simple JSON it has no limitations on which two websites you compare.


== Installation ==

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. Log in to your WordPress dashboard, go to the Plugins menu and click Add New.

Type "Plugins Compare" and click Search Plugins. Once you’ve found this plugin you can install it by simply clicking “Install Now”.

= Manual installation =

To manually install the plugin downloading the plugin and uploading it to your web server via your favorite FTP client application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

1. Upload `website-compare-diff` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How do I compare the list of plugins =

You have to have the plugin installed and activated on both websites. Once installed you navigate to Settings > Website Compare Diff > Compare Plugins (Tab)

= Which website is compared to which one =

The diff user interface always compares in relation to the website you have pasted the JSON in to. 

= I need help or have a feature request  =

Feel free to contact me at https://wwww.lehelmatyus.com/contact

== Screenshots ==

1. Screen shot of the diff compare GUI.
2. Copy this JSON from here and paste it in the other website.
3. You can label and tag your environments to help better distinguish between them.

== Changelog ==

= 1.0.0 =
Initial release of the plugin to help you compare list of installed plugins between two websites.
