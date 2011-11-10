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
		//$title = apply_filters('widget_title', $instance['title']);
		/* REQUIRED */
		echo $before_widget;
		/* 'before' and 'after' are REQUIRED */
		/*
		 if ($title) {
			echo $before_title . $title . $after_title . '&nbsp;';
		 }
		 */
		$link_text = 'Error';
		$link_url = '/';

		if( 'page' == $instance['link_type'] ) {
			// Page
			$link_text = get_page( $instance['page_id'] )->post_title;
			//$link_text = trim( strip_tags( $instance['page_id']->post_title ) );
			$link_url = get_page_link( $instance['page_id'] );
		} else {
			// Link
			echo "Bookmark id: " . $instance['bookmark_id'];
			$link_text = get_bookmark_field( 'link_name', $instance['bookmark_id'] );
			$link_url = get_bookmark_field( 'link_url', $instance['bookmark_id'] );
		}

echo "<!-- Link for [" . $instance['link_type'] . "] -->";
		echo '<a href="' . $link_url . '">' . $link_text . '</a>';
echo "<!-- /Link -->";
		// TODO: children here
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

		/* REQUIRED */
		echo $after_widget;
	}
	/**
	 * Save/update settings
	 */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['link_type'] = $new_instance['link_type'];
		$instance['page_id'] = $new_instance['page_id'];
		$instance['bookmark_id'] = $new_instance['bookmark_id'];
		return $instance;
	}
	/**
	 * Widget options form
	 */
	function form($instance) {
		$defaults = array (
			'link_type' => 'page',
			'page_id' => '',
			'bookmark_id' => ''
		);
		$instance = wp_parse_args((array) $instance, $defaults);
		$pages = get_pages( array (
			'parent' => 0, // top level only
			'post_status' => 'publish'
		) );
		$links = get_bookmarks();
?>
<!--
	<p>Link type [<?php _e( $instance['link_type'] ); ?>]</p>
	<p>Page id [<?php _e( $instance['page_id'] ); ?>]</p>
	<p>Link: [<?php _e( $instance['bookmark_id'] ); ?>]</p>
-->
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
			<option value="<?php _e( $link->ID ); ?>" <?php _e( ($link->ID == $instance['bookmark_id'])?'selected="selected"':'' ); ?>><?php
_e( $link->link_name ); ?></option>
<?php } ?>
		</select></td>
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
