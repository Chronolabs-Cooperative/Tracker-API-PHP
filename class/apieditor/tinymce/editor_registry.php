<?php
/**
 *  TinyMCE adapter for API
 *
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          editor
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

return $config = array(
    'name' => 'tinymce',
    'class' => 'APIFormTinymce',
    'file' => API_ROOT_PATH . '/class/apieditor/tinymce/formtinymce.php',
    'title' => _API_EDITOR_TINYMCE,
    'order' => 5,
    'nohtml' => 0);
