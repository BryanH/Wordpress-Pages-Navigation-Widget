<?php
/*
 * Plugin Name: Pages Navigation Widget
 * Plugin URI: http://hbjitney.com/pages-navigation-widget.html
 * Description: Creates drop-down navigation of select pages
 * Version: 1.03
 * Author: HBJitney, LLC
 * Author URI: http://hbjitney.com
 * License: GPL3

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
if( !class_exists( 'Widget_Pages_Navigation' ) ) {
		class Widget_Pages_Navigation extends WP_Widget {

				function Widget_Pages_Navigation() {
						/* Widget settings. */
						$widget_ops = array (
								'classname' => 'pages_navigation',
								'description' => __('Displays a navigation menu based upon pages and sub-page', 'pages_navigation')
						);
						$control_ops = array (
								'width' => 150,
								'height' => 150,
								'id_base' => 'pages_navigation'
						);
						$this->WP_Widget('pages_navigation', __('Page Navigation', 'pages_navigation'), $widget_ops, $control_ops);
				}
				/**
				 * Public View
				 */
				function widget($args, $instance) {
						extract($args);
						/* REQUIRED */
						_e( $before_widget );
						/* 'before' and 'after' are REQUIRED */

						$link_text = 'Error';
						$link_url = '/';

						switch( $instance['link_type']) {
						case 'page':
								$link_text = trim( strip_tags( get_page( $instance['page_id'] )->post_title ) );
								$link_url = get_page_link( $instance['page_id'] );
								break;
						case 'link':
								$link_text = trim( strip_tags( get_bookmark_field( 'link_name', $instance['bookmark_id'] ) ) );
								$link_url = get_bookmark_field( 'link_url', $instance['bookmark_id'] );
								break;
						case 'category':
								// TODO: stuff here
								$link_text = "Some category (hard coded for now)";
								$link_url = "http://www.im-hard-coded.com/";
								break;
						default:
								throw new Exception('Invalid Link Type');
						}

						_e( "<!-- Link for [" . $instance['link_type'] . "] -->" );
						_e( '<a href="' . $link_url . '">' . $link_text . '</a>' );
						_e( "<!-- /Link -->" );

						// Process children
						if( 'page' == $instance['link_type'] ) {
								$p_id = $instance['page_id']; // Parent's page ID
								_e( "<!-- Getting children -->" );
								_e( "<ul>" );
								$children = get_pages( array(
										'child_of' => $p_id,
										'parent' => $p_id,
										'sort_column' => 'menu_order',
								) );

								//	get_page( $instance['page_id'] ) );
								foreach( $children as $child ) {
										_e( '<li><a href="' . get_page_link( $child->ID ) . '">' . $child->post_title . '</a></li>' );
								}
								_e( "</ul>" );
								_e( "<!-- / children -->" );
						}

						/* REQUIRED */
						echo $after_widget;
				}
				/**
				 * Save/update settings
				 */
				function update($new_instance, $old_instance) {
						$instance = $old_instance;
						$instance['link_type'] = $new_instance['link_type'];
						/*
						 * These are kept separate so that the drop-down lists on the
						 * widget config are not messed up (each drop-down gets
						 * its own index).
						 */

						$instance['page_id'] = $new_instance['page_id'];
						$instance['bookmark_id'] = $new_instance['bookmark_id'];
						$instance['category_id'] = $new_instance['category_id'];
						$new_title = '';

						switch( $instance['link_type'] ) {
						case 'page':
								$new_title = get_page( $instance['page_id'] )->post_title;
								break;
						case 'link':
								$new_title = get_bookmark_field( 'link_name', $instance['bookmark_id'] );
								break;
						case 'category':
								$new_title = "HARD CODED!!11";
								break;
						default:
								throw new Exception('Trying to get title, got Invalid Link Type');
						}

						$instance['title'] = strip_tags( $new_title );
						return $instance;
				}
				/**
				 * Widget options form
				 */
				function form($instance) {
						if( $instance ) {
								$title = esc_attr( $instance['title'] );
						} else {
								$title = __("New Title", 'text_domain');
						}
						$defaults = array (
								'link_type' => 'page',
								'page_id' => '-999',
								'bookmark_id' => '-999',
								'category_id' => '-999',
						);
						$instance = wp_parse_args((array) $instance, $defaults);
						$pages = get_pages( array (
								'parent' => 0, // top level only
								'post_status' => 'publish'
						) );
						$links = get_bookmarks();
						$categories = get_ca
?>
	<table width="100%" summary="Formatting">
	<tr>

	<td><input type="radio"
id="<?php _e( $this->get_field_id( 'page' ) ); ?>"
name="<?php _e($this->get_field_name( 'link_type' ) ); ?>"
value="page"
<?php _e( ('page' == $instance['link_type'])?'checked="checked"':'' ); ?>" /></td>

		<td><label for="<?php _e( $this->get_field_id( 'page' ) ); ?>"><?php _e('Page:'); ?></label></td>
	</tr>
	<tr>
		<td></td>
		<td><select id="<?php _e( $this->get_field_id( 'page_id' ) ); ?>" name="<?php _e( $this->get_field_name( 'page_id' ) ); ?>">
			<option value=""><?php _e( esc_attr( __( 'Select page' ) ) ); ?></option>
<?php foreach( $pages as $page ) { ?>
<option value="<?php _e( $page->ID ); ?>" <?php _e( ($page->ID == $instance['page_id'])?'selected="selected"':'' ); ?>><?php
_e( $page->post_title ); ?></option>
<?php } ?>
		</select></td>
	</tr>
	<tr>

	<td><input type="radio"
id="<?php _e( $this->get_field_id( 'manual' ) ); ?>"
name="<?php _e( $this->get_field_name( 'link_type' ) ); ?>"
value="manual"
<?php _e( ('manual' == $instance['link_type'])?'checked="checked"':'' ); ?>" /></td>

		<td><label for="<?php _e( $this->get_field_id( 'manual' ) ); ?>"><?php _e( 'Manual Link:' ); ?></label></td>
	<tr>
		<td></td>
		<td><select id="<?php _e( $this->get_field_id( 'bookmark_id' ) ); ?>" name="<?php _e( $this->get_field_name( 'bookmark_id' ) ); ?>">
			<option value=""><?php _e( esc_attr( __( 'Select link' ) ) ); ?></option>
<?php foreach( $links as $link ) { ?>
<option value="<?php _e( $link->link_id ); ?>" <?php _e( ($link->link_id == $instance['bookmark_id'])?'selected="selected"':'' ); ?>><?php
_e( $link->link_name ); ?></option>
<?php } ?>
		</select></td>
	</tr>
	</table>
	<input type="hidden" id="<?php
_e( $this->get_field_id( 'title' ) ); ?>" name="<?php
_e( $this->get_field_name( 'title' ) ); ?>" value="<?php
_e( $title ); ?>" />
<?php
				}

				/**
				 * Register widget
				 */
				function register() {
						register_widget('Widget_Pages_Navigation');
				}
		}
}
/*
 * Sanity - was there a problem setting up the class? If so, bail with error
 * Otherwise, class is now defined; create a new one it to get the ball rolling.
 */
if( class_exists( 'Widget_Pages_Navigation' ) ) {
		new Widget_Pages_Navigation();
} else {
		$message = "<h2 style='color:red'>Error in plugin</h2>
				<p>Sorry about that! Plugin <span style='color:blue;font-family:monospace'>add-code-to-head</span> reports that it was unable to start.</p>
				<p><a href='mailto:support@hbjitney.com?subject=Widget-Pages-Navigation%20error&body=What version of Wordpress are you running? Please paste a list of your current active plugins here:'>Please report this error</a>.
				Meanwhile, here are some things you can try:</p>
				<ul><li>Make sure you are running the latest version of the plugin; update the plugin if not.</li>
				<li>There might be a conflict with other plugins. You can try disabling every other plugin; if the problem goes away, there is a conflict.</li>
				<li>Try a different theme to see if there's a conflict between the theme and the plugin.</li>
				</ul>";
		wp_die( $message );
}

add_action('widgets_init', array ('Widget_Pages_Navigation', 'register'));
?>
