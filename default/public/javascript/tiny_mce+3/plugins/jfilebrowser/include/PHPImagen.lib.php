<?php

if(isset($_GET['source'])) {
	highlight_file(__FILE__);
	exit;
}

/**
 * PHPImagen - Manejo de imagenes BETA Release Candidate 3
 * @author Myokram
 * 11/01/08
 */

/**
 * Para usar esta clase:
 * Visita http://php.myokram.info/phpimagen
 *  	
 */

class Imagen {
	
	public $source = null;
	private $url = "/";
	private $ext = "jpg";
	private $w;
	private $h;
	private $mh;
	private $mw;
	private $rh;
	private $rw;
	private $cut = 0;
	private $info = array();
	private $f1 = "imagecreatefromjpeg";
	private $f2 = "imageJpeg";
	private $type = "jpeg";
	private $error = false;
	
	public function __construct($url=null) {
		$url = trim($url);
		if(empty($url)) {
			$this->info['basename'] = "rd_".substr(md5(rand()),0,8).".jpg";
			$this->error("No se especifico la imagen");
			return;
		}
		$this->url = $url;
		if($resources = $this->getResources($this->url)) {
			$this->f1 = $resources[0];
			$this->f2 = $resources[1];
			$this->type = $resources[2];
			$this->info = $resources[3];
		} else return false;
		$f1 = $this->f1;
		$this->source = @$f1($this->url);
		if (!$this->source) {
			$this->error("No se pudo abrir la imagen");
			return false;
		}
		$this->w = imagesx($this->source);
		$this->h = imagesy($this->source);
		$this->rh = $this->h;
		$this->rw = $this->w;
		return true;
	}
	
	public function resize($maxancho=null,$maxalto=null,$cut=false) {
		if($this->stop()) 
			return;
		if($cut != false) $this->cut = true;
		$this->mw = (!is_numeric($maxancho) or $maxancho < 1) ? $this->w : intval($maxancho);
		$this->mh = (!is_numeric($maxalto) or $maxalto < 1) ? $this->h : intval($maxalto);
		$diff_w = $this->w/$this->mw;
		$diff_h = $this->h/$this->mh;
		if($this->cut == true) {
			$this->rh = $this->mh;
			$this->rw = $this->mw;
			if($diff_w > $diff_h) {
				$prop = $this->mh/$this->h;
				$this->mw = round($this->w*$prop);
				$dist_x = ($this->rw-$this->mw)/2;
			} else {
				$prop = $this->mw/$this->w;
				$this->mh = round($this->h*$prop);
				$dist_y = ($this->rh-$this->mh)/2;
			}
		} else {
			if($diff_w > $diff_h) {
				$prop = $this->mw/$this->w;
				$this->mh = round($this->h*$prop);
			} else {
				$prop = $this->mh/$this->h;
				$this->mw = round($this->w*$prop);
			}
			$this->rw = $this->mw;
			$this->rh = $this->mh;
		}
		$output = imagecreatetruecolor($this->rw, $this->rh);
		if($this->type == "gif" or $this->type == "png") {
			$trn_i = imagecolortransparent($this->source);
			if ($trn_i >= 0) {
				$trn_c = imagecolorsforindex($this->source, $trn_i);
				$trn_i = imagecolorallocate($output, $trn_c['red'], $trn_c['green'], $trn_c['blue']);
				imagefill($output, 0, 0, $trn_i);
				imagecolortransparent($output, $trn_i);
			} elseif($this->type == "png") {
                imagealphablending($output, false);
                $color = imagecolorallocatealpha($output, 0, 0, 0, 127);
                imagefill($output, 0, 0, $color);
                imagesavealpha($output, true);
            } 
		}
		$r = imagecopyresampled($output, $this->source, $dist_x, $dist_y, 0, 0, $this->mw, $this->mh, $this->w, $this->h);
		$this->source = $output;
		return $r;
	}
	
