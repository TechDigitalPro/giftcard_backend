<?php    
/*
 * PHP QR Code encoder
 *
 * Exemplatory usage
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

class PhpQRCode{
    
	//processing form input
	//remember to sanitize user input in real-life solution !!!
	private $errorCorrectionLevel = 'H';		// L M Q H
	
	
	
	private $matrixPointSize = 3;				// 1 2 3 4 5 6 7 8 9 10
	
	
	private $date = 'sampledate';
	
	
	private $pngTempDir		= '';
	
	private $pngTempName    = '';

    private $pngLogoUrl     = '';
    
	/**
	 * 设置
	 */
	public function set($key,$value){
		$this->$key = $value;
	}
	
	public function __construct() {
	    include "qrlib.php";
	}
	
    public function init(){
	    //ofcourse we need rights to create temp dir
	    if (!file_exists($this->pngTempDir))
	        mkdir($this->pngTempDir);
	
	    if ($this->date != 'sampledate') { 
	            
	        // user data
	        if ($this->pngTempName != '') {
	            $filename = $this->pngTempDir.$this->pngTempName;
	        } else {
	           $filename = $this->pngTempDir.'test'.md5($this->date.'|'.$this->errorCorrectionLevel.'|'.$this->matrixPointSize).'.png';
	        }
	        QRcode::png($this->date, $filename, $this->errorCorrectionLevel, $this->matrixPointSize, 2);

            ////////////////////////////////// add Logo /////////////////////////////
            if(!empty($this->pngLogoUrl)) {
                $QR = imagecreatefromstring ( file_get_contents ( $filename ) );
                $logo = imagecreatefromstring ( file_get_contents ( $this->pngLogoUrl ) );
                $QR_width = imagesx ( $QR );
                $QR_height = imagesy ( $QR );
                $logo_width = imagesx ( $logo );
                $logo_height = imagesy ( $logo );
                if($logo_width >= $logo_height) {
                    $logo_qr_width = $QR_width / 5;
                    $scale = $logo_width / $logo_qr_width;
                    $logo_qr_height = $logo_height / $scale;
                } else {
                    $logo_qr_height = $QR_height / 5;
                    $scale = $logo_height / $logo_qr_height;
                    $logo_qr_width = $logo_width / $scale;
                }
                $from_width = ($QR_width - $logo_qr_width) / 2;
                $from_height = ($QR_height - $logo_qr_height) / 2;
                imagecopyresampled ( $QR, $logo, $from_width, $from_height, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height );
                imagepng ( $QR, $filename );//带Logo二维码的文件名
            }
	        
	    } else {    
	    
	        //default data
	        QRcode::png('http://tst.slkgou.com', $filename, $this->errorCorrectionLevel, $this->matrixPointSize, 2);
	        
	    }

        /*$return = basename($filename);

        import('function.alioss');
        $alioss_filename = str_replace( BASE_UPLOAD_PATH, SILU_ALIOSS_PATH, $filename);
        upload_to_alioss($filename,$alioss_filename );
        @unlink($filename);*/
	        
	    //display generated file
        $return=$filename;
	    return $return;
	    
	    QRtools::timeBenchmark();    
	}
}