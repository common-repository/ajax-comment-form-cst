<?php
/**
 * Front side library
 * 
 *
 */

class ACForm_CST_Frontend{
    protected $settings;
    
    public function __construct($settings){
        
        $this->settings = $settings;
        add_action( 'wp_enqueue_scripts', array($this,'acform_cst_script_assets'), 999 ); // Add script on front page
        add_action( 'wp_footer', array($this,'acform_cst_style_assets'), 100); // Add inline style
        add_action( 'wp_ajax_acform_cst_post_comment', array($this,'submit_ajax_comment') ); // wp_ajax_{action} for registered user
        add_action( 'wp_ajax_nopriv_acform_cst_post_comment', array($this,'submit_ajax_comment') ); // wp_ajax_nopriv_{action} for not registered users
        
        if($this->settings['captcha'] == 'on' && !is_user_logged_in())
        add_filter('comment_form_submit_field', array($this, 'addCaptchaField'), 9999);

        if($this->settings['gdpr'] == 'on' && !is_user_logged_in())
        add_filter('comment_form_submit_field', array($this, 'addGDPRField'), 999);
    }
    
    public function acform_cst_script_assets(){
        
        wp_enqueue_script( 'acform-cst',  plugin_dir_url( ACFORM_CST_FILE ) . 'js/acform-cst.js', array( 'jquery' ), ACFORM_CST_VERSION, true );
        wp_localize_script( 'acform-cst', 'acformcst', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'errorMessage' => $this->settings['em'],
            'successMessage' => 'Thanks for your comment. We appreciate your response.',
            'mscroll' => $this->settings['mscroll'],
            'captcha' => ($this->settings['captcha'] == false ? 'false' : 'true'),
            'gdpr' => ($this->settings['gdpr'] == false ? 'false' : 'true'),
            //'loader_image' => get_stylesheet_directory_uri().'/images/loader.gif'
        ));
        //var_dump($this->settings['gdpr']);die;
     }
     
     public function acform_cst_style_assets(){            
            echo "<style type='text/css'>\n".$this->settings['css']."\n</style>\n";        
     }

     public function addCaptchaField($submitField = '') {
        $field = apply_filters(
            'acform_cst_captcha_field',
            '<p class="acform_cst_captcha_verification_code"><input type="text" class="form-control" name="security_code" id="security_code" data-bv-field="security_code" placeholder="Catcha Code"></p>
            <p class="acform_cst_captcha_security_image"><iframe src="'.plugin_dir_url( ACFORM_CST_FILE ).'/captcha/php_captcha.php" id="iframe1" scrolling="no" marginheight="0px" marginwidth="0px" margin="0px;" width="240px" height="60px" frameborder="0"></iframe></p>',
            $submitField
        );
        return $field . $submitField;
    }
     
    /**
     * @param string $submitField
     * @return string
     */
    public function addGDPRField($submitField = '') {
        $field = apply_filters(
            'acform_cst_gdprc_field',
            '<p class="acform_cst_gdprc_checkbox"><input type="checkbox" name="gdpr" id="gdpr" value="1" /> By using this form you agree with the storage and handling of your data by this website.</p>',
            $submitField
        );
        return $field . $submitField;
    }
     
    public function submit_ajax_comment(){ 
        session_start();
        // Check for captcha code
        $verification_code = $_POST['security_code'];

        if($verification_code==''){
		$errorArray = "Enter captcha code.";
                echo json_encode(array("error" => 1, "message" => $errorArray));
                wp_die();
	}
	
	if($verification_code!='' && $verification_code != $_SESSION['captcha_val']){
		$errorArray = "Invalid captcha code!";
                echo json_encode(array("error" => 1, "message" => $errorArray));
                wp_die();
	}       

        $comment = wp_handle_comment_submission( wp_unslash( $_POST ) );
        if ( is_wp_error( $comment ) ) {
            $error_data = intval( $comment->get_error_data() );
            if ( ! empty( $error_data ) ) {
                echo json_encode(array("error" => 1, "message" => $comment->get_error_message()));
                wp_die();
            } else {
                echo json_encode(array("error" => 1, "message" => 'Unknown error'));
                wp_die();
            }
        }

        $commentId = $comment->comment_ID;

        if (isset($_POST['gdpr']) && !empty($commentId)) {
            add_comment_meta($commentId, '_gdpr', time());
        }
 
        /*
         * Set Cookies
         */
        $user = wp_get_current_user();
        do_action('set_comment_cookies', $comment, $user);
     
        /*
         * If you do not like this loop, pass the comment depth from JavaScript code
         */
        $comment_depth = 1;
        $comment_parent = $comment->comment_parent;
        while( $comment_parent ){
            $comment_depth++;
            $parent_comment = get_comment( $comment_parent );
            $comment_parent = $parent_comment->comment_parent;
        }
     
        /*
         * Set the globals, so our comment functions below will work correctly
         */
        $GLOBALS['comment'] = $comment;
        $GLOBALS['comment_depth'] = $comment_depth;
     
        /*
         * Here is the comment template, you can configure it for your website
         * or you can try to find a ready function in your theme files
         */
        $comment_html = '<li ' . comment_class('', null, null, false ) . ' id="comment-' . get_comment_ID() . '">
            <article class="comment-body" id="div-comment-' . get_comment_ID() . '">
                <footer class="comment-meta">
                    <div class="comment-author vcard">
                        ' . get_avatar( $comment, 100 ) . '
                        <b class="fn">' . get_comment_author_link() . '</b>
                    </div>
                    <div class="comment-metadata">
                        <a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '">' . sprintf('%1$s at %2$s', get_comment_date(),  get_comment_time() ) . '</a>';
     
                        if( $edit_link = get_edit_comment_link() )
                            $comment_html .= '<span class="edit-link"><a class="comment-edit-link" href="' . $edit_link . '">Edit</a></span>';
     
                    $comment_html .= '</div>';
                    if ( $comment->comment_approved == '0' )
                        $comment_html .= '<p class="comment-awaiting-moderation">Your comment is awaiting moderation.</p>';
     
                $comment_html .= '</footer>
                <div class="comment-content">' . apply_filters( 'comment_text', get_comment_text( $comment ), $comment ) . '</div>
            </article>
        </li>';
        
        echo json_encode(array("success" => 1, 'html' => $comment_html, 'moveto' => "comment-" . get_comment_ID()));
        wp_die(); 
    }
}

        
?>
