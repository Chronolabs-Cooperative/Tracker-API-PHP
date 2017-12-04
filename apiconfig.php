<?php
/**
 * Chronolabs Entitiesing Repository Services REST API API
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Chronolabs Cooperative http://labs.coop
 * @license         General Public License version 3 (http://labs.coop/briefs/legal/general-public-licence/13,3.html)
 * @package         entities
 * @since           2.1.9
 * @author          Simon Roberts <wishcraft@users.sourceforge.net>
 * @subpackage		api
 * @description		Entitiesing Repository Services REST API
 * @link			http://sourceforge.net/projects/chronolabsapis
 * @link			http://cipher.labs.coop
 */


if (!is_file(__DIR__ . DIRECTORY_SEPARATOR . 'mainfile.php') || !is_file(__DIR__ . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'license.php'))
{
    if (strpos($_SERVER["REQUEST_URI"], 'install/')>0)
        return false;
        header('Location: ' . "./install");
        exit(0);
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'mainfile.php';

define('API_TRACKER_ANNOUNCE_INTERVAL', '9');

?>