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

class JeprolabCategoryViewCategory extends JViewLegacy
{
    protected $category;

    public function renderDetails($tpl = null){
        $app = JFactory::getApplication();
        $category_id = $app->input->get('category_id');

        if(!isset($this->context) || empty($this->context)){ $this->context = JeprolabContext::getContext(); }
        if(!JeprolabLabModelLab::isFeaturePublished() && count(JeprolabCategoryModelCategory::getCategoriesWithoutParent()) > 1 && $category_id){
            $categories_tree = array(get_object_vars($this->context->controller->category->getTopCategory()));
        }else{
            $categories_tree = $this->context->controller->category->getParentsCategories();
            $end = end($categories_tree);
            if(isset($categories_tree) && !JeprolabLabModelLab::isFeaturePublished() && $end->parent_id != 0){
                $categories_tree = array_merge($categories_tree, array(get_object_vars($this->context->controller->category->getTopCategory())));
            }
        }

        $count_categories_without_parent = count(JeprolabCategoryModelCategory::getCategoriesWithoutParent());

        if(empty($categories_tree) && ($this->context->controller->category->category_id != 1 || $category_id ) && (JeprolabLabModelLab::getLabContext() == JeprolabLabModelLab::CONTEXT_LAB && !JeprolabLabModelLab::isFeaturePublished() && $count_categories_without_parent > 1)){
            $categories_tree = array(array('name' => $this->context->controller->category->name[$this->context->language->lang_id]));
        }
        $categories_tree = array_reverse($categories_tree);

        $this->assignRef('categories_tree', $categories_tree);
        $this->assignRef('categories_tree_current_id', $this->context->controller->category->category_id);

        $categoryModel = new JeprolabCategoryModelCategory();
        $categories = $categoryModel->getCategoriesList();
        $pagination = $categoryModel->getPagination();
        $this->assignRef('pagination', $pagination);
        $this->assignRef('categories', $categories);
        $this->setLayout('default');
        $this->setLayout('default');
        $this->addToolBar();

        parent::display($tpl); 
    }

    public function renderAddForm($tpl = null){
        if(!isset($this->context)){ $this->context = JeprolabContext::getContext(); }
        $categories_tree = new JeprolabCategoriesTree('jform_categories_tree', JText::_('COM_JEPROLAB_CATEGORIES_LABEL'), null, $this->context->language->lang_id);
        $categories_tree->setTreeTemplate('associated_categories')->setUseCheckBox(true)->setInputName('parent_id');
        $categories = $categories_tree->render();
        $this->assignRef('categories_tree', $categories);
        $groups = JeprolabGroupModelGroup::getGroups($this->context->language->lang_id);

        $helper = new JeprolabHelper();
        $this->assignRef('helper', $helper);
        $this->assignRef('groups', $groups);

        $unidentified = new JeprolabGroupModelGroup(JeprolabSettingModelSetting::getValue('unidentified_group'));
        $guest = new JeprolabGroupModelGroup(JeprolabSettingModelSetting::getValue('guest_group'));
        $default = new JeprolabGroupModelGroup(JeprolabSettingModelSetting::getValue('customer_group'));

        $unidentified_group_information = '<b>' . $unidentified->name[$this->context->language->lang_id] . '</b> ' . JText::_('COM_JEPROLAB_ALL_PEOPLE_WITHOUT_A_VALID_CUSTOMER_ACCOUNT_MESSAGE');
        $guest_group_information = '<b>' . $guest->name[$this->context->language->lang_id] . '</b> ' . JText::_('COM_JEPROLAB_CUSTOMER_WHO_PLACED_AN_ORDER_WITH_THE_GUEST_CHECKOUT_MESSAGE');
        $default_group_information = '<b>' . $default->name[$this->context->language->lang_id] . '</b> ' . JText::_('COM_JEPROLAB_ALL_PEOPLE_WHO_HAVE_CREATED_AN_CREATED_AN_ACCOUNT_ON_THIS_SITE_MESSAGE');


        $this->assignRef('unidentified_group_information', $unidentified_group_information);
        $this->assignRef('guest_group_information', $guest_group_information);
        $this->assignRef('default_group_information', $default_group_information);

        $this->addToolBar();
        $this->sideBar = JHtmlSideBar::render();

        parent::display($tpl);
    }

