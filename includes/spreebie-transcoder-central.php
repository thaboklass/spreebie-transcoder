<?php

/*------------------------------------------------------------------*
 * The 'spreebie_transcoder_central' class
/*------------------------------------------------------------------*/

if (!class_exists('spreebie_transcoder_central')) :

class spreebie_transcoder_central {
    public $spreebie_transcoder_assistant;
    public $spreebie_transcoder_ffmpeg_ext;
    public $spreebie_transcoder_ffmpeg_presets;
    
    // This hold a "boolean" indicating whether or not
    // an upload just occured in the latest refresh - that
    // is, whether or not the request contains any post data
    public $spreebie_transcoder_upload_occured;

    public $upload_function_called;

    // The AJAX data
    public $spreebie_transcoder_gcs_ajax_data;
       
    /**
    * The 'spreebie_transcoder_central' constructor
    * 
    */
    
    public function __construct() {
        $this->upload_function_called == false;

        // create new 'assistant' class
        $this->spreebie_transcoder_assistant = new spreebie_transcoder_assistant();
        
        //if (get_option('spreebie_transcoder_ffmpeg_exists') === FALSE) {
            $this->spreebie_transcoder_assistant->spreebie_transcoder_command_exists_check('ffmpeg', true);
        //}
        
        $this->spreebie_transcoder_ffmpeg_ext = get_option('spreebie_transcoder_ffmpeg_exists', 0);
        
        $this->spreebie_transcoder_ffmpeg_presets = array(
            "ultrafast",
            "superfast",
            "veryfast",
            "faster",
            "fast",
            "medium",
            "slow",
            "slower",
            "veryslow"
        );
        
        // add Spreebie Transcoder options
        $this->spreebie_transcoder_add_options();
        
        // if an upload has been made, the $spreebie_transcoder_upload_occured
        // variable will be set to 1 otherwise, it is set to 0
        // 
        $this->spreebie_transcoder_upload_occured = 0;
        if(isset($_FILES['spreebie_transcoder_vid'])) {
            $this->spreebie_transcoder_upload_occured = 1;
        }
        
        // This loads admin scripts
        add_action('admin_enqueue_scripts', array($this, 'spreebie_transcoder_load_admin_scripts'));
    }
    
    
    /*------------------------------------------------------------------*
     * Menus
    /*------------------------------------------------------------------*/
    
    /**
     * Adds 'Spreebie Transcoder Settings' menu item
     *
     * Adds the 'Basic Functionality' menu titled 'Spreebie Transcoder Settings'
     * as a top level menu item in the dashboard.
     *
     * @param	none
     * @return	none
    */
    
    public function spreebie_transcoder_add_menu_page() {
        
        // Introduces a top-level menu page
        add_menu_page(
            'Transcode Video',                        // The text that is displayed in the browser title bar
            __('Spreebie Transcoder'),                                // The text that is used for the top-level menu
            'manage_options',                                            // The user capability to access this menu
            'spreebie-transcoder_central',                                         // The name of the menu slug that accesses this menu item
            array($this, 'spreebie_transcoder_central_display'),                   // The name of the function used to display the page content
            '');
    } // end of function spreebie_transcoder_add_menu_page
    
    
    
    /*------------------------------------------------------------------*
     * Sections, Settings and Fields
    /*------------------------------------------------------------------*/
    
    /**
     * Register section, fields and page
     *
     * Registers a new settings section and settings fields on the
     * 'Spreebie Transcoder Settings' page of the WordPress dashboard.
     *
     * @param	none
     * @return	none
    */
    
