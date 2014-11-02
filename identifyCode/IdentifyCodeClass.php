<?php
/**
Name:IdentifyCodeClass.php

Author:Tairy

Time:13/05/05

Funtion:idetify the confirm code of lzjtu jwc
*/
define('WORD_WIDTH',8);
define('WORD_HIGHT',12);
define('OFFSET_X',5);
define('OFFSET_Y',6);
define('WORD_SPACING',1);

class IdentifyCodeClass
{
	public function setImage($Image)
	{
		$this->ImagePath = $Image;
	}
	
	public function getData()
	{
		return $data;
	}
	
	public function getResult()
	{
		return $DataArray;
	}
	
	public function getHec()
	{
		$res = imagecreatefromgif($this->ImagePath);
		$size = getimagesize($this->ImagePath);
		$data = array();
		for($i = 0; $i < $size[1]; ++$i)
		{
			for($j = 0; $j < $size[0]; ++$j)
			{
				$rgb = imagecolorat($res,$j,$i);
				$rgbarray = imagecolorsforindex($res, $rgb);
				if($rgbarray['red'] < 140 || $rgbarray['green'] < 140 || $rgbarray['blue'] < 140)
				{
					$data[$i][$j]=1;
				}else{
					$data[$i][$j]=0;
				}
			}
		}
		$this->DataArray = $data;
		$this->ImageSize = $size;
	}
	
	public function run()
	{
		$result = "";
		$data = array("","","","","");
		
		for($i = 0; $i < 5; ++$i)
		{
			$x = ($i * (WORD_WIDTH + WORD_SPACING)) + OFFSET_X;
			$y = OFFSET_Y;
			for($h = $y; $h < (OFFSET_Y + WORD_HIGHT); ++$h)
			{
				for($w = $x; $w < ($x + WORD_WIDTH); ++$w)
				{
					$data[$i] .= $this->DataArray[$h][$w];
				}
			}
		}


		foreach($data as $numKey => $numString)
		{
			$max = 0.0;
			$num = 0;
			foreach($this->Keys as $key => $value)
			{
				$percent = 0.0;
				similar_text($value, $numString, $percent);
				if(intval($percent) > $max)
				{
					$max = $percent;
					$num = $key;
					if(intval($percent) > 95)
						break;
				}
			}
			$result .= $num;
		}
		$this->data = $result;
		//$this -> Draw();
		echo $result;
		return $result;
	}

	public function Draw()
	{
		for($i = 0; $i < $this->ImageSize[1]; ++$i)
		{
	        for($j = 0; $j < $this->ImageSize[0]; ++$j)
		    {
			    echo $this->DataArray[$i][$j];
	        }
		    echo "\n";
		}
	}
	
	public function __construct()
	{
		$this->Keys = array(
			'0'=>'011111101111011111000011110000111100001111010111110000111101011111100111011111100011110001000000',
			'1'=>'000111001011110001101100111011000000110000101100000011000010111000001100101011100000110010101010',
			'2'=>'011111101110101111000011100010110000011000101110000111000011101001100000111111111111111110100000',
			'3'=>'011111111100001100000011010111110001111000010111000000111100011111100111011111110011110001000000',
			'4'=>'000011100000111000011110001101100011011001100110110001101111111111111111000001100000011000000000',
			'5'=>'011111100110000011100000111111001111111011000111000000111101001111100111011111100011110000000100',
			'6'=>'011111110111011111000000110111011111111011110111110000111100011101100011011111110011110000010101',
			'7'=>'111111110101011000001100010111010001100001011101000110000011110100110000011100000011000000000001',
			'8'=>'011111101100001111000011110000110111111001111110110000111100001111000011011111100011110001010101',
			'9'=>'011111101100011011000011110000111110011111111111001110111000101111000110111111100111110000000010',
		);
	}
	
	protected $ImagePath;
	protected $DataArray;
	protected $ImageSize;
	protected $data;
	protected $Keys;
	protected $NumStringArray;
}
?>