<?php

/*------------------------------------------------------------------*
 * The 'spreebie_transcoder_assistant' class
/*------------------------------------------------------------------*/

if (!class_exists('spreebie_transcoder_assistant')) :

class spreebie_transcoder_assistant {
    
    /**
     * FFmpeg existence check
     *
     * This function checks whether or not FFmpeg is
     * installed on the server
     *
     * @param	$command: the command whose existence is being checked
     * @param	$insist_on_check: insist on check instead of getting the option
     * @return	none
     */ 
    public function spreebie_transcoder_command_exists_check($command, $insist_on_check = false) {
        $command = escapeshellarg($command);
        $ffmpeg_path = get_option('spreebie_transcoder_ffmpeg_path');

        // First validate the need to check - important in this context since we are running a shell
        // command on third party software.
        $check = true;
        if (!$insist_on_check) {
            $spreebie_transcoder_ffmpeg_exists = get_option('spreebie_transcoder_ffmpeg_exists', false) ? get_option('spreebie_transcoder_ffmpeg_exists', false) : false;
            
            if($spreebie_transcoder_ffmpeg_exists){
                $check = false;
            }
        } else {
            $check = true;
        }
        
        // This converts the video package with the plugin into an image at the 2 second
        // mark. If it works, that means that FFmpeg is correctly configured.
        if ($check):
            $extra = '-vframes 1 -ss 2 -f image2';
            $source = plugin_dir_path( __FILE__ ).'test/test_vid.mp4';
            if(file_exists($source . '.jpg'))
                unlink($source . '.jpg');
            
            $dir = plugin_dir_path( __FILE__ ) . 'test';
            
            $str = $ffmpeg_path . "ffmpeg -y -i " . $source . " " . $extra . " " . $source . '.jpg 2> out.txt';
            exec($str);
            
            if (file_exists($source . '.jpg')){
                update_option('spreebie_transcoder_ffmpeg_exists', true);
            } else {
                update_option('spreebie_transcoder_ffmpeg_exists', false);
            }
        endif;
    }
    
    /**
     * Check function existence
     *
     * This function whether or not the function 'exec' is
     * available on the server
     *
     * @param	$func: the function io the checked
     * @return	boolean $enabled
     */ 
    public function spreebie_transcoder_check_function($func){
        $enabled = false;
        if(function_exists($func)){
            if(!in_array($func, array_map('trim',explode(', ', ini_get('disable_functions'))))){
                $enabled = true;
            }
        }
        return $enabled;
    }
    
    
    /**
     * Check max upload size
     *
     * This function checks the maximum permissible
     * upload size based on php.ini settings
     *
     * @param	$type: the type of size measurement to be displayed
     * @return	upload size, text or string depending on type entered
     */ 
    static function spreebie_transcoder_max_upload_size($type = true) {
        $max_upload = (int) (ini_get('upload_max_filesize'));
        $max_post = (int) (ini_get('post_max_size'));
        $memory_limit = (int) (ini_get('memory_limit'));
        $upload_mb = min($max_upload, $max_post, $memory_limit);
        if($type)
                $upload_mb = $upload_mb.'MB';
        return $upload_mb;
    }
}

endif;
?>