	private function getResources($file) {
		$info = pathinfo($file);
		$info['extension'] = strtolower($info['extension']);
		$r = array();
		switch($info['extension']) {
			case 'jpg': $r[0] = "imagecreatefromjpeg"; $r[1] = "imageJpeg"; $r[2] = "jpeg"; break;
			case 'gif': $r[0] = "imagecreatefromgif"; $r[1] = "imageGif"; $r[2] = "gif"; break;
			case 'png': $r[0] = "imagecreatefrompng"; $r[1] = "imagePng"; $r[2] = "png"; break;
			default: $this->info['basename'] = $this->info['basename'].".jpg"; $this->error("El tipo de archivo definido no es válido"); return false; break;
		}
		$r[3] = $info; 
		return $r;
	}
	
	private function rgbhex2rgb($c) {
		if($this->stop()) 
			return;
		if(!$c) return false;
		$c = trim($c);
		$out = array();
		if(eregi("^[0-9ABCDEFabcdef\#]+$", $c)){
			$c = str_replace('#','', $c);
			$l = strlen($c);
			if($l != 3 and $l != 6) return false;
			$out[0] = $out['r'] = $out['red'] = ($l == 3) ? hexdec(substr($c,0,1).substr($c,0,1)) : hexdec(substr($c,0,2));
			$out[1] = $out['g'] = $out['green'] = ($l == 3) ? hexdec(substr($c,1,1).substr($c,1,1)) : hexdec(substr($c,2,2));
			$out[2] = $out['b'] = $out['blue'] = ($l == 3) ? hexdec(substr($c,2,1).substr($c,2,1)) : hexdec(substr($c,4,2));
		} elseif (eregi("^[0-9]+(,| |.)+[0-9]+(,| |.)+[0-9]+$", $c)){
			if(eregi(",", $c)) $e = explode(",",$c);
			elseif(eregi(" ", $c)) $e = explode(" ",$c);
			elseif(eregi(".", $c)) $e = explode(".",$c);
			else return false;
			if(count($e) != 3) return false;
			if(is_numeric($e[0]) and $e[0] >= 0 and $e[0] <= 255)
				$out[0] = $out['r'] = $out['red'] = intval($e[0]);
			if(is_numeric($e[1]) and $e[1] >= 0 and $e[1] <= 255)
				$out[1] = $out['g'] = $out['green'] = intval($e[1]);
			if(is_numeric($e[2]) and $e[2] >= 0 and $e[2] <= 255)
				$out[2] = $out['b'] = $out['blue'] = intval($e[2]);
		} 
		return (count($out) != 9) ? false : $out;
	}
	
	private function error($msg) {
		$width = strlen($msg)*8;
		$width = ($width < 370) ? 370 : $width;
		$url = (strlen($this->url) > 45) ? substr($this->url,0,33)."...".substr($this->url,-10) : $this->url;
		$this->rw = $width;
		$this->rh = 145;
	    $this->source = @imagecreate ($this->rw, $this->rh);
	    $background_color = imagecolorallocate ($this->source, 230, 230, 230);
	    $text_color_red = imagecolorallocate ($this->source, 255,0,0);
	    $text_color_black = imagecolorallocate ($this->source, 50, 50, 50);
	    $text_color_green = imagecolorallocate ($this->source, 0, 150, 0);
	    $text_color_blue = imagecolorallocate ($this->source, 0, 0, 150);
	    imagestring ($this->source, 5, 19, 20, "ERROR: LA IMAGEN NO PUDO SER CARGADA", $text_color_red);
	    imagestring ($this->source, 3, 16, 50, "Hubo un error procesando la imagen:", $text_color_black); 
	    imagestring ($this->source, 3, 16, 65, $url, $text_color_green); 
	    imagestring ($this->source, 3, 16, 95, $msg, $text_color_blue);
	    imagestring ($this->source, 2, 10, 125, sprintf(base64_decode("UEhQSW1hZ2VuIChjKSVzIC0gQnkgTXlPa3JhbQ=="),date('Y')),$text_color_black);
		$this->error = true;
		return true;
	}
	
	private function stop() {
		return ($this->error !== false);
	}
	
