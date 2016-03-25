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

class JeprolabFeedViewFeed extends JViewLegacy
{
    public $helper = null;
    public $languages = null;
    public $feed = null;

    public function renderDetails($tpl = NULL){
        $feedModel = new JeprolabFeedModelFeed();
        $feeds = $feedModel->getFeedsList();

        $this->assignRef('feeds', $feeds);
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    public function renderAddForm($tpl = null){
        if(null == $this->languages){
            $this->languages = JeprolabLanguageModelLanguage::getLanguages();
        }
        $this->helper = new JeprolabHelper();
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    public function renderEditForm($tpl = null){
        if(null == $this->languages){
            $this->languages = JeprolabLanguageModelLanguage::getLanguages();
        }
        $this->helper = new JeprolabHelper();
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    private function addToolBar(){
        switch($this->getLayout()){
            case 'edit':
                JToolbarHelper::title(JText::_('COM_JEPROLAB_FEED_EDIT_FEED_TITLE'), 'feed-jeprolab');
                JToolbarHelper::apply('update');
                //JToolbarHelper::custom('preview', 'preview_product.png', 'preview_product_1.png', JText::_('COM_JEPROSHOP_PREVIEW_LABEL'), true);
               // JToolbarHelper::custom('sales', 'sales.png', 'sales_1.png', JText::_('COM_JEPROSHOP_SALES_LABEL'), true);
                //JToolbarHelper::deleteList('delete');
                JToolbarHelper::cancel('cancel');
                break;
            case 'add':
                JToolbarHelper::title(JText::_('COM_JEPROLAB_FEED_ADD_FEED_TITLE'), 'feed-jeprolab');
                JToolbarHelper::apply('save');
                JToolbarHelper::cancel('cancel');
                break;
            default:
                JToolbarHelper::title(JText::_('COM_JEPROLAB_HOME_FEED_LIST_TITLE'), 'feed-jeprolab');
                JToolbarHelper::addNew('add');
                break;
        }
        JeprolabHelper::sideBarRender('feed');
    }

    public function loadObject($option = false){
        $app = JFactory::getApplication();
        $feed_id = $app->input->get('feed_id');
        $isLoaded = false;
        if($feed_id && JeprolabTools::isUnsignedInt($feed_id)){
            if(!$this->feed){
                $this->feed = new JeprolabFeedModelFeed($feed_id);
            }

            if(!JeprolabTools::isLoadedObject($this->feed, 'feed_id')){
                JError::raiseError(500, JText::_('COM_JEPROSHOP_FEED_NOT_FOUND_MESSAGE'));
                $isLoaded = false;
            }else{
                $isLoaded = true;
            }
        }elseif($option){
            if(!$this->feed){ $this->feed = new JeprolabFeedModelFeed(); }
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

    private function createFormSubMenu(){

    }
}