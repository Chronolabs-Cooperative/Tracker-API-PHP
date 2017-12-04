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
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          form
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.api.org/
 */

defined('API_ROOT_PATH') || exit('Restricted access');

api_load('APIFormRadio');

/**
 * Yes/No radio buttons.
 *
 * A pair of radio buttons labelled _YES and _NO with values 1 and 0
 */
class APIFormRadioYN extends APIFormRadio
{
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param string $value Pre-selected value, can be "0" (No) or "1" (Yes)
     * @param string $yes   String for "Yes"
     * @param string $no    String for "No"
     */
    public function __construct($caption, $name, $value = null, $yes = _YES, $no = _NO)
    {
        parent::__construct($caption, $name, $value);
        $this->addOption(1, $yes);
        $this->addOption(0, $no);
    }
}
