/*------------------------------------------------------------------*
 * The 'spreebie-transcoder-form-validation.js' file: Admin ajax and
 * validation
 * @author Thabo David Klass
/*------------------------------------------------------------------*/

// The location of the 'admin-ajax.php' file
var ajaxurl = spreebie_transcoder_gcs_ajax_params.spreebie_transcoder_ajax_url;

// The send receipt via email nonce
var spreebie_transcoder_google_storage_processing_results_nonce = spreebie_transcoder_gcs_ajax_data.spreebie_transcoder_google_storage_processing_results_nonce;

/**
* Validate input and AJAX
*
* This validates the data that visitors/user
* enter when transcoding and enables AJAX calls to
* Google Cloud Storage
*
*/

jQuery(document).ready(function($) {
    $('#spreebie_transcoder_main_content').fadeIn('slow');
    
    var spreebie_transcoder_upload_form = $("#spreebie_transcoder_upload_form");
    var spreebie_transcoder_upload_form_ffmpeg_not_found = $("#spreebie_transcoder_upload_form_ffmpeg_not_found");
    var spreebie_transcoder_vid = $("#spreebie_transcoder_vid");
    var spreebie_transcoder_caption = $("#spreebie_transcoder_caption");
    var spreebie_transcoder_google_storage_form = $("#spreebie_transcoder_google_storage_form");

    var spreebie_transcoder_save_changes_form = $("#spreebie_transcoder_save_changes_form");
    var spreebie_transcoder_max_video_size = $("#spreebie_transcoder_max_video_size");

    var spreebie_transcoder_email_form = $("#spreebie_transcoder_email_form");
    var spreebie_transcoder_error_from_email = $("#spreebie_transcoder_error_from_email");
    var spreebie_transcoder_error_description = $("#spreebie_transcoder_error_description");
    
    spreebie_transcoder_vid.focus(function(e) {
        $('#spreebie_transcoder_incomplete_dialog').hide();
    });
    
    spreebie_transcoder_caption.focus(function(e) {
        $('#spreebie_transcoder_incomplete_dialog').hide();
    });

    spreebie_transcoder_error_from_email.focus(function(e) {
        $('#spreebie_transcoder_support_fields_not_filled').hide();
        $('#spreebie_transcoder_email_invalid').hide();
    });
    
    spreebie_transcoder_error_description.focus(function(e) {
        $('#spreebie_transcoder_support_fields_not_filled').hide();
    });

    spreebie_transcoder_max_video_size.focus(function(e) {
        $('#spreebie_transcoder_video_size_not_number').hide();
    });

    // validation function for when FFmpeg is not being used
    spreebie_transcoder_upload_form.submit(function(e) {
        if (!validate_video() || !validate_caption()) {
            e.preventDefault();
            $('#spreebie_transcoder_incomplete_dialog').show();
        } else {
            $('input[type="submit"]').prop('disabled', true);
            $('#spreebie_transcoder_loading').show();
            $('#spreebie_transcoder_transcoding').show();
        }
    });

    spreebie_transcoder_google_storage_form.submit(function(e) {
        $('input[type="submit"]').prop('disabled', true);
        $('#spreebie_transcoder_google_storage_loading').show();
    });

    // prevent saving if size entered is not a number
    spreebie_transcoder_save_changes_form.submit(function(e) {
        if (!validate_size()) {
            e.preventDefault();
            $('#spreebie_transcoder_video_size_not_number').show();
        }
    });
    
    spreebie_transcoder_upload_form_ffmpeg_not_found.submit(function(e) {
        e.preventDefault();
        $('#spreebie_transcoder_ffmpeg_not_found').show();
    });

    //// validation function for when support form
    spreebie_transcoder_email_form.submit(function(e) {
        if (!validate_from_email() || !validate_error_description()) {
            e.preventDefault();
            $('#spreebie_transcoder_support_fields_not_filled').show();
        } else {
            $('input[type="submit"]').prop('disabled', true);
        }
    });
    
    function validate_video() {
        // get the file name, possibly with path (depends on browser)
        var vid_name = spreebie_transcoder_vid.val();
        
        // format validity boolean
        var format_valid = true;
        
        // Use a regular expression to trim everything before final dot
        var vid_ext = vid_name.replace(/^.*\./, '');

        // check the validity of the extension
        if (vid_ext == '') {
            format_valid = false;
        } else {
            vid_ext = vid_ext.toLowerCase()
            if (vid_ext == 'mp4') {
                format_valid = true;
            } else {
                format_valid = false;
            }
        }
        
        if (vid_name.length > 0 && format_valid) {
            return true;
            
        } else {
            return false;
        }
    }
    
    // validate caption
    function validate_caption() {
        if (spreebie_transcoder_caption.val().length < 1) {
            return false;
        } else {
            return true;
        }
    }

    // validate from email
    function validate_from_email() {
        if (spreebie_transcoder_error_from_email.val().length < 1) {
            return false;
        } else {
            return true;
        }
    }

    // validate error description
    function validate_error_description() {
        if (spreebie_transcoder_error_description.val().length < 1) {
            return false;
        } else {
            return true;
        }
    }

    // validate error description
    function validate_size() {
        return $.isNumeric(spreebie_transcoder_max_video_size.val());
    }


    /// Google Cloud Storage section
    var spreebieTranscoderUploadedToGcsSuccessfully = $("#spreebie_transcoder_uploaded_to_gcs_successfully");
    var spreebieTranscoderUploadingToGcs = $("#spreebie_transcoder_uploading_to_gcs");
    var spreebieTranscoderStoreOnGcsLoading = $("#spreebie_transcoder_store_on_gcs_loading");


    // This responds to the 'Store on GCS' button click
    $("#spreebie_transcoder_store_on_gcs").click(function() {
        spreebieTranscoderUploadingToGcs.show();
        spreebieTranscoderStoreOnGcsLoading.show();

        var filePath = $('#spreebie_transcoder_google_storage_file_path').val();
        var fileName = $('#spreebie_transcoder_google_storage_file_name').val();

        // The data that is to be passed as post data
        // to a php callback called spreebie_barter_get_details_ajax()
        data = {
            action: 'spreebie_transcoder_google_storage_processing_results',
            spreebie_transcoder_google_storage_file_path: filePath,
            spreebie_transcoder_google_storage_file_name: fileName,
            spreebie_transcoder_google_storage_processing_results_nonce: spreebie_transcoder_google_storage_processing_results_nonce
        };
        
        // This section empties an existing section of HTML
        // and replaces it with HTML from the afformentioned
        // callback
        $.post(ajaxurl, data, function(response) {
            $('#spreebie_barter_results').empty();
            $('#spreebie_barter_results').hide();
            $('#spreebie_barter_results').append(response);
            $('#spreebie_barter_results').fadeIn('slow')

            spreebieTranscoderUploadingToGcs.hide();
            spreebieTranscoderStoreOnGcsLoading.hide();
            spreebieTranscoderUploadedToGcsSuccessfully.show();
        });
        
        return false;
    });
});