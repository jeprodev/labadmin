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

$document = JFactory::getDocument();
$app = JFactory::getApplication();
$css_dir = JeprolabContext::getContext()->lab->theme_directory;
$document->addStyleSheet(JURI::base() .'components/com_jeprolab/assets/themes/' . $css_dir .'/css/jeprolab.css');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

?>
<form action="<?php echo JRoute::_('index.php?option=com_jeprolab&view=country'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal" >
    <?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <div class="box_wrapper jeprolab_sub_menu_wrapper" ><?php echo $this->renderSubMenu('country'); ?></div>
        <div class="panel" >
            <div class="panel-title"><i class="icon-globe"></i> <?php echo JText::_('COM_JEPROLAB_YOU_ARE_ABOUT_TO_ADD_LABEL') . ' ' . JText::_('COM_JEPROLAB_COUNTRY_LABEL'); ?></div>
            <div class="panel-content well">
                <div class="control-group">
                    <div class="control-label" ><label for="jform_name" title="<?php echo JText::_('COM_JEPROLAB_COUNTRY_NAME_TITLE_DESC') . ' - ' . JText::_('COM_JEPROLAB_INVALID_CHARACTERS_LABEL'); ?>" ><?php echo JText::_('COM_JEPROLAB_NAME_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $this->helper->multiLanguageInputField('name', 'jform', true, null); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label" ><label for="jform_iso_code" title="<?php echo JText::_('COM_JEPROLAB_ISO_CODE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_ISO_CODE_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" id="jform_iso_code" name="jform[iso_code]" required="required" maxlength="3" value="" class="uppercase" /></div>
                </div>
                <div class="control-group">
                    <div class="control-label" ><label for="jform_call_prefix" title="<?php echo JText::_('COM_JEPROLAB_CALL_PREFIX_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_CALL_PREFIX_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" id="jform_call_prefix" name="jform[call_prefix]" required="required" maxlength="3" class="uppercase" /></div>
                </div>
                <div class="control-group">
                    <div class="control-label" ><label for="jform_need_zip_code" title="<?php echo JText::_('COM_JEPROLAB_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_NEED_ZIP_CODE_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $this->helper->radioButton('need_zip_code', 'add', 1); ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_zip_code_format" title="<?php echo JText::_('COM_JEPROLAB_ZIP_CODE_FORMAT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_ZIP_CODE_FORMAT_LABEL'); ?></label> </div>
                    <div class="controls" ><input type="text" id="jform_zip_code_format" name="jform[zip_code_format]" required="required" value="" /></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_address_layout" title="<?php echo JText::_('COM_JEPROLAB_ADDRESS_LAYOUT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_ADDRESS_LAYOUT_LABEL'); ?></label></div>
                    <div class="controls" ></div>
                </div>
                <div class="control-group">
                    <div class="control-label" ><label for="jform_default_currency" title="<?php echo JText::_('COM_JEPROLAB_DEFAULT_CURRENCY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_DEFAULT_CURRENCY_LABEL'); ?></label></div>
                    <div class="controls" >
                        <select id="jform_default_currency" name="jform[default_currency]" >
                            <option value="<?php echo (int)JeprolabSettingModelSetting::getValue('default_currency'); ?>" ><?php echo JText::_('COM_JEPROLAB_DEFAULT_LAB_CURRENCY_LABEL'); ?></option>
                            <?php foreach($this->currencies as $currency){
                                if($currency->currenccy_id != (int)JeprolabSettingModelSetting::getValue('default_currency')){ ?>
                            <option value="<?php echo $currency->currency_id; ?>" ><?php echo ucfirst($currency->name); ?></option>
                            <?php } }?>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label" ><label for="jform_zone_id" title="<?php echo JText::_('COM_JEPROLAB_GEOGRAPHICAL_REGION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_ZONE_LABEL'); ?></label></div>
                    <div class="controls" >
                        <select id="jform_zone_id" name="jform[zone_id]" >
                            <?php foreach($this->zones as $zone){ ?>
                            <option value="<?php echo $zone->zone_id; ?>" ><?php echo ucfirst($zone->name); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label" ><label for="jform_display_country_customer" title="<?php echo JText::_('COM_JEPROLAB_DISPLAY_COUNTRY_TO_CUSTOMERS_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_PUBLISHED_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $this->helper->radioButton('published', 'add', 1); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label" ><label for="jform_contains_states" title="<?php echo JText::_('COM_JEPROLAB_CONTAINS_STATES_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_CONTAINS_STATES_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $this->helper->radioButton('contains_states', 'add', 1); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label" ><label for="jform_need_identification_number" title="<?php echo JText::_('COM_JEPROLAB_NEED_IDENTIFICATION_NUMBER_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_NEED_IDENTIFICATION_NUMBER_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $this->helper->radioButton('need_identification_number', 'add', 1); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label" ><label for="jform_" title="<?php echo JText::_('COM_JEPROLAB_DISPLAY_TAX_LABEL_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_DISPLAY_TAX_LABEL_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $this->helper->radioButton('display_tax_label', 'add', 1); ?></div>
                </div>
                <?php if(JeprolabLabModelLab::isFeaturePublished()) { ?>
                <div class="control-group">
                    <div class="control-label" ><label for="jform_" title="<?php echo JText::_('COM_JEPROLAB_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_ASSOCIATED_LAB_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $this->associated_lab; ?></div>
                </div>
                <?php } ?>
            </div>
        </div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>

$wrapper = $wrapper ? $wrapper : 'jform';
if (!isset($this->languages) || !$this->languages) {
$this->languages = JeprolabLanguageModelLanguage::getLanguages();
}
<div class="translatable input-append" >
    <?php foreach ($this->languages as $language) { ?>
    <div class="lang_' . $language->lang_id . ' input_lang" ' . (!$language->is_default ? 'style="display:none" ' : '')  >
        <input type="text" id="jform_' . $fieldName . '_' . $language->lang_id . '" name="' . $wrapper . '[' . $fieldName . '_' . $language->lang_id . ']" class="copy_to_friendly_url hasTooltip" value="(isset($content[$language->lang_id]) ? $content[$language->lang_id] : '')" onKeyup="if(isArrowKey(event)) return; updateFriendlyUrl();" <?php echo (isset($required)) ? 'required="required" ' : ''; echo isset($maxLength) ? 'maxlength="" ' : ''; ?>  />
    </div>
    <div class="btn-group-action lang-select btn-group">
        <button type="button" class="btn btn-default dropdown_toggle" tabindex="-1" data-toggle="dropdown" ><?php echo  $language->iso_code ; ?>&nbsp; <i class="caret" ></i> </button>
        <ul class="dropdown-menu" >';
        foreach ($this->languages as $value) {
        $script .= '<li><a href="javascript:hideOtherLanguage(' . $value->lang_id . ');" tabindex="-1" >' . $value->name . '</a></li>';
        }
        </ul>
    </div>';
if ($hint != '') {
$script .= '<p class="preference_description" >' . $hint . '</p>';
}
$script .= '</div>';

<?php } ?>
</div>