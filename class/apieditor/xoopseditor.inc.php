<?php
/**
 * API Editor usage guide
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          editor
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('API_ROOT_PATH') || exit('Restricted access');

if (!function_exists('apieditor_get_rootpath')) {
    /**
     * @return string
     */
    function apieditor_get_rootpath()
    {
        return API_ROOT_PATH . '/class/apieditor';
    }
}
if (defined('API_ROOT_PATH')) {
    return true;
}

$mainfile = dirname(dirname(__DIR__)) . '/mainfile.php';
if (DIRECTORY_SEPARATOR !== '/') {
    $mainfile = str_replace(DIRECTORY_SEPARATOR, '/', $mainfile);
}
include $mainfile;

return defined('API_ROOT_PATH');
