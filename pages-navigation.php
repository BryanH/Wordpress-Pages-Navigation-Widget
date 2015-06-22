<?php
/*
 * Plugin Name: Pages Navigation Widget
 * Plugin URI: http://hbjitney.com/pages-navigation-widget.html
 * Description: Creates drop-down navigation of select pages
 * Version: 1.22
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
						_e( "<!-- Pages Navigation Widget by HBJitney, LLC -->");
						/* REQUIRED */
						_e( $before_widget );
						/* 'before' and 'after' are REQUIRED */

						// Display title, if it exists
						$title = apply_filters('widget_title', $instance['title'] );
						if( $title ) {
							_e( $before_title . $title . $after_title );
						}

						$link_text = 'Error';
						$link_url = '/';

						switch( $instance['link_type']) {
						case 'page':
								$link_text = trim( strip_tags( get_page( $instance['page_id'] )->post_title ) );
								$link_url = get_page_link( $instance['page_id'] );
								break;
						case 'category':
								$link_text = trim( strip_tags( get_category( $instance['category_id'] )->cat_name ) );
								$link_url = get_category_link( $instance['category_id'] );
								break;
						default:
								throw new Exception('Invalid Link Type');
						}

						// Display parent
						_e( "<!-- Link for [" . $instance['link_type'] . "] -->" );
						_e( '<a href="' . $link_url . '">' . $link_text . '</a>' );
						_e( "<!-- /Link -->" );

						// Process children

						/* PAGES ***************/
						if( 'page' == $instance['link_type'] ) {
							$p_id = $instance['page_id']; // Parent's page ID
							_e( "<!-- Getting children -->" );
							_e( "<ul>" );
							$children = get_pages( array(
								'child_of' => $p_id,
								'parent' => $p_id,
								'sort_column' => 'menu_order',
							) );

							foreach( $children as $child ) {
								_e( '<li><a href="' . get_page_link( $child->ID ) . '">' . $child->post_title . '</a></li>' );
							}
							_e( "</ul>" );
							_e( "<!-- / children -->" );
						}

						/* CATEGORIES ***********/
						if( 'category' == $instance['link_type'] ) {
							$c_id = $instance['category_id']; // Parent's category ID
								_e( "<!-- Getting children -->" );
								_e( "<ul>" );

							/* Busted for now
							$children = get_pages( array(
								'child_of' => $p_id,
								'parent' => $p_id,
								'sort_column' => 'menu_order',
							) );
							*/
								_e( "</ul>" );
						}

						/* REQUIRED */
						echo $after_widget;

						_e( "<!-- /Pages Navigation Widget -->");
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
						$instance['title'] = $new_instance['title'];
						$instance['page_id'] = $new_instance['page_id'];
						$instance['category_id'] = $new_instance['category_id'];
						return $instance;
				}
				/**
				 * Widget options form
				 */
				function form($instance) {
						wp_enqueue_script("jquery");

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

						$category_args = array(
							'hide_empty' => 0
							, 'show_option_none'   => esc_html__( "Select category" )
							, 'option_none_value'  => '-1'
							, 'order' => 'name'
							, 'id' => $this->get_field_id('category_id')
							, 'name' => $this->get_field_name('category_id')
							, 'hierarchical' => true
							, 'echo' => 0
							, 'selected' => $instance['category_id']
						);
?>
	<table width="100%" summary="Formatting">
		<tr>
			<td><label for="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:' ); ?></label></td>
			<td><input type="text" id="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>"
				name="<?php esc_attr_e( $this->get_field_name( 'title' ) ); ?>"
				value="<?php esc_attr_e( $instance['title'] ); ?>" /></td>
		</tr>

		<!-- Pages -->
		<tr>
			<td><input type="radio"
				id="<?php esc_attr_e( $this->get_field_id( 'page' ) ); ?>"
				name="<?php esc_attr_e( $this->get_field_name( 'link_type' ) ); ?>"
				value="page"
				<?php _e( ('page' == $instance['link_type'])?'checked="checked"':'' ); ?>" /></td>
			<td><label for="<?php esc_attr_e( $this->get_field_id( 'page' ) ); ?>"><?php esc_html_e('Page:'); ?></label></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><select id="<?php esc_attr_e( $this->get_field_id( 'page_id' ) ); ?>" onchange="jQuery('#<?php esc_attr_e( $this->get_field_id( 'page' ) ) ?>').prop('checked', true)" name="<?php _e( $this->get_field_name( 'page_id' ) ); ?>">
				<option value=""><?php esc_attr_e( 'Select page' ); ?></option>
	<?php foreach( $pages as $page ) { ?>
	<option value="<?php esc_attr_e( $page->ID ); ?>"
		<?php _e( ($page->ID == $instance['page_id'])?'selected="selected"':'' ); ?>><?php
	esc_html_e( $page->post_title ); ?></option>
	<?php } ?>
			</select></td>
		</tr>

		<!-- Categories -->
		<tr>
			<td><input type="radio"
				id="<?php esc_attr_e( $this->get_field_id( 'category' ) ); ?>"
				name="<?php esc_attr_e( $this->get_field_name( 'link_type' ) ); ?>"
				value="category"
				<?php _e( ('category' == $instance['link_type'])?'checked="checked"':'' ); ?>" /></td>
			<td>
<?php
$replace = "<select$1 onchange=\"jQuery('#" . $this->get_field_id( 'category' ) . "').prop('checked', true)\">";
$category_select  = preg_replace( '#<select([^>]*)>#', $replace, wp_dropdown_categories( $category_args ) );
_e( $category_select );
?>
			</td>
		</tr>
	</table>
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
