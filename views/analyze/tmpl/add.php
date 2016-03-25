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
<form action="<?php echo JRoute::_('index.php?option=com_jeprolab&view=analyze'); ?>" method="post" name="adminForm" id="adminForm"  class="form-horizontal">
    <?php if(!empty($this->sideBar)){ ?>
        <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
        <?php echo $this->createFormSubMenu(); ?>
        <div class="panel" >
            <div class="panel-title" ><i class="icon-analyze" ></i> </div>
            <div class="panel-content well" >
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_" title="<?php echo JText::_('COM_JEPROLAB_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROLAB_LABEL'); ?></label> </div>
                    <div class="controls" ></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_" title="<?php echo JText::_('COM_JEPROLAB_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROLAB_LABEL'); ?></label> </div>
                    <div class="controls" ></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_" title="<?php echo JText::_('COM_JEPROLAB_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROLAB_LABEL'); ?></label> </div>
                    <div class="controls" ></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_" title="<?php echo JText::_('COM_JEPROLAB_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROLAB_LABEL'); ?></label> </div>
                    <div class="controls" ></div>
                </div>
                <div class="control-group" >
                    <div class="control-label" ><label for="jform_" title="<?php echo JText::_('COM_JEPROLAB_TITLE_DESC'); ?>"><?php echo JText::_('COM_JEPROLAB_LABEL'); ?></label> </div>
                    <div class="controls" ></div>
                </div>
            </div>
        </div>
    </div>
</form>