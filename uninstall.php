<?php
// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

// delete options from options table
delete_option('spreebie_transcoder_ffmpeg_path');
delete_option('spreebie_transcoder_ffmpeg_quality');
delete_option('spreebie_transcoder_ffmpeg_speed');
delete_option('spreebie_transcoder_max_video_size');
delete_option('spreebie_transcoder_system_environment_section');
delete_option('spreebie_transcoder_ffmpeg_exists'); 
delete_option('spreebie_transcoder_category_children');
delete_option('spreebie_transcoder_error_stage_children');
delete_option('spreebie_transcoder_ffprobe_path');
delete_option('spreebie_transcoder_gcs_bucket');
delete_option('spreebie_transcoder_gcs_key_file_name');
delete_option('spreebie_transcoder_gcs_project_id');
delete_option('spreebie_transcoder_use_google_cloud_storage');
?>