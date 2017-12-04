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
 * Installer path configuration page
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

require_once './include/common.inc.php';
defined('API_INSTALL') || die('API Installation wizard die');

$wizard->loadLangFile('extras');

include_once './include/functions.php';

$pageHasForm = true;
$pageHasHelp = true;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && @$_GET['var'] && @$_GET['action'] === 'checkfile') {
    $file                   = $_GET['var'];
    echo genPathCheckHtml($file, is_file($file));
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $methods = $enabled = array();
    foreach($wizard->configs['api'] as $setting => $values)
    {
        $_SESSION['constants']['api'][$setting] = $_POST[$setting];
        if (is_file($_POST[$setting]))
        {
            $enabled[] = $setting;
            if (!in_array('api', $methods))
                $methods[] = 'api';
        }
    }
    $_SESSION['constants']['methods'] = implode(',', $methods);
    $wizard->redirectToPage('+1');
    return 302;
}
ob_start();
?>
    <script type="text/javascript">
        function removeTrailing(id, val) {
            if (val[val.length - 1] == '/') {
                val = val.substr(0, val.length - 1);
                $(id).value = val;
            }

            return val;
        }

        function existingFile(key, val) {
            val = removeTrailing(key, val);
            $.get( "<?php echo $_SERVER['PHP_SELF']; ?>", { action: "checkfile", var: key, path: val } )
                .done(function( tmp ) {
                    $("#" + key + 'fileimg').html(tmp);
                });
            $("#" + key + 'perms').style.display = 'none';
        }
    </script>
    <div class="panel panel-info">
        <div class="panel-heading"><?php echo API_EXTRAS; ?></div>
        <div class="panel-body">

            <?php
            foreach($wizard->configs['api'] as $setting => $default)
            {?>
                <div class="form-group">
                <label for="<?php echo $setting; ?>"><?php echo constant("API_".strtoupper($setting) . "_LABEL"); ?></label>
                <div class="xoform-help alert alert-info"><?php echo constant("API_".strtoupper($setting) . "_HELP"); ?></div>
                <input type="text" class="form-control" name="<?php echo $setting; ?>" id="<?php echo $setting; ?>" value="<?php echo $default; ?>"/>
            </div>
            <?php }
            ?>
       </div>
   </div>

<?php
$content = ob_get_contents();
ob_end_clean();

include './include/install_tpl.php';
