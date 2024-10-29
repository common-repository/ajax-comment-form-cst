<style>
    .version{
        color:#e3d9d9;
        font-weight:bold;
    }
    .usedfor{
        margin: 10px 0;
    }
    textarea{
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    input[type="text"]{
        width:61%;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .cst-form-container{
        background-color: #fff;
        padding: 0 10px;
        border-radius: 5px;
    }
    .fa-exclamation-triangle{
        color:#CE3939;
    }
    .fa-check-square{
        color:#2EA04D;
    }
    .developer-profile{
        text-align: right;
        color: #e3d9d9;
        padding: 10px 10px 30px 10px;        
        border-radius: 5px;
        margin: 17px 0;
        font-weight: bold;
    }
    .developer-profile img{
        float: left;	
	width: 80px;
	height: auto;
	display: inline-block;
        margin-top: -18px;
    }
    .developer-profile span{
        display: inline-block;
    }
</style>
<div class='wrap'>
    <div class="cst-form-container">
    
    <form method="post">
        <h1><img src="<?php echo plugins_url('images/plugin_logo.png', ACFORM_CST_FILE);?>" width="50"/><?php echo ACFORM_CST;?> <span class="version"><?php echo ACFORM_CST_VERSION;?></span></h1>
        <table class="form-table">
          <tr>
             <th scope="row">Used For:</th>
             <td>
                <div class="usedfor"><label><input type="checkbox" name="acform_cst[post]" <?php if($this->settings['post']) echo "checked='checked'";?>/> Post</label></div>
                <div class="usedfor"><label><input type="checkbox" name="acform_cst[page]" <?php if($this->settings['page']) echo "checked='checked'";?>/> Page</label></div>
                <?php
                /*$args = array(
                    'public'   => true,
                    '_builtin' => false
                 );
                 
                 $output = 'names'; // names or objects, note names is the default
                 $operator = 'and'; // 'and' or 'or'
                 
                 $post_types = get_post_types( $args, $output, $operator ); 
                 
                 foreach ( $post_types  as $post_type ) {
                 
                ?>           
                <div class="usedfor"><label><input type="checkbox" name="acform_cst[<?php echo $post_type;?>]" /> <?php echo $post_type;?></label></div>
                <?php 
                 }*/
                ?>
             </td>
          </tr>
          <tr>
             <th scope="row">Error Message:</th>
             <td><input type="text" name="acform_cst[errormsg]" value="<?php echo $this->settings['em'];?>" />
             
             </td>
          </tr>
          <tr>
             <th scope="row">Success Message:</th>
             <td><input type="text" name="acform_cst[successmsg]" value="<?php echo $this->settings['sm'];?>" />
             
             </td>
          </tr>
          <tr>
             <th scope="row">Custom Styles:</th>
             <td><textarea rows="10" cols="60" name="acform_cst[customstyle]"><?php echo $this->settings['css'];?></textarea></td>
          </tr>
          <tr>
             <th scope="row">Mutual Scroll:</th>
             <td>
                <div class="usedfor"><label><input type="checkbox" name="acform_cst[mscroll]" <?php if($this->settings['mscroll']) echo "checked='checked'";?>/> Scroll to the given commented block, on successfull submit.</label></div>
                
             </td>
          </tr>
          <tr>
             <th scope="row">Captcha:</th>
             <td>
                <div class="usedfor"><label><input type="checkbox" name="acform_cst[captcha]" <?php if($this->settings['captcha']) echo "checked='checked'";?>/> Add captcha to form.</label></div>
                
             </td>
          </tr>
          <tr>
             <th scope="row">GDPR:</th>
             <td>
                <div class="usedfor"><label><input type="checkbox" name="acform_cst[gdpr]" <?php if($this->settings['gdpr']) echo "checked='checked'";?>/> GDPR field added to the comment form.</label></div>
                
             </td>
          </tr>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value='Save Changes'></p>
    </form>
    </div>
    <div class="developer-profile"><img src="<?php echo plugins_url('images/logo.png', ACFORM_CST_FILE);?>" width="50"/> <span>Design & Developed By Codesoftech </span></div>
</div>