    public function spreebie_transcoder_initialize_options() {
        // Introduce an new section that will be rendered on the new
        // settings page.  This section will be populated with settings
        // that will give the 'Spreebie Transcoder' plugin its basic
        // functionality
        add_settings_section(
            'spreebie_transcoder_functionality_settings_section',                                // The ID to use for this section
            'Functionality Settings',                                            // The title of this section that is rendered to the screen
            array($this, 'spreebie_transcoder_functionality_settings_section_display'),  // The function that is used to render the options for this section
            'spreebie-transcoder_central'                                         // The ID of the page on which the section is rendered
        );
        
        // Defines the settings field 'Maxim Video Size'
        // which controls the size of the video comment
        // a user can upload
        add_settings_field(
            'spreebie_transcoder_max_video_size',                                // The ID of the setting field
            'Maximum Video Size(MB) for WordPress',                              // The text to be displayed
            array($this, 'spreebie_transcoder_max_video_size_display'),  // The function used to render the setting field
            'spreebie-transcoder_central',                           // The ID of the page on which the setting field is rendered
            'spreebie_transcoder_functionality_settings_section'                    // The section to which the setting field belongs
        );
        
        // Register the 'spreebie_transcoder_max_video_size'
        // with the 'Functionality Options' section
        register_setting(
            'spreebie_transcoder_functionality_settings_section',  // The section holding the settings fields
            'spreebie_transcoder_max_video_size'                // The name of the settings field to register
        );
        
        // Simply displays the system environment
        add_settings_field(
            'spreebie_transcoder_system_environment_section',
            'FFmpeg',
            array($this, 'spreebie_transcoder_system_environment'),
            'spreebie-transcoder_central',
            'spreebie_transcoder_functionality_settings_section'
        );
        
        // Register the 'spreebie_transcoder_system_environment_section'
        // with the 'Functionality Options' section
        register_setting(
            'spreebie_transcoder_functionality_settings_section',
            'spreebie_transcoder_system_environment_section'
        );
        
        // Defines the settings field 'FFMpeg location'
        // which is the location where FFmpeg is located
        // of a remote host or server
        add_settings_field(
            'spreebie_transcoder_ffmpeg_path',
            '',
            array($this, 'spreebie_transcoder_ffmpeg_path'),
            'spreebie-transcoder_central',
            'spreebie_transcoder_functionality_settings_section'
        );
        
        // Register the 'spreebie_transcoder_ffmpeg_path'
        // with the 'Functionality Options' section
        register_setting(
            'spreebie_transcoder_functionality_settings_section',
            'spreebie_transcoder_ffmpeg_path'
        );

        // Defines the settings field 'FFprobe location'
        // which is the location where FFmpeg is located
        // of a remote host or server
        add_settings_field(
            'spreebie_transcoder_ffprobe_path',
            '',
            array($this, 'spreebie_transcoder_ffprobe_path'),
            'spreebie-transcoder_central',
            'spreebie_transcoder_functionality_settings_section'
        );
        
        // Register the 'spreebie_transcoder_ffprobe_path'
        // with the 'Functionality Options' section
        register_setting(
            'spreebie_transcoder_functionality_settings_section',
            'spreebie_transcoder_ffprobe_path'
        );
        
        // Defines the settings field 'FFMpeg Speed'
        // which is sets the conversion preset that
        // the speeed at which a video is encoded
        add_settings_field(
            'spreebie_transcoder_ffmpeg_speed',
            '',
            array($this, 'spreebie_transcoder_ffmpeg_speed'),
            'spreebie-transcoder_central',
            'spreebie_transcoder_functionality_settings_section'
        );
        
        // Register the 'spreebie_transcoder_ffmpeg_speed'
        // with the 'Functionality Options' section
        register_setting(
            'spreebie_transcoder_functionality_settings_section',
            'spreebie_transcoder_ffmpeg_speed'
        );
        
        // Defines the settings field 'FFmpeg Quality'
        // which is sets the quality of the transcoded
        // video
        add_settings_field(
            'spreebie_transcoder_ffmpeg_quality',
            '',
            array($this, 'spreebie_transcoder_ffmpeg_quality'),
            'spreebie-transcoder_central',
            'spreebie_transcoder_functionality_settings_section'
        );
        
        // Register the 'spreebie_transcoder_ffmpeg_quality'
        // with the 'Functionality Options' section
        register_setting(
            'spreebie_transcoder_functionality_settings_section',
            'spreebie_transcoder_ffmpeg_quality'
        );


        // Simply displays the system environment
        add_settings_field(
            'spreebie_transcoder_use_google_cloud_storage',
            'Google Cloud Storage',
            array($this, 'spreebie_transcoder_use_google_cloud_storage'),
            'spreebie-transcoder_central',
            'spreebie_transcoder_functionality_settings_section'
        );
        
        // Register the 'spreebie_transcoder_system_environment_section'
        // with the 'Functionality Options' section
        register_setting(
            'spreebie_transcoder_functionality_settings_section',
            'spreebie_transcoder_use_google_cloud_storage'
        );
        
        // Defines the settings field 'FFMpeg location'
        // which is the location where FFmpeg is located
        // of a remote host or server
        add_settings_field(
            'spreebie_transcoder_gcs_project_id',
            '',
            array($this, 'spreebie_transcoder_gcs_project_id'),
            'spreebie-transcoder_central',
            'spreebie_transcoder_functionality_settings_section'
        );
        
        // Register the 'spreebie_transcoder_gcs_project_id'
        // with the 'Functionality Options' section
        register_setting(
            'spreebie_transcoder_functionality_settings_section',
            'spreebie_transcoder_gcs_project_id'
        );

        // Defines the settings field 'FFprobe location'
        // which is the location where FFmpeg is located
        // of a remote host or server
        add_settings_field(
            'spreebie_transcoder_gcs_bucket',
            '',
            array($this, 'spreebie_transcoder_gcs_bucket'),
            'spreebie-transcoder_central',
            'spreebie_transcoder_functionality_settings_section'
        );
        
        // Register the 'spreebie_transcoder_gcs_bucket'
        // with the 'Functionality Options' section
        register_setting(
            'spreebie_transcoder_functionality_settings_section',
            'spreebie_transcoder_gcs_bucket'
        );

        // Defines the settings field 'FFprobe location'
        // which is the location where FFmpeg is located
        // of a remote host or server
        add_settings_field(
            'spreebie_transcoder_gcs_key_file_name',
            '',
            array($this, 'spreebie_transcoder_gcs_key_file_name'),
            'spreebie-transcoder_central',
            'spreebie_transcoder_functionality_settings_section'
        );
        
        // Register the 'spreebie_transcoder_gcs_key_file_name'
        // with the 'Functionality Options' section
        register_setting(
            'spreebie_transcoder_functionality_settings_section',
            'spreebie_transcoder_gcs_key_file_name'
        );
        
        // After the user interface elements have been rendered,
        // upload the video data if that has been requested.
        if ($this->upload_function_called == false) {
            $this->spreebie_transcoder_video_data_upload();
            $this->upload_function_called == true;
        }

        /// Send mail if there is any mail post data
        $this->spreebie_transcoder_send_error_email();
    } // end of function spreebie_transcoder_initialize_options
    
    
    
