<?php
/**
 *  APIemotions plugin for tinymce
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

// load mainfile.php
$current_path = __DIR__;
if (DIRECTORY_SEPARATOR !== '/') {
    $current_path = str_replace(DIRECTORY_SEPARATOR, '/', $current_path);
}
$api_root_path = substr($current_path, 0, strpos(strtolower($current_path), '/class/apieditor/tinymce/'));
include_once $api_root_path . '/mainfile.php';
defined('API_ROOT_PATH') || exit('API root path not defined');

// include
include_once API_ROOT_PATH . '/modules/system/constants.php';

// check user/group
$admin = false;

$gperm_handler = api_getHandler('groupperm');
$groups        = is_object($GLOBALS['apiUser']) ? $GLOBALS['apiUser']->getGroups() : array(API_GROUP_ANONYMOUS);
$admin         = $gperm_handler->checkRight('system_admin', API_SYSTEM_IMAGE, $groups);

// check categories readability/writability by group
$imgcat_handler = api_getHandler('imagecategory');
$catreadlist    = $imgcat_handler->getList($groups, 'imgcat_read', 1);    // get readable categories
$catwritelist   = $imgcat_handler->getList($groups, 'imgcat_write', 1);  // get writable categories

$canbrowse = ($admin || !empty($catreadlist) || !empty($catwritelist)) ? true : false;

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . _LANGCODE . '" lang="' . _LANGCODE . '">';
?>
<head>
    <title>{#apiimagemanager_dlg.dialog_title}</title>
    <script type="text/javascript" src="../../tiny_mce_popup.js"></script>
    <script type="text/javascript" src="../../utils/mctabs.js"></script>
    <script type="text/javascript" src="../../utils/form_utils.js"></script>
    <script type="text/javascript" src="../../utils/validate.js"></script>
    <script type="text/javascript" src="js/apiimagemanager.js"></script>
    <link href="css/apiimagemanager.css" rel="stylesheet" type="text/css"/>
    <base target="_self"/>
</head>

<body id="apiimagemanager" style="display: none;">
<form onsubmit="APIimagemanagerDialog.insert();return false;" action="#">
    <div class="tabs">
        <ul>
            <li id="general_tab" class="current"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');"
                                                          onmousedown="return false;">{#apiimagemanager_dlg.tab_general}</a></span></li>
            <li id="appearance_tab"><span><a href="javascript:mcTabs.displayTab('appearance_tab','appearance_panel');" onmousedown="return false;">{#apiimagemanager_dlg.tab_appearance}</a></span>
            </li>
            <li id="advanced_tab"><span><a href="javascript:mcTabs.displayTab('advanced_tab','advanced_panel');" onmousedown="return false;">{#apiimagemanager_dlg.tab_advanced}</a></span>
            </li>
        </ul>
    </div>

    <div class="panel_wrapper">
        <div id="general_panel" class="panel current">
            <fieldset>
                <legend>{#apiimagemanager_dlg.general}</legend>
                <table class="properties">
                    <tr>
                        <td class="column1">
                            <label id="srclabel" for="src">{#apiimagemanager_dlg.src}</label>
                        </td>
                        <td colspan="2">
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <input name="src" type="text" id="src" value=""
                                               onchange="APIimagemanagerDialog.showPreviewImage(this.value);"/>
                                        <?php echo imageBrowser('src', $canbrowse); ?>
                                    </td>
                                    <td id="srcbrowsercontainer">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="src_list">{#apiimagemanager_dlg.image_list}</label></td>
                        <td><select id="src_list" name="src_list"
                                    onchange="document.getElementById('src').value=this.options[this.selectedIndex].value;document.getElementById('alt').value=this.options[this.selectedIndex].text;document.getElementById('title').value=this.options[this.selectedIndex].text;APIimagemanagerDialog.showPreviewImage(this.options[this.selectedIndex].value);"></select>
                        </td>
                    </tr>
                    <tr>
                        <td class="column1"><label id="altlabel" for="alt">{#apiimagemanager_dlg.alt}</label></td>
                        <td colspan="2"><input id="alt" name="alt" type="text" value=""/></td>
                    </tr>
                    <tr>
                        <td class="column1"><label id="titlelabel" for="title">{#apiimagemanager_dlg.title}</label></td>
                        <td colspan="2"><input id="title" name="title" type="text" value=""/></td>
                    </tr>
                </table>
            </fieldset>

            <fieldset>
                <legend>{#apiimagemanager_dlg.preview}</legend>
                <div id="prev"></div>
            </fieldset>
        </div>

        <div id="appearance_panel" class="panel">
            <fieldset>
                <legend>{#apiimagemanager_dlg.tab_appearance}</legend>

                <table border="0" cellpadding="4" cellspacing="0">
                    <tr>
                        <td class="column1"><label id="alignlabel" for="align">{#apiimagemanager_dlg.align}</label></td>
                        <td>
                            <select id="align" name="align"
                                    onchange="APIimagemanagerDialog.updateStyle('align');APIimagemanagerDialog.changeAppearance();">
                                <option value="">{#not_set}</option>
                                <option value="baseline">{#apiimagemanager_dlg.align_baseline}</option>
                                <option value="top">{#apiimagemanager_dlg.align_top}</option>
                                <option value="middle">{#apiimagemanager_dlg.align_middle}</option>
                                <option value="bottom">{#apiimagemanager_dlg.align_bottom}</option>
                                <option value="text-top">{#apiimagemanager_dlg.align_texttop}</option>
                                <option value="text-bottom">{#apiimagemanager_dlg.align_textbottom}</option>
                                <option value="left">{#apiimagemanager_dlg.align_left}</option>
                                <option value="right">{#apiimagemanager_dlg.align_right}</option>
                            </select>
                        </td>
                        <td rowspan="6" valign="top">
                            <div class="alignPreview">
                                <img id="alignSampleImg" src="img/sample.gif" alt="{#apiimagemanager_dlg.example_img}"/>
                                Lorem ipsum, Dolor sit amet, consectetuer adipiscing loreum ipsum edipiscing elit, sed diam
                                nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.Loreum ipsum
                                edipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam
                                erat volutpat.
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="widthlabel" for="width">{#apiimagemanager_dlg.dimensions}</label></td>
                        <td nowrap="nowrap">
                            <input name="width" type="text" id="width" value="" size="5" maxlength="5" class="size"
                                   onchange="APIimagemanagerDialog.changeHeight();"/> x
                            <input name="height" type="text" id="height" value="" size="5" maxlength="5" class="size"
                                   onchange="APIimagemanagerDialog.changeWidth();"/> px
                        </td>
                    </tr>

                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td><input id="constrain" type="checkbox" name="constrain" class="checkbox"/></td>
                                    <td><label id="constrainlabel" for="constrain">{#apiimagemanager_dlg.constrain_proportions}</label></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="vspacelabel" for="vspace">{#apiimagemanager_dlg.vspace}</label></td>
                        <td><input name="vspace" type="text" id="vspace" value="" size="3" maxlength="3" class="number"
                                   onchange="APIimagemanagerDialog.updateStyle('vspace');APIimagemanagerDialog.changeAppearance();"
                                   onblur="APIimagemanagerDialog.updateStyle('vspace');APIimagemanagerDialog.changeAppearance();"/>
                        </td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="hspacelabel" for="hspace">{#apiimagemanager_dlg.hspace}</label></td>
                        <td><input name="hspace" type="text" id="hspace" value="" size="3" maxlength="3" class="number"
                                   onchange="APIimagemanagerDialog.updateStyle('hspace');APIimagemanagerDialog.changeAppearance();"
                                   onblur="APIimagemanagerDialog.updateStyle('hspace');APIimagemanagerDialog.changeAppearance();"/></td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="borderlabel" for="border">{#apiimagemanager_dlg.border}</label></td>
                        <td><input id="border" name="border" type="text" value="" size="3" maxlength="3" class="number"
                                   onchange="APIimagemanagerDialog.updateStyle('border');APIimagemanagerDialog.changeAppearance();"
                                   onblur="APIimagemanagerDialog.updateStyle('border');APIimagemanagerDialog.changeAppearance();"/></td>
                    </tr>

                    <tr>
                        <td><label for="class_list">{#class_name}</label></td>
                        <td><select id="class_list" name="class_list"></select></td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="stylelabel" for="style">{#apiimagemanager_dlg.style}</label></td>
                        <td colspan="2"><input id="style" name="style" type="text" value="" onchange="APIimagemanagerDialog.changeAppearance();"/>
                        </td>
                    </tr>

                    <!-- <tr>
                        <td class="column1"><label id="classeslabel" for="classes">{#apiimagemanager_dlg.classes}</label></td>
                        <td colspan="2"><input id="classes" name="classes" type="text" value="" onchange="selectByValue(this.form,'classlist',this.value,true);" /></td>
                    </tr> -->
                </table>
            </fieldset>
        </div>

        <div id="advanced_panel" class="panel">
            <fieldset>
                <legend>{#apiimagemanager_dlg.swap_image}</legend>

                <input type="checkbox" id="onmousemovecheck" name="onmousemovecheck" class="checkbox"
                       onclick="APIimagemanagerDialog.setSwapImage(this.checked);"/>
                <label id="onmousemovechecklabel" for="onmousemovecheck">{#apiimagemanager_dlg.alt_image}</label>

                <table border="0" cellpadding="4" cellspacing="0" width="100%">
                    <tr>
                        <td class="column1"><label id="onmouseoversrclabel" for="onmouseoversrc">{#apiimagemanager_dlg.mouseover}</label></td>
                        <td>
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <input id="onmouseoversrc" name="onmouseoversrc" type="text" value=""/>
                                        <?php echo imageBrowser('onmouseoversrc', $canbrowse); ?>
                                    </td>
                                    <td id="onmouseoversrccontainer">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="over_list">{#apiimagemanager_dlg.image_list}</label></td>
                        <td><select id="over_list" name="over_list"
                                    onchange="document.getElementById('onmouseoversrc').value=this.options[this.selectedIndex].value;"></select></td>
                    </tr>
                    <tr>
                        <td class="column1"><label id="onmouseoutsrclabel" for="onmouseoutsrc">{#apiimagemanager_dlg.mouseout}</label></td>
                        <td class="column2">
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <input id="onmouseoutsrc" name="onmouseoutsrc" type="text" value=""/>
                                        <?php echo imageBrowser('onmouseoutsrc', $canbrowse); ?>
                                    </td>
                                    <td id="onmouseoutsrccontainer">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="out_list">{#apiimagemanager_dlg.image_list}</label></td>
                        <td><select id="out_list" name="out_list"
                                    onchange="document.getElementById('onmouseoutsrc').value=this.options[this.selectedIndex].value;"></select></td>
                    </tr>
                </table>
            </fieldset>

            <fieldset>
                <legend>{#apiimagemanager_dlg.misc}</legend>

                <table border="0" cellpadding="4" cellspacing="0">
                    <tr>
                        <td class="column1"><label id="idlabel" for="id">{#apiimagemanager_dlg.id}</label></td>
                        <td><input id="id" name="id" type="text" value=""/></td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="dirlabel" for="dir">{#apiimagemanager_dlg.langdir}</label></td>
                        <td>
                            <select id="dir" name="dir" onchange="APIimagemanagerDialog.changeAppearance();">
                                <option value="">{#not_set}</option>
                                <option value="ltr">{#apiimagemanager_dlg.ltr}</option>
                                <option value="rtl">{#apiimagemanager_dlg.rtl}</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="langlabel" for="lang">{#apiimagemanager_dlg.langcode}</label></td>
                        <td>
                            <input id="lang" name="lang" type="text" value=""/>
                        </td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="usemaplabel" for="usemap">{#apiimagemanager_dlg.map}</label></td>
                        <td>
                            <input id="usemap" name="usemap" type="text" value=""/>
                        </td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="longdesclabel" for="longdesc">{#apiimagemanager_dlg.long_desc}</label></td>
                        <td>
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td><input id="longdesc" name="longdesc" type="text" value=""/></td>
                                    <td id="longdesccontainer">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>

    <div class="mceActionPanel">
        <div style="float: left;">
            <input type="button" id="insert" name="insert" value="{#insert}" onclick="APIimagemanagerDialog.insert();"/>
        </div>

        <div style="float: right;">
            <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();"/>
        </div>
    </div>
</form>
</body>
</html>

<?php
/**
 * @param string $inputname
 * @param bool   $canbrowse
 *
 * @return string
 */
function imageBrowser($inputname = 'src', $canbrowse = false)
{
    $html = '';
    if ($canbrowse) {
        $html = "<img title=\"{#apiimagebrowser.desc}\" class=\"apiimagebrowser\" src=\"img/apiimagemanager.png\"
                onclick=\"javascript:APIImageBrowser('" . $inputname . "');\" />\n";
    }

    return $html;
}

?>
