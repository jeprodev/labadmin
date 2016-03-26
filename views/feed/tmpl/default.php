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
$css_dir = $css_dir ? $css_dir : 'default';
$document->addStyleSheet(JURI::base() .'components/com_jeprolab/assets/themes/' . $css_dir .'/css/jeprolab.css');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
?>
<form action="<?php echo JRoute::_('index.php?option=com_jeprolab&view=feed'); ?>" name="adminForm"  id="adminForm" method="post" >
    <div class="feed_edit_form" style="width: 100%; " >
        <?php if(!empty($this->sideBar)){ ?>
            <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
        <?php } ?>
        <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
            <div class="box_wrapper jeproshop_sub_menu_wrapper">
                <fieldset class="btn-group">
                    <a href="<?php echo JRoute::_('index.php?option=com_jeprolab&view=feed'); ?>" class="btn btn-success" ><i class="icon-" ></i> <?php echo JText::_('COM_JEPROLAB_FEED_LABEL'); ?></a>
                    <a href="<?php echo JRoute::_('index.php?option=com_jeprolab&view=feedback'); ?>" class="btn" ><i class="icon-" ></i> <?php echo JText::_('COM_JEPROLAB_FEEDBACK_LABEL'); ?></a>
                </fieldset>
            </div>
            <div class="panel" >
                <div class="panel-content" >
                    <table class="table table-striped" id="addressList">
                        <thead>
                            <tr>
                                <th class="nowrap center" width="1%">#</th>
                                <th class="nowrap center" width="1%"><?php echo JHtml::_('grid.checkall'); ?></th>
                                <th class="nowrap " width="6%"><?php echo JText::_('COM_JEPROLAB_FEED_AUTHOR_LABEL'); ?></th>
                                <th class="nowrap hidden-phone" width="12%"><?php echo JText::_('COM_JEPROLAB_FEED_TITLE_LABEL'); ?></th>
                                <th class="nowrap " width="70%"><?php echo JText::_('COM_JEPROLAB_FEED_DESCRIPTION_LABEL'); ?></th>
                                <th class="nowrap " width="4%"><span class="pull-right" ><?php echo JText::_('COM_JEPROLAB_ACTIONS_LABEL'); ?></span></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(empty($this->feeds)){ ?>
                            <tr>
                                <td colspan="10" ><div class="alert alert-no-items" ><?php echo JText::_('COM_JEPROLAB_NO_MACTHING_RESULTS'); ?></div></td>
                            </tr>
                        <?php } else {
                            foreach ($this->feeds as $index => $feed) {
                                $feed_view_link = JRoute::_('index.php?option=com_jeprolab&view=feed&task=edit&feed_id=' . (int)$feed->feed_id);
                                $delete_feed_link = JRoute::_('index.php?option=com_jeprolab&view=feed&task=delete&feed_id=' . (int)$feed->feed_id);
                                ?>
                        <tr class="row_<?php echo ($index%2); ?>" >
                            <td class="nowrap center "><?php echo $index + 1; ?></td>
                            <td class="nowrap center hidden-phone"><?php echo JHtml::_('grid.id', $index, $feed->feed_id); ?></td>
                            <td class="nowrap "><?php echo ucfirst($feed->feed_author); ?></td>
                            <td class="nowrap "><?php echo $feed->feed_title; ?></td>
                            <td class="hidden-phone"><?php echo $feed->feed_description; ?></td>
                            <td class="nowrap ">
                                <div class="btn-group-action" >
                                    <div class="btn-group pull-right" >
                                        <a href="<?php echo $feed_view_link; ?>" class="btn btn-micro" ><i class="icon-search" ></i>&nbsp;<?php echo JText::_('COM_JEPROLAB_VIEW_LABEL'); ?></a>
                                       <button class="btn btn-micro dropdown_toggle" data-toggle="dropdown" ><i class="caret"></i> </button>
                                       <ul class="dropdown-menu">
                                           <li><a href="<?php echo $delete_feed_link; ?>" onclick="if(confirm('<?php echo JText::_('COM_JEPROLAB_DELETE_LABEL') . $feed->feed_title; ?>')){ return true; }else{ event.stopPropagation(); event.preventDefault(); };" title="<?php echo ucfirst(JText::_('COM_JEPROLAB_DELETE_LABEL')); ?>" class="delete"><i class="icon-trash" ></i>&nbsp;<?php echo ucfirst(JText::_('COM_JEPROLAB_DELETE_LABEL')); ?></a></li>
                                       </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                            <?php }
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>