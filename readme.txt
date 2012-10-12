=== Font Awesome Icons ===
Contributors: BFTrick
Tags: widget, sidebar, dynamic sidebar, limit, restrict
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 1.0.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html


A plugin for WordPress that limits the number of widgets in a sidebar.



== Description ==

This is a simple WordPress plugin which allows a site adminstrator to limit the number of widgets in a sidebar. You simply go the the settings -> limit widgets page and customize the maximum number of widgets used for each menu.

A nice visual effect will appear in the sidebar in the WordPress admin and the client will be unable to drop new widgets into the sidebar once the cap has been reached.

You can set widgets to have unlimited widgets by leaving the field blank. You can also set the sidebar to have 0 widgets effectively diabling it.


__Credits__

This is the Internet which means that nothing is created from scratch. This plugin is mostly a combination of work from other people which I just packaged into something with a user interface for easy management.

* [Jan Fabray](http://wordpress.stackexchange.com/users/8/jan-fabry) on [WordPress Answers](http://wordpress.stackexchange.com/questions/19907/limit-number-of-widgets-in-sidebars)
* [Aman](http://stackoverflow.com/users/821185/aman) on [Stack Overflow](http://stackoverflow.com/questions/6636701/limit-number-of-items-in-sortable-with-draggable-as-source/6663581#6663581)

__Contributors Welcome__

*   Submit a [pull request on Github](https://github.com/BFTrick/limit-widgets)

__Author__

*   [Patrick Rauland](http://www.patrickrauland.com)



== Installation ==

1. Upload Limit Widgets to the `/wp-content/plugins/` directory.

2. Activate the plugin through the 'Plugins' menu in WordPress.

3. Go to the Settings -> Limit Widgets page.

4. Enter an integer for each sidebar on the Limit Widgets settings page.



== Frequently Asked Questions ==

= What do the different colors in the WordPress admin section mean? =

* _Blue_: If you have a sidebar that has reached the maximum number of widets allowed. 

* _Red_: If you have a sidebar that has more widets than is allowed. 

* _No color_: If your sidebar doesn't have a limit or the number of widgets in the sidebar is less than the limit there is  applied to the sidebar.


= How do you configure a sidebar with more widgets than allowed? =

There are two likely ways to get a sidebar that has more widets than allowed.

1. You set the maximum number of widgets on a sidebar that was already too full.

2. Someone disabled the JavaScript. See the next question.


= How does this plugin work? / Is there anyway someone can avoid the set limits? =

This plugin is based on JavaScript. If you have a client who likes to break the rules you set it is possible that they might disable the JavaScript and set the sidebars that way.


== Screenshots ==

1. A sidebar that is full.

2. A sidebar that is beyond full. Normally this happens when you limit an existing sidebar.



== Changelog ==

= 1.0 =

* Initial release