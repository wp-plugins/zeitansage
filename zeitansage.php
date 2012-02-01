<?php
/**
 * Plugin Name: 	Zeitansage
 * Plugin URI: 		http://www.gunnar-schmid.de/zeitansage
 * Description: 	A widget to display the current time as verbal expression.
 * Author: 			Gunnar Schmid
 * Version: 		0.2.0
 * Author URI: 		http://www.gunnar-schmid.de
 * License: 		GPLv2
 */

add_action( 'widgets_init', 'pbr_load_widget' );
$plugin_dir = basename( dirname( __FILE__) );
load_plugin_textdomain( 'zeitansage', null, $plugin_dir );

function pbr_load_widget() {
	register_widget( 'PBR_Zeitansage_Widget' );
}

class PBR_Zeitansage_Widget extends WP_Widget {
	public function __construct() {
		
		$widget_ops = array( 'description' => __( 'Ausgabe der Uhrzeit in Textform' ) );
		parent::__construct( 'zeitansage', __( 'Zeitansage' ), $widget_ops );
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = $instance['title'];
		
		echo '<p>';
		echo '  <label for="' . $this->get_field_id( 'title' ) . '" >' . __( 'Titel:' ) . '</label>';
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
		
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Zeitansage' ) : $instance['title'], $instance, $this->id_base);
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
		$oclock_text = empty( $minute_text ) ? ' ' . __( 'Uhr' ) : '';
		
		return sprintf( __( 'Es ist %1$s %2$s%3$s.' ), $minute_text, $hour_text, $oclock_text );
	}
	
	private function get_hour_text( $hour ) {
		// make sure we are in 12-hour format
		if ( $hour > 12 ) {
			$hour-=12;
		}
		
		switch ( $hour ) {
			case 0:
			case 12:
				return __( 'zwölf' );
			case 1:
				return __( 'eins' );
			case 2:
				return __( 'zwei' );
			case 3:
				return __( 'drei' );
			case 4:
				return __( 'vier' );
			case 5:
				return __( 'fünf' );
			case 6:
				return __( 'sechs' );
			case 7:
				return __( 'sieben' );
			case 8:
				return __( 'acht' );
			case 9:
				return __( 'neun' );
			case 10:
				return __( 'zehn' );
			case 11:
				return __( 'elf' );
			default:
				// should never be the case
				return __( 'Geisterstunde' );
		}
	}
	
	private function get_minute_text( $minute ) {
		switch ( $minute ) {
			case 0:
			case 60:
				return ''; // note: no I18N for the empty string
			case 5:
				return __( 'fünf nach');
			case 10:
				return __( 'zehn nach' );
			case 15:
				return __( 'viertel nach' );
			case 20:
				return __( 'zwanzig nach' );
			case 25:
				return __( 'fünf vor halb' );
			case 30:
				return __( 'halb' );
			case 35:
				return __( 'fünf nach halb' );
			case 40:
				return __( 'zwanzig vor' );
			case 45:
				return __( 'viertel vor' );
			case 50:
				return __( 'zehn vor' );
			case 55:
				return __( 'fünf vor' );
			default:
				// should never be the case
				return __( 'so ungefähr' );
		}
	}
}
?>