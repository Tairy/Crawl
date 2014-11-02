<?php
class GetCookieAndIdentifyCodeClass
{
	public function getCookiesAndIdentifyCode()
	{
		//第一步 获取cookie
		header('Content-Type: text/html; charset=gbk');
		$url = "http://tms.ahnu.edu.cn";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_URL, $url); 
		$cookie_file = dirname(__FILE__).'/cookie.txt'; 
		curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie_file);
		curl_exec($ch);


		//第二步，使用上面的cookie获取一个验证码
		$url = "http://tms.ahnu.edu.cn/(lrmaxm45tofm4h55aafp4rrh)/CheckCode.aspx";
		curl_setopt ($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
		$response = curl_exec($ch);
		curl_close($ch);

		//保存验证码
		$filename = 'file.gif';
		$fh = fopen($filename, "w");
		fwrite($fh, $response);
		fclose($fh);
		return $filename;
	}
}