	public function doPrint($quality=null) {
		$quality = (is_numeric($quality) and $quality <= 100 and $quality >= 1) ? intval($quality) : 75;
		Header("Content-type: image/".$this->type);
		$f2 = $this->f2;
		switch($this->type) {
			case "gif":
				imagegif($this->source);
				break;
			case "jpeg":
				imagejpeg($this->source,null,$quality);
				break;
			case "png":
				$quality = 10 - (round($quality / 10));
				imagepng($this->source,null,$quality,PNG_ALL_FILTERS);
				break;
		} 
		exit;
	}
	
	public function doSave($destination,$quality=null) {
		$quality = (is_numeric($quality) and $quality <= 100 and $quality >= 1) ? intval($quality) : 75;
		Header("Content-type: image/".$this->type);
		$f2 = $this->f2;
		$info = pathinfo($destination);
		if(empty($info['filename']))
			$this->error("Para guardar se debe especificar un nombre de archivo válido");
		if($info['extension'] !== $this->info['extension'])
			$destination .= ".".$this->info['extension'];
		switch($this->type) {
			case "gif":
				return imagegif($this->source,$destination);
				break;
			case "jpeg":
				return imagejpeg($this->source,$destination,$quality);
				break;
			case "png":
				$quality = 10 - (round($quality / 10));
				return imagepng($this->source,$destination,$quality,PNG_ALL_FILTERS);
				break;
		} 
		return false;
	}
	
