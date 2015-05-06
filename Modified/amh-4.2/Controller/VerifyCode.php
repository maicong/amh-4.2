<?php

/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object VerifyCode 验证码控制器
 * Update:2013-11-01
 * 
 */

class VerifyCode extends AmysqlController
{
	function IndexAction()
	{
		$str = "23456789ABCDEFGHJKMNPQRSTUVWXYZ";   
		$code_str = str_shuffle($str);
		$code = str_split(substr($code_str, 0,4));

		$_SESSION['VerifyCode'] = strtolower(implode('',$code));   

		$width = 115;
		$height = 29;

		$im = ImageCreate($width,$height);  // 创建图形   
		ImageColorAllocate($im,255,255,255); // 填充背景颜色为白色   

		// 用淡色给图形添加杂色   
		for ($i=0; $i<100; $i++) 
		{   
			$pxcolor = ImageColorAllocate($im,230,104,66);   
			ImageSetPixel($im,mt_rand(0,$width),mt_rand(0,$height),$pxcolor);   
		}   

		// 用深色调绘制边框   
		$bordercolor = ImageColorAllocate($im,255,255,255);   
		ImageRectangle($im,0,0,$width-1,$height-1,$bordercolor);   

		$offset = rand(10,30);   
		$font = array('View/font/UniversityRomanStd.otf');
		foreach ($code as $char) 
		{   
			$textcolor = ImageColorAllocate($im,230,104,106); 
			shuffle($font);
			imagettftext($im, 22, rand(-20,40), $offset, 26, $textcolor, $font[0], $char);
			$offset += $width/5-rand(0,2);   
		}   

		$code_str = str_shuffle($str);
		$code = str_split(substr($code_str, 0, 5));

		// 干扰字符
		$offset = rand(10,30);   
		foreach ($code as $char) 
		{   
			$textcolor = ImageColorAllocate($im,230,104,66);   
			shuffle($font);
			imagettftext($im, 8, rand(-20,40), $offset, 26, $textcolor, $font[0], $char);
			$offset += rand(5,10);   
		}

		// 禁止缓存   
		header("pragma:no-cache\r\n");   
		header("Cache-Control:no-cache\r\n");   
		header("Expires:0\r\n");   

		if (ImageTypes() & IMG_PNG) 
		{   
			header('Content-Type:image/png');   
			ImagePNG($im);   
		} 
		elseif (ImageTypes() & IMG_JPEG) 
		{   
			header('Content-Type:image/jpeg');   
			ImageJPEG($im);   
		} 
		else 
		{   
			header('Content-Type:image/gif');   
			ImageGif($im);   
		}
	}
}