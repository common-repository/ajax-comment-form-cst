<?php

class ACForm_CST_Base{
    
    var $settings;
    
    public function __construct() {
       // Nothing to load        
    }
    
    public function init(){
        $this->acform_cst_settings_action();
        
        if(!$this->settings)
        return $this->settings(); // Get Plugin settings
    }
    
    protected function acform_cst_settings_action(){
		if(isset($_POST['submit']) && $_GET['page'] == 'acform_cst_settings'){
			$formdata = $_POST['acform_cst'];
			$errormsg = $formdata['errormsg'];
			$successmsg = $formdata['successmsg'];
			$customstyle = $formdata['customstyle'];
			$ptypepost = $formdata['post'];
			$ptypepage = $formdata['page'];
			$captcha = $formdata['captcha'];
                        $gdpr = $formdata['gdpr'];
                        $mscroll = $formdata['mscroll'];
			//print_r($_POST['acform_cst']);
			$acform_cst_form_data = array('em'=>$errormsg,
										  'sm'=>$successmsg,
										  'css'=>$customstyle,
										  'post'=>$ptypepost,
										  'page'=>$ptypepage,
										  'captcha' => $captcha,
										  'gdpr' => $gdpr,
                                          'mscroll' => $mscroll);
			update_option('acform_cst_settings',$acform_cst_form_data);
		}
		
	}
    
    protected function settings(){
        
        $get_settings = get_option('acform_cst_settings'); // Get plugin settings
		
		$defaults = array(
						  "em" => "<strong>ERROR</strong>: You might have left one of the fields blank",
						  "sm" => "Thanks for your comment. We appreciate your response.",
                          "gdpr" => false,
                          "mscroll" => true
						  ); // Default settings that will override further.
		$this->settings = wp_parse_args( $get_settings, $defaults );
		
		$this->settings['css'] = 'input[type="text"].error,  input[type="email"].error,  textarea.error
{
       border: 1px solid red;
}
p.error{
        color:red;
}
        '; // Define inline css whether user hit submit or not
		// This will write css for the first time.
        //print_r($this->settings);var_dump($this->settings);
        $this->settings['post'] = true;
        $this->settings['page'] = false;
        //$this->settings['mscoll'] = true;

        
        if( !empty( $get_settings['css'] ) &&  trim($get_settings['css']) != '' )
		$this->settings['css'] = stripcslashes($get_settings['css']);
        
        //if( !empty( $get_settings['mscoll'] ) &&  trim($get_settings['mscoll']) != '' )
		//$this->settings['css'] = stripcslashes($get_settings['css']);
        
        if( is_array($get_settings) && count($get_settings) > 0){
		$this->settings['post'] = stripcslashes($get_settings['post']);
        $this->settings['page'] = stripcslashes($get_settings['page']);
        }		
        
       return $this->settings;
    }
}

?>
