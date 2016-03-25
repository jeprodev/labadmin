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
        <div class="box_wrapper jeprolab_sub_menu_wrapper" ><?php echo $this->renderSubMenu('zone'); ?></div>
        <table class="table table-striped" id="zoneList" >
            <thead>
            <tr>
                <th width="1%" class="nowrap center hidden-phone" >#</th>
                <th width="1%" class="nowrap center hidden-phone" ><?php echo JHtml::_('grid.checkall'); ?></th>
                <th width="60%" class="nowrap left " ><?php echo JText::_('COM_JEPROLAB_ZONE_NAME_LABEL'); ?></th>
                <th width="3%" class="nowrap center hidden-phone" ><?php echo JText::_('COM_JEPROLAB_ALLOW_DELIVERY_LABEL'); ?></th>
                <th width="1%" class="nowrap center" ><?php echo JText::_('COM_JEPROLAB_ACTIONS_LABEL'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if(empty($this->zones)){ ?>
                <tr>
                    <td colspan="5" ><div class="alert alert-no-items" ><?php echo JText::_('JGLOBAL_NO_MACTHING_RESULTS'); ?></div></td>
                </tr>
            <?php } else {
                foreach($this->zones as $index => $zone){
                    $zoneLink = JRoute::_('index.php?option=com_jeprolab&view=country&task=edit_zone&zone_id=' . $zone->zone_id . '&' . JeprolabTools::getCountryToken() . '=1');
                    $allow_delivery = ($zone->allow_delivery ? 'icon-publish' : 'icon-unpublish')
                    ?>
                    <tr class="row_<?php echo $index % 2; ?>" sortable-group-id="<?php  ?>">
                        <td class="order nowrap center hidden-phone"><?php echo $index + 1; ?></td>
                        <td class="order nowrap center hidden-phone"><?php echo JHtml::_('grid.id', $index, $zone->zone_id); ?></td>
                        <td class="order nowrap"><a href="<?php echo $zoneLink; ?>" ><?php echo $zone->zone_name; ?></a></td>
                        <td class="order nowrap center hidden-phone">
                            <a class="hasTooltip" href="javascript:void(0);" onclick="listZoneTask(<?php echo $zone->zone_id . ', ' . ($zone->allow_delivery ? '\'unpiblish\'' : '\'publish\'');?>)" >
                                <i class="<?php echo $allow_delivery; ?>" ></i>
                            </a>
                        </td>
                        <td class="order nowrap center hidden-phone">
                            <div class="btn-group-action" >
                                <div class="btn-group pull-right" >
                                    <a href="<?php echo $zoneLink; ?>" class="btn btn-micro" ><i class="icon-edit" ></i>&nbsp;<?php echo JText::_('COM_JEPROLAB_EDIT_LABEL'); ?></a>
                                    <button class="btn btn-micro dropdown_toggle" data-toggle="dropdown" ><i class="caret"></i> </button>
                                    <ul class="dropdown-menu">                                        
                                        <li><a href="<?php echo $delete_zone_link; ?>" onclick="if(confirm('<?php echo JText::_('COM_JEPROLAB_ZONE_DELETE_LABEL') . $zone->zone_name; ?>')){ return true; }else{ event.stopPropagation(); event.preventDefault(); };" title="<?php echo ucfirst(JText::_('COM_JEPROLAB_DELETE_LABEL')); ?>" class="delete"><i class="icon-trash" ></i>&nbsp;<?php echo ucfirst(JText::_('COM_JEPROLAB_DELETE_LABEL')); ?></a></li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php }
            } ?>
            </tbody>
        </table>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>