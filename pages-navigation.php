<?php
/*
Plugin Name: Pages Navigation Widget
Plugin URI: http://github.com/BryanH/Pages-Navigation
Description: Creates drop-down navigation of select pages
Version: 0.5
Author: HBJitney, LLC
Author URI: http://hbjitney.com
License:

Instructions: copy the entire directory into wp-content/plugins

*/

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
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title']);
		$topics = $instance['topics'];
		/* REQUIRED */
		echo $before_widget;
		/* 'before' and 'after' are REQUIRED */
		if ($title) {
			echo $before_title . $title . $after_title . '&nbsp;';
		}
		/* Display array separated by delimiter
		 * TODO: make delimiter a configurable option?
		 * */


/**********
	$query = new WP_Query( 'post_parent=93' ); // Get sub posts
$the_query = new WP_Query( $args );

// The Loop
while ( $the_query->have_posts() ) : $the_query->the_post();
	echo '<li>';
	echo the_title();
	echo '</li>';
endwhile;

// Reset Post Data
wp_reset_postdata();

	*******/


		if ($topics && isset ($topics)) {
			foreach ($topics as & $topic) {
				$category_id = get_cat_ID($topic);
				if (0 < $category_id) {
					// modified the category link for the niche sites "site category" functionality
					// @TODO: this should change in the future as we fix the niche site theme
					$category = get_category($category_id);
					$category_link = get_bloginfo('url') . '/category/' . $category->slug ;
					$topic = '<a href="' . $category_link . '" title="' . $topic . '">' . $topic . '</a>';
				} else {
					//	topic is not a link
				}
			}
			echo implode(' | ', $topics);
		}
		/* REQUIRED */
		echo $after_widget;
	}
	/**
	 * Save/update settings
	 */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags(trim($new_instance['title']));
		$topics = explode("\n", strip_tags(trim($new_instance['topics'])));
		$instance['topics'] = $topics;
		return $instance;
	}
	/**
	 * Widget options form
	 */
	function form($instance) {
		$defaults = array (
			'link_type' => "page",
			'manual_link_text' => "Text to display",
			'manual_link_url' => "http://www.example.com",
		);
		$instance = wp_parse_args((array) $instance, $defaults);
		$pages = get_pages( array (
			'parent' => 0, // top level only
			'post_status' => 'publish'
		) );
?>
	<table width="100%" summary="Formatting">
	<tr>
		<td><input type="radio" name="<?php _e($this->get_field_name( 'link_type' ) ); ?>" value="page" /></td>
		<td><label for="<?php _e( $this->get_field_id( 'page' ) ); ?>"><?php _e('Page:'); ?></label></td>
	</tr>
	<tr>
		<td></td>
		<td><select id="<?php _e( $this->get_field_id( 'page' ) ); ?>" name="<?php _e( $this->get_field_name( 'page' ) ); ?>">
			<option value=""><?php _e( esc_attr( __( 'Select page' ) ) ); ?></option>
<?php foreach( $pages as $page ) { ?>
			<option value="<?php _e(  get_page_link( $page->ID ) ); ?>"><?php _e( $page->post_title ); ?></option>
<?php } ?>
		</select></td>
	</tr>
	<tr>
		<td><input type="radio" name="<?php _e( $this->get_field_name( 'link_type' ) ); ?>" value="manual" /></td>
		<td><label for="<?php _e( $this->get_field_id( 'link_type' ) ); ?>"><?php _e( 'Manual Link' ); ?></label></td>
	<tr>
		<td></td>
		<td><label for="<?php _e( $this->get_field_id( 'manual_link_text' ) ); ?>"><?php _e( 'Text to display' ); ?></label><br />
			<input type="text" id="<?php _e( $this->get_field_id( 'manual_link_text' ) ); ?>" name="<?php _e( $this->get_field_name( 'manual_link_text' ) ); ?>" style="width:100%" /></td>
	</tr>
	<tr>
		<td></td>
		<td><label for="<?php _e( $this->get_field_id( 'manual_link_url' ) ); ?>"><?php _e( 'Web address' ); ?></label><br />
			<input type="text" id="<?php _e( $this->get_field_id( 'manual_link_url' ) ); ?>" name="<?php _e( $this->get_field_name( 'manual_link_url' ) ); ?>" style="width:100%" /></td>
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
add_action('widgets_init', array ('Widget_Pages_Navigation', 'register'));

?>
