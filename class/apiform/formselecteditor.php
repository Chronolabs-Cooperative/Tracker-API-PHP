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
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

defined('API_ROOT_PATH') || exit('Restricted access');

api_load('APIFormElementTray');

/**
 * APIFormSelectEditor
 */
class APIFormSelectEditor extends APIFormElementTray
{
    public $allowed_editors = array();
    public $form;
    public $value;
    public $name;
    public $nohtml;

    /**
     * Constructor
     *
     * @param string    $form  the form calling the editor selection
     * @param string    $name  editor name
     * @param string    $value Pre-selected text value
     * @param bool      $nohtml
     * @param array     $allowed_editors
     *
     */

    public function __construct($form, $name = 'editor', $value = null, $nohtml = false, $allowed_editors = array())
    {
        parent::__construct(_SELECT);
        $this->allowed_editors = $allowed_editors;
        $this->form            = $form;
        $this->name            = $name;
        $this->value           = $value;
        $this->nohtml          = $nohtml;
    }

    /**
     * APIFormSelectEditor::render()
     *
     * @return string
     */
    public function render()
    {
        api_load('APIEditorHandler');
        $editor_handler                  = APIEditorHandler::getInstance();
        $editor_handler->allowed_editors = $this->allowed_editors;
        $option_select                   = new APIFormSelect('', $this->name, $this->value);
        $extra                           = 'onchange="if (this.options[this.selectedIndex].value.length > 0) {
            window.document.forms.' . $this->form->getName() . '.submit();
            }"';
        $option_select->setExtra($extra);
        $option_select->addOptionArray($editor_handler->getList($this->nohtml));
        $this->addElement($option_select);

        return parent::render();
    }
}
