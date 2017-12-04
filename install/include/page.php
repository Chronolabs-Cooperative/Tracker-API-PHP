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
$pages = array(
    'langselect'      => array(
        'name'  => LANGUAGE_SELECTION,
        'title' => LANGUAGE_SELECTION_TITLE,
        'icon'  => 'fa fa-fw fa-language'
    ),
    'start'           => array(
        'name'  => INTRODUCTION,
        'title' => INTRODUCTION_TITLE,
        'icon'  => 'fa fa-fw fa-exclamation-circle'
    ),
    'modcheck'        => array(
        'name'  => CONFIGURATION_CHECK,
        'title' => CONFIGURATION_CHECK_TITLE,
        'icon'  => 'fa fa-fw fa-server'
    ),
    'pathsettings'    => array(
        'name'  => PATHS_SETTINGS,
        'title' => PATHS_SETTINGS_TITLE,
        'icon'  => 'fa fa-fw fa-folder-open'
    ),
    'extrasettings'    => array(
        'name'  => PATHS_EXTRA,
        'title' => PATHS_EXTRA_TITLE,
        'icon'  => 'fa fa-fw fa-folder-open'
    ),
    'dbconnection'    => array(
        'name'  => DATABASE_CONNECTION,
        'title' => DATABASE_CONNECTION_TITLE,
        'icon'  => 'fa fa-fw fa-exchange'
    ),
    'dbsettings'      => array(
        'name'  => DATABASE_CONFIG,
        'title' => DATABASE_CONFIG_TITLE,
        'icon'  => 'fa fa-fw fa-tmpbase'
    ),
    'configsave'      => array(
        'name'  => CONFIG_SAVE,
        'title' => CONFIG_SAVE_TITLE,
        'icon'  => 'fa fa-fw fa-download'
    ),
    'tablescreate'    => array(
        'name'  => TABLES_CREATION,
        'title' => TABLES_CREATION_TITLE,
        'icon'  => 'fa fa-fw fa-sitemap'
    ),
    'siteinit'        => array(
        'name'  => INITIAL_SETTINGS,
        'title' => INITIAL_SETTINGS_TITLE,
        'icon'  => 'fa fa-fw fa-sliders'
    ),
    'tablesfill'      => array(
        'name'  => TMP_INSERTION,
        'title' => TMP_INSERTION_TITLE,
        'icon'  => 'fa fa-fw fa-cloud-upload'
    ),
    'end'             => array(
        'name'  => WELCOME,
        'title' => WELCOME_TITLE,
        'icon'  => 'fa fa-fw fa-thumbs-o-up'
    )
);
