<?php
/**
 *  TinyMCE adapter for API
 *
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          editor
 * @since               2.3.0
 * @author              Laurent JEN <dugris@frapi.org>
 */

defined('API_ROOT_PATH') || exit('API root path not defined');

// check categories readability by group
$groups         = is_object($GLOBALS['apiUser']) ? $GLOBALS['apiUser']->getGroups() : array(API_GROUP_ANONYMOUS);
$imgcat_handler = api_getHandler('imagecategory');

return !(count($imgcat_handler->getList($groups, 'imgcat_read', 1)) == 0);
