<?php
$conn=mysql_connect("localhost","root","123456")or die('无法连接数据库: ' . mysql_error());
mysql_select_db("seustuinfo");
ini_set('max_execution_time', 300000000000000);
for($cardnum_i = 213111000; $cardnum_i < 213111001; $cardnum_i++)
{
	
	$url = "http://xk.urp.seu.edu.cn/jw_service/service/stuCurriculum.action?queryStudentId=".$cardnum_i."&queryAcademicYear=12-13-3";  
	$contents = file_get_contents($url);
	echo $contents;
// 	if(preg_match("#.<font class=\"Context_title\">学 生 课 表 查 询</font>.#", $contents))
// 	{
// 		continue;
// 	}
// 	else
// 	{
// 		preg_match("#院系:\[\d*\](.*)?</td>#",$contents, $college);
// 		preg_match("#学号:(.*)?</td>#",$contents, $stunum);
// 		preg_match("#专业:\[\d*\](.*)?</td>#",$contents, $speciality);
// 		preg_match("#一卡通号:(\d*)?</td>#",$contents, $cardnum);
// 		preg_match("#姓名:(.*)?</td>#",$contents, $name);
// 		mysql_query("set names utf8");
// 		if(empty($speciality))
// 			$speciality = null;
// 		$sql = "INSERT INTO `student` (college, stunum, speciality, cardnum, name) VALUES ('".$college[1]."', '".$stunum[1]."', '".$speciality[1]."', '".$cardnum[1]."', '".$name[1]."')";
// 		mysql_query($sql) or die(mysql_error());

// 		$course = explode('<tr height="34">',explode('<td bgcolor="#d7f0ff" width="35%"  valign="top" >',$contents)[1]);
// 		preg_match_all("#<td rowspan=\"5\" class=\"line_topleft\"* align=\"center\">(.*)?</td>#",$course[2], $morning);
// 		preg_match_all("#<td rowspan=\"5\" class=\"line_topleft\" align=\"center\">(.*)?</td>#",$course[7], $afternoon);
// 		preg_match_all("#<td class=\"line_topleft\" rowspan=\"2\"   align=\"center\">(.*)?</td>#",$course[12], $evening);

// 		$sql = "INSERT INTO `course` (stunum, class_order, monday, tuesday, wednesday, thursday, friday) VALUES ('".$stunum[1]."', '1', '".$morning[1][0]."', '".$morning[1][1]."', '".$morning[1][2]."', '".$morning[1][3]."', '".$morning[1][4]."')";
// 		mysql_query($sql);
// 		$sql = "INSERT INTO `course` (stunum, class_order, monday, tuesday, wednesday, thursday, friday) VALUES ('".$stunum[1]."', '2', '".$afternoon[1][1]."', '".$afternoon[1][2]."', '".$afternoon[1][3]."', '".$afternoon[1][4]."', '".$afternoon[1][5]."')";
// 		mysql_query($sql);
// 		$sql = "INSERT INTO `course` (stunum, class_order, monday, tuesday, wednesday, thursday, friday) VALUES ('".$stunum[1]."', '3', '".$evening[1][0]."', '".$evening[1][1]."', '".$evening[1][2]."', '".$evening[1][3]."', '".$evening[1][4]."')";
// 		mysql_query($sql);
// 	}
 }
// mysql_close($conn);
?>