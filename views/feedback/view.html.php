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

class JeprolabFeedbackViewFeedback extends JViewLegacy
{
    protected $pagination;
    public $helper = null;
    public $languages = null;
    public $feedback = null;
    public $customer = null;

    public function renderDetails($tpl = NULL){
        $feedBackModel = new JeproLabFeedBackModelFeedBack();
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        $feeds_back = $feedBackModel->getFeedsBack();
        $this->assignRef('feeds_back', $feeds_back);
        $this->pagination = $feedBackModel->getPagination();
        parent::display($tpl);
    }

    public function renderView($tpl = null){
        if(null == $this->languages){
            $this->languages = JeprolabLanguageModelLanguage::getLanguages();
        }
        $this->helper = new JeprolabHelper();
        $this->loadObject();
        $this->customer = $this->feedback->getCustomer();

        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    private function addToolBar(){
        switch($this->getLayout()){
            default:
                JToolbarHelper::title(JText::_('COM_JEPROLAB_FEEDBACK_TITLE'), 'feedback-jeprolab');
                break;
        }

        JeprolabHelper::sideBarRender('feedback');
    }

    public function loadObject($option = false){
        $app = JFactory::getApplication();
        $feedBackId = $app->input->get('feedback_id');
        $isLoaded = false;
        if($feedBackId&& JeprolabTools::isUnsignedInt($feedBackId)){
            if(!$this->feedback){
                $this->feedback = new JeproLabFeedBackModelFeedBack($feedBackId);
            }

            if(!JeprolabTools::isLoadedObject($this->feedback, 'feedback_id')){
                JError::raiseError(500, JText::_('COM_JEPROSHOP_FEED_NOT_FOUND_MESSAGE'));
                $isLoaded = false;
            }else{
                $isLoaded = true;
            }
        }elseif($option){
            if(!$this->feedback){ $this->feedback = new JeprolabFeedModelFeed(); }
        }else{
            JError::raiseError(500, JText::_('COM_JEPROSHOP_FEED_DOES_NOT_EXIST_MESSAGE'));
            $isLoaded = false;
        }

        //specified
        /*if($isLoaded && JeprolabTools::isLoadedObject($this->feed, 'feed_id')){
            if(JeprolabLabModelLab::getLabContext() == JeprolabLabModelLab::CONTEXT_LAB && JeproshopShopModelShop::isFeaturePublished() && !$this->product->isAssociatedToShop()){
                $this->feed = new JeproshopProductModelProduct((int)$this->product->product_id, false, null, (int)$this->product->default_shop_id);
            }
            $this->product->loadStockData();
        }*/
        return $isLoaded;
    }

    public function createFormSubMenu(){
        $script = '<div class="box_wrapper jeproshop_sub_menu_wrapper"><fieldset class="btn-group"><a href="';
        $script .= JRoute::_('index.php?option=com_jeprolab&view=feed') . '" class="btn" ><i class="icon-" ></i> ';
        $script .= JText::_('COM_JEPROLAB_FEED_LABEL') . '</a><a href="' . JRoute::_('index.php?option=com_jeprolab&view=feedback');
        $script .= '" class="btn btn-success" ><i class="icon-" ></i> ' . JText::_('COM_JEPROLAB_FEEDBACK_LABEL') . '</a></fieldset></div>';
        return $script;
    }

    public function setFeedBackChoice($value){
        $script = '<fieldset class="btn-group" >';
        $script .= '<span class="btn  disabled' . ($value == 'highly_unsatisfy' ? ' btn-danger' : '') . '" >' . JText::_('COM_JEPROLAB_HIGHLY_UNSATISFIED_LABEL') . '</span>';
        $script .= '<span class="btn  disabled' . ($value == 'unsatisfy' ? ' btn-warning' : '') . '" >' . JText::_('COM_JEPROLAB_UNSATISFIED_LABEL') . '</span>';
        $script .= '<span class="btn  disabled' . ($value == 'satisfy' ? ' btn-info' : '') . '" >' . JText::_('COM_JEPROLAB_SATISFIED_LABEL') . '</span>';
        $script .= '<span class="btn  disabled' . ($value == 'highly_satisfy' ? ' btn-success' : '') . '" >' . JText::_('COM_JEPROLAB_HIGHLY_SATISFIED_LABEL') . '</span>';
        $script .= '</fieldset>';
        return $script;
    }

    public function setRadioButton($value){
        $script = '<fieldset class="btn-group" >';
        $script .= '<span class="btn disabled' . ($value == 1 ? ' btn-success' : '') . '" >' . JText::_('COM_JEPROLAB_YES_LABEL') . '</span>';
        $script .= '<span class="btn disabled' . ($value == 0 ? ' btn-danger' : '') . '" >' . JText::_('COM_JEPROLAB_NO_LABEL') . '</span>';
        $script .= '</fieldset>';
        return $script;
    }
}