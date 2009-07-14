<?php
/*
Plugin Name: Feedweber
Plugin URI: http://www.installedforyou.com/feedweber/
Description: Register readers for both your AWeber autoresponder and FeedBurner with one click
Version: 0.1
Author: Jeff Rose
Author URI: http://www.installedforyou.com
*/

/*  Copyright 2009  Jeff Rose  (email : plugins@installedforyou.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define ('FW_VNUM', '0.1');

define('FW_PLUGPATH',get_option('siteurl').'/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/');

    class feed_weber_widget extends WP_Widget {

        function feed_weber_widget(){

            parent::WP_Widget(false, $name = 'Feed Weber');

            $widget_ops = array( 'classname' => 'feed_weber_widget', 'description' => __( "Register for Feedburner and Aweber in one" ));

            $control_ops = array( 'width' => 350, 'height' => 300 );

            $this->WP_Widget( 'FeedWeber', __( 'Feed Weber' ), $widget_ops, $control_ops );

        }

        function update($new_instance, $old_instance) {

            $instance = $old_instance;

            if (is_email($new_instance['fwf_aweberemail'])) {
                    $old_instance['fwf_aweberemail'] = "List name only!";
                    return $old_instance;
            }

            $instance['title'] = $new_instance['title'];
            $instance['fwf_feedburnerURI'] = $new_instance['fwf_feedburnerURI'];
            $instance['fwf_aweberemail'] = $new_instance['fwf_aweberemail'];
            $instance['fwf_beforetext'] = $new_instance['fwf_beforetext'];
            $instance['fwf_aftertext'] = $new_instance['fwf_aftertext'];

            return $instance;
        }


        function widget( $args, $instance ) {
            extract ($args);

            $feedburnerURI = $instance['fwf_feedburnerURI'];
            $aweberemail = $instance['fwf_aweberemail'];
            $fw_beforetext = $instance['fwf_beforetext'];
            $fw_aftertext = $instance['fwf_aftertext'];

            echo '<h2 class="widgettitle">' . $instance['title'] . '</h2>';
            echo $before_widget;
            echo '<form style="border:1px solid #ccc;padding:3px;text-align:left;" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="emailAweber(\'' . $aweberemail .'\');window.open(\'http://feedburner.google.com/fb/a/mailverify?uri='.$feedburnerURI.'\', \'popupwindow\', \'scrollbars=yes,width=550,height=520\');return true">';
            echo '<p>' . $fw_beforetext . '</p>';
            echo '    <p>Enter your email address:</p>';
            echo '    <p><input type="text" style="width:140px" name="email" id="email"/></p>';
            echo '    <input type="hidden" value="InstalledForYou" name="uri"/>';
            echo '    <input type="hidden" name="loc" value="en_US"/>';
            echo '    <input type="submit" value="Subscribe" />';
            echo '<p>' . $fw_aftertext . '</p>';
            echo '</form>            ';
            echo '<div id=resultarea>';
            echo '</div>';

            echo $after_widget;
        }

        function form($instance) {

            /* Set up some default widget settings. */
            $defaults = array( 'title' => 'FeedWeber', 'fwf_feedburnerURI' => 'FeedburnerURI', 'fwf_aweberemail' => 'AWeber Listname' );
            $instance = wp_parse_args( (array) $instance, $defaults );

            $feedburnerURI = $instance['fwf_feedburnerURI'];
            $aweberemail = $instance['fwf_aweberemail'];
            $fwf_beforetext = $instance['fwf_beforetext'];
            $fwf_aftertext = $instance['fwf_aftertext'];

?>

            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><b>Title:</b></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
            <label for="<?php echo $this->get_field_id( 'fwf_beforetext' ); ?>"><b>Text before form:</b></label>
			<input id="<?php echo $this->get_field_id( 'fwf_beforetext' ); ?>" name="<?php echo $this->get_field_name( 'fwf_beforetext' ); ?>" value="<?php echo $instance['fwf_beforetext']; ?>" style="width:100%;" />

            <label for="<?php echo $this->get_field_id( 'fwf_feedburnerURI' ); ?>"><b>Feedburner Feed:</b></label>
			<input id="<?php echo $this->get_field_id( 'fwf_feedburnerURI' ); ?>" name="<?php echo $this->get_field_name( 'fwf_feedburnerURI' ); ?>" value="<?php echo $instance['fwf_feedburnerURI']; ?>" style="width:100%;" />
            <label for="<?php echo $this->get_field_id( 'fwf_aweberemail' ); ?>"><b>AWeber Listname:</b></label>
			<input id="<?php echo $this->get_field_id( 'fwf_aweberemail' ); ?>" name="<?php echo $this->get_field_name( 'fwf_aweberemail' ); ?>" value="<?php echo $instance['fwf_aweberemail']; ?>" style="width:100%;" />
            <label for="<?php echo $this->get_field_id( 'fwf_aftertext' ); ?>"><b>Text after form:</b></label>
			<input id="<?php echo $this->get_field_id( 'fwf_aftertext' ); ?>" name="<?php echo $this->get_field_name( 'fwf_aftertext' ); ?>" value="<?php echo $instance['fwf_aftertext']; ?>" style="width:100%;" />

<?php 
        }
        
    } // End class

function feed_weber_widget_init() {

    register_widget( 'feed_weber_widget' );

}

function register_feed_weber_options_group(){

	register_setting( 'feed_weber_options_group', 'feed_weber_options' );

}


function fw_outputheader() {
    wp_register_script('feedweber', FW_PLUGPATH. 'feedweber.js', array('jquery'), FW_VNUM);
    wp_print_scripts('feedweber');
}

// hook in options register functions

if ( is_admin() ){

	//add_action( 'admin_menu', 'grouped_links_widget_options_register');

	add_action( 'admin_init', 'register_feed_weber_options_group' );

}

add_action( 'widgets_init', 'feed_weber_widget_init' );
add_action('wp_head', 'fw_outputheader');