    /*------------------------------------------------------------------*
     * Callbacks
    /*------------------------------------------------------------------*/
    
    /**
     * This function is used to render all of the page content
     *
     * @param	none
     * @return	none
     */
    
    public function spreebie_transcoder_central_display($active_tab = '') {
    ?>
        <div class="wrap" id="spreebie_transcoder_main_content">
            <?php
                if(isset($_POST['spreebie_transcoder_upload_form_submitted'])) {
            ?>
            <div id="spreebie_transcoder_upload_complete">
                <p>Your video was transcoded sucessfully! Go to "Spreebie Transcoded Media->View..." to review and download the results.</p>
            </div>
            <?php
                }
            ?>
            <div id="icon-options-general" class="icon32"></div>
            <h2>Spreebie Transcoder</h2>
            <?php
            if(isset($_GET[ 'tab' ])) {
                $active_tab = $_GET['tab'];
            } else if($active_tab == 'settings') {
                $active_tab = 'settings';
            } else {
                $active_tab = 'central';
            } // end if/else
            ?>
            <h2 class="nav-tab-wrapper">
                <a href="?page=spreebie-transcoder_central&tab=central" class="nav-tab <?php echo $active_tab == 'central' ? 'nav-tab-active' : ''; ?>">Transcode</a>
                <a href="?page=spreebie-transcoder_central&tab=support" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>">Support</a>
                <a href="?page=spreebie-transcoder_central&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
            </h2>
            <?php 
            if($active_tab == 'central') {
                ?>
                <h3>Transode - Video Transcoding</h3>
                This is where the transcoding of video data happens. Click the 'Settings' tab to set up the environment before going any further.
                <form id="<?php if (get_option('spreebie_transcoder_ffmpeg_exists') == 1) { echo "spreebie_transcoder_upload_form"; } else { echo "spreebie_transcoder_upload_form_ffmpeg_not_found"; }?>" action="" method="post" enctype="multipart/form-data">
                    <?php echo wp_nonce_field('spreebie_transcoder_upload_form', 'spreebie_transcoder_upload_form_submitted'); ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Select a video to transcode:</th>
                            <td>
                                <input type="file" accept="video/*" name="spreebie_transcoder_vid" id="spreebie_transcoder_vid" capture><br>
                                <p>Pick a video from the MP4 file format. The video will be transcoder to one or more</p>
                                <p>lesser resolutions depending on the original video's resolution. Video quality can</p>
                                <p>be adjusted in 'Settings'.</p>
                            </td>
                        </tr>
                        <?php if (function_exists('wp_rml_dropdown')) { ?>
                        <tr>
                            <th scope="row">Pick your video's folder:</th>
                            <td>
                                <?php $this->spreebie_transcoder_folders_display(); ?>
                                <p>The folder you want to place your video in.</p>
                            </td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <th scope="row">Pick your video's category:</th>
                            <td>
                                <?php $this->spreebie_transcoder_categories_display(); ?>
                                <p>The category that best describes your video.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Write a short caption:</th>
                            <td>
                                <input type="text" name="spreebie_transcoder_caption" id="spreebie_transcoder_caption"><br>
                                <p>Make the caption short but relevant so that it will be easy to remember</p>
                                <p>the video when selecting it from the 'Spreebie Transcoded Media' list.</p>
                            </td>
                        </tr>
                    </table>
                    <input type="submit" class="button button-primary" name="spreebie_transcoder_submit" value="Transcode Video" id="spreebie_transcoder_submit">
                    <img src="<?php echo esc_attr(admin_url('/images/wpspin_light.gif')); ?>" id="spreebie_transcoder_loading" class="spreebie_transcoder_loading"/>
                </form>
                <div id="spreebie_transcoder_incomplete_dialog" title="Please fill all required fields!">
                    <p>It appears that you may have not filled one or more fields.  Please make sure the video and caption fields are filled. Also make sure that the correct video format has been selected.</p>
                </div>
                <div id="spreebie_transcoder_ffmpeg_not_found">
                    <p>FFmpeg was not found! Please go to the "Settings" tab and enter the correct FFmpeg path.</p>
                </div>
                <div id="spreebie_transcoder_transcoding" title="Please wait while transcoding happens!">
                    <p>Your video is being processed.  This may take a few minutes, please wait.</p>
                </div>
                <?php
            ?>
            <?php
            } else if($active_tab == 'support') {
                ?>
                <?php
                    if(isset($_POST['spreebie_transcoder_email_form_submitted']) && isset($_POST['spreebie_transcoder_error_from_email'])
                        && !empty($_POST['spreebie_transcoder_error_from_email']) && isset($_POST['spreebie_transcoder_error_description'])
                        && !empty($_POST['spreebie_transcoder_error_description'])) {

                        if(is_email($_POST['spreebie_transcoder_error_from_email'])) {
                ?>
                            <div id="spreebie_transcoder_upload_complete">
                                <p>Your support request has been sent. You will hear from a Spreebie representative shortly.</p>
                            </div>
                <?php
                        } else {
                ?>
                            <div id="spreebie_transcoder_email_invalid">
                                <p>The email you entered was invalid. Please try again.</p>
                            </div>
                <?php
                        }
                    }
                ?>
                <h3>Direct Support</h3>
				Are you having trouble with your FFmpeg setup? Are you encountering any problems related to using this plugin? Please reach out to us below:
				<p><b>Before you continue, please download the manual by clicking here: <a href="https://s3.amazonaws.com/spreebietranscoder/Spreebie_Transcoder_Quick_Start_Guide.zip">DOWNLOAD MANUAL</a></b></p>
                <form id="spreebie_transcoder_email_form" action="" method="post" enctype="multipart/form-data">
                    <?php echo wp_nonce_field('spreebie_transcoder_email_form', 'spreebie_transcoder_email_form_submitted'); ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Enter your email:</th>
                            <td>
                                <input type="text" name="spreebie_transcoder_error_from_email" id="spreebie_transcoder_error_from_email"><br>
								<p>Enter the email you want the Spreebie support team to reach you on.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Select the error stage:</th>
                            <td>
                                <?php $this->spreebie_transcoder_error_stages_display(); ?>
                                <p>This is the stage at which the error first started appearing.</p>
                                <p><b>Installation Stage:</b> This is when an error appears during installation.</p>
                                <p><b>Settings Stage:</b> This is when an error appears while configuring your settings.</p>
                                <p><b>General Usage Stage:</b> This is when an error appears while using the plugin.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Describe the problem:</th>
                            <td>
                                <textarea rows="4" cols="70" name="spreebie_transcoder_error_description" id="spreebie_transcoder_error_description"></textarea><br>
                                <p>Describe the exact nature of the problem you are experiencing in detail.</p>
                            </td>
                        </tr>
                    </table>
                    <input type="submit" class="button button-primary" name="spreebie_transcoder_submit" value="Send Message" id="spreebie_transcoder_submit">
                </form>
                <div id="spreebie_transcoder_support_fields_not_filled">
                    <p>It appears that you may have not filled one or more fields.  Please make sure the email and error description fields are filled. Also make sure that fields are filled correctly.</p>
                </div>
                <?php
            ?>
            <?php 
            } else {
            ?>
            <form id="spreebie_transcoder_save_changes_form" method="post" action="options.php">
            <?php
            
                // Outputs pertinent nonces, actions and options for
                // the section
                settings_fields('spreebie_transcoder_functionality_settings_section');
                
                // Renders the setting sections added to the page
                // 'Basic Fuctionality'
                do_settings_sections('spreebie-transcoder_central');
                
                // Renders a submit button that saves all of the options
                // pertaining to the settings fields
                submit_button();
            ?>

            </form>
            <div id="spreebie_transcoder_video_size_not_number" title="Please enter a number!">
                <p>It appears that you may have not entered a number. Please enter numbers only. Leave out the 'MB' part.</p>
            </div>
            <?php
            }
            ?>
        </div>
    <?php
    }
    
    
    /**
     * Inline 'Functionality Options' description
     *
     * Displays an explanation of the role of the 'Functionality
     * Options' section.
     *
     * @param	none
     * @return	none
     */
    
    public function spreebie_transcoder_functionality_settings_section_display() {
        echo esc_html("These options are designed to help you control the functionality of the Spreebie Transcoder.");
    }
    
    
    /**
     * Renders 'Maximum Video Size'
     *
     * Renders the input field for the 'Maximum Video
     * Size' setting in the 'Functionality Options'
     * section.
     *
     * @param	none
     * @return	none
     */
    
    public function spreebie_transcoder_max_video_size_display() {
    ?>
        <input type="number" name="spreebie_transcoder_max_video_size" id="spreebie_transcoder_max_video_size" value="<?php echo get_option('spreebie_transcoder_max_video_size'); ?>" />
        <p>The maximum size of the video that a user can upload when transcoding.</p>
        <p>According to your system environment, <b><?php echo spreebie_transcoder_assistant::spreebie_transcoder_max_upload_size(false); ?>MB</b> is the maximum size allowed. You can increase</p>
        <p>this figure by adjusting 'upload_max_filesize', 'post_max_size' and 'memory_limit' in your</p>
        <p>php.ini file.</p>
        <!--<div id="spreebie_transcoder_video_size_not_number" title="Please enter a number!">
            <p>It appears that you may have not entered a number. Please enter numbers only. Leave out the 'MB' part.</p>
        </div>-->
    <?php    
    } // end of spreebie_transcoder_max_video_size_display
    
    
    /**
     * Renders 'System Environment' section
     *
     * Renders the 'System Environment' section
     * setting in the 'Functionality Options' section.
     *
     * @param	none
     * @return	none
     */
    public function spreebie_transcoder_system_environment() {
        $exts = array(
            'FFMPEG'=>$this->spreebie_transcoder_ffmpeg_ext
        );
        ?>
            <h4><?php _e('System Environment');?></h4>
            <ul>
                <?php 
                if(is_array($_SERVER)):?>
                        <li><strong><?php _e('Server');?></strong> <span><?php echo $_SERVER['SERVER_SOFTWARE'];?></span></li>
                <?php
                endif;
                ?>
                <?php if(function_exists('phpversion')):?>
                <li><strong><?php _e('PHP');?></strong> <span><?php echo phpversion();?></span></li>
                <?php endif;?>
                <?php 
                    $exec_c = $this->spreebie_transcoder_assistant->spreebie_transcoder_check_function('exec');
                ?>
                <li><strong><?php _e('EXEC');?></strong> <span><?php if($exec_c){ echo '<span class="spreebie_transcoder_true">ENABLED</span>'; } else { echo '<span class="spreebie_transcoder_false">DISABLED</span>'; }?></span></li>
                <?php 
                foreach($exts as $k=>$ext):?>
                        <li><strong><?php echo $k;?></strong> <span class="<?php echo strtolower($k);?>"><?php if($ext) { echo '<span class="spreebie_transcoder_true">FOUND</span>'; } else { echo '<span class="spreebie_transcoder_false">NOT FOUND</span>'; }?></span></li>
                <?php endforeach; ?>
            </ul>
            <!--<input type="button" value="Re-check FFMPEG" class="recheckExt" />-->
            
        <?php    
        } // end of spreebie_transcoder_system_environment


    /**
     * Renders 'System Environment' section
     *
     * Renders the 'System Environment' section
     * setting in the 'Functionality Options' section.
     *
     * @param	none
     * @return	none
     */
    public function spreebie_transcoder_use_google_cloud_storage() {
        $checked = "";
        
        if (get_option('spreebie_transcoder_use_google_cloud_storage') == "1")
            $checked = 'checked = "checked"';
    ?>
        <label for="spreebie_transcoder_use_google_cloud_storage">
            <input type="checkbox" name="spreebie_transcoder_use_google_cloud_storage" id="spreebie_transcoder_use_google_cloud_storage" value="1" <?php echo $checked; ?> />
        </label>
        Would you like to enable Google Cloud Storage?</p>
        <b>If this is enabled, the transcoded media can be stored on GCS.</b><br/>  
    <?php     
    } // end of spreebie_transcoder_use_google_cloud_storage
    
    /**
     * Renders 'FFmpeg Path' section
     *
     * Renders the 'FFmpeg path' section
     * setting in the 'Functionality Options' section.
     *
     * @param	none
     * @return	none
     */
    public function spreebie_transcoder_ffmpeg_path() {
    ?>
        <b>If FFmpeg was not automatically found, enter the path to your FFmpeg on</b><br/>
        <b>your remote host or server in the field below and click the 'Save' button</b><br/>
        <b>at the bottom. If 'EXEC' is not enabled or 'FFMPEG' is still not found after</b><br/>
        <b>enter the path below, FFmpeg transcoding will not work.</b><br/><br/>
        Path to FFmpeg installation: <input type="text" name="spreebie_transcoder_ffmpeg_path" id="spreebie_transcoder_ffmpeg_path" value="<?php echo esc_attr(get_option('spreebie_transcoder_ffmpeg_path')); ?>" />
        <?php _e("(example: /usr/local/bin/ or c:/wamp/www/ffmpeg_folder/)"); ?> 
    <?php    
    } // end of spreebie_transcoder_ffmpeg_path


    /**
     * Renders 'FFmpeg Path' section
     *
     * Renders the 'FFmpeg path' section
     * setting in the 'Functionality Options' section.
     *
     * @param	none
     * @return	none
     */
    public function spreebie_transcoder_gcs_project_id() {
        ?>
            GCS project ID: <input type="text" name="spreebie_transcoder_gcs_project_id" id="spreebie_transcoder_gcs_project_id" value="<?php echo esc_attr(get_option('spreebie_transcoder_gcs_project_id')); ?>" />
            <?php _e("(example: spreebie-test)"); ?>
            <br/>
            <b>This is the project ID you have give you project - no quotes.</b><br/>
        <?php    
        } // end of spreebie_transcoder_gcs_project_id


    /**
     * Renders 'FFprobe Path' section
     *
     * Renders the 'FFprobe path' section
     * setting in the 'Functionality Options' section.
     *
     * @param	none
     * @return	none
     */
    public function spreebie_transcoder_ffprobe_path() {
        ?>
            <b>If FFmpeg was not automatically found, enter the path to your FFprobe on</b><br/>
            <b>your remote host or server in the field below and click the 'Save' button</b><br/>
            <b>at the bottom. If 'EXEC' is not enabled or 'FFMPEG' is still not found after</b><br/>
            <b>enter the path below, FFprobe resolution detection will not work.</b><br/><br/>
            Path to FFprobe installation: <input type="text" name="spreebie_transcoder_ffprobe_path" id="spreebie_transcoder_ffprobe_path" value="<?php echo esc_attr(get_option('spreebie_transcoder_ffprobe_path')); ?>" />
            <?php _e("(example: /usr/local/bin/ or c:/wamp/www/ffprobe_folder/)"); ?>
        <?php    
    } // end of spreebie_transcoder_ffprobe_path


    /**
     * Renders 'FFprobe Path' section
     *
     * Renders the 'FFprobe path' section
     * setting in the 'Functionality Options' section.
     *
     * @param	none
     * @return	none
     */
    public function spreebie_transcoder_gcs_bucket() {
        ?>
            GCS bucket: <input type="text" name="spreebie_transcoder_gcs_bucket" id="spreebie_transcoder_gcs_bucket" value="<?php echo esc_attr(get_option('spreebie_transcoder_gcs_bucket')); ?>" />
            <?php _e("(example: spreebie-test-media)"); ?>
            <br/>
            <b>This is the Google Cloud Storage bucket you want to use - no quotes.</b><br/>
        <?php    
    } // end of spreebie_transcoder_gcs_bucket


    /**
     * Renders 'FFprobe Path' section
     *
     * Renders the 'FFprobe path' section
     * setting in the 'Functionality Options' section.
     *
     * @param	none
     * @return	none
     */
    public function spreebie_transcoder_gcs_key_file_name() {
        ?>
            GCS key file name: <input type="text" name="spreebie_transcoder_gcs_key_file_name" id="spreebie_transcoder_gcs_key_file_name" value="<?php echo esc_attr(get_option('spreebie_transcoder_gcs_key_file_name')); ?>" />
            <?php _e("(example: spreebie-test-523382d2233b.json)"); ?>
            <br/>
            <b>This is the Google Cloud Storage key file name you want to use - no quotes.</b><br/>
        <?php    
    } // end of spreebie_transcoder_gcs_key_file_name


    /**
     * Renders 'Bundled FFmpeg setting'
     *
     * Renders the 'Use Bundled FFmpeg' section
     * setting in the 'Functionality Options' section.
     *
     * @param   none
     * @return  none
     */
    
    public function spreebie_transcoder_use_bundled_ffmpeg() {
        $checked = "";
        
        if (get_option('spreebie_transcoder_use_bundled_ffmpeg') == "1")
            $checked = 'checked = "checked"';
    ?>
        <label for="spreebie_transcoder_use_bundled_ffmpeg">
            <input type="checkbox" name="spreebie_transcoder_use_bundled_ffmpeg" id="spreebie_transcoder_use_bundled_ffmpeg" value="1" <?php echo $checked; ?> />
        </label>
        <b>Would you like to use the FFmpeg bundled with the plugin?</b>
    <?php  
    } // end of spreebie_transcoder_use_bundled_ffmpeg
    
    
    /**
     * Renders 'Unix or Windows'
     *
     * Renders the input field for the 'Unix or Windows'
     * setting in the 'Functionality Options' section.
     *
     * @param   none
     * @return  none
     */
    
    public function spreebie_transcoder_unix_or_windows() {
        $unix_checked = "";
        $windows_checked = "";
        
        if (get_option('spreebie_transcoder_unix_or_windows') == "1")
            $unix_checked = 'checked = "checked"';
        
        if (get_option('spreebie_transcoder_unix_or_windows') == "2")
            $windows_checked = 'checked = "checked"';
        
    ?>
        <input type="radio" value="1" <?php echo $unix_checked; ?> name="spreebie_transcoder_unix_or_windows" id="spreebie_transcoder_unix_or_windows" /><span>Bundled for Unix</span>  <input type="radio" value="2" <?php echo $windows_checked; ?> name="spreebie_transcoder_unix_or_windows" id="spreebie_transcoder_unix_or_windows" /><span>Bundled for Windows</span>
        <!--<p>This orders that way in which the comments are presented - either by latest or first.</p>-->
    <?php    
    } // end of spreebie_transcoder_unix_or_windows
    
    /**
     * Renders 'FFmpeg Speed' section
     *
     * Renders the 'FFmpeg speed' section
     * setting in the 'Functionality Options' section.
     *
     * @param	none
     * @return	none
     */
    public function spreebie_transcoder_ffmpeg_speed() {
    ?>
        FFmpeg processing speed: 
        <select name="spreebie_transcoder_ffmpeg_speed">
            <?php
                for ($i = 0; $i < count($this->spreebie_transcoder_ffmpeg_presets); $i++) {
            ?>
            <option value="<?php echo $this->spreebie_transcoder_ffmpeg_presets[$i]; ?>" <?php selected(get_option('spreebie_transcoder_ffmpeg_speed'), $this->spreebie_transcoder_ffmpeg_presets[$i]); ?>><?php echo $this->spreebie_transcoder_ffmpeg_presets[$i]; ?></option>
            <?php
                }
            ?>
        </select><br/>
        <?php _e("This is the speed at which FFmpeg processing happens. Slower speeds result in"); ?><br/>
        <?php _e("better compression."); ?> 
    <?php    
    } // end of spreebie_transcoder_ffmpeg_speed
    
    /**
     * Renders 'FFmpeg Quality' section
     *
     * Renders the 'FFmpeg quality' section
     * setting in the 'Functionality Options' section.
     *
     * @param	none
     * @return	none
     */
    public function spreebie_transcoder_ffmpeg_quality() {
    ?>
        FFmpeg video quality: 
        <select name="spreebie_transcoder_ffmpeg_quality">
            <?php
                for ($i = 21; $i < 52; $i++) {
            ?>
            <option value="<?php echo $i; ?>" <?php selected(get_option('spreebie_transcoder_ffmpeg_quality'), $i); ?>><?php echo $i . ""; ?></option>
            <?php
                }
            ?>
        </select><br/>
        <?php _e("21 is the best video quality and 51 is the worst."); ?> 
    <?php    
    } // end of spreebie_transcoder_ffmpeg_quality
    
    public function spreebie_transcoder_folders_display() {
    ?>
    </label><?php echo $this->spreebie_transcoder_get_folder_dropdown(); ?><br/>
    <?php
    }
    
    /**
    * Taxonomy drop-down list on front-end widget 
    */
    
    function spreebie_transcoder_get_folder_dropdown() {
        ?>
        <select  name='spreebie_transcoder_folder' id='spreebie_transcoder_folder' class='postform' >
        <?php
        $count = 0;
        echo wp_rml_dropdown();
        ?>
        </select>
        <?php
    }


    public function spreebie_transcoder_categories_display() {
    ?>
    </label><?php echo $this->spreebie_transcoder_get_category_dropdown('spreebie_transcoder_category', 0); ?><br/>
    <?php
    }
    
    /**
    * Taxonomy drop-down list on front-end widget
    *
    * @param $taxonomy     The taxanomy to be used to retrieve data.
    * @param $selected     Which item is to be selected on the list   
    */
    
    function spreebie_transcoder_get_category_dropdown($taxonomy, $selected){
        return wp_dropdown_categories(array('taxonomy' => $taxonomy, 'name' => 'spreebie_transcoder_category', 'selected' => $selected, 'hide_empty' => 0, 'echo' => 0));
    }


    public function spreebie_transcoder_error_stages_display() {
    ?>
    </label><?php echo $this->spreebie_transcoder_get_error_stage_dropdown('spreebie_transcoder_error_stage', 0); ?><br/>
    <?php
    }
    
    /**
    * Taxonomy drop-down list on front-end widget
    *
    * @param $taxonomy     The taxanomy to be used to retrieve data.
    * @param $selected     Which item is to be selected on the list   
    */
    
    function spreebie_transcoder_get_error_stage_dropdown($taxonomy, $selected){
        return wp_dropdown_categories(array('taxonomy' => $taxonomy, 'name' => 'spreebie_transcoder_error_stage', 'selected' => $selected, 'hide_empty' => 0, 'echo' => 0));
    }
    
    
    /**
    * Upload the content provided by the visitor to create the
    * video comment
    *
    * @param    none
    * @return   none
    */

    function spreebie_transcoder_video_data_upload() {
        // If the $_POST data and nonce are set, upload the data
        // within the video comment inputs
        if(isset($_POST['spreebie_transcoder_upload_form_submitted']) && wp_verify_nonce($_POST['spreebie_transcoder_upload_form_submitted'], 'spreebie_transcoder_upload_form')) {
            // sanitize the caption
            $spreebie_transcoder_sanitized_caption = sanitize_text_field($_POST['spreebie_transcoder_caption']);
            
            // This parses through any possible errors of the input
            // data and returns a description of the error if it
            // exists.
            
            $result = spreebie_transcoder_upload::spreebie_transcoder_parse_file_errors($_FILES['spreebie_transcoder_vid'], $spreebie_transcoder_sanitized_caption);
            
            if($result['error']){
             
                echo '<p>WHOOPS: ' . $result['error'] . '</p>';
             
            } else { // if no errors were present, the upload continues
                $spreebie_transcoder_visitor_id = get_current_user_id();
                
                $video_cc_data = array(
                  'post_title' => $result['caption'],
                  'post_status' => 'publish',
                  'post_author' => $spreebie_transcoder_visitor_id,
                  'post_type' => 'spreebie_t_m'    
                );
                 
                if ($spreebie_transcoder_post_id = wp_insert_post($video_cc_data)) {
                    // This uploads the video, processes it, creates a thumbnail, inserts the caption and parent post id
                    if (function_exists('wp_rml_dropdown')) {
                        spreebie_transcoder_upload::spreebie_transcoder_process_everything('spreebie_transcoder_vid', $spreebie_transcoder_post_id, $result['caption'], get_option('spreebie_transcoder_ffmpeg_path'), get_option('spreebie_transcoder_ffprobe_path'), intval((int)$_POST['spreebie_transcoder_folder']));
                    } else {
                        spreebie_transcoder_upload::spreebie_transcoder_process_everything('spreebie_transcoder_vid', $spreebie_transcoder_post_id, $result['caption'], get_option('spreebie_transcoder_ffmpeg_path'), get_option('spreebie_transcoder_ffprobe_path'), -1);
                    }
                    
                    // This refreshes the custom post type and taxonomy
                    spreebie_transcoder_upload::spreebie_transcoder_post_type_and_taxonomy_init();
                  
                    // This adds one term out of the taxonomy (one region)
                    // to the video comment. This is data viewed on the
                    // backend by an administrator
                    $term_taxonomy_ids = wp_set_object_terms($spreebie_transcoder_post_id, (int)$_POST['spreebie_transcoder_category'], 'spreebie_transcoder_category');
                  
                    if (is_wp_error($term_taxonomy_ids)) {
                        echo esc_html('<p>WHOOPS: There was an error assigning a region to the comment</p>');
                        var_dump($term_taxonomy_ids);
                    }
                }
            }
        }
    }

    
    /**
    * Add options for new activation
    *
    * This checks whether or not backend options that define the basic
    * functionality have been added and if not, they are added
    * with what have been determined as the most efficient defaults
    *
    * @param	none
    * @return	none
   */
   
    public function spreebie_transcoder_add_options() {
       if (!get_option('spreebie_transcoder_max_video_size')) {
           $max_size = spreebie_transcoder_assistant::spreebie_transcoder_max_upload_size(false);
           $max_size_string = $max_size . "";
           add_option('spreebie_transcoder_ffmpeg_path');
           add_option('spreebie_transcoder_ffprobe_path');
           add_option('spreebie_transcoder_ffmpeg_quality', '32');
           add_option('spreebie_transcoder_ffmpeg_speed', 'ultrafast');
           add_option('spreebie_transcoder_max_video_size', $max_size_string);
           add_option('spreebie_transcoder_system_environment_section');
       }
    }
    
    /**
    * Load scripts
    *
    * Load all relevant styles and scripts - in this case we load just
    * one stylesheet and two javascript files
    *
    * @param	none
    * @return	none
   */
   
    public function spreebie_transcoder_load_admin_scripts() {
        wp_register_style('spreebie_transcoder_admin_css', plugins_url('../css/admin.css', __FILE__));
        wp_register_script('spreebie_transcoder_after_upload', plugins_url('../js/spreebie-transcoder-form-validation.js', __FILE__), array('jquery'), null, true);     

        // Pass the ajax data to the javascript
        $this->spreebie_transcoder_gcs_ajax_data = array(
            'spreebie_transcoder_google_storage_processing_results_nonce' => wp_create_nonce('spreebie_transcoder_google_storage_processing_results')
        );

        // Pass the PHP parameters to Javascript by localizing them
        wp_localize_script('spreebie_transcoder_after_upload', 'spreebie_transcoder_gcs_ajax_data', $this->spreebie_transcoder_gcs_ajax_data);

        // Get the address of this WP installation's 'admin-ajax.php'
        $spreebie_transcoder_ajax_url = admin_url('admin-ajax.php');
            
        // The ajax_url parameter being passed to the ajax handler
        $spreebie_transcoder_ajax_url = array(
            'spreebie_transcoder_ajax_url' => $spreebie_transcoder_ajax_url
        );

        // Pass the PHP parameter to Javascript by localizing it
        wp_localize_script('spreebie_transcoder_after_upload', 'spreebie_transcoder_gcs_ajax_params', $spreebie_transcoder_ajax_url);
        
        wp_enqueue_style('spreebie_transcoder_admin_css');
        wp_enqueue_script('spreebie_transcoder_after_upload');
    }


    /**
    * Sends an error email to the Spreebie support team
    *
    * @param    none
    * @return   none
    */

    function spreebie_transcoder_send_error_email() {
        // If the $_POST data and nonce are set, upload the data
        // within the error inputs
        if(isset($_POST['spreebie_transcoder_email_form_submitted']) && wp_verify_nonce($_POST['spreebie_transcoder_email_form_submitted'], 'spreebie_transcoder_email_form')) {
            // the 'from' email
            
            if (isset($_POST['spreebie_transcoder_error_from_email']) && !empty($_POST['spreebie_transcoder_error_from_email'])
                && isset($_POST['spreebie_transcoder_error_description']) && !empty($_POST['spreebie_transcoder_error_description'])) {
                
                $spreebie_transcoder_from_email = $_POST['spreebie_transcoder_error_from_email'];
                $spreebie_transcoder_from_email = sanitize_email($spreebie_transcoder_from_email);

                // the 'to' email
                $spreebie_transcoder_to_email = "thabo@openbeacon.biz";

                $validated_stage = intval((int)$_POST['spreebie_transcoder_error_stage']);
                $stage = $validated_stage;
                $term = get_term($stage);

                $stage_description = $term->name;

                $sanitized_spreebie_transcoder_error_description = sanitize_text_field($_POST['spreebie_transcoder_error_description']);
                $error_description = $sanitized_spreebie_transcoder_error_description;

                $stage_description = $stage_description . " (" . $spreebie_transcoder_from_email . ")";
                $stage_description = sanitize_text_field($stage_description);

                $message = $error_description . " - respond to: " . $spreebie_transcoder_from_email;
                wp_mail($spreebie_transcoder_to_email, $stage_description, $message);
            }
        }
	}
}

endif;
?>