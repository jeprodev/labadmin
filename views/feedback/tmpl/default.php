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
$css_dir = JeproLabContext::getContext()->lab->theme_directory;
$document->addStyleSheet(JURI::base(true) .'/components/com_jeprolab/assets/themes/' . $css_dir . '/css/jeprolab.css');
?>
<div id="feedback" >
    <div class="feed_edit_form" style="width: 100%; " >
        <?php if(!empty($this->sideBar)){ ?>
            <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
        <?php } ?>
        <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
            <div class="panel" >
                <div class="panel-content" >
                    <table class="table table-striped table-header-rotated" id="feedbackList">
                        <thead>
                            <tr>
                                <th width="1%" class="nowrap center" >#</th>
                                <th width="1%" class="nowrap center" ><?php echo JHtml::_('grid.checkall'); ?></th>
                                <th width="9%" class="nowrap center" ><?php echo ucfirst(JText::_('COM_JEPROLAB_CUSTOMER_NAME_LABEL')); ?></th>
                                <th width="3%" class="nowrap center hidden-phone rotate-45" ><div><span class="design_wrap" ><?php echo JText::_('COM_JEPROLAB_ENJOY_WORKING_WITH_US_LABEL'); ?></span><div></th>
                                <th width="3%" class="nowrap center hidden-phone rotate-45" ><div><span class="design_wrap" ><?php echo JText::_('COM_JEPROLAB_STAFF_COURTESY_LABEL'); ?></span><div></th>
                                <th width="3%" class="nowrap center hidden-phone rotate-45" ><div><span class="design_wrap" ><?php echo JText::_('COM_JEPROLAB_TEAM_ABILITIES_LABEL'); ?></span><div></th>
                                <th width="3%" class="nowrap center hidden-phone rotate-45" ><div><span class="design_wrap" ><?php echo JText::_('COM_JEPROLAB_PROBLEM_SUPPORT_LABEL'); ?></span><div></th>
                                <th width="3%" class="nowrap center hidden-phone rotate-45" ><div><span class="design_wrap" ><?php echo JText::_('COM_JEPROLAB_TEAM_AVAILABILITY_LABEL'); ?></span><div></th>
                                <th width="3%" class="nowrap center hidden-phone rotate-45" ><div><span class="design_wrap" ><?php echo JText::_('COM_JEPROLAB_RECOMMEND_OUR_SERVICE_LABEL'); ?></span><div></th>
                                <th width="3%" class="nowrap center hidden-phone rotate-45" ><div><span class="design_wrap" ><?php echo JText::_('COM_JEPROLAB_REUSE_OUR_SERVICE_LABEL'); ?></span><div></th>
                                <th width="3%" class="nowrap center hidden-phone rotate-45" ><div><span class="design_wrap" ><?php echo JText::_('COM_JEPROLAB_SERVICE_SPEED_LABEL'); ?></span<div></th>
                                <th width="3%" class="nowrap center hidden-phone rotate-45" ><div><span class="design_wrap" ><?php echo JText::_('COM_JEPROLAB_SAMPLE_DELIVERY_SPEED_LABEL'); ?></span><div></th>
                                <th width="3%" class="nowrap center hidden-phone rotate-45" ><div><span class="design_wrap" ><?php echo JText::_('COM_JEPROLAB_SUBMISSION_LABEL'); ?></span><div></th>
                                <th width="3%" class="nowrap center hidden-phone rotate-45" ><div><span class="design_wrap" ><?php echo JText::_('COM_JEPROLAB_REPORTS_QUALITY_LABEL'); ?></span><div></th>
                                <th width="1%" class="nowrap center hidden-phone rotate-45" ><div><span class="design_wrap" ><?php echo JText::_('COM_JEPROLAB_ANALYZE_SPEED_LABEL'); ?></span><div></th>
                                <th width="1%" class="nowrap center hidden-phone rotate-45" ><div><span class="design_wrap" ><?php echo JText::_('COM_JEPROLAB_ONLINE_SERVICES_LABEL'); ?></span><div></th>
                                <th width="1%" class="nowrap center hidden-phone rotate-45" ><div><span class="design_wrap" ><?php echo JText::_('COM_JEPROLAB_GLOBAL_QUALITY_LABEL'); ?></span><div></th>
                                <th width="4%" class="nowrap  rotate-45" ><div><span class="design_wrap" ><?php echo JText::_('COM_JEPROLAB_ACTIONS_LABEL'); ?></span></div></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($this->feeds_back)){ ?>
                            <tr><td colspan="17" ><div class="alert alert-no-items" ><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div></td><tr>
                            <?php } else {
                                foreach($this->feeds_back as $index => $feedback){
                                    $feedBackViewLink = JRoute::_('index.php?option=com_jeprolab&view=feedback&task=view&feedback_id=' . (int)$feedback->feedback_id);
                                    $customerLink = JRoute::_('index.php?option?com_jeprolab&view=customer&task=view&customer_id=' . (int)$feedback->customer_id . '&' . JSession::getFormToken() . '=1' );?>
                                <tr class="row_<?php echo $index % 2; ?>" >
                                    <td width="1%" class="nowrap center" ><?php echo $index; ?></td>
                                    <td width="1%" class="nowrap center" ><?php echo JHtml::_('grid.id', $index, $feedback->feedback_id); ?></td>
                                    <td width="9%" class="nowrap center" ><a href="<?php echo $customerLink; ?>" ><?php echo $feedback->customer_name; ?></a></td>
                                    <td width="3%" class="nowrap center hidden-phone" ><i class="icon-<?php echo $feedback->enjoy_working_with_us; ?>" ></i></td>
                                    <td width="3%" class="nowrap center hidden-phone" ><i class="icon-<?php echo $feedback->staff_courtesy; ?>" ></i></td>
                                    <td width="3%" class="nowrap center hidden-phone" ><i class="icon-<?php echo $feedback->team_abilities; ?>" ></i></td>
                                    <td width="3%" class="nowrap center hidden-phone" ><i class="icon-<?php echo $feedback->problem_support; ?>" ></i></td>
                                    <td width="3%" class="nowrap center hidden-phone" ><i class="icon-<?php echo $feedback->team_availability; ?>" ></i></td>
                                    <td width="3%" class="nowrap center hidden-phone" ><i class="icon-<?php echo $feedback->reuse_our_services ? 'publish' : 'unpublish'; ?>" ></i></td>
                                    <td width="3%" class="nowrap center hidden-phone" ><i class="icon-<?php echo $feedback->recommend_our_services ? 'publish' : 'unpublish'; ?>" ></i> </td>
                                    <td width="3%" class="nowrap center hidden-phone" ><i class="icon-<?php echo $feedback->services_speed; ?>" ></i></td>
                                    <td width="3%" class="nowrap center hidden-phone" ><i class="icon-<?php echo $feedback->sample_delivery_speed; ?>" ></i></td>
                                    <td width="3%" class="nowrap center hidden-phone" ><i class="icon-<?php echo $feedback->submission; ?>" ></i></td>
                                    <td width="3%" class="nowrap center hidden-phone" ><i class="icon-<?php echo $feedback->reports_quality; ?>" ></i></td>
                                    <td width="3%" class="nowrap center hidden-phone" ><i class="icon-<?php echo $feedback->analyze_speed; ?>" ></i></td>
                                    <td width="3%" class="nowrap center hidden-phone" ><i class="icon-<?php echo $feedback->online_services; ?>" ></i></td>
                                    <td width="3%" class="nowrap center hidden-phone" ><i class="icon-<?php echo $feedback->global_quality; ?>" ></i></td>
                                    <td class="nowrap ">
                                        <div class="btn-group-action" >
                                            <div class="btn-group pull-right" >
                                                <a href="<?php echo $feedBackViewLink; ?>" class="btn btn-micro" ><i class="icon-search" ></i>&nbsp;<?php echo JText::_('COM_JEPROLAB_VIEW_LABEL'); ?></a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php }
                            } ?>
                        </tbody>
                        <tfoot><tr><td colspan="17" class="center nowrap" ><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>