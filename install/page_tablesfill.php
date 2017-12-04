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
 * Installer db inserting page
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
 */

require_once __DIR__ . '/include/common.inc.php';
defined('API_INSTALL') || die('API Installation wizard die');

$pageHasForm = false;
$pageHasHelp = false;

$vars =& $_SESSION['settings'];

include_once dirname(__DIR__) . '/mainfile.php';
include_once __DIR__ . '/class/dbmanager.php';
$dbm = new Db_manager();

if (!$dbm->isConnectable()) {
    $wizard->redirectToPage('dbsettings');
    exit();
}
$res = $dbm->query('SELECT COUNT(*) FROM ' . $dbm->db->prefix('users'));
if (!$res) {
    $wizard->redirectToPage('dbsettings');
    exit();
}

list($count) = $dbm->db->fetchRow($res);
$process = ($count == 0);
$update  = false;

extract($_SESSION['siteconfig'], EXTR_SKIP);

include_once './include/makedata.php';
//$cm = 'dummy';
$wizard->loadLangFile('install2');

$licenseFile = API_ROOT_PATH . '/include/license.php';
$touched = touch($licenseFile);
if ($touched) {
    $licenseReport = '<div class="alert alert-success"><span class="fa fa-check text-success"></span> '
        . writeLicenseKey($vars) . '</div>';
} else {
    $licenseReport = '<div class="alert alert-danger"><span class="fa fa-ban text-danger"></span> '
        . sprintf(LICENSE_NOT_WRITEABLE, $licenseFile) . '</div>';
}
$error = false;

$hashedAdminPass = password_hash($adminpass, PASSWORD_DEFAULT);

$content .= $licenseReport;

include './include/install_tpl.php';
