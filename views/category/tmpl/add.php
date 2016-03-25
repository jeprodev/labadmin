<?php
/**
 * @version     1.0.3
 * @package     Components
 * @subpackage  com_jeprolab
 * @link        http://jeprodev.net
 * @copyright   (C) 2009 - 2011
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
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
$document->addScript(JURI::base(). 'components/com_jeprolab/assets/javascript/editor/ckeditor.js');
?>
<form action="<?php echo JRoute::_('index.php?option=com_jeprolab&view=category'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
    <?php if(!empty($this->sideBar)){ ?>
        <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <?php echo $this->createFormSubMenu(); ?>
        <div class="panel" >
            <div class="panel-title" ><i class="icon-category" ></i> <?php echo JText::_('COM_JEPROLAB_YOU_ARE_ABOUT_TO_ADD_LABEL') . JText::_('COM_JEPROLAB_CATEGORY_LABEL'); ?></div>
            <div id="category" class="panel-content well" >
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROLAB_CATEGORY_NAME_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_CATEGORY_NAME_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->helper->multiLanguageInputField('name', true, null); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROLAB_CATEGORY_PUBLISHED_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_CATEGORY_PUBLISHED_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->helper->radioButton('published', 'edit'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROLAB_CATEGORY_PARENT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_CATEGORY_PARENT_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->categories_tree; ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROLAB_CATEGORY_DESCRIPTION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_CATEGORY_DESCRIPTION_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->helper->multiLanguageTextAreaField('description', null); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROLAB_CATEGORY_IMAGE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_CATEGORY_IMAGE_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->helper->imageFileChooser(); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROLAB_CATEGORY_META_TITLE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_META_TITLE_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->helper->multiLanguageInputField('meta_title', true, null); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROLAB_CATEGORY_META_DESCRIPTION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_META_DESCRIPTION_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->helper->multiLanguageInputField('meta_description', true, null); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROLAB_CATEGORY_META_KEYWORDS_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_META_KEYWORDS_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->helper->multiLanguageInputField('meta_keywords', true, null); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROLAB_CATEGORY_LINK_REWRITE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_LINK_REWRITE_LABEL'); ?></label></div>
                <div class="controls" ><?php echo $this->helper->multiLanguageInputField('link_rewrite', true, null); ?></div>
            </div>
            <?php if(JeprolabSettingModelSetting::getValue('multi_lab_feature_active') && 1){ ?>
                <div class="control-group" >
                    <div class="control-label" ><label title="<?php echo JText::_('COM_JEPROLAB_IS_ROOT_CATEGORY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_IS_ROOT_CATEGORY_LABEL'); ?></label> </div>
                    <div class="controls" ><?php echo $this->helper->radioButton('is_root_category', 'edit', null); ?></div>
                </div>
            <?php } ?>
            <div class="control-group">
                <div class="control-label"><label title="<?php echo JText::_('COM_JEPROLAB_CATEGORY_ALLOWED_GROUP_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_CATEGORY_ALLOWED_GROUP_LABEL'); ?></label></div>
                <div class="controls" >
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th class="nowrap center" width="1%"><?php echo JHtml::_('grid.checkall'); ?></th>
                            <th class="nowrap center" width="1%"><?php echo JText::_('COM_JEPROLAB_ID_LABEL'); ?></th>
                            <th class="nowrap " ><?php echo JText::_('COM_JEPROLAB_GROUP_NAME_LABEL'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(empty($this->groups)){ ?>
                            <tr><td colspan="3" ><div class="alert alert-no-items" ><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div></td><tr>
                        <?php } else {
                            foreach($this->groups as $index => $group){
                                $group_link = JRoute::_('index.php?option=com_jeprolab&view=group&task=edit&group_id=' . $group->group_id .'&' . JeprolabTools::getGroupToken() .'=1');
                                ?>
                                <tr class="row_<?php echo $index % 2; ?>" >
                                    <td class="nowrap center" width="1%" ><?php echo JHtml::_('grid.id', $index, $group->group_id); ?></td>
                                    <td class="nowrap center" width="1%" ><?php echo $group->group_id; ?></td>
                                    <td class="nowrap " width="40%" ><?php echo $group->name; ?></td>
                                </tr>
                            <?php }
                        } ?>
                        </tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label"></div>
                <div class="controls">
                    <div class="alert alert-info">
                        <h4><?php $allowed_groups = isset($this->allowed_groups) ? count($this->allowed_groups) : 0;
                            echo JText::_('COM_JEPROLAB_YOU_NOW_HAVE_MESSAGE') . ' ' . ((int)($allowed_groups) + 3) . ' ' . JText::_('COM_JEPROLAB_ALLOWED_GROUP_MESSAGE'); ?></h4><br />
                        <p>
                            <?php echo $this->unidentified_group_information .'<br />'. $this->guest_group_information .'<br />'. $this->default_group_information .'<br />';
                            if(isset($this->allowed_groups)) {
                                foreach ($this->allowed_groups as $ag) { ?>
                                    <b><?php echo $ag->name; ?></b> - <?php echo $ag->description; ?><br/>
                                <?php }
                            }?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="category_id" value="<?php echo (int)$this->context->controller->category->category_id; ?>" />
        <input type="hidden" name="<?php echo JeprolabTools::getCategoryToken(); ?>"  value="1" />
    </div>
</form>