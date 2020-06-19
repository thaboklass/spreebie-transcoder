<?php
/*
 Plugin Name: Spreebie Transcoder
 Plugin URI: http://openbeacon.biz/spreebie-transcoder-video-transcoding-for-wordpress-with-ffmpeg/
 Description: A simple and intuitive plugin that allows WordPress administrators to resize video files, compress existing MP4 files using FFmpeg and store them locally and/or remotely (remote WordPress installation or Google Cloud Storage). This plugin performs trans-sizing, transrating but not converting. The administrator can then use the processed video on their site or download it locally for further use. To get started: 1) Click the "Activate" link, 2) Go to Spreebie Transcoder 3) Click the "Settings" tab and configure the plugin to your requirements and 4) Click the "Transcode" tab and start transcoding video.
 Author: Spreebie
 Version: 1.0.1
 Author URI: http://getspreebie.com/
*/


/*------------------------------------------------------------------*
 * Constants and dependencies
/*------------------------------------------------------------------*/

/**
 * Define constants
 * 
 */

define( 'SPREEBIE_TRANSCODER_VERSION', '1.0.1' );
define( 'SPREEBIE_TRANSCODER_ROOT' , dirname( __FILE__ ) );
define( 'SPREEBIE_TRANSCODER_FILE_PATH' , SPREEBIE_TRANSCODER_ROOT . '/' . basename( __FILE__ ) );
define( 'SPREEBIE_TRANSCODER_URL' , plugins_url( '/', __FILE__ ) );


/**
 * Include other plugin dependencies
 * 
 */

require SPREEBIE_TRANSCODER_ROOT . '/includes/spreebie-transcoder-central.php';
require SPREEBIE_TRANSCODER_ROOT . '/includes/spreebie-transcoder-upload.php';
require SPREEBIE_TRANSCODER_ROOT . '/includes/spreebie-transcoder-assistant.php';

/*------------------------------------------------------------------*
 * Prepare the plugin to function
/*------------------------------------------------------------------*/


/**
* Custom post type backend callback
*
* @param    none
* @return   none
*/

function spreebie_transcoded_media_post_type_init() {
    new spreebie_transcoder_upload();
}


/**
* Deactivation callback: removes assorted data
* that will be added in later versions of Spreebie
* Transcoder
*
* @param    none
* @return   none
*/

function spreebie_transcoder_deactivate() {
    // do nothing, not yet; at least not in version 1.0.1
}

// This initializes the custom post type, taxonomy and other relevant backend forms
add_action('init', 'spreebie_transcoded_media_post_type_init');

$spreebie_transcoder_central_1 = new spreebie_transcoder_central();

// This add a central page
add_action('admin_menu', array($spreebie_transcoder_central_1, 'spreebie_transcoder_add_menu_page'));

// This adds settings functionality to the settings page
add_action('admin_init', array($spreebie_transcoder_central_1, 'spreebie_transcoder_initialize_options'));

register_deactivation_hook(__FILE__, 'spreebie_transcoder_deactivate');
?>