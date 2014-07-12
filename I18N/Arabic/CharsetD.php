<?php
/**
 * ----------------------------------------------------------------------
 *  
 * Copyright (c) 2006-2013 Khaled Al-Sham'aa.
 *  
 * http://www.ar-php.org
 *  
 * PHP Version 5 
 *  
 * ----------------------------------------------------------------------
 *  
 * LICENSE
 *
 * This program is open source product; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License (LGPL)
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 *  
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *  
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/lgpl.txt>.
 *  
 * ----------------------------------------------------------------------
 *  
 * Class Name: Detect Arabic String Character Set
 *  
 * Filename:   CharsetD.php
 *  
 * Original    Author(s): Khaled Al-Sham'aa <khaled@ar-php.org>
 *  
 * Purpose:    This class will return Arabic character set that used for
 *             a given Arabic string passing into this class, those available
 *             character sets that can be detected by this class includes
 *             the most popular three: Windows-1256, ISO 8859-6, and UTF-8.
 *              
 * ----------------------------------------------------------------------
 *  
 * Detect Arabic String Character Set
 *
 * The last step of the Information Retrieval process is to display the found 
 * documents to the user. However, some difficulties might occur at that point. 
 * English texts are usually written in the ASCII standard. Unlike the English 
 * language, many languages have different character sets, and do not have one 
 * standard. This plurality of standards causes problems, especially in a web 
 * environment.
 *
 * This PHP class will return Arabic character set that used for a given
 * Arabic string passing into this class, those available character sets that can
 * be detected by this class includes the most popular three: Windows-1256,
 * ISO 8859-6, and UTF-8.
 *
 * Example:
 * <code>
 *   include('./I18N/Arabic.php');
 *   $obj = new I18N_Arabic('CharsetD');
 * 
 *   $charset = $obj->getCharset($text);    
 * </code>
 *                
 * @category  I18N 
 * @package   I18N_Arabic
 * @author    Khaled Al-Sham'aa <khaled@ar-php.org>
 * @copyright 2006-2013 Khaled Al-Sham'aa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org
 */

// New in PHP V5.3: Namespaces
// namespace I18N\Arabic;
// 
// $obj = new I18N\Arabic\CharsetD();
// 
// use I18N\Arabic;
// $obj = new Arabic\CharsetD();
//
// use I18N\Arabic\CharsetD as CharsetD;
// $obj = new CharsetD();

/**
 * This PHP class detect Arabic string character set
 *  
 * @category  I18N 
 * @package   I18N_Arabic
 * @author    Khaled Al-Sham'aa <khaled@ar-php.org>
 * @copyright 2006-2013 Khaled Al-Sham'aa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org
 */ 
class I18N_Arabic_CharsetD
{
    private $_arLogodd;
    private $_enLogodd;

    /**
     * Loads initialize values
     *
     * @ignore
     */         
    public function __construct()
    {
        $this->_arLogodd = file(dirname(__FILE__).'/data/ar-logodd.php');
        $this->_enLogodd = file(dirname(__FILE__).'/data/en-logodd.php');
    }

