<?php
header('Content-Type: text/html; charset=gbk');
$cookie_file = dirname(__FILE__).'/cookie.txt'; 
$url="http://jwxt.hhu.edu.cn:7778/pls/wwwbks/bks_login2.login";
$ch = curl_init($url); //初始化
$data = array('stuid' => '1105040329', 'pwd'=>'180232'); 
curl_setopt($ch, CURLOPT_POST, 1);  
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HEADER, 0); //不返回header部分
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //返回字符串，而非直接输出
curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie_file); //存储cookies
curl_exec($ch);
curl_close($ch);
//使用上面保存的cookies再次访问
$url = "http://jwxt.hhu.edu.cn:7778/pls/wwwbks/xk.CourseView";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); //使用上面获取的cookies
$response = curl_exec($ch);
curl_close($ch);

/*$deleteHeadResult这个数组里面的元素
	索引1包含姓名院系课表
	索引3包含上课的时间
	其他的都没有用
*/

$deleteHeadResult = explode('</table>',$response);

///////////////////////////////////////////////////////////
////////////一下代码获取学号姓名专业院系///////////////////
///////////////////////////////////////////////////////////
$a = $deleteHeadResult[1];
$b = explode('</span>',$a);
$c = explode('<span class="td1">',$b[0]);
$nameAndColledge = explode(' ',$c[1]);
//院系
$College = $nameAndColledge[0];
//专业
$Specialty = $nameAndColledge[1];
$nameAndNum = explode('(',$nameAndColledge[2]);
//姓名
$name = $nameAndNum[0];
//学号
$d = explode(')',$nameAndNum[1]);
$StudyNum = $d[0];
//End

/*
1.先删除姓名和学分信息
2.获取每一大节的课程，下标运算符中的数据表示第几大节
3.获取每一节课，有html标签[]
4.出去html标签
5.出去&nbsp，产生干净的课程信息
*/
//echo $deleteHeadResult[1];
//第一大节
$firstCourse = explode('</span>',$deleteHeadResult[1]);
$secondCourse = explode('</TR>',$firstCourse[2]);
//echo $secondCourse[7];
$thirdCourse = explode('<td class=td_biaogexian>',$secondCourse[5]);//这里的1表示第一大节
$forthCourse = strip_tags($thirdCourse[0]);//这里的1表示周一
//echo $forthCourse;
$fifthCourse = explode('&nbsp',$forthCourse);
$resultCourse = $fifthCourse[0];


//时间和地点
$firstInfo = explode('<TR>',$deleteHeadResult[3]);
$secondInfo = strip_tags($firstInfo[1]);//这里的1表示第一行课程的信息
$thirdInfo = explode('&nbsp;',$secondInfo);//这个数组存储每一行过滤好的信息
//echo count($firstInfo);
print_r($thirdInfo);
?> 