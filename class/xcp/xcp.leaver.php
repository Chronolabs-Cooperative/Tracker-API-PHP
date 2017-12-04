<?php
// $Id: xcp.leaver.php 2.0.0 - xcp 2015-01-13 01:27 wishcraft $
//  ------------------------------------------------------------------------ //
//                        Chronolabs Australia                               //
//                         Copyright (c) 2015                                //
//                    <[ https://xortify.com/xcp/ ]>                         //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the SDPL Source Directive Public Licence           //
//  as published by Chronolabs Australia; either version 2 of the License,   //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Chronolab Australia        //
//  Chronolabs Cooperative:- 10/466 Illawarra Rd, Marrickville, NSW, 2204    //
//  ------------------------------------------------------------------------ //

if (!class_exists('xcp_leaver'))
{
	/**
	 * 
	 * @author 		Simon Antony Roberts aka. Leshy Cipherhouse <wishcraft@users.sourceforge.net>
	 * @package 	checksum
	 * @subpackage 	xcp
	 * @version 	2.0.0
	 * @copyright 	Chronolabs Cooperative Copyright (c) 2015
	 * @category 	forensics
	 * @namespace	xcp
	 * @since		2.0.0
	 * @license		GPL2
	 * @link		https://sourceforge.net/projects/chronolabs
	 * @link		https://sourceforge.net/projects/xortify
	 * @link		https://xortify.com/xcp
	 *
	 */
	class xcp_leaver extends xcp
	{
		var $crc;
		
		function __construct($enum_calc, $base, $len=29)
		{
			@$this->crc = $this->calc_crc($enum_calc, $base, $len);
		}
		
		function calc_crc ($enum_calc, $base, $len)
		{
			for ($qi=0; $qi<$len+1; $qi++)
			{
				$da = floor(9*($qi/$len));
				$pos = $this->GetPosition($enum_calc, $len, $qi);
				$pos = ceil($pos / ($len/ ($qi-1)));
				for($v=-$qi;$v<$pos;$v++)
				{
					if ($c>64)
						$c=0;
							
					$c++;
				}
				if (strlen($base->base[$c])>1)
				{
					$crc .= $da;
				} else {
					$crc .= $base->base[$c];
				}
				
				if ($qi<ceil($len/2))
				{
					$crc = $this->nux_cycle($crc, $enum_calc['result'], $len);
					$crc = $this->nux_cycle($crc, $enum_calc['prince'], $len);
				} elseif ($qi<ceil(($len/3)*2)) {
					$crc = $this->nux_cycle($crc, $enum_calc['motivation'], $len);
					$crc = $this->nux_cycle($crc, $enum_calc['official'], $len);
				} else {
					$crc = $this->nux_cycle($crc, $enum_calc['outsidecause'], $len);															
					$crc = $this->nux_cycle($crc, $enum_calc['karma'], $len);					
				}
				$crc = $this->nux_cycle($crc, $enum_calc['yin'], $len);
			}

			$crc = $this->nux_cycle($crc, $enum_calc['result'], $len);
			$crc = $this->nux_cycle($crc, $enum_calc['prince'], $len);
			$crc = $this->nux_cycle($crc, $enum_calc['karma'], $len);
			$crc = $this->nux_cycle($crc, $enum_calc['motivation'], $len);
			$crc = $this->nux_cycle($crc, $enum_calc['official'], $len);
			$crc = $this->nux_cycle($crc, $enum_calc['outsidecause'], $len);															
			$crc = $this->nux_cycle($crc, $enum_calc['yang'], $len);
			
			$crc = $this->nux_xor($crc, $enum_calc['nx_key']);			
			
			for ($qi=0; $qi<$len+1; $qi++)
			{
				$da = $len-floor(9*($qi/$len));
				$pos = ceil(ord($crc{$qi}) / 4);
				for($v=-$qi;$v<$pos;$v++)
				{
					if ($c>64)
						$c=0;
							
					$c++;
				}
				if (strlen($base->base[$c])>1)
				{
					$final_crc .= $da;
				} else {
					$final_crc .= $base->base[$c];
				}
			}
			return $final_crc;
		}
		
		private function GetPosition($enum_calc, $len, $qi)
		{
			if ($enum_calc['yin']>$enum_calc['yang'])
			{
				$cycle = floor((256*($enum_calc['yin']/$enum_calc['yang']))/(256*($enum_calc['yang']/$enum_calc['yin'])))+($len - $qi);
			} else {
				$cycle = ceil((256*($enum_calc['yang']/$enum_calc['yin']))/(256*($enum_calc['yin']/$enum_calc['yang'])))+($len - $qi);		
			}
			
			$result = $this->nuc_step($enum_calc['nuclear'], $enum_calc['result'], $cycle+$qi);
			$prince = $this->nuc_step($enum_calc['nuclear'], $enum_calc['prince'], $cycle+$qi);
			$karma = $this->nuc_step($enum_calc['nuclear'], $enum_calc['karma'], $cycle+$qi);
			$motivation = $this->nuc_step($enum_calc['nuclear'], $enum_calc['motivation'], $cycle+$qi);
			$official = $this->nuc_step($enum_calc['nuclear'], $enum_calc['official'], $cycle+$qi);
			$outsidecause = $this->nuc_step($enum_calc['nuclear'], $enum_calc['outsidecause'], $cycle+$qi);															

			$char = decbin($result.$prince.$karma.$motivation.$official.$outsidecause);
			
			return (ord($char));
		}
		
		private function nuc_step($nuclear, $var, $cycle)
		{
			$c=1;
			for($v=0;$v<($var+$cycle);$v++)
			{
				if ($c>strlen($nuclear))
					$c=0;
					
				$c++;
			}
			return substr($nuclear,$c,1);
		}
		
		private function nux_cycle($crc, $var, $len)
		{
			for($v=0;$v<($var+1);$v++)
			{
				for($y=1;$y<$len;$y++)
				{	
					$crc = substr($crc, $y, $len - $y).substr($crc, 0, $len-($len - $y));
				}
			}
			return $crc;
		}
		
		private function nux_xor($text_crc, $key)
		{
			for($i=0;$i<strlen($text_crc);) // Dont need to increment here
			{
				for($j=0;$j<strlen($key);$j++,$i++)
				{
					$crc .= $text_crc{$i} ^ $key{$j};
				}
			}			
			return $crc;
		}

	}
}