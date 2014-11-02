<?php
class GetUserInfoClass
{
	public function resolveUserInfo( $html )
	{
		$userinfo = array();
		$firsthtml = explode( '<TR class="trbg1">', $html );
		$secondhtml = explode('&nbsp', $firsthtml[1]);
		$thirdhtml = iconv('GB2312', 'UTF-8', $secondhtml[0]);

		preg_match("#\W*学号：(\d{9})?#",$thirdhtml , $stunum);
		preg_match("#\W*姓名：(.*)?<#",$thirdhtml, $name);
		preg_match("#\W*学院：(.*)?<#",$thirdhtml, $college);
		preg_match("#\W*专业：(.*)?<#",$thirdhtml, $specialty);
		preg_match("#\W*行政班：(.*)?<#",$thirdhtml, $class);

		$userinfo['stunum'] = $stunum[1];
		$userinfo['name'] = $name[1];
		$userinfo['college'] = $college[1];
		$userinfo['specialty'] = $specialty[1];
		$userinfo['class'] = $class[1];

		$this -> addUserinfo( $userinfo );
	}
	public function addUserinfo( $userinfo )
	{	
		$wx_id = "oW_C8jhd2LE4YIPC3XPP3VyUKV-c";
		$sql = "UPDATE `user` SET `stu_num` = '".$userinfo['stunum']."', `truename` = '".$userinfo['name']."', `college` = '".$userinfo['college']."', `specialty` = '".$userinfo['specialty']."', `class` = '".$userinfo['class']."' WHERE `wx_id` = '".$wx_id."'";
		mysql_query($sql) or die(mysql_error());
	}
}