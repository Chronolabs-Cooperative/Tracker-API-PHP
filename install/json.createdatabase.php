<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * Installer mainfile creation page
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 API Project (www.api.org)
 * @license          GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@api.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           DuGris (aka L. JEN) <dugris@frapi.org>
 **/

require_once __DIR__ . '/include/common.inc.php';
require_once API_ROOT_PATH . '/class/apilists.php';
include_once dirname(__DIR__) . '/mainfile.php';
include_once __DIR__ . '/class/dbmanager.php';

defined('API_INSTALL') || die('API Installation wizard die');

header('Content-type: application/json');

$leftsql = $ransql = 0;
$files = APILists::getFileListAsArray(__DIR__ . DIRECTORY_SEPARATOR . 'sql');
foreach($files as $key => $file)
    if (substr($file, strlen($file)-3,3) != 'sql' && substr($file, strlen($file)-3,3) != 'ran')
        unset($files[$key]);
    elseif (substr($file, strlen($file)-3,3) == 'sql')
        $leftsql++;
    elseif (substr($file, strlen($file)-3,3) == 'ran')
    {
        unset($files[$key]);
        $ransql++;
    }
        
sort($files, SORT_DESC);
if (count($files) == 0)
    echo json_encode(array('dbreport' => file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'dbreport.html'), 'buttons' => '<button class="btn btn-lg btn-success" type="button" accesskey="n" onclick="location.href=\'' . API_URL . '/install/page_siteinit.php\'"> Continue  <span class="fa fa-caret-right"></span></button>', 'leftsql' => $leftsql, 'totalsql' => $leftsql+$ransql, 'endmsg' => 'All SQL Executed - Too proceed with installation <a href="'.API_URL . '/install/page_siteinit.php"> Click to Continue! </a>', 'refreshurl' => API_URL . '/install/page_siteinit.php', 'titlemsg' => ''));
else 
{
    rename(__DIR__ . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . $files[0], __DIR__ . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . $files[0] . '.ran');
    $dbm = new Db_manager();
    $result  = $dbm->queryFromFile(__DIR__ . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . $files[0] . '.ran');
    $html = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'dbreport.html');
    $html .= "<ul class=\"prestige\" style=\"list-style-bullet: none; float: left; width: 32%; padding: 4px; margin: 3px; font-size: 0.78764em;\"><il><h3 style=\"font-size: 1.44812em;\">".$files[0] . "</h3></li>" . $dbm->report() . "</ul>\n";
    file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'dbreport.html', $html);
    echo json_encode(array('dbreport' => $html, 'buttons' => '&nbsp;', 'leftsql' => $leftsql, 'totalsql' => $leftsql+$ransql, 'endmsg' => 'Still: ' . number_format((100-($ransql-1/($leftsql+$ransql))/100), 2) . '% too process!', 'refreshurl' => '', 'titlemsg' => "$leftsql / " . $leftsql + $ransql . ' ~ ' . $files[0]));
}