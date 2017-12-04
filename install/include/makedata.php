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
 * Installer tmp generation file
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             installer
 * @since               2.3.0
 * @author              Haruki Setoyama <haruki@planewave.org>
 * @author              Kazumi Ono <webmaster@myweb.ne.jp>
 * @author              Skalpa Keo <skalpa@api.org>
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @author              DuGris (aka L. JEN) <dugris@frapi.org>
 * @param $dbm
 * @return bool
 */
// include_once './class/dbmanager.php';
// RMV
// TODO: Shouldn't we insert specific field names??  That way we can use
// the defaults specified in the tmpbase...!!!! (and don't have problem
// of missing fields in install file, when add new fields to tmpbase)
function make_groups(&$dbm)
{
    return array();
}

/**
 * @param $dbm
 * @param $adminname
 * @param $hashedAdminPass
 * @param $adminmail
 * @param $language
 * @param $groups
 *
 * @return mixed
 */
function make_data(&$dbm, $adminname, $hashedAdminPass, $adminmail, $language, $groups)
{
    $dbm->insert('users', " VALUES (1,'','" . addslashes($adminname) . "','" . addslashes($adminmail) . "','" . API_URL . "/','avatars/blank.gif','" . time() . "','','','', '" . $temp . "',0,1,'".date_default_timezone_get() . "',".time().",".time().",1)");    
    return true;
}
