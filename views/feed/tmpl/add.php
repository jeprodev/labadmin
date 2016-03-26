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
$app = JFactory::getApplication(); /*
$css_dir = JeproshopContext::getContext()->lab->theme_directory;
$document->addStyleSheet(JURI::base() .'components/com_jeprolab/assets/themes/' . $css_dir . '/css/jeprolab.css'); */
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
/*ùif($this->check_product_association_ajax){
    $class_input_ajax = 'check_feed_name';
}else{
    $class_input_ajax = '';
} */
$document->addScript(JURI::base(). 'components/com_jeprolab/assets/javascript/editor/ckeditor.js');
?>
<form action="<?php echo JRoute::_('index.php?option=com_jeprolab&view=feed'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal" >
    <div class="feed_edit_form" style="width: 100%; " >
        <?php if(!empty($this->sideBar)){ ?>
            <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
        <?php } ?>
        <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
            <div id="feed_add_form" >
                <!--div class="control-group" >
                    <div class="control-label" ><label for="jform_-label" ><?php echo JText::_('COM_JEPROLAB_LANGUAGE_LABEL'); ?></label></div>
                    <div class="controls" >
                        <select id="jform_feed_lang_id" name="jform[lang_id]" >
                            <?php foreach($this->languages as $language){ ?>
                            <option value="<?php echo $language->lang_id; ?>" ><?php echo $language->name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div-->
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_feed_title-label" ><?php echo JText::_('COM_JEPROLAB_FEED_TITLE_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo  $this->helper->multiLanguageInputField('feed_title', true, true, null); ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_feed_link" id="jform_feed_link-label" title="<?php echo JText::_('COM_JEPROLAB_FEED_LINK_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_FEED_LINK_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $this->helper->multiLanguageInputField("feed_link", true, true, null); ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_feed_description" id="jform_feed_description-label" title="<?php echo JText::_('COM_JEPROLAB_FEED_DESCRIPTION_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROLAB_FEED_DESCRIPTION_LABEL'); ?></label></div>
                    <div class="controls" ><?php echo $this->helper->multiLanguageTextAreaField('feed_description',  NULL);  ?></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_feed_author" id="jform_feed_author-label" title="<?php echo JText::_('COM_JEPROLAB_FEED_AUTHOR_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_FEED_AUTHOR_LABEL'); ?></label></div>
                    <div class="controls" ><input type="text" id="jform_feed_author" name="jform[feed_author]" /></div>
                </div>
            </div>
        </div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="return" value="<?php echo $app->input->getCmd('return'); ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>

<script type="text/javascript" >
    //$('#feed_add_form').dom(function(){});
</script>