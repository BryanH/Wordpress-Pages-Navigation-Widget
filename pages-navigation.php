<?php
/*
Plugin Name: Pages Navigation
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
		$this->WP_Widget('pages_navigation', __('Hot Topics', 'pages_navigation'), $widget_ops, $control_ops);
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
			'title' => __('Pages_Navigation', 'pages_navigation'),
			'topics' => array (
				"General",
				"Featured",
				"News"
			)
		);
		$instance = wp_parse_args((array) $instance, $defaults);
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Caption:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'topics' ); ?>"><?php _e('Topics (one per line):', 'pages_navigation'); ?></label><br />
			 <textarea id="<?php echo $this->get_field_id( 'topics' ); ?>" name="<?php echo $this->get_field_name( 'topics' ); ?>" rows="6" cols="15" style="width:100%"><?php echo implode("\n", $instance['topics'] ); ?></textarea>
		</p>
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
