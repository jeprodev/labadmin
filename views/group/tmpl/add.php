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
$icon_directory = JURI::base() . 'components/com_jeprolab/assets/themes/' . $css_dir . '/images/';
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
?>
<form action="<?php echo JRoute::_('index.php?option=com_jeprolab&view=group'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
    <?php if(!empty($this->sideBar)){ ?>
    <div id="j-sidebar-container" class="span2" ><?php echo $this->sideBar; ?></div>   
    <?php } ?>
    <div id="j-main-container"  <?php if(!empty($this->sideBar)){ echo 'class="span10"'; }?> > 
        <?php echo $this->renderSubMenu('group'); ?>
        <div class="separation"></div>
        <div class="panel" >
        	<div class="panel-title" ><?php echo JText::_('COM_JEPROLAB_CREATE_NEW_GROUP_LABEL'); ?></div>
        	<div class="panel-content well" >
        		<div class="control-group" >
        			<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_GROUP_NAME_LABEL'); ?></label></div>
        			<div class="controls" ><?php echo $this->helper->multiLanguageInputField('name', 'jform'); ?></div>
        		</div>
        		<div class="control-group" >
        			<div class="control-label" ><label for="jform_group_reduction"><?php echo JText::_('COM_JEPROLAB_GROUP_REDUCTION_LABEL'); ?></label></div>
        			<div class="controls" >
        				<div class="input-append" >
        					<input type="text" class="price_box hasTooltip" id="jform_group_reduction" name="jform[group_reduction]" value="0.00"  />
        					<button type="button" class="btn" id="jform_img" >%</button>
        				</div>
        			</div>
        		</div>
        		<div class="control-group" >
        			<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_PRICE_DISPLAY_METHOD_LABEL'); ?></label></div>
        			<div class="controls" >
        				<select id="jform_price_display_metho" name="jform[price_display_method]" >
        					<option value="0" ><?php echo JText::_('COM_JEPROLAB_TAX_EXCLUDED_LABEL'); ?></option>
        					<option value="1" ><?php echo JText::_('COM_JEPROLAB_TAX_INCLUDED_LABEL'); ?></option>
        				</select>
        			</div>
        		</div>
        		<div class="control-group" >
        			<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_DISPLAY_PRICES_LABEL'); ?></label></div>
        			<div class="controls" >
        				<fieldset class="radio btn-group" id="jform_show_prices" >
        					<input type="radio" id="jform_show_prices_yes" name="jform[show_prices]" value="1" checked="checked" /><label for="jform_show_prices_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        					<input type="radio" id="jform_show_prices_no" name="jform[show_prices]" value="0" /><label for="jform_show_prices_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        				</fieldset>
        			</div>
        		</div>
        		<div class="control-group" >
        			<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_ASSOCIATED_CATEGORIES_LABEL'); ?></label></div>
        			<div class="controls" ></div>
        		</div>
        	</div> 
        </div>
        <div class="panel" >
        	 <div class="panel-title" ><?php echo JText::_('COM_JEPROLAB_AUTHORIZED_MODULES_LABEL'); ?></div>
        	<div class="panel-content well" >
        		<div class="half_wrapper left" >
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_SOCIAL_NETWORK_SHARING_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_social_network_sharing" >
        						<input type="radio" id="jform_social_network_sharing_yes" name="jform[social_network_sharing]" value="1" checked="checked" /><label for="jform_social_network_sharing_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_social_network_sharing_no" name="jform[social_network_sharing]" value="0" /><label for="jform_social_network_sharing_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_BANNERS_BLOCK_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_banners_block" >
        						<input type="radio" id="jform_banners_block_yes" name="jform[banners_block]" value="1" checked="checked" /><label for="jform_banners_block_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_banners_block_no" name="jform[banners_block]" value="0" /><label for="jform_banners_block_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_BANK_WIRE_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_bank_wire" >
        						<input type="radio" id="jform_bank_wire_yes" name="jform[bank_wire]" value="1" checked="checked" /><label for="jform_bank_wire_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_bank_wire_no" name="jform[bank_wire]" value="0" /><label for="jform_bank_wire_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_BEST_SALES_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_best_sales" >
        						<input type="radio" id="jform_best_sales_yes" name="jform[best_sales]" value="1" checked="checked" /><label for="jform_best_sales_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_best_sales_no" name="jform[best_sales]" value="0" /><label for="jform_best_sales_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_CART_BLOCK_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_cart_block" >
        						<input type="radio" id="jform_cart_block_yes" name="jform[cart_block]" value="1" checked="checked" /><label for="jform_cart_block_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_cart_block_no" name="jform[cart_block]" value="0" /><label for="jform_cart_block_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_SOCIAL_BLOCK_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_social_block" >
        						<input type="radio" id="jform_social_block_yes" name="jform[social_block]" value="1" checked="checked" /><label for="jform_social_block_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_social_block_no" name="jform[social_block]" value="0" /><label for="jform_social_block_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_CATEGORIES_BLOCK_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_categories_block" >
        						<input type="radio" id="jform_categories_block_yes" name="jform[categories_block]" value="1" checked="checked" /><label for="jform_categories_block_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_categories_block_no" name="jform[categories_block]" value="0" /><label for="jform_categories_block_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_CURRENCIES_BLOCK_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_currencies_block" >
        						<input type="radio" id="jform_currencies_block_yes" name="jform[currencies_block]" value="1" checked="checked" /><label for="jform_currencies_block_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_currencies_block_no" name="jform[currencies_block]" value="0" /><label for="jform_currencies_block_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB__LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_cart_block" >
        						<input type="radio" id="jform__yes" name="jform[]" value="1" checked="checked" /><label for="jform__yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform__no" name="jform[]" value="0" /><label for="jform__no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB__LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_cart_block" >
        						<input type="radio" id="jform__yes" name="jform[]" value="1" checked="checked" /><label for="jform__yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform__no" name="jform[]" value="0" /><label for="jform__no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        		</div>
        		<div class="half_wrapper right" >
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB__LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_cart_block" >
        						<input type="radio" id="jform__yes" name="jform[]" value="1" checked="checked" /><label for="jform__yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform__no" name="jform[]" value="0" /><label for="jform__no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_GRAB_STATISTIC_DATA_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_grab_statistics_data" >
        						<input type="radio" id="jform_grab_statistics_data_yes" name="jform[grab_statistics_data]" value="1" checked="checked" /><label for="jform_grab_statistics_data_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_grab_statistics_data_no" name="jform[grab_statistics_data]" value="0" /><label for="jform_grab_statistics_data_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_BROWSER_AND_OPERATING_SYSTEM_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_browser_and_operating_system" >
        						<input type="radio" id="jform_browser_and_operating_system_yes" name="jform[browser_and_operating_system]" value="1" checked="checked" /><label for="jform_browser_and_operating_system_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_browser_and_operating_system_no" name="jform[browser_and_operating_system]" value="0" /><label for="jform_browser_and_operating_system_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_DASHBOARD_STATISTICS_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_dashboard_statistics" >
        						<input type="radio" id="jform_dashboard_statistics_yes" name="jform[dashboard_statistics]" value="1" checked="checked" /><label for="jform_dashboard_statistics_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_dashboard_statistics_no" name="jform[dashboard_statistics]" value="0" /><label for="jform_dashboard_statistics_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_ONLINE_VISITORS_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_online_visitors" >
        						<input type="radio" id="jform_online_visitors_yes" name="jform[online_visitors]" value="1" checked="checked" /><label for="jform_online_visitors_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_online_visitors_no" name="jform[online_visitors]" value="0" /><label for="jform_online_visitors_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_INFORMATION_LETTER_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_information_letter" >
        						<input type="radio" id="jform_information_letter_yes" name="jform[information_letter]" value="1" checked="checked" /><label for="jform_information_letter_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_information_letter_no" name="jform[information_letter]" value="0" /><label for="jform_information_letter_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_AFFILIATED_WEBSITE_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_affiliated_website" >
        						<input type="radio" id="jform_affiliated_website_yes" name="jform[affiliated_website]" value="1" checked="checked" /><label for="jform_affiliated_website_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_affiliated_website_no" name="jform[affiliated_website]" value="0" /><label for="jform_affiliated_website_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_CUSTOMER_DETAILS_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_customer_details" >
        						<input type="radio" id="jform_customer_details_yes" name="jform[customer_details]" value="1" checked="checked" /><label for="jform_customer_details_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_customer_details_no" name="jform[customer_details]" value="0" /><label for="jform_customer_details_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_PRODUCT_DETAIL_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_product_detail" >
        						<input type="radio" id="jform_product_detail_yes" name="jform[product_detail]" value="1" checked="checked" /><label for="jform_product_detail_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_product_detail_no" name="jform[product_detail]" value="0" /><label for="jform_product_detail_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_CUSTOMERS_ACCOUNTS_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_customers_accounts" >
        						<input type="radio" id="jform_customers_accounts_yes" name="jform[customers_accounts]" value="1" checked="checked" /><label for="jform_customers_accounts_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_customers_accounts_no" name="jform[customers_accounts]" value="0" /><label for="jform_customers_accounts_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_ORDERS_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_orders" >
        						<input type="radio" id="jform_orders_yes" name="jform[orders]" value="1" checked="checked" /><label for="jform_orders_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_orders_no" name="jform[orders]" value="0" /><label for="jform_orders_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_STORE_SEARCH_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_store_search" >
        						<input type="radio" id="jform_store_search_yes" name="jform[store_search]" value="1" checked="checked" /><label for="jform_store_search_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_store_search_no" name="jform[store_search]" value="0" /><label for="jform_store_search_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_AVAILABLE_QUANTITIES_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_available_quantities" >
        						<input type="radio" id="jform_available_quantities_yes" name="jform[available_quantities]" value="1" checked="checked" /><label for="jform_available_quantities_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_available_quantities_no" name="jform[available_quantities]" value="0" /><label for="jform_available_quantities_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_VISITS_VISITORS_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_visits_visitors" >
        						<input type="radio" id="jform_visits_visitors_yes" name="jform[visits_visitors]" value="1" checked="checked" /><label for="jform_visits_visitors_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_visits_visitors_no" name="jform[visits_visitors]" value="0" /><label for="jform_visits_visitors_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        			<div class="control-group" >
        				<div class="control-label" ><label><?php echo JText::_('COM_JEPROLAB_THEME_CONFIGURATOR_LABEL'); ?></label></div>
        				<div class="controls" >
        					<fieldset class="radio btn-group" id="jform_theme_configurator" >
        						<input type="radio" id="jform_theme_configurator_yes" name="jform[theme_configurator]" value="1" checked="checked" /><label for="jform_theme_configurator_yes" ><?php echo JText::_('COM_JEPROLAB_YES_LABEL'); ?></label>
        						<input type="radio" id="jform_theme_configurator_no" name="jform[theme_configurator]" value="0" /><label for="jform_theme_configurator_no" ><?php echo JText::_('COM_JEPROLAB_NO_LABEL'); ?></label>
        					</fieldset>
        				</div>
        			</div>
        		</div>
                <div style="clear:both" ></div>
            </div>
        	<div style="clear:both" ></div> 
        </div>
        <div style="clear:both" ></div>
    	<input type="hidden" name="task" value="" />
        <input type="hidden" name="" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>    
 </form>