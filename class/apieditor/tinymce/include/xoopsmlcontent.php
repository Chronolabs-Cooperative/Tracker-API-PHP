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

// Xlanguage
if ($GLOBALS['module_handler']->getByDirname('xlanguage') && defined('XLANGUAGE_LANG_TAG')) {
    return true;
}

// Easiest Multi-Language Hack (EMLH)
return defined('EASIESTML_LANGS') && defined('EASIESTML_LANGNAMES');
