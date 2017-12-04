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
 * Installer table creation page
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


define('_API_FATAL_MESSAGE', 'Fatal:~ %s!');
require_once __DIR__ . '/include/common.inc.php';
defined('API_INSTALL') || die('API Installation wizard die');

$pageHasForm = false;
$pageHasHelp = false;

$vars =& $_SESSION['settings'];

include_once '../mainfile.php';

require_once __DIR__ . '/class/dbmanager.php';
$dbm = new Db_manager();

if (!$dbm->isConnectable()) {
    $wizard->redirectToPage('-3');
    exit();
}

require_once API_ROOT_PATH . '/class/apilists.php';
$files = APILists::getFileListAsArray(__DIR__ . DIRECTORY_SEPARATOR . 'sql');
foreach($files as $key => $file)
    if (substr($file, strlen($file)-3,3) != 'sql')
        unset($files[$key]);
sort($files, SORT_DESC);

if (count($files)==0) {
    $content = '<div class="alert alert-info"><span class="fa fa-info-circle text-info"></span> ' . API_TABLES_FOUND . '</div>';
} else {
    $content = "<script>
    // Loads Creation of Database
    $(document).ready(function() {
        // Redirects to URL
        function redirect(data){
            if (data.length>0) {
                $('#refreshurl').attr('content', '0;url='.data);
                $('#refreshurl').attr('http-equiv', 'Refresh');
                $.ajax({
                    url: data,
                    headers: {'Location': data}
                });
            }
        }
        // Updates DIV IDs with HTML
        function updateDiv(){
            $.ajax({
                url: \"" . API_URL . "/install/json.createdatabase.php\",
                dataType: \"json\",
                cache: false,
                success: function(data) {
                    $('#dbreport').html(data.dbreport);
                    $('#buttons').html(data.buttons);
                    $('#leftsql').html(data.leftsql);
                    $('#totalsql').html(data.totalsql);
                    $('#endmsg').html(data.endmsg);
                    if (data.refreshurl.length>0) {
                        redirect(data.refreshurl);
                    }
                }             
            });              
        }
        updateDiv();
        ".(count($files)>0?"
        setInterval(updateDiv, 169);
        $('#buttons').html('&nbsp;');
        ":"         $.ajax({ url: ".API_URL . "/install/page_siteinit.php', headers: { 'Location': '".API_URL . "/install/page_siteinit.php' }; });   ")."
    });
</script>
<div class=\"alert alert-success\"><h2><span class=\"fa fa-check text-success\" id='leftsql'>&nbsp;</span> / <span class=\"fa fa-check text-success\" id='totalsql'>&nbsp;</span> ~ <span class=\"text-success\" id='endmsg'>&nbsp;</span></h2></div>
<div class=\"alert alert-success\"><span class=\"fa fa-check text-success\"></span> " . API_TABLES_CREATED
        . "</div><div class=\"well\" id=\"dbreport\">&nbsp;</div>";
}

include './include/install_tpl.php';