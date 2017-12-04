<?php
/**
 *  APImlcontent plugin for tinymce
 *
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class / apieditor
 * @subpackage          tinymce / api plugins
 * @since               2.3.0
 * @author              ralf57
 * @author              luciorota <lucio.rota@gmail.com>
 * @author              Laurent JEN <dugris@frapi.org>
 */

// load mainfile.php - start
$current_path = __DIR__;
if (DIRECTORY_SEPARATOR !== '/') {
    $current_path = str_replace(DIRECTORY_SEPARATOR, '/', $current_path);
}
$api_root_path = substr($current_path, 0, strpos(strtolower($current_path), '/class/apieditor/tinymce/'));
include_once $api_root_path . '/mainfile.php';
defined('API_ROOT_PATH') || exit('API root path not defined');
// load mainfile.php - end

function langDropdown()
{
    $content = '';

    $time = time();
    if (!isset($_SESSION['APIMLcontent']) && @$_SESSION['APIMLcontent_expire'] < $time) {
        include_once API_ROOT_PATH . '/kernel/module.php';
        $xlanguage = APIModule::getByDirname('xlanguage');
        if (is_object($xlanguage) && $xlanguage->getVar('isactive')) {
            include_once(API_ROOT_PATH . '/modules/xlanguage/include/vars.php');
            include_once(API_ROOT_PATH . '/modules/xlanguage/include/functions.php');
            $xlanguage_handler = api_getModuleHandler('language', 'xlanguage');
            $xlanguage_handler->loadConfig();
            $lang_list =& $xlanguage_handler->getAllList();

            $content .= '<select name="mlanguages" id="mlanguages">';
            $content .= '<option value="">{#apimlcontent_dlg.sellang}</option>';
            if (is_array($lang_list) && count($lang_list) > 0) {
                foreach (array_keys($lang_list) as $lang_name) {
                    $lang =& $lang_list[$lang_name];
                    $content .= '<option value="' . $lang['base']->getVar('lang_code') . '">' . $lang['base']->getVar('lang_name') . '</option>';
                }
            }
            $content .= '</select>';
        } elseif (defined('EASIESTML_LANGS') && defined('EASIESTML_LANGNAMES')) {
            $easiestml_langs = explode(',', EASIESTML_LANGS);
            $langnames       = explode(',', EASIESTML_LANGNAMES);
            $lang_options    = '';

            $content .= '<select name="mlanguages" id="mlanguages">';
            $content .= '<option value="">{#apimlcontent_dlg.sellang}</option>';
            foreach ($easiestml_langs as $l => $lang) {
                $content .= '<option value="' . $lang . '">' . $langnames[$l] . '</option>';
            }
            $content .= '</select>';
        } else {
            $content .= '<input type="text" name="mlanguages" />';
        }
        $_SESSION['APIMLcontent']        = $content;
        $_SESSION['APIMLcontent_expire'] = $time + 300;
    }

    echo $_SESSION['APIMLcontent'];
}

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . _LANGCODE . '" lang="' . _LANGCODE . '">';
echo '<head>';
echo '<meta http-equiv="content-type" content="text/html; charset=' . _CHARSET . '" />';
echo '<meta http-equiv="content-language" content="' . _LANGCODE . '" />';
?>
<title>{#apimlcontent_dlg.title}</title>
<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
<script type="text/javascript" src="../../utils/mctabs.js"></script>
<script type="text/javascript" src="../../utils/form_utils.js"></script>
<script type="text/javascript" src="../../utils/validate.js"></script>
<script type="text/javascript" src="js/apimlcontent.js"></script>
<link href="<?php echo api_getcss($apiConfig['theme_set']); ?>" rel="stylesheet" type="text/css"/>
<link href="css/apimlcontent.css" rel="stylesheet" type="text/css"/>
<base target="_self"/>
</head>
<body>
<form>
    <div class="tabs">
        <ul>
            <li id="tab_mlcontent" class="current"><span><a href="javascript:mcTabs.displayTab('tab_mlcontent','mlcontent_panel');"
                                                            onmousedown="return false;">{#apimlcontent_dlg.title}</a></span></li>
        </ul>
    </div>

    <div class="panel_wrapper">
        <div id="mlcontent_panel" class="panel current" style="overflow:auto;">
            <table border="0" cellspacing="1" width="100%">
                <tr>
                    <th>{#apimlcontent_dlg.subtitle}</th>
                </tr>

                <tr>
                    <td class="even">
                    <?php langDropdown(); ?></th>
                </tr>

                <tr>
                    <td nowrap="nowrap">
                        <textarea type="text" id="mltext" name="mltext" value="" onkeyup="APImlcontentDialog.onkeyupMLC(this);"></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="foot bold">
                        <div id="mltext_msg">
                            <script type="text/javascript">APImlcontentDialog.onkeyupMLC(this);</script>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="mceActionPanel floatright">
                <input type="submit" id="insert" name="insert" value="{#insert}" onclick="APImlcontentDialog.insertMLC();return false;"/>
                <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();"/>
            </div>
        </div>
    </div>
</form>
</body>
</html>
