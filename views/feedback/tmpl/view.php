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
<div id="feedback" >
    <div class="feed_edit_form" style="width: 100%; " >
        <?php if(!empty($this->sideBar)){ ?>
            <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>
        <?php } ?>
        <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> >
            <?php echo $this->createFormSubMenu(); ?>
            <div class="panel form-horizontal">
                <div class="panel-title" ><?php echo JText::_('COM_JEPROLAB_FEEDBACK_LABEL'); ?></div>
                <div class="panel-container well" >
                    <div>
                        <div class="" ></div>
                        <div class="half_wrapper_left half_wrapper">
                            <div class="control-group" >
                                <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_CUSTOMER_NAME_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_CUSTOMER_NAME_LABEL'); ?></label></div>
                                <div class="controls" ><?php echo $this->feedback->customer_name; ?></div>
                            </div>
                            <div class="control-group" >
                                <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_CUSTOMER_PHONE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_CUSTOMER_PHONE_LABEL'); ?></label></div>
                                <div class="controls" ><?php echo $this->feedback->customer_phone; ?></div>
                            </div>
                        </div>
                        <div class="half_wrapper_right half_wrapper" >
                            <div class="control-group" >
                                <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_CUSTOMER_COMPANY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_CUSTOMER_COMPANY_LABEL'); ?></label></div>
                                <div class="controls" ><?php echo $this->feedback->customer_company; ?></div>
                            </div>
                            <div class="control-group" >
                                <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_CUSTOMER_EMAIL_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_CUSTOMER_EMAIL_LABEL'); ?></label></div>
                                <div class="controls" ><?php echo $this->feedback->customer_email; ?></div>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div>
                        <div></div>
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_ENJOY_WORKING_WITH_US_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_ENJOY_WORKING_WITH_US_LABEL'); ?></label></div>
                            <div class="controls" ><?php echo $this->setFeedBackChoice($this->feedback->enjoy_working_with_us); ?></div>
                        </div>
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_STAFF_COURTESY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_STAFF_COURTESY_LABEL'); ?></label></div>
                            <div class="controls" ><?php echo $this->setFeedBackChoice($this->feedback->staff_courtesy); ?></div>
                        </div>
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_TEAM_ABILITIES_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_TEAM_ABILITIES_LABEL'); ?></label></div>
                            <div class="controls" ><?php echo $this->setFeedBackChoice($this->feedback->team_abilities); ?></div>
                        </div>
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_PROBLEM_SUPPORT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_PROBLEM_SUPPORT_LABEL'); ?></label></div>
                            <div class="controls" ><?php echo $this->setFeedBackChoice($this->feedback->problem_support); ?></div>
                        </div>
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_TEAM_AVAILABILITY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_TEAM_AVAILABILITY_LABEL'); ?></label></div>
                            <div class="controls" ><?php echo $this->setFeedBackChoice($this->feedback->team_availability); ?></div>
                        </div>
                        <hr />
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_GENERAL_COMMENT_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_GENERAL_COMMENT_LABEL'); ?></label></div>
                            <div class="controls" ><p><?php echo $this->feedback->general_comment; ?></p></div>
                        </div>
                        <div></div>
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_SUBMISSION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_SUBMISSION_LABEL'); ?></label></div>
                            <div class="controls" ><?php echo $this->setFeedBackChoice($this->feedback->submission); ?></div>
                        </div>
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_SAMPLE_DELIVERY_SPEED_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_SAMPLE_DELIVERY_SPEED_LABEL'); ?></label></div>
                            <div class="controls" ><?php echo $this->setFeedBackChoice($this->feedback->sample_delivery_speed); ?></div>
                        </div>
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_ANALYZE_SPEED_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_ANALYZE_SPEED_LABEL'); ?></label></div>
                            <div class="controls" ><?php echo $this->setFeedBackChoice($this->feedback->analyze_speed); ?></div>
                        </div>
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_REPORTS_QUALITY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_REPORTS_QUALITY_LABEL'); ?></label></div>
                            <div class="controls" ><?php echo $this->setFeedBackChoice($this->feedback->reports_quality); ?></div>
                        </div>
                        <!--div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB__TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB__LABEL'); ?></label></div>
                            <div class="controls" ><?php //echo $this->setFeedBackChoice($this->feedback->); ?></div>
                        </div -->
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_ONLINE_SERVICES_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_ONLINE_SERVICES_LABEL'); ?></label></div>
                            <div class="controls" ><?php echo $this->setFeedBackChoice($this->feedback->online_services); ?></div>
                        </div>
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_GLOBAL_QUALITY_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_GLOBAL_QUALITY_LABEL'); ?></label></div>
                            <div class="controls" ><?php echo $this->setFeedBackChoice($this->feedback->global_quality); ?></div>
                        </div>
                        <hr />
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_COMMENT_SUGGESTION_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_COMMENT_SUGGESTION_LABEL'); ?></label></div>
                            <div class="controls" ></div>
                        </div>
                        <hr />
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_REUSE_OUR_SERVICES_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_REUSE_OUR_SERVICE_LABEL'); ?></label></div>
                            <div class="controls" ><?php echo $this->setRadioButton($this->feedback->reuse_our_services); ?></div>
                        </div>
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_RECOMMEND_OUR_SERVICES_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_RECOMMEND_OUR_SERVICE_LABEL'); ?></label></div>
                            <div class="controls" ><?php echo $this->setRadioButton($this->feedback->recommend_our_services); ?></div>
                        </div>
                        <hr />
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_HOW_DO_YOU_LEARN_ABOUT_US_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_HOW_DO_YOU_LEARN_ABOUT_US_LABEL'); ?></label></div>
                            <div class="controls" ><p><?php echo $this->feedback->how_do_you_learn_about_us; ?></p></div>
                        </div>
                        <div></div>
                        <div class="control-group" >
                            <div class="control-label" ><label for="jform_-label" title="<?php echo JText::_('COM_JEPROLAB_HELP_US_IMPROVE_OUR_SERVICE_TITLE_DESC'); ?>" ><?php echo JText::_('COM_JEPROLAB_HELP_US_IMPROVE_OUR_SERVICE_LABEL'); ?></label></div>
                            <div class="controls" ><p><?php echo $this->feedback->help_us_improve_our_service ; ?></p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>