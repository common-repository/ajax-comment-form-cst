<?php
class Captcha{
	var $chars = 'ABCDEFGHKLMNPQRSTUVWYZabcedfghijklmnopqrstuvwxyz123456789'; // O  and 0 (Zero) are visually similar, that's why I am not using it
	var $RandomStr = '';
    var $height = 60;
    var $width = 240;
    var $bgcolorhex = "ffffff";
    var $fgcolorhex = "000000";
    var $security = 1;
  
  function imagecolorallocatehex($image,$hexcolor) {
	  $red = substr($hexcolor,0,2);
	  $green = substr($hexcolor,2,2);
	  $blue = substr($hexcolor,4,2);
	  $result = imagecolorallocate($image,hexdec($red),hexdec($green),hexdec($blue));
	  return($result);
  }
   function randcolor() {
	  $red = mt_rand(0,100);
	  $blue = mt_rand(0,100);
	  $green = mt_rand(0,100);
	  $hexcolor = dechex2($red).dechex2($green).dechex2($blue);
	  return($hexcolor);
   }
 function OutputCaptcha($width=100,$height=30,$length=6){
		
		for($i = 0; $i < $length; $i++){ // Generating the captcha string

		   $pos = mt_rand(0, strlen($this->chars)-1);

		   $this->RandomStr .= substr($this->chars, $pos, 1);

		}
        $fonts["./ariblk.ttf"]["min"] = 35; // For windows / Localhost
        $fonts["./ariblk.ttf"]["max"] = 35; // For windows / Localhost
		$ResultStr = $this->RandomStr;
		$NewImage = imagecreate ($this->width, $this->height);
		$bgcolor = $this->imagecolorallocatehex($NewImage,$this->bgcolorhex);
        //imagefilledrectangle($NewImage,0,0,$this->width,$this->height,$bgcolor);
        // create our foreground color
		if (empty($this->fgcolorhex)) $fgcolorhex = $this->randcolor();
		$fgcolor1 = $this->imagecolorallocatehex($NewImage,$fgcolorhex);
		
		// draw static on the background
		$totalpix = $this->width * $this->height;
		for ($t=0; $t<($totalpix / $this->security); $t++) {
		  $pixx = mt_rand(0,$this->width);
		  $pixy = mt_rand(0,$this->height);
		  imagesetpixel($NewImage,$pixx,$pixy,$fgcolor1);
		}
		//$NewImage =imagecreatefromjpeg("img.jpg");//image create by existing image and as back ground 
		
		$TextColor = imagecolorallocate($NewImage, 255, 20, 20);//text color-Black
		
		$line_clr = imagecolorallocate($NewImage, 0, 255, 11);
		//Top left to Bottom Left	
		imageline($NewImage, 0, $height-22, $width, $height-1, $line_clr);	
		// Bottom Left to Bottom Right	
		imageline($NewImage, $width-1, 0, $width-100, $height, $line_clr);
		imageline($NewImage, $height-1, 0, $width-100, $width, $line_clr);
		imageline($NewImage, $width-1, 0, $height-1, $width, $line_clr);
		// if there's more than one font specified, randomly select one (and it's size)
		$fontfiles = array_keys($fonts);
		$fontfile = $fontfiles[mt_rand(0,count($fontfiles)-1)];
		//$fontsize = mt_rand($fonts[$fontfile]["min"],$fonts[$fontfile]["max"]);
		$fontsize = 35;

		// find out how big our text will be
		$box = $this->imagettfbbox_t($fontsize,0,$fontfile,$ResultStr);
		$box = $this->fixbbox($box);
		
		// randomly place our text inside the box somewhere
		//$fontx = rand(0,($this->width - $box["width"]));
		//$fonty = rand(0,($this->height - $box["height"]));

		$fontx = 9;
		$fonty = 40;
					
		//imagestring($NewImage,15, 20, 6, "Hello world", $TextColor);// Draw a random string horizontally 
		imagettftext($NewImage,$fontsize,0,$fontx,$fonty,$bgcolor,$fontfile,$ResultStr);
		
		$_SESSION['captcha_val'] = $ResultStr;// carry the data through session
		
		
		header("Content-type: image/jpeg");// out out the image 
		
		imagejpeg($NewImage);//Output image to browser 
		
		}
		function imagettfbbox_t($size, $angle, $fontfile, $text) {
		  // compute size with a zero angle
		  $coords = imagettfbbox($size, 0, $fontfile, $text);
		
		  // convert angle to radians
		  $a = $angle * M_PI / 180;
		
		  // compute some usefull values
		  $ca = cos($a);
		  $sa = sin($a);
		  $ret = array();
		
		  // perform transformations
		  for($i = 0; $i < 7; $i += 2)
		  {
		   $ret[$i] = round($coords[$i] * $ca + $coords[$i+1] * $sa);
		   $ret[$i+1] = round($coords[$i+1] * $ca - $coords[$i] * $sa);
		  }
		  return $ret;
       }
       function fixbbox($bbox) {
		  $tmp_bbox["left"] = 0 - min($bbox[0],$bbox[2],$bbox[4],$bbox[6]);
		  $tmp_bbox["top"] = 0 - min($bbox[1],$bbox[3],$bbox[5],$bbox[7]);
		  $tmp_bbox["width"] = max($bbox[0],$bbox[2],$bbox[4],$bbox[6]) - min($bbox[0],$bbox[2],$bbox[4],$bbox[6]) + 1;
		  $tmp_bbox["height"] = max($bbox[1],$bbox[3],$bbox[5],$bbox[7]) - min($bbox[1],$bbox[3],$bbox[5],$bbox[7]);
		  return $tmp_bbox;
       }

}

?>