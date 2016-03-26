<?php
/**
 * @version         1.0.3
 * @package         components
 * @sub package     com_jeprolab
 * @link            http://jeprodev.net

 * @copyright (C)   2009 - 2011
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of,
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class JeprolabHelper
{
    public function __construct()
    {
        $this->context = JeprolabContext::getContext();
    }

    public function multiLanguageInputField($fieldName, $wrapper, $required = false, $content = null, $maxLength = null, $hint = '')
    {
        $wrapper = $wrapper ? $wrapper : 'jform';
        if (!isset($this->languages) || !$this->languages) {
            $this->languages = JeprolabLanguageModelLanguage::getLanguages();
        }
        $script = '<div class="translatable input-append" >';
        foreach ($this->languages as $language) {
            $script .= '<div class="lang_' . $language->lang_id . ' input_lang" ';
            $script .= (!$language->is_default ? 'style="display:none" ' : '') . ' >';
            $script .= '<input type="text" id="jform_' . $fieldName . '_' . $language->lang_id . '" name="' . $wrapper . '[';
            $script .= $fieldName . '_' . $language->lang_id . ']" class="copy_to_friendly_url hasTooltip" ';
            $script .= 'value="' . (count($content) ? $content[$language->lang_id] : '') . '" onKeyup="if(isArrowKey(event)) return; updateFriendlyUrl();" ';
            if ($required) {
                $script .= 'required="required" ';
            }
            if ($maxLength) {
                $script .= 'maxlength="' . (int)$maxLength . '" ';
            }
            $script .= '/></div><div class="btn-group-action lang-select"><div class="btn-group" >';
            $script .= '<button type="button" class="btn btn-default dropdown_toggle" tabindex="-1" data-toggle="dropdown" > ';
            $script .= $language->iso_code . '&nbsp; <i class="caret" ></i> </button><ul class="dropdown-menu" >';
            foreach ($this->languages as $value) {
                $script .= '<li><a href="javascript:hideOtherLanguage(' . $value->lang_id . ');" tabindex="-1" >' . $value->name . '</a></li>';
            }
            $script .= '</ul></div>';
            if ($hint != '') {
                $script .= '<p class="preference_description" >' . $hint . '</p>';
            }
            $script .= '</div>';

        }
        $script .= '</div>';
        return $script;
    }

    public function inputAppendField($fieldName, $fieldValue, $link, $icon)
    {
        $script = '<div class="input-append" ><input type="text" id="jform_' . $fieldName . '" name="jform[' . $fieldName . ']" value="';
        $script .= $fieldValue . '" /><a class="btn btn-primary" href="' . $link . '" ><i class="icon-' . $icon . '" ></i></a></div>';

        return $script;
    }

    public function radioButton($fieldName, $layout = 'add', $state = 1)
    {
        if ($layout == 'add') {
            $state_published = ' checked="checked"';
            $state_unpublished = '';
        } else {
            if ($state) {
                $state_published = ' checked="checked" ';
                $state_unpublished = '';
            } else {
                $state_published = '';
                $state_unpublished = ' checked="checked"';
            }
        }
        $script = '<fieldset class="btn-group radio" >';
        $script .= '<input type="radio" id="jform_' . $fieldName . '_1" name="jform[' . $fieldName . ']" value="1" ' . $state_published . ' /><label for="jform_' . $fieldName . '_1" >' . JText::_('COM_JEPROLAB_YES_LABEL') . '</label>';
        $script .= '<input type="radio" id="jform_' . $fieldName . '_0" name="jform[' . $fieldName . ']" value="0" ' . $state_unpublished . ' /><label for="jform_' . $fieldName . '_0" >' . JText::_('COM_JEPROLAB_NO_LABEL') . '</label>';
        $script .= '</fieldset>';
        return $script;
    }

    public function multiLanguageTextAreaField($fieldName, $wrapper, $content = NULL, $width = '550', $height = '100')
    {
        $wrapper = $wrapper ? $wrapper : 'jform';
        if (!isset($this->languages) || !$this->languages) {
            $this->languages = JeprolabLanguageModelLanguage::getLanguages();
        }
        $script = '<div class="translatable" >';
        foreach ($this->languages as $language) {
            $script .= '<div class="lang_' . $language->lang_id . ' input_lang"' . (!$language->is_default ? 'style="display:none" ' : '') . ' >';
            $script .= '<textarea class="ckeditor" name="' . $wrapper . '[' . $fieldName . '_' . $language->lang_id . ']" id="jform_' . $fieldName . '_' . $language->lang_id . '" >';
            $script .= ($content ? $content[$language->lang_id] : '') . '</textarea>';
            $script .= '</div><div class="btn-group-action lang-select" ><div class="btn-group" >';
            $script .= '<button type="button" class="btn btn-default dropdown_toggle" tabindex="-1" data-toggle="dropdown" > ';
            $script .= $language->iso_code . '&nbsp; <i class="caret" ></i> </button><ul class="dropdown-menu" >';
            foreach ($this->languages as $value) {
                $script .= '<li><a href="jeproLang.hideOtherLanguage(' . $value->lang_id . ');" tabindex="-1" >' . $value->name . '</a></li>';
            }
            $script .= '</ul></div></div>';
            //$script .= '</div></div>';
        }
        $script .= '</div>';
        return $script;
    }

    public function imageFileChooser()
    {
        $script = '<div class="col_lg_9" ><div class="col_sm_6" >';
        $script .= '<input type="file" id="jform_name" name="jform[image]" class="hide" />';
        $script .= '<div class="input-append" ><button type="button" class="btn" ><i class="icon-file" ></i></button>';
        $script .= '<input id="jform_image_name" type="text" name="jform[filename]" readonly  class="hasTooltip" /><span class="input_group_btn">';
        $script .= '<button id="jform_image_select_button" type="button" name="jform[submit_add_attachments]" class="btn default_btn" >';
        $script .= '<i class="icon-folder-open" ></i>' . JText::_('COM_JEPROLAB_ADD_FILE_LABEL') . '</button></span>';
        $script .= '</div></div></div>';

        return $script;
    }

    public function inputFileUploader($fieldName, $name, $files = null, $use_ajax = false, $max_files = 5, $url = '')
    {
        $script = '<div class="' . $fieldName . '-images-thumbnails" ' . ((count($files) <= 0) ? ' style="display:none" ' : '') . ' >';
        if (isset($files) && count($files)) {
            foreach ($files as $file) {
                if (isset($file->image) && $file->type = 'image') {
                    $script .= '<div class="" ><img src="' . $file->image . '" />';
                    if (isset($file->size)) {
                        $script .= '<p>' . JText::_('COM_JEPROLAB_FILE_SIZE_LABEL') . ' : ' . $file->size . ' kb</p>';
                    }
                    if (isset($file->delete_url)) {
                        $script .= '<p><a class="btn btn-default" href="' . $file->delete_url . '" ><i class="icon-trash" ></i> ' . JText::_('COM_JEPROLAB_DELETE_LABEL') . '</a></p>';
                    }
                    $script .= '</div>';
                }
            }
        }
        $script .= '</div>';

        $javaScript = "";
        if (isset($max_files) && count($files) >= $max_files) {
            $script .= '<div class="row" ><div class="alert alert-warning" >' . JText::_('COM_JEPROLAB_YOU_HAVE_REACHED_THE_LIMIT_LABEL') . $max_files . JText::_('COM_JEPROLAB_OF_FILES_TO_UPLOAD_PLEASE_REMOVE_FILES_TO_CONTINUE_UPLOADING_LABEL') . '</div></div>';
        } else {
            $script .= '<div class="input-append" >';
            $javaScript = "jQuery(document).ready(function(){ var jeproFile = new JeprolabFiles({'field_name' : '" . $fieldName . "'";
            if ($use_ajax) {
                $script .= '<input type="file" id="jform_' . $fieldName . '" name="jform[' . $fieldName . ']" data-url="' . ((isset($url) && $url) ? $url : '');
                $script .= '" ' . ((isset($multiple) && $multiple) ? 'multiple="multiple" ' : '') . ' style="width:0px; height:0px" /><button class="btn btn-default" ';
                $script .= ' data-style="expand-right" data-size="small" type="button" id="jform_' . $fieldName . '_add_button" ><i class="icon-folder-open" ></i> ';
                $script .= ((isset($multiple) && $multiple) ? JText::_('COM_JEPROLAB_FILES_LABEL') : JText::_('COM_JEPROLAB_FILE_LABEL')) . '...</button><div class="well" ';
                $script .= 'style="display:none" ><div id="jform_' . $fieldName . '_files_list" ></div><button class="ladda-button btn btn-primary" data-style="expand-right" type="button"';
                $script .= ' id="jform_' . $fieldName . '_upload_button" style="display:none; " ><span class="ladda-label" ><i class="icon-check" ></i> ' . ((isset($multiple) && $multiple) ? JText::_('COM_JEPROLAB_UPLOAD_FILES_LABEL') : JText::_('COM_JEPROLAB_UPLOAD_FILES_LABEL'));
                $script .= '</span></button></div><div class="row" style="display: none" ><div class="alert alert-success" id="jform_' . $fieldName . '_success" ></div></div><div class="row" style="display:none" ><div class="alert alert-danger" id="jform_' . $fieldName . '_errors" ></div></div>';

                $javaScript .= "});jeproFile.ajaxUploadManager(); ";
            } else {
                $script .= '<input type="file" name="jform[' . $fieldName . ']" id="jform_' . $fieldName . '" ' . ((isset($multiple) && $multiple) ? ' multiple="multiple" ' : '') . ' value="" class="hide" />';
                $script .= '<div class="dummyfile input-append"><input id="jform_' . $fieldName . '_name" type="text" name="filename" readonly />';
                $script .= '<span class="input-group-btn"><button id="jform_' . $fieldName . '_select_button" type="button" name="submitAddAttachments" class="btn btn-default"><i class="icon-folder-open"></i> ';
                $script .= ((isset($multiple) && $multiple) ? JText::_('COM_JEPROLAB_FILES_LABEL') : JText::_('COM_JEPROLAB_FILE_LABEL')) . '...</button>';
                if ((!isset($multiple) || !$multiple) && isset($files) && count($files) == 1 && isset($files[0]->download_url)) {
                    $script .= '<a href="' . $files[0]->download_url . '" class="btn btn-default"><i class="icon-cloud-download"></i> ';
                    if (isset($size)) {
                        $script .= JText::_('COM_JEPROLAB_DOWNLOAD_CURRENT_FILE_LABEL') . ' (' . $size . 'kb)';
                    } else {
                        $script .= JText::_('COM_JEPROLAB_DOWNLOAD_CURRENT_FILE_LABEL');
                    }
                    $script .= '</a>';
                }
                $script .= '</span></div>';
                $javaScript .= "});jeproFile.uploadManager(); ";

            }
            $script .= '</div>';
            $javaScript .= "}) ";
        }
        JFactory::getDocument()->addScript(JURI::base() . 'components/com_jeprolab/assets/javascript/jquery/ui/jquery.ui.widget.min.js');
        JFactory::getDocument()->addScript(JURI::base() . 'components/com_jeprolab/assets/javascript/jquery/jquery.fileupload.js');
        JFactory::getDocument()->addScript(JURI::base() . 'components/com_jeprolab/assets/javascript/jquery/jquery.fileupload-process.js');
        JFactory::getDocument()->addScript(JURI::base() . 'components/com_jeprolab/assets/javascript/jquery/jquery.fileupload-validate.js');
        JFactory::getDocument()->addScript(JURI::base() . 'components/com_jeprolab/assets/javascript/jquery/ladda/spin.min.js');
        JFactory::getDocument()->addScript(JURI::base() . 'components/com_jeprolab/assets/javascript/jquery/ladda/ladda.min.js');
        JFactory::getDocument()->addScriptDeclaration($javaScript);

        return $script;
    }

    public static function sideBarRender($active)
    {
        $dashboard = $catalog = $customer = $feedback = $feeds = $stats = $admins = $localisation = $setting = $shipping = $prices = false;
        switch ($active) {
            case 'catalog':
                $catalog = true;
                break;
            case 'customer':
                $customer = true;
                break;
            case 'feeds':
                $feeds = true;
                break;
            case 'prices':
                $prices = true;
                break;
            case 'settings':
                $setting = true;
                break;
            case 'admins':
                $admins = true;
                break;
            case 'localisation':
                $localisation = true;
                break;
            case 'feedback':
                $feedback = true;
                break;
            case 'stats' :
                $stats = true;
                break;
            case 'dashboard':
            default :
                $dashboard = true;
                break;

        }
        JHtmlSidebar::addEntry(JText::_('COM_JEPROLAB_DASHBOARD_LABEL'), 'index.php?option=com_jeprolab', $dashboard);
        JHtmlSidebar::addEntry(JText::_('COM_JEPROLAB_CATALOGS_LABEL'), 'index.php?option=com_jeprolab&task=catalogs', $catalog);
        JHtmlSidebar::addEntry(JText::_('COM_JEPROLAB_CUSTOMERS_LABEL'), 'index.php?option=com_jeprolab&task=customers', $customer);
        JHtmlSidebar::addEntry(JText::_('COM_JEPROLAB_FEEDBACK_LABEL'), 'index.php?option=com_jeprolab&view=feedback', $feedback);
        JHtmlSidebar::addEntry(JText::_('COM_JEPROLAB_FEED_LABEL'), 'index.php?option=com_jeprolab&view=feed', $feeds);
        JHtmlSidebar::addEntry(JText::_('COM_JEPROLAB_PRICE_RULES_LABEL'), 'index.php?option=com_jeprolab&view=prices', $prices);
        JHtmlSidebar::addEntry(JText::_('COM_JEPROLAB_SHIPPING_LABEL'), 'index.php?option=com_jeprolab&view=shipping', $shipping);
        JHtmlSidebar::addEntry(JText::_('COM_JEPROLAB_LOCALISATION_LABEL'), 'index.php?option=com_jeprolab&view=country', $localisation);
        JHtmlSidebar::addEntry(JText::_('COM_JEPROLAB_SETTING_LABEL'), 'index.php?option=com_jeprolab&view=setting', $setting);
        JHtmlSidebar::addEntry(JText::_('COM_JEPROLAB_ADMINISTRATION_LABEL'), 'index.php?option=com_jeprolab&view=administration', $admins);
        JHtmlSidebar::addEntry(JText::_('COM_JEPROLAB_STATS_LABEL'), 'index.php?option=com_jeprolab&view=stats', $stats);
    }
}

class JeprolabHelperForm extends JeprolabHelper
{
    public $id;
    public $first_call = true;

    /** @var array of forms fields */
    protected $fields_form = array();
}