    /**
     * Count number of hits for the most frequented letters in Arabic language 
     * (Alef, Lam and Yaa), then calculate association ratio with each of 
     * possible character set (UTF-8, Windows-1256 and ISO-8859-6)
     *                           
     * @param String $string Arabic string in unknown format
     *      
     * @return Array Character set as key and string association ratio as value
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    public function guess($string)
    {
        // The most frequent Arabic letters are Alef, Lam, and Yeh
        $charset['windows-1256']  = substr_count($string, chr(199));
        $charset['windows-1256'] += substr_count($string, chr(225));
        $charset['windows-1256'] += substr_count($string, chr(237));

        $charset['iso-8859-6']  = substr_count($string, chr(199));
        $charset['iso-8859-6'] += substr_count($string, chr(228));
        $charset['iso-8859-6'] += substr_count($string, chr(234));
        
        $charset['utf-8']  = substr_count($string, chr(216).chr(167));
        $charset['utf-8'] += substr_count($string, chr(217).chr(132));
        $charset['utf-8'] += substr_count($string, chr(217).chr(138));
        
        $total = $charset['windows-1256'] + 
                 $charset['iso-8859-6'] + 
                 $charset['utf-8'] + 1;
        
        $charset['windows-1256'] = round($charset['windows-1256'] * 100 / $total);
        $charset['iso-8859-6']   = round($charset['iso-8859-6'] * 100 / $total);
        $charset['utf-8']        = round($charset['utf-8'] * 100 / $total);
        
        return $charset;
    }
    
    /**
     * Find the most possible character set for given Arabic string in unknown 
     * format
     *         
     * @param String $string Arabic string in unknown format
     *      
     * @return String The most possible character set for given Arabic string in
     *                unknown format[utf-8|windows-1256|iso-8859-6]
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    public function getCharset($string)
    {
        if (preg_match('/<meta .* charset=([^\"]+)".*>/sim', $string, $matches)) {
            $value = $matches[1];
        } else {
            $charset = $this->guess($string);
            arsort($charset);
            $value = key($charset);
        }

        return $value;
    }


    protected function checkEn($str) {
        $lines  = $this->_enLogodd;
        $logodd = array();
        
        $line   = array_shift($lines);
        $line   = rtrim($line);
        $second = preg_split("/\t/", $line);
        $temp   = array_shift($second);
        
        foreach ($lines as $line) {
            $line   = rtrim($line);
            $values = preg_split("/\t/", $line);
            $first  = array_shift($values);
            
            for ($i=0; $i<28; $i++) {
                $logodd["$first"]["{$second[$i]}"] = $values[$i];
            }
        }
        
        $str  = mb_strtolower($str);
        $max  = mb_strlen($str, 'UTF-8');
        $rank = 0;
        
        for ($i=1; $i<$max; $i++) {
            $first  = mb_substr($str, $i-1, 1, 'UTF-8');
            $second = mb_substr($str, $i, 1, 'UTF-8');
     
            if (isset($logodd["$first"]["$second"])) {
                $rank += $logodd["$first"]["$second"]; 
            } else {
                $rank -= 10;
            }
        }
        
        return $rank;
    }

    protected function checkAr($str) {
        $lines  = $this->_arLogodd;
        $logodd = array();
        
        $line   = array_shift($lines);
        $line   = rtrim($line);
        $second = preg_split("/\t/", $line);
        $temp   = array_shift($second);
        
        foreach ($lines as $line) {
            $line   = rtrim($line);
            $values = preg_split("/\t/", $line);
            $first  = array_shift($values);
            
            for ($i=0; $i<37; $i++) {
                $logodd["$first"]["{$second[$i]}"] = $values[$i];
            }
        }
        
        $max  = mb_strlen($str, 'UTF-8');
        $rank = 0;
        
        for ($i=1; $i<$max; $i++) {
            $first  = mb_substr($str, $i-1, 1, 'UTF-8');
            $second = mb_substr($str, $i, 1, 'UTF-8');
     
            if (isset($logodd["$first"]["$second"])) {
                $rank += $logodd["$first"]["$second"]; 
            } else {
                $rank -= 10;
            }
        }

        return $rank;
    }
    
    public function getLanguage($str) {
		include dirname(__FILE__).'/KeySwap.php';
		$obj = new I18N_Arabic_KeySwap();

        preg_match_all("/([\x{0600}-\x{06FF}])/u", $str, $matches);

        $arNum    = count($matches[0]);
        $nonArNum = mb_strlen(str_replace(' ', '', $str), 'UTF-8') - $arNum;

        if ($arNum > $nonArNum) {
            $arStr = $str;
            $enStr = $obj->swapAe($str);
            $isAr  = true;
        } else {            
            $arStr = $obj->swapEa($str);
            $enStr = $str;

            $strCaps   = strtr($str, $capital, $small);
            $arStrCaps = $obj->swapEa($strCaps);

            $isAr = false;
        }

        $enRank = checkEn($enStr);
        $arRank = checkAr($arStr);
        
        if ($arNum > $nonArNum) {
            $arCapsRank = $arRank;
        } else {
            $arCapsRank = checkAr($arStrCaps);
        }

        if ($enRank > $arRank && $enRank > $arCapsRank) {
            if ($isAr) {
                $fix = $enStr;
            } else {
                preg_match_all("/([A-Z])/u", $enStr, $matches);
                $capsNum = count($matches[0]);
                
                preg_match_all("/([a-z])/u", $enStr, $matches);
                $nonCapsNum = count($matches[0]);
                
                if ($capsNum > $nonCapsNum && $nonCapsNum > 0) {
                    $enCapsStr = strtr($enStr, $capital, $small);
                    $fix       = $enCapsStr;
                } else {
                    $fix = '';
                }
            }
        } else {
            if ($arCapsRank > $arRank) {
                $arStr  = $arStrCaps;
                $arRank = $arCapsRank;
            }
            
            if (!$isAr) {
                $fix = $arStr;
            } else {
                $fix = '';
            }
        }

        return $fix;
    }
}
