<?php
/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Define Renderer interface for forms
 *
 * Each form class has a corresponding renderer method, allowing exact details of the form elements
 * to be modified as needed,
 *
 * @category  APIForm
 * @package   APIFormRendererInterface
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2017 API Project (http://api.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://api.org
 */
interface APIFormRendererInterface
{
    /**
     * Render support for APIFormButton
     *
     * @param APIFormButton $element form element
     *
     * @return string rendered form element
     */
    public function renderFormButton(APIFormButton $element);

    /**
     * Render support for APIFormButtonTray
     *
     * @param APIFormButtonTray $element form element
     *
     * @return string rendered form element
     */
    public function renderFormButtonTray(APIFormButtonTray $element);

    /**
     * Render support for APIFormCheckBox
     *
     * @param APIFormCheckBox $element form element
     *
     * @return string rendered form element
     */
    public function renderFormCheckBox(APIFormCheckBox $element);

    /**
     * Render support for APIFormColorPicker
     *
     * @param APIFormColorPicker $element form element
     *
     * @return string rendered form element
     */
    public function renderFormColorPicker(APIFormColorPicker $element);

    /**
     * Render support for APIFormDhtmlTextArea
     *
     * @param APIFormDhtmlTextArea $element form element
     *
     * @return string rendered form element
     */
    public function renderFormDhtmlTextArea(APIFormDhtmlTextArea $element);

    /**
     * Render support for APIFormElementTray
     *
     * @param APIFormElementTray $element form element
     *
     * @return string rendered form element
     */
    public function renderFormElementTray(APIFormElementTray $element);

    /**
     * Render support for APIFormFile
     *
     * @param APIFormFile $element form element
     *
     * @return string rendered form element
     */
    public function renderFormFile(APIFormFile $element);

    /**
     * Render support for APIFormLabel
     *
     * @param APIFormLabel $element form element
     *
     * @return string rendered form element
     */
    public function renderFormLabel(APIFormLabel $element);

    /**
     * Render support for APIFormPassword
     *
     * @param APIFormPassword $element form element
     *
     * @return string rendered form element
     */
    public function renderFormPassword(APIFormPassword $element);

    /**
     * Render support for APIFormRadio
     *
     * @param APIFormRadio $element form element
     *
     * @return string rendered form element
     */
    public function renderFormRadio(APIFormRadio $element);

    /**
     * Render support for APIFormSelect
     *
     * @param APIFormSelect $element form element
     *
     * @return string rendered form element
     */
    public function renderFormSelect(APIFormSelect $element);

    /**
     * Render support for APIFormText
     *
     * @param APIFormText $element form element
     *
     * @return string rendered form element
     */
    public function renderFormText(APIFormText $element);

    /**
     * Render support for APIFormTextArea
     *
     * @param APIFormTextArea $element form element
     *
     * @return string rendered form element
     */
    public function renderFormTextArea(APIFormTextArea $element);

    /**
     * Render support for APIFormTextDateSelect
     *
     * @param APIFormTextDateSelect $element form element
     *
     * @return string rendered form element
     */
    public function renderFormTextDateSelect(APIFormTextDateSelect $element);

    /**
     * Render support for APIThemeForm
     *
     * @param APIThemeForm $form form to render
     *
     * @return string rendered form
     */
    public function renderThemeForm(APIThemeForm $form);

    /**
     * Support for themed addBreak
     *
     * @param APIThemeForm $form  form
     * @param string         $extra pre-rendered content for break row
     * @param string         $class class for row
     *
     * @return void
     */
    public function addThemeFormBreak(APIThemeForm $form, $extra, $class);
}
