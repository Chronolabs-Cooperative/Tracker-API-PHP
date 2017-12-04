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
 *  API Form Class Elements
 *
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          form
 * @since               2.3.0
 * @author              John Neill <catzwolf@api.org>
 */
defined('API_ROOT_PATH') || exit('Restricted access');

api_load('APIFormCheckBox');

/**
 * API Form Select Check Groups
 *
 * @author              John Neill <catzwolf@api.org>
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 * @package             kernel
 * @subpackage          form
 * @access              public
 */
class APIFormSelectCheckGroup extends APIFormCheckBox
{
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param mixed  $value    Pre-selected value (or array of them).
     */
    public function __construct($caption, $name, $value = null)
    {
        /* @var $member_handler APIMemberHandler */
        $member_handler   = api_getHandler('member');
        $userGroups = $member_handler->getGroupList();
        parent::__construct($caption, $name, $value);
        $this->columns = 3;
        foreach ($userGroups as $group_id => $group_name) {
            $this->addOption($group_id, $group_name);
        }
    }
}
