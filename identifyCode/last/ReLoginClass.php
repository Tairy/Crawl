<?php
class ReLoginClass
{
	public function reLogin( $confirmCode )
	{
		
		$ch = curl_init();
		header('Content-Type: text/html; charset=gbk');
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$cookie_file = dirname(__FILE__).'/cookie.txt';
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);


		//第四步 使用之前的cookie和验证码登陆系统
		$url = "http://202.201.29.195";
		$data = array('__VIEWSTATE' => "dDwtMTg3MTM5OTI5MTs7PnfOQLJbLWbaWp8pZXfgrVSeSYYB",'TextBox1' => "201100048", 'TextBox2'=>"620422199308053536", 'TextBox3' => $confirmCode, 'RadioButtonList1' => "%D1%A7%C9%FA", 'Button1'=>"",'lbLanguage'=>"");
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_exec($ch);


		//第五步 获取课表信息
		$url = "http://202.201.29.195/xskbcx.aspx?xh=201100048&xm=%E4%BD%99%E7%92%87&gnmkdm=N121603";
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_REFERER, "http://202.201.29.195/xskbcx.aspx?xh=201100048&xm=%E4%BD%99%E7%92%87&gnmkdm=N121603");
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
}