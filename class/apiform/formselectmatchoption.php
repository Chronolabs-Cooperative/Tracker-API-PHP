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

api_load('APIFormSelect');

/**
 * A selection box with options for matching search terms.
 */
class APIFormSelectMatchOption extends APIFormSelect
{
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param mixed  $value   Pre-selected value (or array of them).
     *                        Legal values are {@link API_MATCH_START}, {@link API_MATCH_END},
     *                        {@link API_MATCH_EQUAL}, and {@link API_MATCH_CONTAIN}
     * @param int    $size    Number of rows. "1" makes a drop-down-list
     */
    public function __construct($caption, $name, $value = null, $size = 1)
    {
        parent::__construct($caption, $name, $value, $size, false);
        $this->addOption(API_MATCH_START, _STARTSWITH);
        $this->addOption(API_MATCH_END, _ENDSWITH);
        $this->addOption(API_MATCH_EQUAL, _MATCHES);
        $this->addOption(API_MATCH_CONTAIN, _CONTAINS);
    }
}
