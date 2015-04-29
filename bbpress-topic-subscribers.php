<?php
/*
Plugin Name: bbPress Topic Subscribers
Plugin URI: https://github.com/easydigitaldownloads/bbPress-Topic-Subscribers
Description: Shows people subscribed to a topic and lets admins remove them as a sidebar widget
Author: Chris Christoff
Version: 1.0
Author URI: http://www.chriscct7.com
*/

/**
 * Adds bbPress_Topic_Subscribers widget.
 */
class bbPress_Topic_Subscribers extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'bbpress_topic_subscribers_widget',
			__( 'bbPress Topic Subscribers', 'bbpress_topic_subscribers' ),
			array( 'description' => __( 'bbPress Topic Subscribers', 'bbpress_topic_subscribers' ), )
		);
	}

	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		
		if ( !function_exists( 'bbp_get_topic_id' ) ){
			return;
		}

		$topic_id = bbp_get_topic_id();
		
		if ( $topic_id && current_user_can( 'moderate' ) ){
			$subscribers = bbp_get_topic_subscribers( $topic_id );
			if ( $subscribers ){
				echo '<table style="width: 100%">';
				foreach ( $subscribers as $subscriber ){
					$subscriber = get_user_by( 'id', $subscriber );
					echo '<tr>';
						// avatar
						echo get_avatar( $subscriber->ID, 45 );
						echo ' ';
						//username
						echo $subscriber->user_login;
						echo ' ';
						// remove button
						echo bbp_get_topic_subscription_link(
							array(
								'user_id'     => $subscriber->ID
							)
						);
					echo '</tr>';
				}
				echo '</table>';

			}
			else{
				echo __( 'No subscribers!', 'bbpress_topic_subscribers' );
			}
		}

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Topic Subscribers:', 'bbpress_topic_subscribers' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class bbPress_Topic_Subscribers
function register_bbpress_topic_subscribers_widget() {
    register_widget( 'bbPress_Topic_Subscribers' );
}
add_action( 'widgets_init', 'register_bbpress_topic_subscribers_widget' );
