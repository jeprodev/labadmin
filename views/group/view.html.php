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

class JeprolabGroupViewGroup extends JViewLegacy
{
    protected $helper = NULL;

    protected $context = null;

    protected $group;

    public function renderDetails($tpl = null){
        if($this->getLayout() != 'modal'){
            $this->addToolBar();
            $this->sideBar = JHtmlSidebar::render();
        }
        $unidentified = new JeprolabGroupModelGroup(JeprolabSettingModelSetting::getValue('unidentified_group'));
        $guest = new JeprolabGroupModelGroup(JeprolabSettingModelSetting::getValue('guest_group'));
        $default = new JeprolabGroupModelGroup(JeprolabSettingModelSetting::getValue('customer_group'));
        /*
        $unidentified_group_information = sprintf(
                /*$this->l('%s - All persons without a customer account or customers that are not logged in.'),
                '<b>'.$unidentified->name[$this->context->language->id].'</b>' * /
        );
        $guest_group_information = sprintf(
                /*$this->l('%s - All persons who placed an order through Guest Checkout.'),
                '<b>'.$guest->name[$this->context->language->id].'</b>' * /
        );
        $default_group_information = sprintf(
                /*$this->l('%s - All persons who created an account on this site.'),
                '<b>'.$default->name[$this->context->language->id].'</b>' */
        //);
        $groupModel = new JeprolabGroupModelGroup();
        $groups = $groupModel->getGroupList();

        $this->assignRef('groups', $groups);
        /*$this->displayInformation($this->l('PrestaShop has three default customer groups:'));
        $this->displayInformation($unidentified_group_information);
        $this->displayInformation($guest_group_information);
        $this->displayInformation($default_group_information); */

        parent::display($tpl);
    }

    public function renderView($tpl = null){
        $app = JFactory::getApplication();
        $this->context = JeprolabContext::getContext();
        $customerList = $this->group->getCustomersList();
        $this->assignRef('customers', $customerList);
        $categoryReductions = $this->formatCategoryDiscountList($this->group->group_id);
        $this->assignRef('category_reductions', $categoryReductions);

        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();

        parent::display($tpl);
    }

    public function formatCategoryDiscountList($group_id){
        return false;
    }

    public function addGroup($tpl = null){
        if($this->getLayout() != 'modal'){
            $this->addToolBar();
            $this->sideBar = JHtmlSidebar::render();
        }
        $this->helper = new JeprolabHelper();
        parent::display($tpl);
    }
    public function renderAddForm($tpl = null){
        $this->helper = new JeprolabHelper();
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();

        parent::display($tpl);
    }

    public function renderEditForm($tpl = null){
        if($this->getLayout() != 'modal'){
            $this->addToolBar();
            $this->sideBar = JHtmlSidebar::render();
        }
        $this->helper = new JeprolabHelper();
        parent::display($tpl);
    }

    private function addToolBar(){
        switch ($this->getLayout()){
            case 'add':
                JToolBarHelper::title(JText::_('COM_JEPROLAB_ADD_GROUP_TITLE'), 'group-jeprolab');
                JToolBarHelper::apply('save');
                JToolBarHelper::cancel('cancel');
                break;
            case 'view' :
                break;
            default:
                JToolBarHelper::title(JText::_('COM_JEPROLAB_GROUPS_LIST_TITLE'), 'group-jeprolab');
                JToolBarHelper::addNew('add');
                break;
        }
        JeprolabHelper::sideBarRender('customer');
    }

    /**
     * Load class object using identifier in $_GET (if possible)
     * otherwise return an empty object, or die
     *
     * @param boolean $opt Return an empty object if load fail
     * @return object|boolean
     */
    public function loadObject($opt = false){
        $app = JFactory::getApplication();
        $group_id = (int)$app->input->get('group_id');
        if ($group_id && JeprolabTools::isUnsignedInt($group_id)){
            if (!$this->group)
                $this->group = new JeprolabGroupModelGroup($group_id);
            if (JeprolabTools::isLoadedObject($this->group, 'group_id'))
                return true;
            // throw exception
            $this->context->controller->has_errors= true;
            JError::raiseError('The object cannot be loaded (or found)');
            return false;
        }
        elseif ($opt){
            if (!$this->group)
                $this->group = new JeprolabGroupModelGroup();
            return $this->group;
        } else {
            $this->context->controller->has_errors = true;
            JError::raiseError('The object cannot be loaded (the identifier is missing or invalid)');
            return false;
        }
    }

    protected function renderSubMenu($current = 'customer'){
        $script = '<div class="box_wrapper jeprolab_sub_menu_wrapper"><fieldset class="btn-group">';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=customer') . '" class="btn jeprolab_sub_menu ' . (($current == 'customer' ) ? 'btn-success' : '') . '" ><i class="icon-customer" ></i> ' . ucfirst(JText::_('COM_JEPROLAB_CUSTOMERS_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=address') . '" class="btn jeprolab_sub_menu ' . (($current == 'address' ) ? 'btn-success' : '') . '" ><i class="icon-address" ></i> '. ucfirst(JText::_('COM_JEPROLAB_ADDRESSES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=group') . '" class="btn jeprolab_sub_menu ' . (($current == 'group' ) ? 'btn-success' : '') . '" ><i class="icon-group" ></i> ' . ucfirst(JText::_('COM_JEPROLAB_GROUPS_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=cart') . '" class="btn jeprolab_sub_menu ' . (($current == 'cart' ) ? 'btn-success' : '') . '" ><i class="icon-cart" ></i> ' . ucfirst(JText::_('COM_JEPROLAB_SHOPPING_CARTS_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=customer&task=threads') . '" class="btn jeprolab_sub_menu ' . (($current == 'threads' ) ? 'btn-success' : '') . '" ><i class="icon-thread" ></i> ' .  ucfirst(JText::_('COM_JEPROLAB_CUSTOMER_THREADS_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=contact') . '" class="btn jeprolab_sub_menu ' . (($current == 'contact' ) ? 'btn-success' : '') . '" ><i class="icon-contact" ></i> ' . ucfirst(JText::_('COM_JEPROLAB_CONTACTS_LABEL')) . '</a>';
        $script .= '</fieldset></div>';
        return $script;
    }
}