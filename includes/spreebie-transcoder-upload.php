<?php

# Imports the Google Cloud client library
use Google\Cloud\Storage\StorageClient;

/*------------------------------------------------------------------*
 * The 'spreebie_transcoder_upload' class
/*------------------------------------------------------------------*/

if(!class_exists('spreebie_transcoder_upload')) :

    class spreebie_transcoder_upload {
	// Taxonomy name for custom taxonomy metabox
	public $spreebie_transcoder_taxonomy;
	
	// Taxonomy id for custom taxonomy metabox
	public $spreebie_transcoder_taxonomy_metabox_id;
	
	// Post type text for custom taxonomy metabox
	public $spreebie_transcoder_post_type;

	// The AJAX data
    public $spreebie_transcoder_gcs_ajax_data;
	
	
	/**
	* The 'spreebie_transcoder_upload' constructor
	* 
	*/
	
	public function __construct() {
	    // Start the static function to initialize the custom post type
	    // and custom taxonomy
	    self::spreebie_transcoder_post_type_and_taxonomy_init();
	    
	    // The video concomp and caption metaboxes
	    $this->spreebie_transcoder_video_metaboxes();
	    
	    $this->spreebie_transcoder_taxonomy = 'spreebie_transcoder_category';
	    $this->spreebie_transcoder_taxonomy_metabox_id = 'spreebie_transcoder_categorydiv';
	    $this->spreebie_transcoder_post_type = 'spreebie_t_m';
	    
	    // Remove old taxonomy meta box  
	    add_action( 'admin_menu', array($this, 'spreebie_transcoder_remove_meta_box'));
	    
	    // Add new screenshot meta box  
	    add_action('add_meta_boxes', array($this, 'spreebie_transcoder_add_screenshot_meta_box'));
	    
	    // Add new taxonomy meta box  
	    add_action('add_meta_boxes', array($this, 'spreebie_transcoder_add_taxonomy_meta_box'));
	    
	    // Load admin scripts
		add_action('wp_ajax_radio_spreebie_transcoder_ajax_add_term', array($this, 'spreebie_transcoder_ajax_add_term'));
		
		// AJAX dependency
        add_action('wp_ajax_spreebie_transcoder_google_storage_processing_results', array($this, 'spreebie_transcoder_google_storage_processing_ajax'));
	}
	
	
	/**
	 * Register post type and taxonomy
	 *
	 * Registers a custom post type called 'spreebie_t_m'
	 * and a custom taxonomy called �b_mp4_cc_categories'
	 *
	 * @param	none
	 * @return	none
	*/
	
	public static function spreebie_transcoder_post_type_and_taxonomy_init() {
	    $spreebie_transcoder_type_args = array(
			'labels' => array(
				'name' => _x('Spreebie Transcoded Media', 'post type general name'),
				'singular_name' => _x('Spreebie Transcoded Media', 'post type singular name'),
				'add_new' => _x('Add New Spreebie Transcoded Media', 'image'),
				'add_new_item' => __('Add Spreebie Transcoded Media'),
				'edit_item' => __('Edit Spreebie Transcoded Media'),
				'new_item' => __('Add New Spreebie Transcoded Media'),
				'all_items' => __('View Spreebie Transcoded Media'),
				'view_item' => __('View Spreebie Transcoded Media'),
				'search_items' => __('Search Spreebie Transcoded Media'),
				'not_found' =>  __('No Spreebie Transcoded Media found'),
				'not_found_in_trash' => __('No Spreebie Transcoded Media found in Trash'), 
				'parent_item_colon' => '',
				'menu_name' => 'Spreebie Transcoded Media'
			),
			'public' => true,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'capabilities' => array(
				'create_posts' => false, // Removes support for the "Add New" functionality
			),
			'has_archive' => true, 
			'hierarchical' => false,
			'map_meta_cap' => true,
			'menu_position' => null,
			'supports' => array(
				'thumbnail',
				'custom-fields'
			)
	    );
	    register_post_type('spreebie_t_m', $spreebie_transcoder_type_args);
	    
	    $spreebie_transcoder_category_args = array(
			'labels' => array(
				'name' => _x( 'Spreebie Transcoded Media Categories', 'taxonomy general name' ),
				'singular_name' => _x( 'Spreebie Transcoded Media Category', 'taxonomy singular name' ),
				'search_items' =>  __( 'Spreebie Transcoded Media Categories' ),
				'all_items' => __( 'All Spreebie Transcoded Media Categories' ),
				'parent_item' => __( 'Parent Spreebie Transcoded Media Category' ),
				'parent_item_colon' => __( 'Parent Spreebie Transcoded Media Category:' ),
				'edit_item' => __( 'Edit Spreebie Transcoded Media Category' ), 
				'update_item' => __( 'Update Spreebie Transcoded Media Category' ),
				'add_new_item' => __( 'Add New Spreebie Transcoded Media Category' ),
				'new_item_name' => __( 'New Spreebie Transcoded Media Category' ),
				'menu_name' => __( 'Spreebie Transcoded Media Categories' ),
			),   
			'hierarchical' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array(
				'slug' => 'spreebie_transcoder_category'
			)
	    );
	     
	    register_taxonomy('spreebie_transcoder_category', array('spreebie_t_m'), $spreebie_transcoder_category_args);
	    
	    // Default concomp categories 
	    $spreebie_transcoder_default_categories = array('Autos & Vehicles', 'Comedy', 'Education', 'Film & Animation',
		'Gaming', 'Howto & Style', 'Music', 'News & Politics', 'Nonprofits & Activism',
		'People & Blogs', 'Pets & Animals', 'Science & Technology', 'Sports',
		'Travel & Events'
		);
	     
	    foreach($spreebie_transcoder_default_categories as $cat) {
	     
		if(!term_exists($cat, 'spreebie_transcoder_category')) wp_insert_term($cat, 'spreebie_transcoder_category');
	       
	    }

	    $spreebie_transcoder_error_stage_args = array(
			'labels' => array(
				'name' => _x( 'Spreebie Transcoded Media Error Stages', 'taxonomy general name' ),
				'singular_name' => _x( 'Spreebie Transcoded Media Error Stage', 'taxonomy singular name' ),
				'search_items' =>  __( 'Spreebie Transcoded Media Error Stages' ),
				'all_items' => __( 'All Spreebie Transcoded Media Error Stages' ),
				'parent_item' => __( 'Parent Spreebie Transcoded Media Error Stage' ),
				'parent_item_colon' => __( 'Parent Spreebie Transcoded Media Error Stage:' ),
				'edit_item' => __( 'Edit Spreebie Transcoded Media Error Stage' ), 
				'update_item' => __( 'Update Spreebie Transcoded Media Error Stage' ),
				'add_new_item' => __( 'Add New Spreebie Transcoded Media Error Stage' ),
				'new_item_name' => __( 'New Spreebie Transcoded Media Error Stage' ),
				'menu_name' => __( 'Spreebie Transcoded Media Error Stages' ),
			),   
			'hierarchical' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array(
				'slug' => 'spreebie_transcoder_error_stage'
			)
	    );
	     
	    register_taxonomy('spreebie_transcoder_error_stage', array('spreebie_t_m'), $spreebie_transcoder_error_stage_args);

	    // Error stages
	    $spreebie_transcoder_error_stages = array('Installation Stage', 'FFmpeg Setup Stage', 'General Usage Stage');
	     
	    foreach($spreebie_transcoder_error_stages as $stage) {
	     
		if(!term_exists($stage, 'spreebie_transcoder_error_stage')) wp_insert_term($stage, 'spreebie_transcoder_error_stage');
	       
	    }
	}
	
	
	/**
	 * Adds metabaxes to the custom post type backend
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function spreebie_transcoder_video_metaboxes() {
	    add_action('add_meta_boxes', array($this, 'spreebie_transcoder_add_video_metaboxes'));
	}


	function spreebie_transcoder_google_storage_processing_ajax() {
		check_ajax_referer('spreebie_transcoder_google_storage_processing_results', 'spreebie_transcoder_google_storage_processing_results_nonce');

        // If the $_POST data and nonce are set, upload the data
        // within the video comment inputs
		if(isset($_POST['spreebie_transcoder_google_storage_file_path']) && isset($_POST['spreebie_transcoder_google_storage_file_name'])) {
			// sanitize_file_name() not used below - internal ajax call with no user input
			// required plus sanitize_file_name() creates errors if folder names
			// have spaces. *** sanitize_file_name() creates upload errors in this context ***
			$file_name = $_POST['spreebie_transcoder_google_storage_file_name'];
			$file_path = $_POST['spreebie_transcoder_google_storage_file_path'];

			$plugin_dir = WP_PLUGIN_DIR . '/spreebie-transcoder';

			# Includes the autoloader for libraries installed with composer
			require $plugin_dir . '/vendor/autoload.php';

			# Your Google Cloud Platform project ID
			$projectId = get_option('spreebie_transcoder_gcs_project_id');

			# Instantiates a client
			$storage = new StorageClient([
				'projectId' => $projectId
			]);

			putenv('GOOGLE_APPLICATION_CREDENTIALS='. $plugin_dir . '/' . get_option('spreebie_transcoder_gcs_key_file_name'));

			$file = fopen($file_path, 'r');
			$bucket = $storage->bucket(get_option('spreebie_transcoder_gcs_bucket'));
			$object = $bucket->upload($file, [
				'name' => $file_name
			]);
		}
		
		die();
    }
	
	
	/**
	 * Metaboxes callback
	 *
	 * Callback implements the video comment and caption metaboxex.
	 * Through this, a user will be able to see the caption,
	 * view the video concomp and get direct access to the
	 * video concomp file through a URL
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function spreebie_transcoder_add_video_metaboxes() {       
	    add_meta_box('spreebie_transcoder_meta_box_caption', 'Caption', 'spreebie_transcoder_caption', 'spreebie_t_m');
	    add_meta_box('spreebie_transcoder_meta_box_video', 'Video', 'spreebie_transcoder_video', 'spreebie_t_m');
	    
	    function spreebie_transcoder_video($post) {
		$spreebie_transcoder_video = get_attached_media('video', $post->ID);
		    foreach ($spreebie_transcoder_video as $video) {
		?>
			<p>
			    <label for="spreebie_transcoder_meta_box_video"> URL: </label>
			    <input type="text" name="spreebie_transcoder_meta_box_video" id="spreebie_transcoder_meta_box_video" value="<?php echo esc_attr(wp_get_attachment_url($video->ID, 'full')); ?>" />
			    <a href="<?php echo esc_attr(wp_get_attachment_url($video->ID, 'full')); ?>" target="_blank">
                                <input type="button" id="spreebie_transcoder_other_download_button" class="button button-primary" name="spreebie_transcoder_other_download_button" value="Download"/>
                            </a>
			    <label for="spreebie_transcoder_other_download_help"> (Right Click and "Save Target/Link As...")  </label>
			    <div class="wp_attachment_holder">
				<div style="width: 640px; max-width: 100%;" class="wp-video"><!--[if lt IE 9]><script>document.createElement('video');</script><![endif]-->
				    <video class="wp-video-shortcode" width="640" height="360" preload="metadata" controls="controls"><source type="<?php echo esc_attr(get_post_mime_type($video->ID)); ?>" src="<?php echo esc_attr(wp_get_attachment_url($video->ID, 'full')); ?>" /><a href="<?php echo esc_attr(wp_get_attachment_url($video->ID, 'full')); ?>"><?php echo esc_html(wp_get_attachment_url($video->ID, 'full')); ?></a></video>
				</div>
			    </div>                
			</p>
			<?php if (get_option('spreebie_transcoder_use_google_cloud_storage') == "1") { ?>
			<form id="spreebie_transcoder_google_storage_form" action="" method="post" enctype="multipart/form-data">
				<?php echo wp_nonce_field('spreebie_transcoder_google_storage_form', 'spreebie_transcoder_google_storage_form_submitted'); ?>
				<table class="form-table">
					<tr>
						<th scope="row">Store Video On Google Cloud Storage:</th>
						<td>
						<input type="button" class="button button-primary" name="spreebie_transcoder_store_on_gcs" value="Store On GCS" id="spreebie_transcoder_store_on_gcs">
						<img src="<?php echo esc_attr(admin_url('/images/wpspin_light.gif')); ?>" id="spreebie_transcoder_store_on_gcs_loading" class="spreebie_transcoder_store_on_gcs_loading"/>
						<input id="spreebie_transcoder_google_storage_file_path" name="spreebie_transcoder_google_storage_file_path" type="hidden" value="<?php echo esc_attr(get_attached_file($video->ID)); ?>">
						<input id="spreebie_transcoder_google_storage_file_name" name="spreebie_transcoder_google_storage_file_name" type="hidden" value="<?php echo esc_attr(basename(get_attached_file($video->ID))); ?>"><br>
							<p>It is important that the Google Cloud Storage settings be properly</p>
							<p>configured before you store the video.</p>
							<p>Refer to the <a href="https://s3.amazonaws.com/spreebietranscoder/Spreebie_Transcoder_Quick_Start_Guide.zip">MANUAL</a> to help you setup GCS.</p>
						</td>
						<div id="spreebie_transcoder_uploading_to_gcs" title="Please wait while transcoding happens!">
							<p>Your video is being uploaded to Google Cloud Storage. This may take a few minutes, please wait.</p>
						</div>
						<div id="spreebie_transcoder_uploaded_to_gcs_successfully" title="Please wait while transcoding happens!">
							<p>Your video was successfully stored on GCS.</p>
						</div>
					</tr>
					
				</table>
			</form>
			<?php } ?>
		<?php
			break;
		    }
	    }
	    
	    function spreebie_transcoder_caption($post) {
	    ?>
		<p>
		    <textarea class="widefat" rows="1" cols="40" name="spreebie_transcoder_meta_box_caption" id="spreebie_transcoder_meta_box_caption"><?php echo esc_html(get_the_title($post->ID)); ?></textarea>
		</p>
	    <?php
	    }
	}
	
	
	/**
	 * Removes the existing taxonomy metabox
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function spreebie_transcoder_remove_meta_box(){  
	    remove_meta_box($this->spreebie_transcoder_taxonomy_metabox_id, $this->spreebie_transcoder_post_type, 'normal');  
	}
	
	
	/**
	 * Add radio buttons-based metabox
	 *
	 * Add the new custom taxonomy metabox that is based
	 * on radio buttons instead of checkboxes. In this way,
	 * a video comment can be associated with only one term.
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function spreebie_transcoder_add_taxonomy_meta_box() {  
	    add_meta_box(
		'spreebie_transcoder_meta_box_categories',
		'Spreebie Transcoded Media Categories',
		array($this, 'spreebie_transcoder_taxonomy_metabox'),
		$this->spreebie_transcoder_post_type,
		'side',
		'core'
	    );  
	}
	
	/**
	 * Radio buttons-based metaxbox callback
	 *
	 * Callback implements the new taxonomy metabox
	 * based on radio buttons
	 *
	 * @param	$post: the video concomp post variable
	 * @return	none
	*/ 
	public function spreebie_transcoder_taxonomy_metabox($post) {
	    //Get taxonomy and terms  
	    $taxonomy = $this->spreebie_transcoder_taxonomy;
	  
	    //Set up the taxonomy object and get terms  
	    $tax = get_taxonomy($taxonomy);
	    $terms = get_terms($taxonomy, array('hide_empty' => 0));
	  
	    //Name of the form
	    $name = 'tax_input[' . $taxonomy . ']';
	  
	    //Get current and popular terms  
	    $popular = get_terms( $taxonomy, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );  
	    $postterms = get_the_terms($post->ID, $taxonomy);  
	    $current = ($postterms ? array_pop($postterms) : false);  
	    $current = ($current ? $current->term_id : 0);  
	    ?>  
	  
		<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
		    <!-- Display tabs-->
		    <ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
			<li class="tabs"><a href="#<?php echo $taxonomy; ?>-all" tabindex="3"><?php echo $tax->labels->all_items; ?></a></li>
			<li class="hide-if-no-js"><a href="#<?php echo $taxonomy; ?>-pop" tabindex="3"><?php _e( 'Most Used' ); ?></a></li>
		    </ul>
    
		    <!-- Display taxonomy terms -->
		    <div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
			<ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
			<?php
			    foreach($terms as $term) {
				$id = $taxonomy.'-'.$term->term_id;
				$value = (is_taxonomy_hierarchical($taxonomy) ? "value='{$term->term_id}'" : "value='{$term->term_slug}'");
				echo "<li id='$id'><label class='selectit'>";
				echo "<input type='radio' id='in-$id' name='{$name}'".checked($current,$term->term_id,false)." {$value} />$term->name<br />";
				echo "</label></li>";
			     }
			?>
			</ul>
		    </div>
    
		    <!-- Display popular taxonomy terms -->
		    <div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
			<ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
			<?php
			    foreach($popular as $term){
				$id = 'popular-'.$taxonomy.'-'.$term->term_id;
				$value= (is_taxonomy_hierarchical($taxonomy) ? "value='{$term->term_id}'" : "value='{$term->term_slug}'");
				echo "<li id='$id'><label class='selectit'>";
				echo "<input type='radio' id='in-$id'".checked($current,$term->term_id,false)." {$value} />$term->name<br />";
				echo "</label></li>";
			    }
			?>
			</ul>
		    </div>
    
		    <p id="<?php echo $taxonomy; ?>-add" class="">
			<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>"><?php echo $tax->labels->add_new_item; ?></label>
			<input type="text" name="new<?php echo $taxonomy; ?>" id="new<?php echo $taxonomy; ?>" class="form-required form-input-tip" value="<?php echo esc_attr( $tax->labels->new_item_name ); ?>" tabindex="3" aria-required="true"/>
			<input type="button" id="" class="radio-tax-add button" value="<?php echo esc_attr( $tax->labels->add_new_item ); ?>" tabindex="3" />
			<?php wp_nonce_field( 'radio-tax-add-'.$taxonomy, '_wpnonce_radio-add-tag', false ); ?>
		    </p>
		</div>
	    <?php  
	}
	
	
	/**
	 * Add screenshot metabox
	 *
	 * Add the video concomp screenshot metabox that is based.
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function spreebie_transcoder_add_screenshot_meta_box() {  
	    add_meta_box(
		'spreebie_transcoder_meta_box_screenshot',
		'Screenshot',
		array($this, 'spreebie_transcoder_screenshot_metabox'),
		$this->spreebie_transcoder_post_type,
		'side',
		'core'
	    );
	}
	
	/**
	 * Screenshot metaxbox callback
	 *
	 * Callback implements the screenshot metabox
	 *
	 * @param	$post: the video concomp post variable
	 * @return	none
	*/ 
	public function spreebie_transcoder_screenshot_metabox($post) {
	    $spreebie_transcoder_screenshot = get_attached_media('image', $post->ID);
	    foreach ($spreebie_transcoder_screenshot as $screenshot) {
	?>
		<p>
		    <label for="spreebie_transcoder_meta_box_screenshot"> URL: </label>
		    <input type="text" name="spreebie_transcoder_meta_box_screenshot" id="spreebie_transcoder_meta_box_screenshot" value="<?php echo esc_attr(wp_get_attachment_url($screenshot->ID, 'full')); ?>" />
		    <a href="<?php echo esc_attr(wp_get_attachment_url($screenshot->ID, 'full')); ?>" target="_blank">
			<input type="button" id="spreebie_transcoder_screenshot_view_button" class="action" name="spreebie_transcoder_screenshot_view_button" value="View"/>
		    </a>               
		</p>
	<?php
		break;
	    } 
	}
	
	/**
	 * Add scripts to make the new radio button based
	 * metabox work
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function spreebie_transcoder_admin_script() {  
	    wp_register_script('radiotax', plugins_url('js/radiotax.js', __FILE__), array('jquery'), null, true ); // We specify true here to tell WordPress this script needs to be loaded in the footer  
	    wp_localize_script('radiotax', 'radio_tax', array('slug'=>$this->spreebie_transcoder_taxonomy));
	    wp_enqueue_script('radiotax');  
	}
	
	
	/**
	 * Add terms to the new radio button based custom
	 * taxonomy metabox
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function spreebie_transcoder_ajax_add_term() {    
		$taxonomy = !empty($_POST['taxonomy']) ? $_POST['taxonomy'] : '';
		$taxonomy = sanitize_text_field($taxonomy);
		$term = !empty($_POST['term']) ? $_POST['term'] : '';
		$term = sanitize_text_field($term);
		$tax = get_taxonomy($taxonomy);
    
	    check_ajax_referer('radio-tax-add-'.$taxonomy, '_wpnonce_radio-add-tag');
    
	    if(!$tax || empty($term))
		    exit();
    
	    if ( !current_user_can( $tax->cap->edit_terms ) )
		    die('-1');
    
	    $tag = wp_insert_term($term, $taxonomy);
    
	    if ( !$tag || is_wp_error($tag) || (!$tag = get_term( $tag['term_id'], $taxonomy )) ) {
		    //TODO Error handling
		    exit();
	    }
    
	    $id = $taxonomy.'-'.$tag->term_id;
	    $name = 'tax_input[' . $taxonomy . ']';
	    $value= (is_taxonomy_hierarchical($taxonomy) ? "value='{$tag->term_id}'" : "value='{$term->tag_slug}'");
    
	    $html ='<li id="'.$id.'"><label class="selectit"><input type="radio" id="in-'.$id.'" name="'.$name.'" '.$value.' />'. $tag->name.'</label></li>';
    
	    echo json_encode(array('term'=>$tag->term_id,'html'=>$html));
	    exit();
	}
	
	
	/**
	 * This validates the inputs a visitor entered when
	 * creating a video comment
	 *
	 * @param	$video_file: the video file to be uploaded
	 * @param	$video_caption: the caption
	 * @return	$result: the upload result
	*/
	
	public static function spreebie_transcoder_parse_file_errors($video_file = '', $video_caption) {
	    $result = array();
	    $result['error'] = 0;
	    
	    if($video_file['error']){
		$result['error'] = "Either your video is larger than 32MB, there was no video selected or there was an upload error!";
	       
		return $result;
	    }
	   
	    $video_caption = trim(preg_replace('/[^a-zA-Z0-9\s]+/', ' ', $video_caption));
	     
	    if($video_caption == ''){
		$result['error'] = "Your caption may only contain letters, numbers and spaces! Your caption cannot be empty!";
	       
		return $result;
	    }
	    
	    // validate video file
	    $video_file_type = wp_check_filetype($video_file['name']);
	    
	    if (((strcasecmp($video_file_type['ext'], 'mov') == 0) && (strcasecmp($video_file_type['type'], 'video/quicktime') == 0)) ||
		((strcasecmp($video_file_type['ext'], 'mov') == 0) && (strcasecmp($video_file_type['type'], 'video/x-quicktime') == 0)) ||
		((strcasecmp($video_file_type['ext'], 'mov') == 0) && (strcasecmp($video_file_type['type'], 'image/mov') == 0)) ||
		((strcasecmp($video_file_type['ext'], 'mp4') == 0) && (strcasecmp($video_file_type['type'], 'video/mp4') == 0)) ||
		((strcasecmp($video_file_type['ext'], 'flv') == 0) && (strcasecmp($video_file_type['type'], 'video/x-flv') == 0))) {
		// do nothing - no errors here
	    } else {
		$result['error'] = "You did not select a video file for the comment! Supported video formats are MOV, MP4 and FLV.";
	       
		return $result;
	    }
	    
	     
	    $result['caption'] = $video_caption;
	    
	    $video_size = filesize($video_file['tmp_name']);
	    
	    $max_size = 1000000 * (float) get_option('spreebie_transcoder_max_video_size');
	    
	    // validate video size
	    if(($video_size > $max_size)){
		$result['error'] = 'Your video was ' . $video_size . ' bytes! It must not exceed ' . $max_size . ' bytes.';
	    }
	       
	    return $result;
	}


	/**
	 * This scales down a video uploaded by a visitor.
	 * This happens until the last possible resolution
	 * which is 144p
	 *
	 * @param	$input_height: the video height
	 * @param	$input_video_path: the converted video's path
	 * @param	$post_id: the main post's id
	 * @param   $caption: the post caption
	 * @param   $caption: the post caption
	 * @param   $caption: the post caption
	*/
	
	public static function spreebie_transcoder_scale_down($input_height, $input_video_path, $post_id, $caption, $ffmpeg_path, $ffprobe_path, $folder_id, $unique_id) {
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');

		// initialize the video sizes
		$output_height = 0;
		$output_width = 0;

		// if take down the video size to the next
		// standard video size
		if ($input_height == 1080) {
			$output_height = 720;
			$output_width = 1280;
		} else if ($input_height == 720) {
			$output_height = 480;
			$output_width = 854;
		} else if ($input_height == 480) {
			$output_height = 360;
			$output_width = 640;
		} else if ($input_height == 360) {
			$output_height = 240;
			$output_width = 320;
		}  else if ($input_height == 240) {
			$output_height = 144;
			$output_width = 256;
		}

		// if there is no issue with the sizes, begin the resizing process
		if ($output_height != 0 && $output_width != 0) {
			// the video description
			$video_description = "'" . $caption . "' - Resolution: " . $output_width . " x " . $output_height . ".";

			// the uploads array
			$uploads_array = wp_upload_dir();

			// the path to the current upload dir
			$uploads_path = $uploads_array['path'];
			
			// the path of the new video
			$scaled_video_path = $uploads_path . "/" . $unique_id . "_" . $output_height . "p.mp4";

			// command to scale down the video
			$scaled_video_command = $ffmpeg_path . "ffmpeg -i " . $input_video_path . " -strict -2 -vf scale=" . $output_width . ":" . $output_height . " -preset " . get_option('spreebie_transcoder_ffmpeg_speed') . " -crf ". get_option('spreebie_transcoder_ffmpeg_quality') . " " . $scaled_video_path . " 2>" . $uploads_path . "/ffmpeg_" . $output_height . ".log";
			exec($scaled_video_command);
			
			// Check the type of file. We'll use this as the 'post_mime_type'.
			$scaled_video_filetype = wp_check_filetype(basename($scaled_video_path), null);
			
			// Get the path to the upload directory.
			$wp_upload_dir = wp_upload_dir();
			
			// Prepare an array of post data for the attachment.
			$scaled_video_attachment = array(
				'guid'           => $wp_upload_dir['url'] . '/' . basename($scaled_video_path), 
				'post_mime_type' => $scaled_video_filetype['type'],
				'post_title'     => $caption,//$output,//preg_replace( '/\.[^.]+$/', '', basename($converted_video_path)),
				'post_content'   => $video_description,
				'post_status'    => 'inherit'
			);
			
			// Insert the attachment.
			$scaled_video_attachment_id = wp_insert_attachment($scaled_video_attachment, $scaled_video_path, $post_id);
			
			// Generate the metadata for the attachment, and update the database record.
			$scaled_video_attachment_data = wp_generate_attachment_metadata($scaled_video_attachment_id, $scaled_video_path);
			wp_update_attachment_metadata($scaled_video_attachment_id, $scaled_video_attachment_data);
			
			update_post_meta($post_id, '_video_id_' . $output_height, $converted_video_attachment_id);
			
			/// Add the video to the selected folder
			$spreebie_attachment_ids = array($scaled_video_attachment_id);
			
			if (function_exists('wp_rml_move')) {
				wp_rml_move($folder_id, $spreebie_attachment_ids);
			}
		}
	}

	/**
	 * Insert an attachment from an URL address.
	 *
	 * @param  String $url
	 * @param  Int    $parent_post_id
	 * @return Int    Attachment ID
	 */
	
	public static function crb_insert_attachment_from_url($url, $parent_post_id = null) {
		if( !class_exists( 'WP_Http' ) )
			include_once( ABSPATH . WPINC . '/class-http.php' );
		$http = new WP_Http();
		$response = $http->request( $url );
		if( $response['response']['code'] != 200 ) {
			return false;
		}
		$upload = wp_upload_bits( basename($url), null, $response['body'] );
		if( !empty( $upload['error'] ) ) {
			return false;
		}
		$file_path = $upload['file'];
		$file_name = basename( $file_path );
		$file_type = wp_check_filetype( $file_name, null );
		$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
		$wp_upload_dir = wp_upload_dir();
		$post_info = array(
			'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
			'post_mime_type' => $file_type['type'],
			'post_title'     => $attachment_title,
			'post_content'   => '',
			'post_status'    => 'inherit',
		);
		// Create the attachment
		$attach_id = wp_insert_attachment( $post_info, $file_path, $parent_post_id );
		// Include image.php
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		// Define attachment metadata
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
		// Assign metadata to attachment
		wp_update_attachment_metadata( $attach_id,  $attach_data );
		return $attach_id;
	}


	/**
	 * Upload a file.
	 *
	 * @param string $bucketName the name of your Google Cloud bucket.
	 * @param string $objectName the name of the object.
	 * @param string $source the path to the file to upload.
	 *
	 * @return Psr\Http\Message\StreamInterface
	 */

	public static function spreebie_transcoder_upload_object($bucketName, $objectName, $source, $post_id, $video_description, $caption) {
		$plugin_dir = WP_PLUGIN_DIR . '/spreebie-transcoder';

		# Includes the autoloader for libraries installed with composer
		require $plugin_dir . '/vendor/autoload.php';

		# Your Google Cloud Platform project ID
		$projectId = get_option('spreebie_transcoder_gcs_project_id');

		# Instantiates a client
		$storage = new StorageClient([
			'projectId' => $projectId
		]);

		putenv('GOOGLE_APPLICATION_CREDENTIALS='. $plugin_dir . '/' . get_option('spreebie_transcoder_gcs_key_file_name'));

		$file = fopen($source, 'r');
		$bucket = $storage->bucket($bucketName);
		$object = $bucket->upload($file, [
			'name' => $objectName
		]);
	}

	
	/**
	 * This processes the video uploaded by a visitor
	 * and transcoders it to lesser resolutions.  The mp4 video is
	 * the used to create a snapshot and the snapshot
	 * is used to create a 150x150 pixel thumbnail
	 *
	 * @param	$file: the image file to be uploaded
	 * @param	$post_id: the id of the video comment
	 * @param	$caption: the video comment's caption
	 * @param   $ffmpeg_path the path to the ffmpeg executable
	*/
	
	public static function spreebie_transcoder_process_everything($file, $post_id, $caption, $ffmpeg_path, $ffprobe_path, $folder_id) {
	    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	    
	    // Upload of the untouched video
	    $original_video_attachment_id = media_handle_upload($file, $post_id);
	    
	    update_post_meta($post_id, '_video_id', $original_video_attachment_id);
	    
	    // The full path to the untouched video
	    $original_video_path = get_attached_file($original_video_attachment_id);
	    
	    // array containing upload dir info
	    $uploads_array = wp_upload_dir();
	    // the path to the current upload dir
	    $uploads_path = $uploads_array['path'];
		
		// Get video resolution data
		$show_streams_command = $ffprobe_path . "ffprobe -v quiet -print_format json -show_format -show_streams " . $original_video_path . "  2>&1";
		$output = shell_exec($show_streams_command);
		$output_object = json_decode($output);
		$width = $output_object->streams[0]->width;
		$height = $output_object->streams[0]->height;

		$video_description = "'" . $caption . "' - Resolution: " . $width . " x " . $height . ".";

		$unique_id = uniqid('spreebie_transcoder_');

		// the path of the new video
		$converted_video_path = $uploads_path . "/" . $unique_id . "_" . $height . "p.mp4";
		
		/// Rename the original file to the conversion path - for now
		rename($original_video_path, $converted_video_path);
	    
	    // the path of the new image screenshot taken from the new video
		$screenshot_path = $converted_video_path . ".jpg";

	    // command to create screenshot
	    $create_screenshot_command = $ffmpeg_path . "ffmpeg -y  -i " . $converted_video_path . " -f mjpeg -vframes 1 -ss 1 " . $screenshot_path . " 2>ffmpeg_3.log";
	    exec($create_screenshot_command);
	    
	    // get WP_IMAGE_EDITOR for file
	    $image = wp_get_image_editor($screenshot_path);
	    
	    // path of 150x150 thumbnail
	    $thumbnail_path = $screenshot_path . "150x150.jpg";

	    if (!is_wp_error($image)) {
			$image->save($thumbnail_path);
	    }
	    
	    // Attaching thumbnail to post
	    
	    // Check the type of file. We'll use this as the 'post_mime_type'.
	    $thumnail_filetype = wp_check_filetype(basename($thumbnail_path), null);
	    
	    // Get the path to the upload directory.
	    $wp_upload_dir = wp_upload_dir();
	    
	    // Prepare an array of post data for the attachment.
	    $thumbnail_attachment = array(
		    'guid'           => $wp_upload_dir['url'] . '/' . basename($thumbnail_path), 
		    'post_mime_type' => $thumnail_filetype['type'],
		    'post_title'     => $caption,//$output,//preg_replace( '/\.[^.]+$/', '', basename($thumbnail_path)),
		    'post_content'   => '150x150 screenshot in jpeg format.',
		    'post_status'    => 'inherit'
	    );
	    
	    // Insert the attachment.
	    $thumbnail_attachment_id = wp_insert_attachment($thumbnail_attachment, $thumbnail_path, $post_id);
	    
	    // Generate the metadata for the attachment, and update the database record.
	    $thumbnail_attachment_data = wp_generate_attachment_metadata($thumbnail_attachment_id, $thumbnail_path);
	    wp_update_attachment_metadata($thumbnail_attachment_id, $thumbnail_attachment_data);
	    
	    // add featured image to post
	    update_post_meta($post_id, '_thumbnail_id', $thumbnail_attachment_id);
	    
	    // Remove video attachment to make room for new one
	    wp_delete_attachment($original_video_attachment_id);
	    
	    // Attaching new video to post
	    
	    // Check the type of file. We'll use this as the 'post_mime_type'.
	    $converted_video_filetype = wp_check_filetype(basename($converted_video_path), null);
	    
	    // Get the path to the upload directory.
	    $wp_upload_dir = wp_upload_dir();
	    
	    // Prepare an array of post data for the attachment.
	    $converted_video_attachment = array(
		    'guid'           => $wp_upload_dir['url'] . '/' . basename($converted_video_path), 
		    'post_mime_type' => $converted_video_filetype['type'],
		    'post_title'     => $caption,//$output,//preg_replace( '/\.[^.]+$/', '', basename($converted_video_path)),
		    'post_content'   => $video_description,
		    'post_status'    => 'inherit'
	    );
	    
	    // Insert the attachment.
	    $converted_video_attachment_id = wp_insert_attachment($converted_video_attachment, $converted_video_path, $post_id);
	    
	    // Generate the metadata for the attachment, and update the database record.
	    $converted_video_attachment_data = wp_generate_attachment_metadata($converted_video_attachment_id, $converted_video_path);
	    wp_update_attachment_metadata($converted_video_attachment_id, $converted_video_attachment_data);
	    
		update_post_meta($post_id, '_video_id', $converted_video_attachment_id);
		
		/// Add the video to the selected folder
		$spreebie_attachment_ids = array($converted_video_attachment_id);

		if (function_exists('wp_rml_move')) {
			wp_rml_move($folder_id, $spreebie_attachment_ids);
		}

		if ((int)$height > 480) {
			self::spreebie_transcoder_scale_down(480, $converted_video_path, $post_id, $caption, $ffmpeg_path, $ffprobe_path, $folder_id, $unique_id);
		} else if ((int)$height > 144) {
			self::spreebie_transcoder_scale_down((int)$height, $converted_video_path, $post_id, $caption, $ffmpeg_path, $ffprobe_path, $folder_id, $unique_id);

			$next_height = 0;

			if ((int)$height == 1080) {
				$next_height = 720;
			} else if ((int)$height == 720) {
				$next_height = 480;
			} else if ((int)$height == 480) {
				$next_height = 360;
			} else if ((int)$height == 360) {
				$next_height = 240;
			}  else if ((int)$height == 240) {
				$next_height = 144;
			}

			if ($next_height > 144) {
				self::spreebie_transcoder_scale_down($next_height, $converted_video_path, $post_id, $caption, $ffmpeg_path, $ffprobe_path, $folder_id, $unique_id);
			}	
		}
	}
    }

endif;
?>