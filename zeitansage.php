<?php
/**
 * Plugin Name: 	Zeitansage
 * Plugin URI: 		http://www.gunnar-schmid.de/zeitansage
 * Description: 	A widget to display the current time as verbal expression
 * Author: 			Gunnar Schmid, plan.build.run
 * Version: 		1.0.0
 * Author URI: 		http://www.gunnar-schmid.de
 * License: 		GPLv2
 * Text domain:		zeitansage
 */

add_action( 'widgets_init', 'pbr_load_widget' );
$plugin_dir = basename( dirname( __FILE__) );
load_plugin_textdomain( 'zeitansage', null, $plugin_dir );

function pbr_load_widget() {
	register_widget( 'PBR_Zeitansage_Widget' );
}

class PBR_Zeitansage_Widget extends WP_Widget {
	public function __construct() {
		
		$widget_ops = array( 'description' => __( 'Displays the current time as verbal expression' , 'zeitansage') );
		parent::__construct( 'zeitansage', __( 'Zeitansage' , 'zeitansage'), $widget_ops );
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = $instance['title'];
		
		echo '<p>';
		echo '  <label for="' . $this->get_field_id( 'title' ) . '" >' . __( 'Title:' , 'zeitansage') . '</label>';
		echo '  <input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" value="' . esc_attr($title) . '" />';
		echo '</p>';
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '') );
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	public function widget( $args, $instance ) {
		extract( $args );
		
		echo $before_widget;
		
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Zeitansage' , 'zeitansage') : $instance['title'], $instance, $this->id_base);
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		echo '<div class="zeitansage">' . $this->get_time() . '</div>';
		
		echo $after_widget;
	}

	private function get_time() {
		// get current time
		$blogtime = current_time( 'mysql' ); 
		list( $today_year, $today_month, $today_day, $hour, $minute, $second ) = split( '([^0-9])', $blogtime );

		// round to 5-minute "precision"
		$minute = round( $minute / 5 ) * 5;
		
		// round up to next hour
		
		// TEMP: this should go into translations somehow
		$roundup_to_next_hour = 35;
		if ( substr( get_locale(), 0, 2 ) == 'de' ) {
			$roundup_to_next_hour = 25;
		}
		
		if ( $minute >= $roundup_to_next_hour ) {
			$hour++;
		}
		
		// create text out of the numeric values
		$hour_text = $this->get_hour_text( $hour );
		$minute_text = $this->get_minute_text( $minute );
		$oclock_text = empty( $minute_text ) ? ' ' . __( 'o\'clock' , 'zeitansage') : '';
		
		return sprintf( __( 'It\'s %1$s %2$s%3$s.' , 'zeitansage'), $minute_text, $hour_text, $oclock_text );
	}
	
	private function get_hour_text( $hour ) {
		// make sure we are in 12-hour format
		if ( $hour > 12 ) {
			$hour-=12;
		}
		
		// TODO: in English use "midnight" for 00:00/12 p. m.
		
		switch ( $hour ) {
			case 0:
			case 12:
				return __( 'twelve' , 'zeitansage');
			case 1:
				return __( 'one' , 'zeitansage');
			case 2:
				return __( 'two' , 'zeitansage');
			case 3:
				return __( 'three' , 'zeitansage');
			case 4:
				return __( 'four' , 'zeitansage');
			case 5:
				return __( 'five' , 'zeitansage');
			case 6:
				return __( 'six' , 'zeitansage');
			case 7:
				return __( 'seven' , 'zeitansage');
			case 8:
				return __( 'eight' , 'zeitansage');
			case 9:
				return __( 'nine' , 'zeitansage');
			case 10:
				return __( 'ten' , 'zeitansage');
			case 11:
				return __( 'eleven' , 'zeitansage');
			default:
				// should never be the case
				return __( 'witching hour' , 'zeitansage');
		}
	}
	
	private function get_minute_text( $minute ) {
		switch ( $minute ) {
			case 0:
			case 60:
				return ''; // note: no I18N for the empty string
			case 5:
				return __( 'five past', 'zeitansage');
			case 10:
				return __( 'ten past' , 'zeitansage');
			case 15:
				return __( 'quarter past' , 'zeitansage');
			case 20:
				return __( 'twenty past' , 'zeitansage');
			case 25:
				return __( 'twenty-five past' , 'zeitansage');
			case 30:
				return __( 'half past' , 'zeitansage');
			case 35:
				return __( 'twenty-five to' , 'zeitansage');
			case 40:
				return __( 'twenty to' , 'zeitansage');
			case 45:
				return __( 'quarter to' , 'zeitansage');
			case 50:
				return __( 'ten to' , 'zeitansage');
			case 55:
				return __( 'five to' , 'zeitansage');
			default:
				// should never be the case
				return __( 'about' , 'zeitansage');
		}
	}
}
?>