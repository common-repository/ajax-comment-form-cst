<?php
class ACForm_CST_Backend{
	protected $settings;

	public function __construct($settings) {
		//parent::__construct();\
		//
		$this->settings = $settings;
		add_action( 'admin_menu', array( $this, 'acform_cst_menu' ) , 10); // Add menu for settings page
		add_action( 'admin_notices', array( $this, 'acform_cst_notice_success' ) ); // success message on submit
		//add_action( 'admin_enqueue_scripts', array( $this, 'acform_cst_enqueue_script' ) );
		
		if($this->settings['gdpr'] == 'on'){ // add comment column if gdpr is active
		add_filter('manage_edit-comments_columns', array( $this, 'displayGDPRDateColumnInComment' ) );
        add_action('manage_comments_custom_column', array( $this, 'displayGDPRDateInComment' ) , 10, 2);
		}
	}

	public function acform_cst_menu() {
		add_options_page(			
			ACFORM_CST,
            'ACForm CST',
			'manage_options',
			'acform_cst_settings',
			array(
				$this,
				'acform_cst_settings_page'
			)
		);
	}
	
	public function acform_cst_enqueue_script(){
		wp_enqueue_style( 'acform_cst_fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css' );
	}

	public function acform_cst_settings_page() {		
        require_once ACFORM_CST_DIR . '/acform-cst-settings.php';
	}
	
	
	
	function acform_cst_notice_success(){
		$screen = get_current_screen();		
		
		if($screen->id == "settings_page_acform_cst_settings" && isset($_POST['submit'])){
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php _e( 'Settings successfully updated!', 'acform_cst' ); ?></p>
			</div>
			<?php
		}
	}
	
	function displayGDPRDateInComment($column = '', $commentId = 0) {
		//print_r($column);
        if ($column === 'acform-cst-gdpr-date') {
            $date = get_comment_meta($commentId, '_gdpr', true);
            $value = (!empty($date)) ? $this->localDateFormat(get_option('date_format') . ' ' . get_option('time_format'), $date) : __('Not accepted.');
            echo apply_filters('acform_cst_gdprc_accepted_date', $value, $commentId);
        }
        return $column;
	}
	
	function localDateFormat($format = '', $timestamp = 0) {
			$date = $this->localDateTime($timestamp);
			return date_i18n($format, $date->getTimestamp(), true);
	}
	
	function localDateTime($timestamp = 0) {
			$gmtOffset = get_option('gmt_offset', '');
			if ($gmtOffset !== '') {
				$negative = ($gmtOffset < 0);
				$gmtOffset = str_replace('-', '', $gmtOffset);
				$hour = floor($gmtOffset);
				$minutes = ($gmtOffset - $hour) * 60;
				if ($negative) {
					$hour = '-' . $hour;
					$minutes = '-' . $minutes;
				}
				$date = new \DateTime(null, new \DateTimeZone('UTC'));
				$date->setTimestamp($timestamp);
				$date->modify($hour . ' hour');
				$date->modify($minutes . ' minutes');
			} else {
				$date = new \DateTime(null, new \DateTimeZone(get_option('timezone_string', 'UTC')));
				$date->setTimestamp($timestamp);
			}
			return new \DateTime($date->format('Y-m-d H:i:s'), new \DateTimeZone('UTC'));
	}
	
	function displayGDPRDateColumnInComment($columns = array()) {
			$columns['acform-cst-gdpr-date'] = apply_filters('acform_cst_gdprc_accepted_date', __('GDPR Accepted On'));
			return $columns;
	}	



}

?>