<?php
class GetCurriculumClass
{
	public function resolveCurriculumInfo( $html )
	{
		$html = iconv('GB2312', 'UTF-8', $html);
		$courseinfo = array();
		$firsthtml = explode('</table>', $html);
		$secondhtml = explode('</tr>', $firsthtml[0]);

		for( $i = 2; $i < 12; $i++)
		{
			$thirdhtml = explode('节</td>',$secondhtml[$i]);//这里的数字2表示第一节课
			$eachline = $thirdhtml[1];
			$eachclass = explode('</td>', $eachline);
			for( $j = 0; $j < 5; $j++)
			{
				if(!empty($eachclass[$j]))
				{
					if(preg_match("#<td align=\"Center\" rowspan=\".*\">(.*)?#",$eachclass[$j] , $course))
					{
						preg_match("#.* rowspan=\"(\d)?\".*#",$eachclass[$j], $rowspan);
						$rowspannum = $rowspan[1];
						$courseinfo[$i-2][$j] = str_replace('<br>','\n',$course[1])."@".$rowspannum;

					}
					else
					{
						$courseinfo[$i-2][$j] = "";
					}
				}
				else
				{
					$courseinfo[$i-2][$j] = "";
				}
			}
		}
		//print_r($courseinfo);
		$this -> addCurriculumInfo($courseinfo);
	}
	public function addCurriculumInfo($courseinfo)
	{
		for ($i=0; $i < 9; $i++) 
		{ 
				$wx_id = "oW_C8jhd2LE4YIPC3XPP3VyUKV-c";
				$class_ord = $i + 1;
				$sql = "INSERT INTO `curriculum` (wx_id, class_ord, monday, tuesday, wednesday, thursday, friday) VALUES ('".$wx_id."','".$class_ord."', '".$courseinfo[$i][0]."','".$courseinfo[$i][1]."','".$courseinfo[$i][2]."','".$courseinfo[$i][3]."','".$courseinfo[$i][4]."')";
				mysql_query($sql) or die(mysql_error());
		}
	}
}