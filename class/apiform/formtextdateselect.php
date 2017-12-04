<?php
/**
 * API form element
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2017 API Project (www.api.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          form
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.api.org/
 */

defined('API_ROOT_PATH') || exit('API root path not defined');

/**
 * A text field with calendar popup
 */
class APIFormTextDateSelect extends APIFormText
{
    /**
     * @param string $caption
     * @param string $name
     * @param int $size
     * @param int $value
     */
    public function __construct($caption, $name, $size = 15, $value = 0)
    {
        $value = !is_numeric($value) ? time() : (int)$value;
        $value = ($value == 0) ? time() : $value;
        parent::__construct($caption, $name, $size, 25, $value);
    }

    /**
     * {@inheritDoc}
     * @see APIFormText::render()
     */
    public function render()
    {
        return APIFormRenderer::getInstance()->get()->renderFormTextDateSelect($this);
    }
}