    public function renderEditForm($tpl = null){
        $this->loadObject(true);
        $app = JFactory::getApplication();

        if(!isset($this->context)){ $this->context = JeprolabContext::getContext(); }

        $lab_id = JeprolabContext::getContext()->lab->lab_id;
        $selected_categories = array((isset($this->context->controller->category->parent_id) && $this->context->controller->category->isParentCategoryAvailable($lab_id)) ? (int)$this->context->controller->category->parent_id : $app->input->get('parent_id', JeprolabCategoryModelCategory::getRootCategory()->category_id));

        $unidentified = new JeprolabGroupModelGroup(JeprolabSettingModelSetting::getValue('unidentified_group'));
        $guest = new JeprolabGroupModelGroup(JeprolabSettingModelSetting::getValue('guest_group'));
        $default = new JeprolabGroupModelGroup(JeprolabSettingModelSetting::getValue('customer_group'));

        $unidentified_group_information = '<b>' . $unidentified->name[$this->context->language->lang_id] . '</b> ' . JText::_('COM_JEPROLAB_ALL_PEOPLE_WITHOUT_A_VALID_CUSTOMER_ACCOUNT_MESSAGE');
        $guest_group_information = '<b>' . $guest->name[$this->context->language->lang_id] . '</b> ' . JText::_('COM_JEPROLAB_CUSTOMER_WHO_PLACED_AN_ORDER_WITH_THE_GUEST_CHECKOUT_MESSAGE');
        $default_group_information = '<b>' . $default->name[$this->context->language->lang_id] . '</b> ' . JText::_('COM_JEPROLAB_ALL_PEOPLE_WHO_HAVE_CREATED_AN_CREATED_AN_ACCOUNT_ON_THIS_SITE_MESSAGE') ;

        $this->assignRef('unidentified_group_information', $unidentified_group_information);
        $this->assignRef('guest_group_information', $guest_group_information);
        $this->assignRef('default_group_information', $default_group_information);

        $image = COM_JEPROLAB_CATEGORY_IMAGE_DIR . $this->context->controller->category->category_id . '.jpg';
        $image_url = JeprolabImageManager::thumbnail($image, 'category_' . $this->context->controller->category->category_id . '.jpg' , 350, 'jpg', true, true);
        $imageSize = file_exists($image) ? filesize($image)/1000 : false;

        $shared_category = JeprolabTools::isLoadedObject($this->context->controller->category, 'category_id') && $this->context->controller->category->hasMultilabEntries();
        $this->assignRef('shared_category', $shared_category);
        $allow_accented_chars_url = (int)JeprolabSettingModelSetting::getValue('allow_accented_chars_url');
        $this->assignRef('allow_accented_chars_url', $allow_accented_chars_url);
        //$this->assignRef('selected_categories', $selected_categories);

        $categories_tree = new JeprolabCategoriesTree('jform_categories_tree', JText::_('COM_JEPROLAB_CATEGORIES_LABEL'), null, $this->context->language->lang_id);
        $categories_tree->setTreeTemplate('associated_categories')->setSelectedCategories($selected_categories)->setUseCheckBox(true)->setInputName('parent_id');
        $categories_data = $categories_tree->render();
        $this->assignRef('categories_tree', $categories_data);

        $image = JeprolabImageManager::thumbnail(COM_JEPROLAB_CATEGORY_IMAGE_DIR . '/' . $this->context->controller->category->category_id . '.jpg', 'category_' . (int)$this->context->controller->category->category_id . '.jpg', 350, 'jpg', true);
        $this->assignRef('image', ($image ? $image : false));
        $size =  $image ? filesize(COM_JEPROLAB_CATEGORY_IMAGE_DIR . '/' . $this->context->controller->category->category_id . 'jpg') / 1000 : false;
        $this->assignRef('size', $size);

        $category_group_ids = $this->context->controller->category->getGroups();

        $groups = JeprolabGroupModelGroup::getGroups($this->context->language->lang_id);

        //if empty $carrier_groups_ids : object creation : we set the default groups
        if(empty($category_group_ids)){
            $preSelected = array(JeprolabSettingModelSetting::getValue('unidentified_group'), JeprolabSettingModelSetting::getValue('guest_group'), JeprolabSettingModelSetting::getValue('customer_group'));
            $category_group_ids = array_merge($category_group_ids, $preSelected);
        }

        foreach($groups as $group){
            $groupBox = $app->input->get('group_box_' . $group->group_id, (in_array($group->group_id, $category_group_ids)));
            $this->assignRef('group_box_' . $group->group_id, $groupBox);
        }
        $is_root_category = (bool)$app->input->get('is_root_category');
        $this->assignRef('is_root_category', $is_root_category);

        $helper = new JeprolabHelper();
        $this->assignRef('helper', $helper);
        $this->assignRef('groups', $groups);

        $this->addToolBar();
        $this->sideBar = JHtmlSideBar::render();

        parent::display($tpl);
    }

