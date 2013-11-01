<?php
class GetSenateInfoClass
{
	public function getSenateInfo()
	{
		header('Content-Type: text/html; charset=gbk');
		$url = "http://jiaowu.lzjtu.edu.cn/index.php?m=content&c=index&a=lists&catid=20";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		$announcement = curl_exec($ch);
		$announcement = explode('<div class="jd_news">', $announcement);
		$announcement = explode('<div id="pages"', $announcement[1]);
		$announcement = explode('</li>', $announcement[0]);
		for($i = 0; $i < 10;$i++ )
		{
			preg_match("#<a href=\"(.*?)\" .*>(.*?)<\/a><span class=\"ennum\">\| (.*?)</span>#", $announcement[$i],$matches);
			$matches[2] = iconv('GB2312', 'UTF-8', $matches[2]);
			$this -> addSenateInfo( $matches[2], $matches[1], $matches[3]);
		}
	}
	public function addSenateInfo($title,$link,$time)
	{
		$sql = "INSERT INTO `senateinfo` ( title, link, time ) VALUES ( '".$title."','".$link."','".$time."')";
		mysql_query($sql);
	}
	
}