	public function doDownload($quality=null) {
		$quality = (is_numeric($quality) and $quality <= 100 and $quality >= 1) ? intval($quality) : 75; 
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".$this->info['basename']."\"\n");
		$f2 = $this->f2;
		switch($this->type) {
			case "gif":
				imagegif($this->source);
				break;
			case "jpeg":
				imagejpeg($this->source,null,$quality);
				break;
			case "png":
				$quality = 10 - (round($quality / 10));
				imagepng($this->source,null,$quality,PNG_ALL_FILTERS);
				break;
		} 
		exit;
	}
	
	public function watermark($file,$posx = 0,$posy = 0,$loop=false,$rotation = 0) {
		if($this->stop()) 
			return;
		if(false == ($resources = $this->getResources($file)))
			return;
		$f1 = $resources[0];
		if(false == ($wm = @$f1($file)))
			return $this->error("La marca de agua \"$file\"no pudo ser cargada");
			
		if($resources[3]['extension'] == "gif") {
			$newwm = md5($file.rand()).".png";
			imagepng($wm,$newwm);
			if(file_exists($newwm)) { 
				$wm = imagecreatefrompng($newwm);
				@unlink($newwm);
			}
		}
		
		if($rotation <> 0 and is_numeric($rotation)) 
			$wm = imagerotate($wm, $rotation, -1);
			
		$wm_w = @imagesx($wm);
		$wm_h = @imagesy($wm);
		
		if($this->type == "gif") {
			$matte = imagecreatetruecolor($wm_w,$wm_h);
			$trans_color = imagecolorallocatealpha($matte,254,254,254,0);
			imagefill($matte, 0,0,$trans_color);
			imagecopy($matte,$wm,0,0,0,0,$wm_w,$wm_h);
			imagecolortransparent($matte,$trans_color);
			$wm = $matte;
		}
		
		imagealphablending($this->source, true);  
		imagesavealpha($this->source, true); 
		imagealphablending($wm, false);  
		
		$posx = ($posx === false) ? ($this->rw - $wm_w) / 2 : intval($posx);
		$posy = ($posy === false) ? ($this->rh - $wm_h) / 2 : intval($posy);
		if($posx < 0) $posx = ($this->rw - $wm_w) + $posx;
		if($posy < 0) $posy = ($this->rh - $wm_h) + $posy;
		
		$this->imagecopy2($this->source, $wm, $posx, $posy, 0, 0, $wm_w, $wm_h, 100);
		
		if($loop === 1 or $loop === 3) {
			$rest = ceil($posx/$wm_w);
			$n = 1;
			while($n <= $rest) {
		        $this->imagecopy2($this->source, $wm, $posx-($wm_w*$n), $posy, 0, 0, $wm_w, $wm_h, 100);
		        $n++;
		    }
			$rest = ceil(($this->rw - $posx)/$wm_w);
			$n = 1;
			while($n <= $rest) {
		        $this->imagecopy2($this->source, $wm, $posx+($wm_w*$n), $posy, 0, 0, $wm_w, $wm_h, 100);
		        $n++;
		    }
		} 
		if($loop === 2 or $loop === 3) {
			$rest = ceil($posy/$wm_h);
			$n = 1;
			while($n <= $rest) {
		        $this->imagecopy2($this->source, $wm, $posx, $posy-($wm_h*$n), 0, 0, $wm_w, $wm_h, 100);
		        $n++;
		    }
			$rest = ceil(($this->rh - $posy)/$wm_h);
			$n = 1;
			while($n <= $rest) {
		        $this->imagecopy2($this->source, $wm, $posx, $posy+($wm_h*$n), 0, 0, $wm_w, $wm_h, 100);
		        $n++;
		    }
		}
		return true;
	}
	
	private function imagecopy2(&$s, $wm, $posx, $posy, $srcx, $srcy, $wm_w, $wm_h, $pct = null) {
		if($this->type == "gif")
		 	return imagecopymerge($s, $wm, $posx, $posy, $srcx, $srcy, $wm_w, $wm_h, $pct);
		else
		 	return imagecopy($s, $wm, $posx, $posy, $srcx, $srcy, $wm_w, $wm_h);
	}
	
	public function textmark($text,$color,$size,$font=null,$rot=0,$posx = 0,$posy = 0) {
		if($this->stop()) 
			return;
		if($color = $this->rgbhex2rgb($color)) {
			$color = imagecolorallocate ($this->source, intval($color[0]), intval($color[1]), intval($color[2]));
			$posx = ($posx === false) ? $this->rw / 2 : intval($posx);
			$posy = ($posy === false) ? $this->rh / 2 : intval($posy);
			if($posx < 0) $posx = $this->rw + $posx;
			if($posy < 0) $posy = $this->rh + $posy;
			if(function_exists("imagettftext") and isset($font)) 
				if(@imagettftext($this->source, $size, intval($rot), $posx, $posy, $color, $font, $text))
					return true;
		    imagestring($this->source, $size, $posx, $posy, $text, $color);
			return true;
		}
		$this->error("El color de texto debe ser indicado en formato RGB o HTML válido");
		return false;
	}
	
	public function colorize($color,$exact=false) {
		if($this->stop()) 
			return;
		if($color = $this->rgbhex2rgb($color)) {
			if($exact == true)
				$this->grayscale();	
			if(function_exists("imagefilter"))
				return imagefilter($this->source, IMG_FILTER_COLORIZE, $color[0], $color[1], $color[2]);
			imagetruecolortopalette($this->source, FALSE, 1024); 
			$total = imagecolorstotal($this->source);
			for ($i = 0; $i < $total; $i++){ 
				$c = imagecolorsforindex($this->source, $i);           
				imagecolorset($this->source, $i, $color[0]*$c['red']/256, $color[1]*$c['green']/256, $color[2]*$c['blue']/256);
			}
			return true;
		}
		$this->error("El nuevo color de imagen debe ser indicado en formato RGB o HTML válido");
		return false;
	} 
	
	public function grayscale() {
		if($this->stop()) 
			return;
		if(function_exists("imagefilter"))
			return imagefilter($this->source, IMG_FILTER_GRAYSCALE);
		if(imageistruecolor($this->source))
			imagetruecolortopalette($this->source, false, 256);
		$total = imagecolorstotal($this->source);
		for ($i = 0; $i < $total; $i++) {
			$col = imagecolorsforindex($this->source, $i);
			$gray = round(0.299 * $col['red'] + 0.587 * $col['green'] + 0.114 * $col['blue']);
			imagecolorset($this->source, $i, $gray, $gray, $gray);
		}
		imagealphablending($this->source,true);
		imagesavealpha($this->source,true);
	}
	
}

// Release date: 11/01/2008 @ 21:11:23 GMT-5

?>