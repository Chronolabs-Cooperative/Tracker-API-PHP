<?php
/**
 * Extension to mimetype lookup table
 *
 * This file is provided as an helper for objects who need to perform filename to mimetype translations.
 * Common types have been provided, but feel free to add your own one if you need it.
 * <br><br>
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author              Skalpa Keo <skalpa@api.org>
 * @since               2.0.9.3
 * 
 * @deprecated
 */
defined('API_ROOT_PATH') || exit('Restricted access');

$GLOBALS['apiLogger']->addDeprecated("'/class/mimetypes.inc.php' is deprecated, use '/include/mimetypes.inc.php' directly.");

return include API_ROOT_PATH . '/include/mimetypes.inc.php';
