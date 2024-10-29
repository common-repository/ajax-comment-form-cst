<?php
/**
 * Front / Backend side library
 * 
 *
 */

class ACForm_CST{
	protected $settings;
    
	public function __construct($settings){
		// Plugin initialization
		// Take no action before all plugins are loaded
		//parent::__construct();
		//print_r($settings);
		$this->settings = $settings;
		add_action( 'plugins_loaded', array( $this, 'init_backend' ), 10 );
		add_action( 'wp', array( $this, 'init_frontend' ), 1 );
	}
	
	public function init_backend(){
		$this->acform_cst_backend_constants();
		//var_dump(ACFORM_CST_AJAX_ON_FRONT);echo "b<br/>";
		$this->acform_cst_load();
	}
	
	public function init_frontend(){
		$this->acform_cst_frontend_constants();
		$this->acform_cst_load();
	}	
	
	public function acform_cst_backend_constants() {
		
		if ( ! defined( 'ACFORM_CST_AJAX_ON_FRONT' ) ) {
			define( 'ACFORM_CST_AJAX_ON_FRONT', self::acform_cst_is_ajax_on_front() );
		}
		
		// Settings page whatever the tab
		if ( ! defined( 'ACFORM_CST_SETTINGS' ) ) {
			define( 'ACFORM_CST_SETTINGS',(boolean) (is_admin() && ( ( isset( $_GET['page'] ) && 0 === strpos( $_GET['page'], 'acform_cst' ) ) || ! empty( $_REQUEST['acform_cst_settings'] ) ) ? true : false) );
		}
		
		// Admin
		if ( ! defined( 'ACFORM_CST_ADMIN' ) ) {
			define( 'ACFORM_CST_ADMIN', defined( 'DOING_CRON' ) || ( defined( 'WP_CLI' ) && WP_CLI ) || ( is_admin() && ! ACFORM_CST_AJAX_ON_FRONT ) );
		}        
        
	}
	
	public function acform_cst_frontend_constants() {
		
        // Front page
		if ( ! defined( 'ACFORM_CST_FRONTEND_SINGLE' ) ) {//var_dump(is_single());
			if($this->settings['post'] && $this->settings['page']){//echo "1";
				define( 'ACFORM_CST_FRONTEND_SINGLE', (is_single() || is_page() ? true : false));
			}
			if($this->settings['post'] && !$this->settings['page']){//echo "2";
				define( 'ACFORM_CST_FRONTEND_SINGLE', (is_single() ? true : false));
			}
			if(!$this->settings['post'] && $this->settings['page']){
				//echo "3";
				define( 'ACFORM_CST_FRONTEND_SINGLE', (is_page() ? true : false));
			}			
		}
	}
	
	public function acform_cst_load(){	 
		 
		if ( defined('ACFORM_CST_ADMIN') && ACFORM_CST_ADMIN ) {
		   require_once ACFORM_CST_LIB . '/acform-cst-backend-class.php';
		   new ACForm_CST_Backend($this->settings);
		}		
		
		if( ( defined('ACFORM_CST_FRONTEND_SINGLE') && ACFORM_CST_FRONTEND_SINGLE ) || ( defined('ACFORM_CST_AJAX_ON_FRONT') && ACFORM_CST_AJAX_ON_FRONT ) ){//var_dump(ACFORM_CST_AJAX_ON_FRONT);echo "f<br/>";
		   require_once ACFORM_CST_LIB . '/acform-cst-frontend-class.php';
		   new ACForm_CST_Frontend($this->settings);
		}
	}
	
	/**
	 * Tells whether the current request is an ajax request on frontend or not
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public static function acform_cst_is_ajax_on_front() {
		// Special test for plupload which does not use jquery ajax and thus does not pass our ajax prefilter
		// Special test for customize_save done in frontend but for which we want to load the admin
		$in = isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], array( 'acform_cst_post_comment') );
		$is_ajax_on_front = wp_doing_ajax() && $in;

		/**
		 * Filters whether the current request is an ajax request on front.
		 *
		 * @since 1.0
		 *
		 * @param bool $is_ajax_on_front Whether the current request is an ajax request on front.
		 */
		return apply_filters( 'acform_cst_is_ajax_on_front', $is_ajax_on_front );
	}
}
new ACForm_CST($acform_cst_settings);
        
?>