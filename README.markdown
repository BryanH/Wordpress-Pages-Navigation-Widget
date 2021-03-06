# Pages Navigation Widget
**Contributors:** bryanhanks

**Tags:** pages, navigation, automatic

**Requires at least:** 4.2.0

**Tested up to:** 4.2.2

**Stable tag:** master

Enables you to create navigation to your pages, with automatic drop-downs to sub-pages.

## Description

This plugin creates a widget that allows you to specify a page or a category as a navigation item. Child pages/categories are displayed below the parent.

It is possible to mix-and-match any number of pages in your sidebar. Simply drop new widgets for each item.

If you wish to reorder the navigation, simply drag-drop the widgets.

## Installation
* Copy the directory into your `wp-content/plugin` directory.
* Go into your Wordpress admin screen and activate the plugin.
* Go to your widgets screen and drag the widget to the appropriate sidebar.

## Frequently Asked Questions

### I added the plugin and nothing happens

The plugin creates a widget. Go to the widget menu and you'll see it.

### The widget isn't showing up in the right place on my template.

Your template needs to define the navigation location as a widget area.
Contact your template designer or modify the template yourself.

### What sidebar settings should I use?

You will want to wrap the output in a list item.

In `function.php`, do something like this:

`  register_sidebar( array(
          'name' => 'Navigation Sidebar',
          'before_title' => '',
          'after_title' => '',
          'before_widget' => '<li>',
          'after_widget' => '</li>',
  ));
`

In your template's file (`header.php`?) place the sidebar code where you want the menu to appear, something like this:

`<div id="navbar">
  <div id="navbarleft">
    <ul id="nav">
      <li><a href="/">Home</a></li>
<?php
if (
 !function_exists('dynamic_sidebar') ||
 !dynamic_sidebar("Navigation Sidebar")
) {}
?>
`

*Note:* A page's children will be wrapped in an unordered list (this cannot be changed for now).

## Screenshots

![Widget configuration page](screenshot-1.png?raw=true)

## Changelog

### 1.22
* Added categories as a navigation option

### 1.21
* Code Cleanups

### 1.20
* Removed option to specify links (as they are not used by default in WP)
* Cleaned up code to work properly in WP 4.2.2

### 1.11
* Tested on Wordpress 4.2.2

### 1.10
* Added Category as a menu option
* Cleaned up code
* Fixed some minor bugs
* Removed unused files and directories

### 1.01
* Fixed plugin site link

### 1.0
* Initial release

### 0.5
* Alpha release

## Upgrade Notice

### 1.22
* Added Categories as a navigation option

### 1.21
* Tested with Wordpress 4.2.x
* Removed links as a navigation option

### 1.01
* Cosmetic change in plugin management page