    public function  renderView($tpl = null){
        $this->renderDetails($tpl);
    }

    private function addToolBar(){
        switch ($this->getLayout()){
            case 'add':
                JToolBarHelper::title(JText::_('COM_JEPROLAB_ADD_NEW_CATEGORY_TITLE'), 'jeprolab-category');
                JToolBarHelper::apply('save');
                JToolBarHelper::cancel('cancel');
                break;
            case 'edit' :
                JToolBarHelper::title(JText::_('COM_JEPROLAB_EDIT_CATEGORY_TITLE'), 'jeprolab-category');
                JToolBarHelper::apply('update', JText::_('COM_JEPROLAB_UPDATE_LABEL'));
                break;
            default:
                JToolBarHelper::title(JText::_('COM_JEPROLAB_CATEGORIES_LIST_TITLE'), 'jeprolab-category');
                JToolBarHelper::addNew('add');
                break;
        }
        JeprolabHelper::sideBarRender('catalog');
        $this->sideBar = JHtmlSideBar::render();
    }

    protected function createFormSubMenu()
    {
       $script = '<div class="box_wrapper jeprolab_sub_menu_wrapper"><fieldset class="btn-group">';
       $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=analyze') . '" class="btn jeprolab_sub_menu" ><i class="icon-analyze" ></i> ' . JText::_('COM_JEPROLAB_ANALYSES_LABEL') . '</a>';
       $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=category') . '" class="btn jeprolab_sub_menu btn-success" ><i class="icon-" ></i> ' . JText::_('COM_JEPROLAB_CATEGORIES_LABEL') . '</a>';
       $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=attachment') . '" class="btn jeprolab_sub_menu" ><i class="icon-attachment" ></i> ' . JText::_('COM_JEPROLAB_ATTACHMENTS_LABEL') . '</a>';
       $script .= '</fieldset></div>';

       return $script;
    }

    public function loadObject($option = false){
        $app = JFactory::getApplication();
        $category_id = $app->input->get('category_id');
        if(!isset($this->context) || $this->context == null){ $this->context = JeprolabContext::getContext(); }
        $isLoaded = false;
        if($category_id && JeprolabTools::isUnsignedInt($category_id)){
            if(!$this->context->controller->category){
                $this->context->controller->category = new JeprolabCategoryModelCategory($category_id);
            }

            if(!JeprolabTools::isLoadedObject($this->context->controller->category, 'category_id')){
                JError::raiseError(500, JText::_('COM_JEPROLAB_CATEGORY_NOT_FOUND_MESSAGE'));
                $isLoaded = false;
            }else{
                $isLoaded = true;
            }
        }elseif($option){
            if(!$this->context->controller->category){
                $this->context->controller->category = new JeprolabCategoryModelCategory();
            }
        }else{
            JError::raiseError(500, JText::_('COM_JEPROLAB_CATEGORY_DOES_NOT_EXIST_MESSAGE'));
            $isLoaded = false;
        }
        return $isLoaded;
    }
}