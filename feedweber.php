<?php
/*
Plugin Name: Feedweber
Plugin URI: http://www.installedforyou.com/wordpress/feedweber-plugin/
Description: Register readers for both your AWeber autoresponder and FeedBurner with one click
Version: 0.3
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

define ('FW_VNUM', '0.3');

define('FW_PLUGPATH',get_option('siteurl').'/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/');

    class feed_weber_widget extends WP_Widget {

        function feed_weber_widget(){

            parent::WP_Widget(false, $name = 'Feed Weber');

            $widget_ops = array( 'classname' => 'feed_weber_widget', 'description' => __( "Register for Feedburner and Aweber in one" ));

            $control_ops = array( 'width' => 250, 'height' => 300 );

            $this->WP_Widget( 'FeedWeber', __( 'Feed Weber' ), $widget_ops, $control_ops );

        }

        function update($new_instance, $old_instance) {

            $instance = $old_instance;

            if (is_email($new_instance['fwf_aweberemail'])) {
                    $old_instance['fwf_aweberemail'] = "List name only!";
                    return $old_instance;
            }

            /*
             * Save WIDGET title, feedburner URI and AWeber email field
             */
            $instance['title'] = wp_filter_post_kses($new_instance['title']);
            $instance['fwf_feedburnerURI'] = wp_filter_post_kses($new_instance['fwf_feedburnerURI']);
            $instance['fwf_aweberemail'] = wp_filter_post_kses($new_instance['fwf_aweberemail']);

            /*
             * Updated to strip html for users who aren't allowed to enter HTML
             * Although, I'm not sure how they got here.
             */
            if ( current_user_can('unfiltered_html') ) {
                $instance['fwf_beforetext'] =  $new_instance['fwf_beforetext'];
                $instance['fwf_aftertext'] = $new_instance['fwf_aftertext'];
            } else {
                $instance['fwf_beforetext'] = wp_filter_post_kses( $new_instance['fwf_beforetext'] );
                $instance['fwf_aftertext'] = wp_filter_post_kses( $new_instance['fwf_aftertext'] );
            }

            /*
             * Save form and field classes. Since these shouldn't contain HTML
             * let's clean the up just in case.
             */
                $instance['fwf_formclass'] = wp_filter_post_kses( $new_instance['fwf_formclass'] );
                $instance['fwf_fieldclass'] = wp_filter_post_kses( $new_instance['fwf_fieldclass'] );
            
            return $instance;
        }


        function widget( $args, $instance ) {
            extract ($args);

            $widgetTitle = $instance['title'];
            $feedburnerURI = $instance['fwf_feedburnerURI'];
            $aweberemail = $instance['fwf_aweberemail'];
            $fw_beforetext = $instance['fwf_beforetext'];
            $fw_aftertext = $instance['fwf_aftertext'];
            $fw_formclass = $instance['fwf_formclass'];
            $fw_fieldclass = $instance['fwf_fieldclass'];

?>
            <?php if (!is_null($instance['title'])) { ?>
            <h2 class="widgettitle"><?php _e( $widgetTitle ); ?></h2>
            <?php } ?>
            <?php _e($before_widget); ?>
            <form<?php if (!is_null($fw_formclass) && !($fw_formclass=="")) _e(' class="' . $fw_formclass .'"') ; ?> style="padding:3px;text-align:left;" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="emailAweber('<?php _e($aweberemail); ?>'); window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php _e($feedburnerURI); ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520'); return true;">
            <p><?php _e($fw_beforetext); ?></p>
            <p>Enter your email address:</p>
            <p>
                <input type="text" style="width:140px;" name="email" id="email" <?php if (!is_null($fw_fieldclass)) _e(' class="' . $fw_fieldclass .'"') ; ?>/>
            </p>
            <input type="hidden" value="<?php _e($feedburnerURI); ?>" name="uri"/>
            <input type="hidden" name="loc" value="en_US"/>
            <input type="submit" value="Subscribe" />
            <p><?php _e($fw_aftertext); ?></p>
            </form>
            <!-- <div id=resultarea>
            </div> -->
<?php
            echo $after_widget;
        }

        function form($instance) {

            /* Set up some default widget settings. */
            $defaults = array( 'title' => 'FeedWeber', 'fwf_feedburnerURI' => 'FeedburnerURI', 'fwf_aweberemail' => 'AWeber Listname' );
            $instance = wp_parse_args( (array) $instance, $defaults );

            //$feedburnerURI = $instance['fwf_feedburnerURI'];
            //$aweberemail = $instance['fwf_aweberemail'];
            //$fwf_beforetext = $instance['fwf_beforetext'];
            //$fwf_aftertext = $instance['fwf_aftertext'];

?>

            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><b>Title:</b> (optional)</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:95%;" />
            <label for="<?php echo $this->get_field_id( 'fwf_beforetext' ); ?>"><b>Text before form:</b></label>
			<textarea id="<?php echo $this->get_field_id( 'fwf_beforetext' ); ?>" name="<?php echo $this->get_field_name( 'fwf_beforetext' ); ?>" style="width:95%;" ><?php echo $instance['fwf_beforetext']; ?></textarea>

            <label for="<?php echo $this->get_field_id( 'fwf_feedburnerURI' ); ?>"><b>Feedburner Feed:</b></label>
			<input id="<?php echo $this->get_field_id( 'fwf_feedburnerURI' ); ?>" name="<?php echo $this->get_field_name( 'fwf_feedburnerURI' ); ?>" value="<?php echo $instance['fwf_feedburnerURI']; ?>" style="width:95%;" />
            <label for="<?php echo $this->get_field_id( 'fwf_aweberemail' ); ?>"><b>AWeber Listname:</b></label>
			<input id="<?php echo $this->get_field_id( 'fwf_aweberemail' ); ?>" name="<?php echo $this->get_field_name( 'fwf_aweberemail' ); ?>" value="<?php echo $instance['fwf_aweberemail']; ?>" style="width:95%;" />
            <label for="<?php echo $this->get_field_id( 'fwf_aftertext' ); ?>"><b>Text after form:</b></label>
			<textarea id="<?php echo $this->get_field_id( 'fwf_aftertext' ); ?>" name="<?php echo $this->get_field_name( 'fwf_aftertext' ); ?>" style="width:95%;" ><?php echo htmlspecialchars($instance['fwf_aftertext']); ?></textarea>
            <label for="<?php echo $this->get_field_id( 'fwf_formclass' ); ?>"><b>Form class name:</b> (optional)</label>
			<input id="<?php echo $this->get_field_id( 'fwf_formclass' ); ?>" name="<?php echo $this->get_field_name( 'fwf_formclass' ); ?>" value="<?php echo $instance['fwf_formclass']; ?>" style="width:95%;" />
            <label for="<?php echo $this->get_field_id( 'fwf_fieldclass' ); ?>"><b>Field class name:</b> (optional)</label>
			<input id="<?php echo $this->get_field_id( 'fwf_fieldclass' ); ?>" name="<?php echo $this->get_field_name( 'fwf_fieldclass' ); ?>" value="<?php echo $instance['fwf_fieldclass']; ?>" style="width:95%;" />
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

