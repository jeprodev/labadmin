<?php
/**
 * @version         1.0.3
 * @package         components
 * @sub package      com_jeprolab
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

class JeprolabCategoryModelCategory extends JModelLegacy
{
    public $category_id;

    public $lang_id;

    public $lab_id;

    public $name;

    public $published = true;

    public $position;

    public $description;

    public $parent_id;

    public $depth_level;

    public $n_left;

    public $n_right;

    public $link_rewrite;

    public $meta_title;
    public $meta_keywords;
    public $meta_description;

    public $date_add;
    public $date_upd;

    public $lab_list_ids;

    public $is_root_category;

    public $default_lab_id;

    public $groupBox;

    private $pagination;

    public $image_id = 'default';
    public $image_dir = '';

    public $multiLang = true;
    public $multiLangLab = true;

    protected $deleted_category = FALSE;

    protected static $_links = array();

    public function __construct($category_id = NULL, $lang_id = NULL, $lab_id = NULL) {
        
        if($lang_id !== null){
            $this->lang_id = (JeprolabLanguageModelLanguage::getLanguage($lang_id) !== FALSE) ? $lang_id : JeprolabSettingModelSetting::getValue('default_lang');
        }

        if($lab_id && $this->isMultiLab()){
            $this->lab_id = (int)$lab_id;
            $this->getLabFromContext = FALSE;
        }

        if($this->isMultiLab() && !$this->lab_id){
            $this->lab_id = JeprolabContext::getContext()->lab->lab_id;
        }
        $db = JFactory::getDBO();

        if($category_id){
            /** load category from data base if id provided **/
            $cache_id = 'jeprolab_model_category_'. (int)$category_id . '_' . $lang_id . '_' . $lab_id;
            if(!JeprolabCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_category') . " AS category ";
                $where = "";
                if($lang_id){
                    $query .= " LEFT JOIN " . $db->quoteName('#__jeprolab_category_lang') . " AS category_lang ON (";
                    $query .= "category.category_id = category_lang.category_id AND category_lang.lang_id = " . (int)$lang_id . ")";
                    if($this->lab_id && !empty($this->multiLangLab)){
                        $where .= " AND category_lang.lab_id = " . (int)  $this->lab_id;
                    }
                }
                /** Get Lab information **/
                if(JeprolabLabModelLab::isTableAssociated('category')){
                    $query .= " LEFT JOIN " . $db->quoteName('#__jeprolab_category_lab') . " AS lab ON ( category.";
                    $query .= "category_id = lab.category_id AND lab.lab_id = " . (int)$this->lab_id . ")";
                }
                $query .= " WHERE category.category_id = " . (int)$category_id . $where;

                $db->setQuery($query);
                $category_data = $db->loadObject();
                if($category_data){
                    if(!$lang_id && isset($this->multiLang) && $this->multiLang){
                        $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_category_lang') . " WHERE " . $db->quoteName('category_id') . " = " . (int)$category_id;
                        $query .= (($this->lab_id && $this->isLangMultiLab()) ? " AND " . $db->quoteName('lab_id') . " = " . $this->lab_id : "");

                        $db->setQuery($query);
                        $category_lang_data = $db->loadObjectList();
                        if($category_lang_data){
                            foreach($category_lang_data as $row){
                                foreach ($row as $key => $value){
                                    if(array_key_exists($key, $this) && $key != 'category_id'){
                                        if(!isset($category_data->{$key}) || !is_array($category_data->{$key})){
                                            $category_data->{$key} = array();
                                        }
                                        $category_data->{$key}[$row->lang_id] = $value;
                                    }
                                }
                            }
                        }
                    }
                    JeprolabCache::store($cache_id, $category_data);
                }
            }else{
                $category_data = JeprolabCache::retrieve($cache_id);
            }

            if($category_data){
                $this->category_id = $category_id;
                foreach($category_data as $key =>$value){
                    if(array_key_exists($key, $this)){
                        $this->{$key} = $value;
                    }
                }
            }
        }

        $this->image_id = (file_exists(COM_JEPROLAB_CATEGORY_IMAGE_DIR . (int)  $this->category_id . '.jpg')) ? (int)$this->category_id : FALSE;
        $this->image_dir = COM_JEPROLAB_CATEGORY_IMAGE_DIR;
    }

    public static function getCategories(JeprolabContext $context = NULL, $sql_sort = ''){
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        if(!$context){ $context = JeprolabContext::getContext(); }

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limit_start = $app->getUserStateFromRequest($option. $view. '.limitstart', 'limitstart', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
        $published = $app->getUserStateFromRequest($option. $view. '.published', 'published', 0, 'string');

        $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_category') . " AS category " . JeprolabLabModelLab::addSqlAssociation('category');
        $query .= " LEFT JOIN " . $db->quoteName('#__jeprolab_category_lang') . " AS category_lang ON (category." . $db->quoteName('category_id');
        $query .= " = category_lang." . $db->quoteName('category_id'). JeprolabLabModelLab::addSqlRestrictionOnLang('category_lang') . ") WHERE 1";
        $query .= ($lang_id ? " AND " . $db->quoteName('lang_id') . " = " . (int)$lang_id : "") . ($published ? " AND " . $db->quoteName('published') . "= 1" : "" );
        $query .= (!$lang_id ? " GROUP BY category.category_id " : "") . ($sql_sort ? $sql_sort : " ORDER BY category." . $db->quoteName('depth_level') . " ASC, category_lab." . $db->quoteName('position') . " ASC");


        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function getRootCategory($lang_id = null, JeprolabLabModelLab $lab = NULL){
        $context = JeprolabContext::getContext();
        if(is_null($lang_id)){ $lang_id = $context->language->lang_id; }

        if(!$lab){
            if(JeprolabLabModelLab::isFeaturePublished() && JeprolabLabModelLab::getLabContext() != JeprolabLabModelLab::CONTEXT_LAB ){
                $lab = new JeprolabLabModelLab(JeprolabSettingModelSetting::getValue('default_lab'));
            }else{
                $lab = $context->lab;
            }
        }else{
            return new JeprolabCategoryModelCategory($lab->getCategoryId(), $lang_id);
        }

        $has_more_than_one_root_category = count(JeprolabCategoryModelCategory::getCategoriesWithoutParent()) > 1;
        if (JeprolabLabModelLab::isFeaturePublished() && $has_more_than_one_root_category && JeprolabLabModelLab::getLabContext() != JeprolabLabModelLab::CONTEXT_LAB){
            $category = JeprolabCategoryModelCategory::getTopCategory($lang_id);
        }else{
            $category = new JeprolabCategoryModelCategory($lab->getCategoryId(), $lang_id);
        }
        return $category;
    }

    public static function getNestedCategories($root_category = null, $lang_id = false, $published = true, $groups = null,
                                               $use_lab_restriction = true, $sql_filter = '', $sql_sort = '', $sql_limit = '')
    {
        $db = JFactory::getDBO();

        if (isset($root_category) && !JeprolabTools::isInt($root_category))
            die(Tools::displayError());

        if (!JeprolabTools::isBool($published))
            die(Tools::displayError());

        if(isset($groups) && JeprolabGroupModelGroup::isFeaturePublished() && !is_array($groups)){
            $groups = (array)$groups;
        }

        $cache_id = 'Category::getNestedCategories_'.md5((int)$root_category.(int)$lang_id . '_' .(int)$published . '_' .(int)$published
                .(isset($groups) && JeprolabGroupModelGroup::isFeaturePublished() ? implode('', $groups) : ''));

        if (!JeprolabCache::isStored($cache_id)){
            $query = "SELECT category.*, category_lang.* FROM " . $db->quoteName('#__jeprolab_category') . " AS category ";
            $query .= ($use_lab_restriction ? JeprolabLabModelLab::addSqlAssociation('category') : "") . " LEFT JOIN ";
            $query .= $db->quoteName('#__jeprolab_category_lang') . " AS category_lang ON category." . $db->quoteName('category_id');
            $query .= " = category_lang." . $db->quoteName('category_id') . JeprolabLabModelLab::addSqlRestrictionOnLang('category_lang');
            $selector = " LEFT JOIN " . $db->quoteName('#__jeprolab_category_group') . " AS category_group ON category.";
            $selector .= $db->quoteName('category_id') . " = category_group." . $db->quoteName('category_id');
            $query .= (isset($groups) && JeprolabGroupModelGroup::isFeaturePublished() ? $selector : "");
            $selector = " RIGHT JOIN " . $db->quoteName('#__jeprolab_category') . " AS category_2 ON category_2." . $db->quoteName('category_id');
            $selector .= " = " . (int)$root_category . " AND category." . $db->quoteName('n_left') . " >= category_2." . $db->quoteName('n_left');
            $selector .= " AND category." . $db->quoteName('n_right') . " <= category_2." . $db->quoteName('n_right');
            $query .= (isset($root_category) ? $selector : "") . " WHERE 1 " . $sql_filter . ($lang_id ? " AND " . $db->quoteName('lang_id') . " = " .(int)$lang_id : "");
            $query .= ($published ? " AND category." . $db->quoteName('published') . " = 1" : "") ;

            $query .= (isset($groups) && JeprolabGroupModelGroup::isFeaturePublished() ? " AND category_group." . $db->quoteName('group_id') . " IN (" . implode(',',  $groups). ") " : "");
            $selector = " GROUP BY category." . $db->quoteName('category_id');
            $query .= (!$lang_id || (isset($groups) && JeprolabGroupModelGroup::isFeaturePublished()) ? $selector : "");
            $query .= ($sql_sort != "" ? $sql_sort : " ORDER BY category." . $db->quoteName('depth_level') . " ASC");
            $query .= ($sql_sort == "" && $use_lab_restriction ? ", category_lab." . $db->quoteName('position') . " ASC" : "");
            $query .= ($sql_limit != "" ? $sql_limit : "");

            $db->setQuery($query);
            $result = $db->loadObjectList();

            $categories = array();
            $buff = array();

            if (!isset($root_category)){
                $root_category = JeprolabCategoryModelCategory::getRootCategory()->category_id;
            }

            foreach ($result as $row){
                $current = &$buff[$row->category_id];
                $current = $row;

                if ($row->category_id == $root_category)
                    $categories[$row->category_id] = &$current;
                else
                    $buff[$row->parent_id]->children[$row->category_id] = &$current;
            }

            JeprolabCache::store($cache_id, $categories);
        }

        return JeprolabCache::retrieve($cache_id);
    }

    public static function getRootCategories($lang_id = null, $published = true){
        if (!$lang_id){
            $lang_id = JeprolabContext::getContext()->language->lang_id;
        }

        $db = JFactory::getDBO();
        $query = "SELECT DISTINCT(category." . $db->quoteName('category_id') . "), category_lang.";
        $query .= $db->quoteName('name') . " FROM " . $db->quoteName('#__jeprolab_category') . "AS category";
        $query .= " LEFT JOIN " . $db->quoteName('#__jeprolab_category_lang') . " AS category_lang ON (";
        $query .= " category_lang." . $db->quoteName('category_id') . " = category." . $db->quoteName('category_id');
        $query .= " AND category_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ") WHERE ";
        $query .= $db->quoteName('is_root_category') . " = 1 " .($published ? "AND " . $db->quoteName('published') . " = 1": "");

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function getCategoriesWithoutParent(){
        $cache_id = 'jeprolab_category_get_Categories_Without_parent_'.(int)JeprolabContext::getContext()->language->lang_id;
        if (!JeprolabCache::isStored($cache_id)){
            $db = JFactory::getDBO();

            $query = "SELECT DISTINCT category.* FROM " . $db->quoteName('#__jeprolab_category') . " AS category";
            $query .= "	LEFT JOIN " . $db->quoteName('#__jeprolab_category_lang') . " AS category_lang ON (category.";
            $query .= $db->quoteName('category_id') . " = category_lang." . $db->quoteName('category_id') . " AND category_lang.";
            $query .= $db->quoteName('lang_id') . " = " .(int)JeprolabContext::getContext()->language->lang_id;
            $query .= ") WHERE " . $db->quoteName('depth_level') . " = 1";

            $db->setQuery($query);
            $result = $db->loadObjectList();
            JeprolabCache::store($cache_id, $result);
        }
        return JeprolabCache::retrieve($cache_id);
    }

    /**
     *
     * @param Array $category_ids
     * @param int $lang_id
     * @return Array
     */
    public static function getCategoryInformations($category_ids, $lang_id = null){
        if ($lang_id === null){
            $lang_id = JeprolabContext::getContext()->language->lang_id;
        }

        if (!is_array($category_ids) || !count($category_ids)){ return; }

        $categories = array();

        $db = JFactory::getDBO();

        $query = "SELECT category." . $db->quoteName('category_id') . ", category_lang." . $db->quoteName('name');
        $query .= ", category_lang." . $db->quoteName('link_rewrite') . ", category_lang." . $db->quoteName('lang_id');
        $query .= " FROM " . $db->quoteName('#__jeprolab_category') . " AS category LEFT JOIN ";
        $query .= $db->quoteName('#__jeprolab_category_lang') . " AS category_lang ON (category." ;
        $query .= $db->quoteName('category_id') . " = category_lang." . $db->quoteName('category_id');
        $query .= JeprolabLabModelLab::addSqlRestrictionOnLang('category_lang') . ") " . JeprolabLabModelLab::addSqlAssociation('category');
        $query .= " WHERE category_lang." . $db->quoteName('lang_id') . " = " .(int)$lang_id . " AND category.";
        $query .= $db->quoteName('category_id') . " IN (" . implode(',', array_map('intval', $category_ids)) . ")";

        $db->setQuery($query);
        $results = $db->loadObjectList();

        foreach ($results as $category){
            $categories[$category->category_id] = $category;
        }
        return $categories;
    }

    /**
     * @param $lab_id
     * @return bool
     */
    public function isParentCategoryAvailable($lab_id){
        if(!$lab_id) {
            $lab_id = JeprolabContext::getContext()->lab->lab_id;
        }
        $lab_id = $lab_id ? $lab_id : JeprolabSettingModelSetting::getValue('default_lab');
        $db = JFactory::getDBO();

        $query = "SELECT category." . $db->quoteName('category_id') . " FROM " . $db->quoteName('#__jeprolab_category') . " AS category ";
        $query .= JeprolabLabModelLab::addSqlAssociation('category') . " WHERE category_lab." . $db->quoteName('lab_id') . " = " .(int)$lab_id;
        $query .= " AND category." . $db->quoteName('parent_id') . " = " . (int)$this->parent_id;

        $db->setQuery($query);
        return (bool)$db->loadResult();
    }

    public function getGroups(){
        $cache_id = 'jeprolab_category::getGroups_'.(int)$this->category_id;
        if (!JeprolabCache::isStored($cache_id)){
            $db = JFactory::getDBO();
            $query = "SELECT category_group." . $db->quoteName('group_id') . " FROM " . $db->quoteName('#__jeprolab_category_group');
            $query .= " AS category_group WHERE category_group." . $db->quoteName('category_id') . " = " .(int)$this->category_id;

            $db->setQuery($query);
            $groups = $db->loadObjectList();

            JeprolabCache::store($cache_id, $groups);
        }
        return JeprolabCache::retrieve($cache_id);
    }

    /**
     * Check if current object is associated to a lab
     *
     * @param int $lab_id
     * @return bool
     */
    public function isAssociatedToLab($lab_id = null){
        if ($lab_id === null){
            $lab_id = JeprolabContext::getContext()->lab->lab_id;
        }
        $cache_id = 'jeprolab_category_model_lab_' . (int)$this->category_id . '_' . (int)$lab_id;
        if (!JeprolabCache::isStored($cache_id)){
            $db = JFactory::getDBO();
            $query = "SELECT lab_id FROM " . $db->quoteName('#__jeprolab_category_lab') . " WHERE " . $db->quoteName('category_id') . " = ";
            $query .= (int)$this->category_id . " AND lab_id = " . (int)$lab_id;
            $db->setQuery($query);
            JeprolabCache::store($cache_id, (bool)$db->loadResult());
        }
        return JeprolabCache::retrieve($cache_id);
    }

    /**
     * @static
     * @param null $lang_id
     * @return JeprolabCategoryModelCategory
     */
    public static function getTopCategory($lang_id = null){
        if(is_null($lang_id)){
            $lang_id = (int)JeprolabContext::getContext()->language->lang_id;
        }
        $cache_id = 'jeprolab_category::getTopCategory_'.(int)$lang_id;
        if (!JeprolabCache::isStored($cache_id)){
            $db = JFactory::getDBO();
            $query = "SELECT " . $db->quoteName('category_id') . " FROM " . $db->quoteName('#__jeprolab_category');
            $query .= "	WHERE " . $db->quoteName('parent_id') . " = 0";
            $db->setQuery($query);
            $category_id = (int)$db->loadResult();
            JeprolabCache::store($cache_id, new JeprolabCategoryModelCategory($category_id, $lang_id));
        }
        return JeprolabCache::retrieve($cache_id);
    }


    public static function getLinkRewrite($category_id, $lang_id){
        if (!JeprolabTools::isUnsignedInt($category_id) || !JeprolabTools::isUnsignedInt($lang_id)){
            return false;
        }

        if (!isset(self::$_links[$category_id . '_' . $lang_id])){
            $db = JFactory::getDBO();

            $query = "SELECT category_lang." . $db->quoteName('link_rewrite') . " FROM " . $db->quoteName('#__jeprolab_category_lang');
            $query .= " AS category_lang WHERE " . $db->quoteName('lang_id') . " = " . (int)$lang_id ;
            $query .= JeprolabLabModelLab::addSqlRestrictionOnLang('category_lang') . " AND category_lang.";
            $query .= $db->quoteName('category_id') . " = " .(int)$category_id;
            self::$_links[$category_id . '_' . $lang_id] = $db->loadResult();
        }
        return self::$_links[$category_id . '_' . $lang_id];
    }

    public function getCategoriesList(){
        jimport('joomla.html.pagination');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        $context = JeprolabContext::getContext();

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
        $lab_id = $app->getUserStateFromRequest($option. $view. '.lab_id', 'lab_id', $context->lab->lab_id, 'int');
        $lab_group_id = $app->getUserStateFromRequest($option. $view. '.lab_group_id', 'lab_group_id', $context->lab->lab_group_id, 'int');
        $category_id = $app->getUserStateFromRequest($option. $view. '.category_id', 'category_id', 0, 'int');
        $order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'date_add', 'string');
        $order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC', 'string');
        $published = $app->getUserStateFromRequest($option. $view. '.published', 'published', 0, 'string');

        $count_categories_without_parent = count(JeprolabCategoryModelCategory::getCategoriesWithoutParent());

        $top_category = JeprolabCategoryModelCategory::getTopCategory();
        $parent_id = 0;
        if($category_id){
            $category = new JeprolabCategoryModelCategory($category_id);
            $parent_id = $category->category_id;
        }elseif(!JeprolabLabModelLab::isFeaturePublished() && $count_categories_without_parent > 1){
            $parent_id = $top_category->category_id;
        }elseif(JeprolabLabModelLab::isFeaturePublished() && $count_categories_without_parent == 1){
            $parent_id = JeprolabSettingModelSetting::getValue('root_category');
        }elseif(JeprolabLabModelLab::isFeaturePublished() && $count_categories_without_parent > 1 && JeprolabLabModelLab::getLabContext() != JeprolabLabModelLab::CONTEXT_LAB){
            if(JeprolabSettingModelSetting::getValue('multi_lab_feature_active') && count(JeprolabLabModelLab::getLabs(true, null, true)) == 1){
                $parent_id = $context->lab->category_id;
            }else{
                $parent_id = $top_category->category_id;
            }
        }

        $explicitSelect = true;


        /* Manage default params values */
        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        $join = " LEFT JOIN " . $db->quoteName('#__jeprolab_category_lab') . " AS category_lab ON (category." . $db->quoteName('category_id') . " = category_lab." . $db->quoteName('category_id') . " AND ";
        if (JeprolabLabModelLab::getLabContext() == JeprolabLabModelLab::CONTEXT_LAB){
            $join .= " category_lab.lab_id = " . (int)$context->lab->lab_id . ") ";
        }else{
            $join .= " category_lab.lab_id = category.default_lab_id)" ;
        }

        // we add restriction for lab
        if(JeprolabLabModelLab::getLabContext() == JeprolabLabModelLab::CONTEXT_LAB && JeprolabLabModelLab::isFeaturePublished()){
            $where = " AND category_lab." . $db->quoteName('lab_id') . " = " . (int)JeprolabContext::getContext()->lab->lab_id;
        }
        /* Check params validity */
        if (!JeprolabTools::isOrderBy($order_by) || !JeprolabTools::isOrderWay($order_way)
            || !is_numeric($limitstart) || !is_numeric($limit) || !JeprolabTools::isUnsignedInt($lang_id)){
            echo JError::raiseError(500,('get list params is not valid'));
        }

        /* Cache */
        if (preg_match('/[.!]/', $order_by)){
            $order_by_split = preg_split('/[.!]/', $order_by);
            $order_by = bqSQL($order_by_split[0]).'.`'.bqSQL($order_by_split[1]).'`';
        }elseif ($order_by){
            $order_by = $db->quoteName($db->escape($order_by));
        }

        // Add SQL lab restriction
        $labLinkType = "";
        $select_lab = $join_lab = $where_lab = '';
        /*if ($labLinkType){
            $select_lab = ", lab.lab_name as lab_name ";
            $join_lab = " LEFT JOIN " ._DB_PREFIX_.$this->labLinkType.' lab
                            ON a.id_'.$this->labLinkType.' = lab.id_'.$this->labLinkType;
            $where_lab = JeprolabLabModelLab::addSqlRestriction('1', 'category');
        }*/

        if ($context->controller->multilab_context && JeprolabLabModelLab::isTableAssociated('category')){
            if (JeprolabLabModelLab::getLabContext() != JeprolabLabModelLab::CONTEXT_ALL || !$context->employee->isSuperAdmin()){
                $test_join = !preg_match('/`?'.preg_quote('#__jeprolab_category_lab').'`? *category_lab/', $join);
                if (JeprolabLabModelLab::isFeaturePublished() && $test_join && JeprolabLabModelLab::isTableAssociated('category')){
                    $where .= " AND category.category_id IN ( SELECT category_lab.category_id FROM ";
                    $where .= $db->quoteName('#__jeprolab_category__lab') . " AS category_lab WHERE category_lab.";
                    $where .= "lab_id IN (" . implode(', ', JeprolabLabModelLab::getContextListLabIds()). ") )";
                }
            }
        }

        $select = ", category_lab.position AS position ";
        $tmpTableFilter = "";

        /* Query in order to get results with all fields */
        $lang_join = " LEFT JOIN " . $db->quoteName('#__jeprolab_category_lang') . " AS category_lang ON (";
        $lang_join .= "category_lang." . $db->quoteName('category_id') . " = category." . $db->quoteName('category_id');
        $lang_join .= " AND category_lang." . $db->quoteName('lang_id') . " = " .(int)$lang_id;
        if ($context->lab->lab_id){
            if (!JeprolabLabModelLab::isFeaturePublished()){
                $lang_join .= " AND category_lang." . $db->quoteName('lab_id') . " = 1";
            }elseif (JeprolabLabModelLab::getLabContext() == JeprolabLabModelLab::CONTEXT_LAB){
                $lang_join .=  " AND category_lang." . $db->quoteName('lab_id') . " = " .(int)$context->lab->lab_id;
            }else{
                $lang_join .=  " AND category_lang." . $db->quoteName('lab_id') . " = category.default_lab_id";
            }
        }
        $lang_join .= ") ";


        $having_clause = '';
        if (isset($this->_filterHaving) || isset($this->_having)){
            $having_clause = ' HAVING ';
            if (isset($this->_filterHaving)){
                $having_clause .= ltrim($this->_filterHaving, ' AND ');
            }
            if(isset($this->_having)){
                $having_clause .= $this->_having.' ';
            }
        }

        do{
            $query = "SELECT SQL_CALC_FOUND_ROWS " .($tmpTableFilter ? " * FROM (SELECT " : "");
            if ($explicitSelect){
                $query .= "category." . $db->quoteName('category_id') . ", category_lang." . $db->quoteName('name') . ", category_lang." . $db->quoteName('description');
                $query .= " , category." . $db->quoteName('position') ." AS category_position, " . $db->quoteName('published');
            }else{
                $query .= ($lang_id ? " category_lang.*," : "") . " category.*";
            }
            $query .= (isset($select) ? rtrim($select, ", ") : "") . $select_lab . " FROM " . $db->quoteName('#__jeprolab_category') . " AS category " . $lang_join . (isset($join) ? $join . " " : "") ;
            $query .= $join_lab . " WHERE 1 ". (isset($where) ? $where . " " : "") . ($this->deleted_category ? " AND category." .$db->quoteName('deleted') . " = 0 " : "") .  "AND " . $db->quoteName('parent_id');
            $query .= "= " . (int)$parent_id . $where_lab .(isset($group) ? $group . " " : "") . $having_clause . " ORDER BY " . ((str_replace('`', '', $order_by) == 'category_id') ? "category." : ""). " category.";
            $query .= $order_by . " " . $db->escape($order_way) . ($tmpTableFilter ? ") tmpTable WHERE 1" . $tmpTableFilter : "");

            $db->setQuery($query);
            $total = count($db->loadObjectList());
            $query .= (($use_limit === true) ? " LIMIT " . (int)$limitstart.", ".(int)$limit : "" );

            $db->setQuery($query);
            $categories = $db->loadObjectList();

            if ($use_limit === true){
                $limitstart = (int)$limitstart - (int)$limit;
                if ($limitstart < 0){ break; }
            }else{ break; }
        } while (empty($categories));

        if(!empty($categories)){
            foreach($categories as $item){
                $category_tree = JeprolabCategoryModelCategory::getChildren((int)$item->category_id, $context->language->lang_id);
                $item->set_view = (count($category_tree) ? 1 : 0);
            }
        }

        $this->pagination = new JPagination($total, $limitstart, $limit);
        return $categories;
    }

    public function isMultiLab(){
        return JeprolabLabModelLab::isTableAssociated('category') || !empty($this->multiLangLab);
    }

    public function isLangMultilab(){
        return !empty($this->multiLang) && !empty($this->multiLangLab);
    }

    public function getPagination(){
        return $this->pagination;
    }

    /**
     * Get Each parent category of this category until the root category
     *
     * @param integer $lang_id Language ID
     * @return array Corresponding categories
     */
    public function getParentsCategories($lang_id = null) {
        $context = JeprolabContext::getContext()->cloneContext();
        $context->lab = clone($context->lab);
        $category_id = JFactory::getApplication()->input->get('category_id');

        if (is_null($lang_id)){ $lang_id = $context->language->lang_id; }

        $categories = null;
        $current_id = $this->category_id;
        if (count(JeprolabCategoryModelCategory::getCategoriesWithoutParent()) > 1 && JeprolabSettingModelSetting::getValue('multi_lab_feature_active') && count(JeprolabLabModelLab::getLabs(true, null, true)) != 1) {
            $context->lab->category_id = JeprolabCategoryModelCategory::getTopCategory()->category_id;
        }elseif (!$context->lab->lab_id) {
            $context->lab = new JeprolabLabModelLab(JeprolabSettingModelSetting::getValue('default_lab'));
        }

        $lab_id = $context->lab->lab_id;
        $db = JFactory::getDBO();
        while (true){
            $query = "SELECT category.*, category_lang.* FROM " . $db->quoteName('#__jeprolab_category') . " AS category LEFT JOIN " . $db->quoteName('#__jeprolab_category_lang') . " AS category_lang ON (category.";
            $query .= $db->quoteName('category_id') . " = category_lang." . $db->quoteName('category_id') . " AND " . $db->quoteName('lang_id') . " = " . (int)$lang_id . JeprolabLabModelLab::addSqlRestrictionOnLang('category_lang') . ")";
            if (JeprolabLabModelLab::isFeaturePublished() && JeprolabLabModelLab::getLabContext() == JeprolabLabModelLab::CONTEXT_LAB) {
                $query .= " LEFT JOIN " . $db->quoteName('#__jeprolab_category_lab') . " AS category_lab ON (category." . $db->quoteName('category_id') . " = category_lab.";
                $query .= $db->quoteName('category_id') . " AND category_lab." . $db->quoteName('lab_id') . " = " . (int)$lab_id . ")";
            }
            $query .= " WHERE category." . $db->quoteName('category_id') . " = " .(int)$current_id;
            if (JeprolabLabModelLab::isFeaturePublished() && JeprolabLabModelLab::getLabContext() == JeprolabLabModelLab::CONTEXT_LAB) {
                $query .= " AND category_lab." . $db->quoteName('lab_id') . " = "  . (int)$context->lab->lab_id;
            }
            $root_category = JeprolabCategoryModelCategory::getRootCategory();
            if (JeprolabLabModelLab::isFeaturePublished() && JeprolabLabModelLab::getLabContext() == JeprolabLabModelLab::CONTEXT_LAB && (!$category_id || (int)$category_id == (int)$root_category->category_id || (int)$root_category->category_id == (int)$context->lab->category_id)) {
                $query .= " AND category." . $db->quoteName('parent_id') . " != 0";
            }

            $db->setQuery($query);
            $result = $db->loadObject();

            if ($result)
                $categories[] = $result;
            elseif (!$categories)
                $categories = array();
            if (!$result || ($result->category_id == $context->lab->category_id))
                return $categories;
            $current_id = $result->parent_id;
        }
    }

    /**
     *
     * @param int $parent_id
     * @param int $lang_id
     * @param bool $published
     * @return array
     */
    public static function getChildren($parent_id, $lang_id, $published = true, $lab_id = false){
        if (!JeprolabTools::isBool($published)){
            die(JError::raiseError(JText::_('COM_JEPROLAB_WRONG_PARAMETER_SUPPLIED_MESSAGE')));
        }

        $cache_id = 'jeprolab_category_get_children_'.(int)$parent_id.'_'.(int)$lang_id.'_'.(bool)$published.'_'.(int)$lab_id;
        if (!JeprolabCache::isStored($cache_id)){
            $db = JFactory::getDBO();
            $query = "SELECT category." . $db->quoteName('category_id') . ", category_lang." . $db->quoteName('name') . ", category_lang." . $db->quoteName('link_rewrite') . ", category_lab." . $db->quoteName('lab_id');
            $query .= " FROM " . $db->quoteName('#__jeprolab_category') . " AS category LEFT JOIN " . $db->quoteName('#__jeprolab_category_lang') . " AS category_lang ON (category." . $db->quoteName('category_id') ;
            $query .= " = category_lang." . $db->quoteName('category_id') . JeprolabLabModelLab::addSqlRestrictionOnLang('category_lang').") " . JeprolabLabModelLab::addSqlAssociation('category') . " WHERE ";
            $query .= $db->quoteName('lang_id') . " = " . (int)$lang_id . " AND category." . $db->quoteName('parent_id') . " = " .(int)$parent_id . ($published ? " AND " . $db->quoteName('published') . " = 1" : "");
            $query .= " GROUP BY category." . $db->quoteName('category_id') . "	ORDER BY category_lab." . $db->quoteName('position') . " ASC ";

            $db->setQuery($query);
            $result = $db->loadObjectList();
            JeprolabCache::store($cache_id, $result);
        }
        return JeprolabCache::retrieve($cache_id);
    }

    /**
     * Check if there is more than one entries in associated lab table for current entity
     *
     * @since 1.5.0
     * @return bool
     */
    public function hasMultilabEntries() {
        if (!JeprolabLabModelLab::isTableAssociated('category') || !JeprolabLabModelLab::isFeaturePublished())
            return false;
        $db = JFactory::getDBO();

        $query = "SELECT COUNT(*) FROM " . $db->quoteName('#__jeprolab_category_lab') . " WHERE " . $db->quoteName('category_id') . " = " .(int)$this->category_id;
        $db->setQuery($query);
        return (bool)$db->loadResult();
    }

    /**
     * Get the depth level for the category
     * @return int Depth level
     * @throws JException
     */
    public function calculateDepthLevel(){
        /* Root category */
        if (!$this->parent_id)
            return 0;

        $parent_category = new JeprolabCategoryModelCategory((int)$this->parent_id);
        if (!JeprolabTools::isLoadedObject($parent_category, 'category_id'))
            throw new JException('Parent category does not exist');
        return $parent_category->depth_level + 1;
    }

    public function saveCategory(){
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();
        $input = JRequest::get('post');
        $category_data = $input['jform'];
        $languages = JeprolabLanguageModelLanguage::getLanguages();
        $category_id = $app->input->get('category_id');
        $context = JeprolabContext::getContext();

        $view = $app->input->get('view');
        $parent_id = (int)$category_data['parent_id'];

        $this->date_add = date('Y-m-d H:i:s');
        $this->date_upd = date('Y-m-d H:i:s');

        //if true, we are in a root category creation
        if(!$parent_id){
            $this->is_root_category = $category_data['depth_level'] = 1;
            $parent_id = (int)JeprolabSettingModelSetting::getValue('root_category');
        }

        if($category_id) {
            if ($category_id != $parent_id) {
                if (!JeprolabCategoryModelCategory::checkBeforeMove($category_id, $parent_id)) {
                    $context->controller->has_errors = true;
                    JError::raiseError(500, JText::_('COM_JEPROLAB_THE_CATEGORY_CANNOT_BE_MOVED_HERE_MESSAGE'));
                }
            }else{
                $context->controller->has_errors = true;
                JError::raiseError(500, JText::_('COM_JEPROLAB_THE_CATEGORY_CANNOT_BE_A_PARENT_OF_ITSELF_MESSAGE'));
            }
        }

        if(!isset($view) || $view != 'category'){
            $app->input->set('category_id', null);
            $app->redirect('index.php?option=com_jeprolab&view=category');
            return false;
        }

        if(!$context->controller->has_errors){
            $published = (int)$category_data['published'];

            if(!isset($depth_level_)){
                $depth_level = $this->calculateDepthLevel();
            }

            $rootCategoryId = (int)JeprolabSettingModelSetting::getValue('root_category');
            if($this->is_root_category && $rootCategoryId){
                $parent_id = $rootCategoryId;
            }

            $lab_list_ids = null;
            if(JeprolabLabModelLab::isTableAssociated('category')){
                $lab_list_ids = JeprolabLabModelLab::getContextListLabIds();
                if(count($this->lab_list_ids) > 0){ $lab_list_ids = $this->lab_list_ids; }
            }

            if(JeprolabLabModelLab::checkDefaultLabId('category')){
                $this->default_lab_id = min($lab_list_ids);
            }

            $default_lab_id = JeprolabContext::getContext()->lab->lab_id;
            $position = 0;
            if(JeprolabLabModelLab::checkDefaultLabId('category')){
                $default_lab_id = min($lab_list_ids);
            }

            $result =  true;
            $query =  "INSERT INTO " . $db->quoteName('#__jeprolab_category') . "(" . $db->quoteName('parent_id') . ", " . $db->quoteName('depth_level');
            $query .= ", " . $db->quoteName('default_lab_id') . ", " . $db->quoteName('published') . ", " . $db->quoteName('date_add') . ", " . $db->quoteName('date_upd') . ", ";
            $query .= $db->quoteName('is_root_category') . ") VALUES (" . (int)$parent_id . ", " . (int)$depth_level . ", " . (int)$default_lab_id . ", " . (int)$published;
            $query .= ", " .$db->quote($this->date_add) . ", " . $db->quote($this->date_upd) . ", " . (int)$this->is_root_category .")";

            $db->setQuery($query);
            $result &= $db->query();
            if($result){
                $this->category_id = $db->insertid();
                foreach($lab_list_ids as $lab_id){
                    $query = "INSERT INTO " .  $db->quoteName('#__jeprolab_category_lab') . "(" . $db->quoteName('category_id') . ", " . $db->quoteName('lab_id');
                    $query .= ", " . $db->quoteName('position') . ") VALUES (" . (int)$this->category_id . ", " . (int)$lab_id . ", " . (int)$position . ")";

                    $db->setQuery($query);
                    $result &= $db->query();
                    foreach($languages as $language){
                        $query = "INSERT INTO " .  $db->quoteName('#__jeprolab_category_lang') . "(" . $db->quoteName('category_id') . ", " . $db->quoteName('lab_id');
                        $query .= ", " . $db->quoteName('lang_id') . ", " . $db->quoteName('name') . ", " . $db->quoteName('description') . ", " . $db->quoteName('link_rewrite');
                        $query .= ", " . $db->quoteName('meta_title') . ", " . $db->quoteName('meta_keywords') . ", " . $db->quoteName('meta_description') . ") VALUES (";
                        $query .= (int)$this->category_id . ", " . (int)$lab_id . ", " . (int)$language->lang_id . ", " . $db->quote($category_data['name_' . (int)$language->lang_id]);
                        $query .= ", " . $db->quote($category_data['description_' . $language->lang_id]) . ", " . $db->quote($category_data['link_rewrite_' . $language->lang_id]) . ", ";
                        $query .= $db->quote($category_data['meta_title_' . $language->lang_id]) . ", " . $db->quote($category_data['meta_keywords_' . $language->lang_id]) . ", ";
                        $query .= $db->quote($category_data['meta_description_' . $language->lang_id]) . ")";

                        $db->setQuery($query);
                        $result &= $db->query();
                    }

                }
            }
        }

        if($category_data['check_box_lab_associated_category']){
            foreach($category_data['check_box_lab_associated_category'] as $lab_id => $value){
                $position = JeprolabCategoryModelCategory::getLastPosition((int)$this->parent_id, $lab_id);
                $this->addPosition($position, $lab_id);
            }
        }else{
            foreach(JeprolabLabModelLab::getLabs() as $lab){
                $position = JeprolabCategoryModelCategory::getLastPosition((int)$this->parent_id, $lab->lab_id);
                if(!$position){
                    $position = 1;
                }
                $this->addPosition($position, $lab->lab_id);
            }
        }

        if (!isset($this->doNotRegenerateNTree) || !$this->doNotRegenerateNTree) {
            JeprolabCategoryModelCategory::regenerateEntireNestedTree();
        }
        $this->updateGroup($this->groupBox);

        //if we create a new root category you have to associate to a lab before to add sub categories in. So we redirect to AdminCategories listing
        if($this->is_root_category){
            $link = JRoute::_('index.php?option=com_jeprolab&view=category&category_id=' . (int)JeprolabCategoryModelCategory::getTopCategory()->category_id . JeprolabTools::getCategoryToken());
            $message = '';
            $app->redirect($link, $message);
        }
    }

    /**
     * Re-calculate the values of all branches of the nested tree
     */
    public static function regenerateEntireNestedTree(){
        $db = JFactory::getDBO();
        $lab_id = JeprolabContext::getContext()->lab->lab_id;
        $lab_id = $lab_id ? $lab_id: JeprolabSettingModelSetting::getValue('default_lab');

        $query = "SELECT category." . $db->quoteName('category_id') . ", category." . $db->quoteName('parent_id') . " FROM ";
        $query .= $db->quoteName('#__jeprolab_category') . " AS category LEFT JOIN " . $db->quoteName('#__jeprolab_category_lab');
        $query .= " AS category_lab ON (category." . $db->quoteName('category_id') . " = category_lab." . $db->quoteName('category_id');
        $query .= " AND category_lab." . $db->quoteName('lab_id') . " = " .(int)$lab_id . ") ORDER BY category.";
        $query .= $db->quoteName('parent_id') . ", category_lab." . $db->quoteName('position') . " ASC";

        $db->setQuery($query);
        $categories = $db->loadObjectList();

        $categories_array = array();
        foreach ($categories as $category) {
            $categories_array[$category->parent_id]['subcategories'][] = $category->category_id;
        }
        $n = 1;

        if (isset($categories_array[0]) && $categories_array[0]['subcategories']) {
            JeprolabCategoryModelCategory::subTree($categories_array, $categories_array[0]['subcategories'][0], $n);
        }
    }

    /** this function return the number of category + 1 having $id_category_parent as parent.
     *
     *
     * @param int $category_parent_id the parent category
     * @param int $lab_id
     * @return int
     */
    public static function getLastPosition($category_parent_id, $lab_id){
        $db = JFactory::getDBO();

        $query = "SELECT MAX(category_lab." . $db->quoteName('position') . ") FROM " . $db->quoteName('#__jeprolab_category') . " AS category LEFT JOIN ";
        $query .= $db->quoteName('#__jeprolab_category_lab') . " AS category_lab ON (category." . $db->quoteName('category_id') . " = category_lab.";
        $query .= $db->quoteName('category_id') . " AND category_lab." . $db->quoteName('lab_id') . " = " .(int)$lab_id . ") WHERE category.";
        $query .= $db->quoteName('parent_id') . " = " .(int)$category_parent_id;

        $db->setQuery($query);
        return (1 + (int)$db->loadResult());
    }


    /**
     * Updates level_depth for all children of the given id_category
     *
     * @param integer $category_id parent category
     * @throws JException
     */
    public function recalculateDepthLevel($category_id){
        $db = JFactory::getDBO();
        if (!is_numeric($category_id))
            throw new JException('category_id is not numeric');

        /* Gets all children */
        $query = "SELECT " . $db->quoteName('category_id') . ", "  . $db->quoteName('parent_id') . ", " . $db->quoteName('depth_level') . " FROM ";
        $query .= $db->quoteName('#__jeprolab_category') . " WHERE parent_id = " . (int)$category_id ;

        $db->setQuery($query);
        $categories = $db->loadObjectList();

        /* Gets level_depth */
        $query = "SELECT depth_level FROM " . $db->quoteName('#__jeprolab_category') . " WHERE category_id = " .(int)$category_id;
        $db->setQuery($query);
        $level = $db->loadObject()->depth_level;
        /* Updates level_depth for all children */
        foreach ($categories as $sub_category){
            $query = "UPDATE " . $db->quoteName('#__jeprolab_category') . " SET depth_level = " . (int)($level + 1) . " WHERE category_id = " . (int)$sub_category->category_id;

            $db->setQuery($query);
            $db->query();
            /* Recursive call */
            $this->recalculateLevelDepth($sub_category->category_id);
        }
    }

    public function addPosition($position, $lab_id = null){
        $return = true;
        $db = JFactory::getDBO();
        if (is_null($lab_id)){
            if (JeprolabLabModelLab::getLabContext() != JeprolabLabModelLab::CONTEXT_LAB) {
                foreach (JeprolabLabModelLab::getContextListLabIds() as $lab_id) {
                    $query = "INSERT INTO " . $db->quoteName('#__jeprolab_category_lab') . "(" . $db->quoteName('category_id') . ", " . $db->quoteName('lab_id');
                    $query .= ", " . $db->quoteName('position') . ") VALUES (" . (int)$this->category_id . ", " . (int)$lab_id . ", " . (int)$position . ") ";
                    $query .= "	ON DUPLICATE KEY UPDATE " . $db->quoteName('position') . " = "  . (int)$position;

                    $db->setQuery($query);
                    $return &= $db->query();
                }
            }else {
                $lab_id = JeprolabContext::getContext()->lab->lab_id;
                $lab_id = $lab_id ? $lab_id : JeprolabSettingModelSetting::getValue('default_lab');

                $query = "INSERT INTO " . $db->quoteName('#__jeprolab_category_lab') . " ( " . $db->quoteName('category_id') . ", " . $db->quoteName('lab_id') . ", ";
                $query .= $db->quoteName('position') . " VALUES (" . (int)$this->category_id . ", " . (int)$lab_id . ", " . (int)$position . ") ON DUPLICATE KEY UPDATE ";
                $query .= $db->quoteName('position') . " = " . (int)$position;

                $db->setQuery($query);
                $return &= $db->query();
            }
        }
        else{
            $query = "INSERT INTO " . $db->quoteName('#__jeprolab_category_lab') . " (" . $db->quoteName('category_id') . ", " . $db->quoteName('lab_id');
            $query .= ", " . $db->quoteName('position') . ") VALUES (" .(int)$this->category_id.', '.(int)$lab_id . ", " .(int)$position. ") ON DUPLICATE ";
            $query .= "KEY UPDATE " . $db->quoteName('position') . " = " .(int)$position;

            $db->setQuery($query);
            $return &= $db->query();
        }
        return $return;
    }

    /**
     * Update customer groups associated to the object
     *
     * @param array $list groups
     */
    public function updateGroup($list) {
        $this->cleanGroups();
        if (empty($list)) {
            $list = array(JeprolabSettingModelSetting::getValue('unidentified_group'), JeprolabSettingModelSetting::getValue('guest_group'), JeprolabSettingModelSetting::getValue('customer_group'));
        }
        $this->addGroups($list);
    }

    public function addGroups($groups){
        $db = JFactory::getDBO();
        foreach ($groups as $group_id) {
            $query = "INSERT INTO " . $db->quoteName('#__jeprolab_category_group') . " (" . $db->quoteName('category_id') .", ";
            $query .= $db->quoteName('group_id') . ") VALUES (" . (int)$this->category_id . ", " . (int)$group_id . ")";

            $db->setQuery($query);
            $db->query();
        }
    }

    public function cleanGroups(){
        $db = JFactory::getDBO();
        $query = "DELETE FROM " . $db->quoteName('#__jeprolab_category_group') . " WHERE " . $db->quoteName('category_id') . " = " . (int)$this->category_id;
        $db->setQuery($query);
        $db->query();
    }

    protected static function subTree(&$categories, $category_id, &$n) {
        $left = $n++;
        $db = JFactory::getDBO();
        if (isset($categories[(int)$category_id]['subcategories'])) {
            foreach ($categories[(int)$category_id]['subcategories'] as $subcategory_id) {
                JeprolabCategoryModelCategory::subTree($categories, (int)$subcategory_id, $n);
            }
        }
        $right = (int)$n++;

        $query = "UPDATE " . $db->quoteName('#__jeprolab_category') . " SET n_left = " .(int)$left. ", n_right = " .(int)$right;
        $query .= "	WHERE category_id = " . (int)$category_id . " LIMIT 1";

        $db->setQuery($query);
        $db->query();
    }

    public function updateCategory(){
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $input = JRequest::get('post');
        $category_data = $input['jform'];
        $languages = JeprolabLanguageModelLanguage::getLanguages();

        $context = JeprolabContext::getContext();

        if(!$context->controller->has_errors){
            $category_id = (int)$app->input->get('category_id');

            /** update category  */
            if(isset($category_id) && !empty($category_id)){
                //$category = new JeprolabCategoryModelCategory($category_id);
                if(JeprolabTools::isLoadedObject($this, 'category_id')){
                    if($this->category_id == $this->parent_id){
                        $context->controller->has_errors = true;
                        JError::_(500, JText::_('COM_JEPROLAB_A_CATEGORY_CANNOT_BE_ITS_OWN_PARENT_LABEL'));
                    }
                    if($this->is_root_category){
                        $this->parent_id = JeprolabSettingModelSetting::getValue('root_category');
                    }
                    // Update group selection
                    $this->updateGroup($this->groupBox);
                    $this->depth_level = $this->calculateDepthLevel();

                    // If the parent category was changed, we don't want to have 2 categories with the same position
                    if ($this->getDuplicatedPosition()){
                        if ($category_data['check_box_lab_associated_category']) {
                            foreach ($category_data['check_box_lab_associated_category'] as $associated_category_id => $row) {
                                foreach ($row as $lab_id => $value) {
                                    $this->addPosition(JeprolabCategoryModelCategory::getLastPosition((int)$this->parent_id, (int)$lab_id), (int)$lab_id);
                                }
                            }
                        }else {
                            foreach (JeprolabLabModelLab::getLabs(true) as $lab) {
                                $this->addPosition(max(1, JeprolabCategoryModelCategory::getLastPosition((int)$this->parent_id, $lab->lab_id)), $lab->lab_id);
                            }
                        }
                    }
                    $this->cleanPositions((int)$this->parent_id);

                    $this->clearCache();
                    $this->date_upd = date('Y-m-d H:i:s');

                    $lab_list_ids = JeprolabLabModelLab::getContextListLabIds();
                    if(count($this->lab_list_ids) > 0){
                        $lab_list_ids = $this->lab_list_ids;
                    }

                    if(JeprolabLabModelLab::checkDefaultLabId('category') && !$this->default_lab_id){
                        $this->default_lab_id = min($lab_list_ids);
                    }
                    $result = true;

                    $query = "UPDATE " . $db->quoteName('#__jeprolab_category') . " SET " . $db->quoteName('n_left') . " = " . (int)$this->n_left . ", " ;
                    $query .= $db->quoteName('n_right') . " = " . (int)$this->n_right . ", " . $db->quoteName('depth_level') . " = " . (int)$this->depth_level;
                    $query .= ", " . $db->quoteName('published') . " = " . (int)$category_data['published'] . ", " . $db->quoteName('default_lab_id') . " = " . (int)$this->default_lab_id;
                    $query .= ", " . $db->quoteName('is_root_category') . " = " . (int)$category_data['is_root_category'] . ", " . $db->quoteName('position') . " = ";
                    $query .= (int)$this->position . ", " . $db->quoteName('date_upd') . " = " . $db->quote($this->date_upd) . " WHERE " . $db->quoteName('category_id');
                    $query .= " = " . (int)$this->category_id;

                    $db->setQuery($query);
                    $result &= $db->query();

                    foreach($lab_list_ids as $lab_id){
                        $where = " WHERE " . $db->quoteName('category_id') . " = " . (int)$this->category_id . " AND " . $db->quoteName('lab_id') . " = " . (int)$lab_id;
                        $select = "SELECT " . $db->quoteName('category_id') . " FROM " . $db->quoteName('#__jeprolab_category_lab') . $where;
                        $db->setQuery($select);
                        $lab_exist = ($db->loadObject()->category_id > 0);
                        if($lab_exist){
                            $query = "UPDATE " . $db->quoteName('#__jeprolab_category_lab') . " SET " . $db->quoteName('position') . " = " . (int)$this->position . $where;
                            $db->setQuery($query);
                            $result &= $db->query();
                        }elseif(JeprolabLabModelLab::getLabContext() == JeprolabLabModelLab::CONTEXT_LAB){
                            $query = "INSERT INTO " . $db->quoteName('#__jeprolab_category_lab') . "(" . $db->quoteName('category_id') . ", " . $db->quoteName('lab_id') . ", " . $db->quoteName('position') ;
                            $query .= ") VALUES (" . (int)$this->category_id . ", " . (int)$lab_id . ", "  . (int)$this->position . ")";
                            $db->setQuery($query);
                            $result &= $db->query();
                        }

                        foreach($languages as $language) {
                            $where = " WHERE " . $db->quoteName('category_id') . " = " . (int)$this->category_id . " AND " . $db->quoteName('lab_id');
                            $where .= " = " . (int)$lab_id . " AND " . $db->quoteName('lang_id') . " = " . (int)$language->lang_id;
                            $select = "SELECT COUNT(*) FROM " . $db->quoteNAme('#__jeprolab_category_lang') . $where;
                            $db->setQuery($select);
                            $lang_exist = $db->loadResult();

                            if($lang_exist) {
                                $query = "UPDATE " . $db->quoteName('#__jeprolab_category_lang') . " SET " . $db->quoteName('name') . " = " . $db->quote($category_data['name_' . $language->lang_id]) . ", ";
                                $query .= $db->quoteName('description') . " = " . $db->quote($category_data['description_' . $language->lang_id]) . ", " . $db->quoteName('link_rewrite') . " = ";
                                $query .= $db->quote($category_data['link_rewrite_' . $language->lang_id]) . ", " . $db->quoteName('meta_title') . " = " . $db->quote($category_data['meta_title_' . $language->lang_id]);
                                $query .= ", "  . $db->quoteName('meta_keywords') . " = " . $db->quote($category_data['meta_keywords_' . $language->lang_id]) . ", " . $db->quoteName('meta_description');
                                $query .= " = " . $db->quote($category_data['meta_description_' . $language->lang_id]) . $where;
                            }else{
                                $query = "INSERT INTO " . $db->quoteName('#__jeprolab_category_lang') . " (" . $db->quoteName('name') . ", " . $db->quoteName('description') . ", " . $db->quoteName('link_rewrite');
                                $query .= ", " . $db->quoteName('meta_title') . ", "  . $db->quoteName('meta_keywords') . ", " . $db->quoteName('meta_description') . ") VALUES (" . $db->quote($category_data['name_' . $language->lang_id]);
                                $query .= ", " . $db->quote($category_data['description_' . $language->lang_id]) . ", " . $db->quote($category_data['link_rewrite_' . $language->lang_id]) . ", ";
                                $query .= $db->quote($category_data['meta_title_' . $language->lang_id]) . ", " . $db->quote($category_data['meta_keywords_' . $language->lang_id]) . ", ";
                                $query .= $db->quote($category_data['meta_description_' . $language->lang_id]) . ") " . $where;
                            }
                            $db->setQuery($query);
                            $result &= $db->query();
                        }
                    }

                    if (!isset($this->doNotRegenerateNTree) || !$this->doNotRegenerateNTree){
                        JeprolabCategoryModelCategory::regenerateEntireNestedtree();
                        $this->recalculateLevelDepth($this->category_id);
                    }
                }
                $message = '';
                $link ='index.php?option=com_jeprolab&view=category&category_id=' . (int)$this->category_id . '&task=edit' . JeprolabTools::getCategoryToken();
            }else{
                $message = '';
                $link = 'index.php?option=com_jeprolab&view=category&category_id=' . (int)$this->category_id . '&task=edit' . JeprolabTools::getCategoryToken();
            }
            $app->redirect($link, $message);
        }
    }

    /**
     * Search for another category with the same parent and the same position
     *
     * @return array first category found
     */
    public function getDuplicatedPosition(){
        $db = JFactory::getDBO();

        $query = "SELECT category." . $db->quoteName('category_id') . " FROM " . $db->quoteName('#__jeprolab_category') . " AS category ";
        $query .= JeprolabLabModelLab::addSqlAssociation('category') . " WHERE category." . $db->quoteName('parent_id') . " = ";
        $query .= (int)$this->parent_id . " AND category_lab." . $db->quoteName('position') . " = " .(int)$this->position . " AND category.";
        $query .= $db->quoteName('category_id') . " != " . (int)$this->category_id;

        $db->setQuery($query);
        return $db->loadObject()->category_id;
    }

    /**
     * cleanPositions keep order of category in $id_category_parent,
     * but remove duplicate position. Should not be used if positions
     * are clean at the beginning !
     *
     * @param mixed $category_parent_id
     * @return boolean true if succeed
     */
    public static function cleanPositions($category_parent_id = null){
        if ($category_parent_id === null){  return true; }

        $return = true;
        $db = JFactory::getDBO();

        $query = "SELECT category." . $db->quoteName('category_id') . " FROM " . $db->quoteName('#__jeprolab_category') . " AS category ";
        $query .= JeprolabLabModelLab::addSqlAssociation('category') . "	WHERE category." . $db->quoteName('parent_id') . " = ";
        $query .= (int)$category_parent_id . " ORDER BY category_lab." . $db->quoteName('position');

        $db->setQuery($query);
        $result = $db->loadObjezctList();
        $count = count($result);
        for ($i = 0; $i < $count; $i++){
            $query = "UPDATE " . $db->quoteName('#__jeprolab_category') . " AS category " .JeprolabLabModelLab::addSqlAssociation('category');
            $query .= " SET category_lab." . $db->quoteName('position') . " = " . (int)($i + 1) . " WHERE category." . $db->quoteName('parent_id');
            $query .= " = " . (int)$category_parent_id . " AND category." . $db->quoteName('category_id') . " = " .(int)$result[$i]->category_id;

            $db->setQuery($query);
            $return &= $db->query();
        }
        return $return;
    }

    public function clearCache($all = false){
        if ($all)
            JeprolabCache::clean('jeprolab_model_category_*');
        elseif ($this->category_id)
            JeprolabCache::clean('jeprolab_model_category_' . (int)$this->category_id.'_*');
    }

    /**
     * Update categories for a lab
     *
     * @param string $categories Categories list to associate a lab
     * @param string $lab_id Categories list to associate a lab
     * @return array Update/insertion result
     */
    public static function updateFromLab($categories, $lab_id){
        $lab = new JeprolabLabModelLab($lab_id);
        // if array is empty or if the default category is not selected, return false
        if (!is_array($categories) || !count($categories) || !in_array($lab->category_id, $categories))
            return false;

        // delete categories for this lab
        JeprolabCategoryModelCategory::deleteCategoriesFromLab($lab_id);

        // and add $categories to this lab
        return JeprolabCategoryModelCategory::addToLab($categories, $lab_id);
    }

    /**
     * Delete category from lab $id_lab
     * @param int $lab_id
     * @return bool
     */
    public function deleteFromLab($lab_id){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeprolab_category_lab') . " WHERE " . $db->quoteName('lab_id') . " = " . (int)$lab_id . " AND " . $db->quoteName('category_id') . " = " . (int)$this->category_id;
        $db->setQuery($query);
        return $db->query();
    }

    /**
     * Delete every categories
     * @param $lab_id
     * @return bool
     */
    public static function deleteCategoriesFromLab($lab_id){
        $db = JFactory::getDBO();

        $query = "DELETE FROM " . $db->quoteName('#__jeprolab_category_lab') . " WHERE " . $db->quoteName('lab_id') . " = " .(int)$lab_id;
        $db->setQuery($query);
        return $db->query();
    }

    /**
     * Add some categories to a lab
     * @param array $categories
     * @param $lab_id
     * @return bool
     */
    public static function addToLab(array $categories, $lab_id){
        if (!is_array($categories)){ return false; }
        $db = JFactory::getDBO();
        $query = "INSERT INTO " . $db->quoteName('#__jeprolab_category_lab') . " (" . $db->quoteName('category_id') . ", " . $db->quoteName('lab_id') . ") VALUES ";
        $tab_categories = array();
        foreach ($categories as $category_id){
            $tab_categories[] = new JeprolabCategoryModelCategory($category_id);
            $query .= "(" .(int)$category_id . ", " .(int)$lab_id . "),";
        }
        // removing last comma to avoid SQL error
        $query = substr($query, 0, strlen($query) - 1);

        $db->setQuery($query);
        $return = $db->query();
        // we have to update position for every new entries
        foreach ($tab_categories as $category)
            $category->addPosition(JeprolabCategoryModelCategory::getLastPosition($category->parent_id, $lab_id), $lab_id);

        return $return;
    }

    public function existsInLab($lab_id){
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('category_id') . " FROM " . $db->quoteName('#__jeprolab_category_lab') . " WHERE " . $db->quoteName('category_id') . " = " . (int)$this->category_id . " AND " . $db->quoteName('lab_id') . " = " . (int)$lab_id;

        $db->setQuery($query);
        return (bool)$db->loadResult();
    }

    public function isRootCategoryForALab(){
        return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `id_lab`
		FROM `'._DB_PREFIX_.'lab`
		WHERE `id_category` = '.(int)$this->id);
    }

    public static function getLabsByCategory($id_category){
        return Db::getInstance()->executeS('
		SELECT `id_lab`
		FROM `'._DB_PREFIX_.'category_lab`
		WHERE `id_category` = '.(int)$id_category);
    }

    /**
     * Add association between lab and categories
     * @param int $id_lab
     * @return bool
     */
    public function addLab($id_lab){
        $data = array();
        if (!$id_lab)
        {
            foreach (Lab::getLabs(false) as $lab)
                if (!$this->existsInLab($lab['id_lab']))
                    $data[] = array(
                        'id_category' => (int)$this->id,
                        'id_lab' => (int)$lab['id_lab'],
                    );
        }
        elseif (!$this->existsInLab($id_lab))
            $data[] = array(
                'id_category' => (int)$this->id,
                'id_lab' => (int)$id_lab,
            );

        return Db::getInstance()->insert('category_lab', $data);
    }

    public static function inStaticLab($id_category, JeprolabLabModelLab $lab = null)
    {
        if (!$lab || !is_object($lab))
            $lab = Context::getContext()->lab;

        if (!$interval = JeprolabCategoryModelCategory::getInterval($lab->getCategoryId()))
            return false;
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT nleft, nright FROM `'._DB_PREFIX_.'category` WHERE id_category = '.(int)$id_category);
        return ($row['nleft'] >= $interval['nleft'] && $row['nright'] <= $interval['nright']);
    }

    public static function getUrlRewriteInformations($id_category)
    {
        return Db::getInstance()->executeS('
			SELECT l.`id_lang`, c.`link_rewrite`
			FROM `'._DB_PREFIX_.'category_lang` AS c
			LEFT JOIN  `'._DB_PREFIX_.'lang` AS l ON c.`id_lang` = l.`id_lang`
			WHERE c.`id_category` = '.(int)$id_category.'
			AND l.`active` = 1'
        );
    }

    /**
     * Return nleft and nright fields for a given category
     *
     * @since 1.5.0
     * @param int $id
     * @return array
     */
    public static function getInterval($id){
        $cache_id = 'Category::getInterval_'.(int)$id;
        if (!Cache::isStored($cache_id))
        {
            $result = Db::getInstance()->getRow('
			SELECT nleft, nright, level_depth
			FROM '._DB_PREFIX_.'category
			WHERE id_category = '.(int)$id);
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }

    public function cleanAssociatedProducts(){
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_product` WHERE `id_category` = '.(int)$this->id);
    }

    public function addGroupsIfNoExist($id_group)
    {
        $groups = $this->getGroups();
        if (!in_array((int)$id_group, $groups))
            return $this->addGroups(array((int)$id_group));
        return false;
    }

    /**
     * Search with Pathes for categories
     *
     * @param integer $id_lang Language ID
     * @param string $path of category
     * @param boolean $object_to_create a category
     * 	  * @param boolean $method_to_create a category
     * @return array Corresponding categories
     */
    public static function searchByPath($id_lang, $path, $object_to_create = false, $method_to_create = false)
    {
        $categories = explode('/', trim($path));
        $category = $id_parent_category = false;

        if (is_array($categories) && count($categories))
            foreach($categories as $category_name)
            {
                if ($id_parent_category)
                    $category = JeprolabCatetoryModelCategory::searchByNameAndParentCategoryId($id_lang, $category_name, $id_parent_category);
                else
                    $category = JeprolabCategoryModelCategory::searchByName($id_lang, $category_name,true);

                if (!$category && $object_to_create && $method_to_create)
                {
                    call_user_func_array(array($object_to_create, $method_to_create), array($id_lang, $category_name , $id_parent_category));
                    $category = JeprolabCategoryModelCategory::searchByPath($id_lang, $category_name);
                }
                if (isset($category['id_category']) && $category['id_category'])
                    $id_parent_category = (int)$category['id_category'];
            }
        return $category;
    }

    /**
     * Specify if a category already in base
     *
     * @param int $id_category Category id
     * @return boolean
     */
    public static function categoryExists($id_category){
        $row = Db::getInstance()->getRow('
		SELECT `id_category`
		FROM '._DB_PREFIX_.'category c
		WHERE c.`id_category` = '.(int)$id_category);

        return isset($row['id_category']);
    }

    public function getName($lang_id = null){
        if (!$lang_id){
            if (isset($this->name[JeprolabContext::getContext()->language->lang_id])) {
                $lang_id = JeprolabContext::getContext()->language->lang_id;
            }else {
                $lang_id = (int)JeprolabSettingModelSetting::getValue('default_lang');
            }
        }
        return isset($this->name[$lang_id]) ? $this->name[$lang_id] : '';
    }

    /**
     * Light back office search for categories
     *
     * @param integer $lang_id Language ID
     * @param string $query Searched string
     * @param boolean $unrestricted allows search without lang and includes first category and exact match
     * @return array Corresponding categories
     */
    public static function searchByName($lang_id, $query, $unrestricted = false){
        if ($unrestricted === true)
            return Db::getInstance()->getRow('
			SELECT c.*, cl.*
			FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` '.Lab::addSqlRestrictionOnLang('cl').')
			WHERE `name` LIKE \''.pSQL($query).'\'');
        else
            return Db::getInstance()->executeS('
			SELECT c.*, cl.*
			FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.(int)$lang_id .' '.Lab::addSqlRestrictionOnLang('cl').')
			WHERE `name` LIKE \'%'.pSQL($query).'%\'
			AND c.`id_category` != '.(int)Configuration::get('PS_HOME_CATEGORY'));
    }

    /**
     * Retrieve category by name and parent category id
     *
     * @param integer $id_lang Language ID
     * @param string  $category_name Searched category name
     * @param integer $id_parent_category parent category ID
     * @return array Corresponding category
     */
    public static function searchByNameAndParentCategoryId($id_lang, $category_name, $id_parent_category){
        return Db::getInstance()->getRow('
		SELECT c.*, cl.*
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
			ON (c.`id_category` = cl.`id_category`
			AND `id_lang` = '.(int)$id_lang.Lab::addSqlRestrictionOnLang('cl').')
		WHERE `name`  LIKE \''.pSQL($category_name).'\'
			AND c.`id_category` != '.(int)Configuration::get('PS_HOME_CATEGORY').'
			AND c.`id_parent` = '.(int)$id_parent_category);
    }

    /**
     * checkAccess return true if id_customer is in a group allowed to see this category.
     *
     * @param mixed $id_customer
     * @access public
     * @return boolean true if access allowed for customer $id_customer
     */
    public function checkAccess($id_customer)
    {
        $cache_id = 'Category::checkAccess_'.(int)$this->id.'-'.$id_customer.(!$id_customer ? '-'.(int)Group::getCurrent()->id : '');
        if (!Cache::isStored($cache_id))
        {
            if (!$id_customer)
                $result = (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT ctg.`id_group`
				FROM '._DB_PREFIX_.'category_group ctg
				WHERE ctg.`id_category` = '.(int)$this->id.' AND ctg.`id_group` = '.(int)Group::getCurrent()->id);
            else
                $result = (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT ctg.`id_group`
				FROM '._DB_PREFIX_.'category_group ctg
				INNER JOIN '._DB_PREFIX_.'customer_group cg on (cg.`id_group` = ctg.`id_group` AND cg.`id_customer` = '.(int)$id_customer.')
				WHERE ctg.`id_category` = '.(int)$this->id);
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }

    public static function setNewGroupForHome($id_group)
    {
        if (!(int)$id_group)
            return false;

        return Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'category_group` (`id_category`, `id_group`)
		VALUES ('.(int)Context::getContext()->lab->getCategory().', '.(int)$id_group.')');
    }

    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('
			SELECT cp.`id_category`, category_lab.`position`, cp.`id_parent`
			FROM `'._DB_PREFIX_.'category` cp
			'.Lab::addSqlAssociation('category', 'cp').'
			WHERE cp.`id_parent` = '.(int)$this->id_parent.'
			ORDER BY category_lab.`position` ASC'
        ))
            return false;

        $moved_category = false;
        foreach ($res as $category)
            if ((int)$category['id_category'] == (int)$this->id)
                $moved_category = $category;

        if ($moved_category === false || !$position)
            return false;
        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        $result = (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'category` c '.Lab::addSqlAssociation('category', 'c').'
			SET category_lab.`position`= category_lab.`position` '.($way ? '- 1' : '+ 1').'
			WHERE category_lab.`position`
			'.($way
                    ? '> '.(int)$moved_category['position'].' AND category_lab.`position` <= '.(int)$position
                    : '< '.(int)$moved_category['position'].' AND category_lab.`position` >= '.(int)$position).'
			AND c.`id_parent`='.(int)$moved_category['id_parent'])
            && Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'category` c '.Lab::addSqlAssociation('category', 'c').'
			SET category_lab.`position` = '.(int)$position.'
			WHERE c.`id_parent` = '.(int)$moved_category['id_parent'].'
			AND c.`id_category`='.(int)$moved_category['id_category']));
        Hook::exec('actionCategoryUpdate');
        return $result;
    }

    /**
     * This method allow to return children categories with the number of sub children selected for a product
     *
     * @param int $parent_id
     * @param int $product_id
     * @param int $lang_id
     * @return array
     */
    public static function getChildrenWithNumberOfSelectedSubCategories($parent_id, $selected_category, $lang_id, JeprolabLabModelLab $lab = null, $use_lab_context = true){
        if (!$lab)
            $lab = Context::getContext()->lab;

        $id_lab = $lab->id ? $lab->id : Configuration::get('PS_LAB_DEFAULT');
        $selected_cat = explode(',', str_replace(' ', '', $selected_category));
        $sql = '
		SELECT c.`id_category`, c.`level_depth`, cl.`name`,
		IF((
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'category` c2
			WHERE c2.`id_parent` = c.`id_category`
		) > 0, 1, 0) AS has_children,
		'.($selected_cat ? '(
			SELECT count(c3.`id_category`)
			FROM `'._DB_PREFIX_.'category` c3
			WHERE c3.`nleft` > c.`nleft`
			AND c3.`nright` < c.`nright`
			AND c3.`id_category`  IN ('.implode(',', array_map('intval', $selected_cat)).')
		)' : '0').' AS nbSelectedSubCat
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` '.Lab::addSqlRestrictionOnLang('cl', $id_lab).')
		LEFT JOIN `'._DB_PREFIX_.'category_lab` cs ON (c.`id_category` = cs.`id_category` AND cs.`id_lab` = '.(int)$id_lab.')
		WHERE `id_lang` = '.(int)$lang_id.'
		AND c.`id_parent` = '.(int)$parent_id;
        if (Lab::getContext() == Lab::CONTEXT_LAB && $use_lab_context)
            $sql .= ' AND cs.`id_lab` = '.(int)$lab->id;
        if (!Lab::isFeatureActive() || Lab::getContext() == Lab::CONTEXT_LAB && $use_lab_context)
            $sql .= ' ORDER BY cs.`position` ASC';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * Copy products from a category to another
     *
     * @param integer $old_id Source category ID
     * @param boolean $new_id Destination category ID
     * @return boolean Duplication result
     */
    public static function duplicateProductCategories($old_id, $new_id){
        $db = JFactory::getDBO();
        $query = "SELECT " . $db->quoteName('category_id') . " FROM " . $db->quoteName('#__jeprolab_category_product') . " WHERE " . $db->quoteName('product_id') . " = " .(int)$old_id;
        $db->setQuery($query);
        $result = $db->loadObjectList();

        $row = array();
        if ($result) {
            foreach ($result as $id) {
                $row[] = '(' . implode(', ', array((int)$new_id, $id->category_id, '(SELECT tmp.max + 1 FROM (
					SELECT MAX(cp.' . $db->quoteName('position') . ' AS max FROM ' . $db->quoteName('#__jeprolab_category_product'). ' cp
					WHERE cp.' . $db->quoteName('category_id') . ' = ' . (int)$id->category_id . ') AS tmp)'
                    )) . ')';
            }
        }
        $query = "INSERT IGNORE INTO " . $db->quoteName('#__jeprolab_category_product') . " (" . $db->quoteName('product_id') . ", " . $db->quoteName('category_id') . ", " . $db->quoteName('position') . ") VALUES ".implode(',', $row);
        $db->setQuery($query);
        $flag = $db->query();

        return $flag;
    }

    /**
     * Check if category can be moved in another one.
     * The category cannot be moved in a child category.
     *
     * @param integer $category_id current category
     * @param integer $parent_id Parent candidate
     * @return boolean Parent validity
     */
    public static function checkBeforeMove($category_id, $parent_id){
        if ($category_id == $parent_id) return false;
        if ($parent_id == Configuration::get('PS_HOME_CATEGORY')) return true;
        $i = (int)$parent_id;
        $db = JFactory::getDBO();

        while (42) {
            $query = "SELECT " . $db->quoteName('parent_id') . " FROM " . $db->quoteName('#__jeprolab_category') . " WHERE " . $db->quoteName('category_id') . " = " .(int)$i;
            $db->setQuery($query);
            $result = $db->loadObject();
            if (!isset($result->parent_id)) return false;
            if ($result->parent_id == $category_id) return false;
            if ($result->parent_id == JeprolabSettingModelSetting::getValue('root_category')) return true;
            $i = $result->parent_id;
        }
    }

    public function getLink(Link $link = null, $id_lang = null){
        if (!$link)
            $link = Context::getContext()->link;

        if (!$id_lang && is_array($this->link_rewrite))
            $id_lang = Context::getContext()->language->id;

        return $link->getCategoryLink($this, is_array($this->link_rewrite) ? $this->link_rewrite[$id_lang] : $this->link_rewrite, $id_lang);
    }


    /**
     * Return main categories
     *
     * @param integer $lang_id Language ID
     * @param boolean $active return only active categories
     * @param bool $lab_id
     * @return array categories
     */
    public static function getHomeCategories($lang_id, $active = true, $lab_id = false){
        return self::getChildren(JeprolabSettingModelSetting::getValue('root_category'), $lang_id, $active, $lab_id);
    }


    /**
     * Return an array of all children of the current category
     *
     * @param int $lang_id
     * @return Collection of Category
     */
    public function getAllChildren($lang_id = null){
        if (is_null($id_lang))
            $id_lang = Context::getContext()->language->id;

        $categories = new PrestaLabCollection('Category', $id_lang);
        $categories->where('nleft', '>', $this->n_left);
        $categories->where('nright', '<', $this->n_right);
        return $categories;
    }

    /**
     * Return current category products
     *
     * @param integer $lang_id Language ID
     *
     * @param bool $check_access
     * @param JeprolabContext $context
     * @return mixed Products or number of products
     */
    public function getProducts($lang_id, $check_access = true, JeprolabContext $context = null) { //$p, $n, $order_by = null, $order_way = null, $get_total = false, $active = true, $random = false, $random_number_products = 1, ,
        if (!$context)
            $context = JeprolabContext::getContext();
        if ($check_access && !$this->checkAccess($context->customer->customer_id))
            return false;

        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront')))
            $front = false;

        if ($p < 1) $p = 1;

        if (empty($order_by))
            $order_by = 'position';
        else
            /* Fix for all modules which are now using lowercase values for 'orderBy' parameter */
            $order_by = strtolower($order_by);

        if (empty($order_way))
            $order_way = 'ASC';

        $order_by_prefix = false;
        if ($order_by == 'id_product' || $order_by == 'date_add' || $order_by == 'date_upd')
            $order_by_prefix = 'p';
        elseif ($order_by == 'name')
            $order_by_prefix = 'pl';
        elseif ($order_by == 'manufacturer')
        {
            $order_by_prefix = 'm';
            $order_by = 'name';
        }
        elseif ($order_by == 'position')
            $order_by_prefix = 'cp';

        if ($order_by == 'price')
            $order_by = 'order_price';

        if (!Validate::isBool($active) || !Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way))
            die (Tools::displayError());

        $id_supplier = (int)Tools::getValue('id_supplier');

        /* Return only the number of products */
        if ($get_total)
        {
            $sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM `'._DB_PREFIX_.'product` p
					'.Lab::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE cp.`id_category` = '.(int)$this->id.
                ($front ? ' AND product_lab.`visibility` IN ("both", "catalog")' : '').
                ($active ? ' AND product_lab.`active` = 1' : '').
                ($id_supplier ? 'AND p.id_supplier = '.(int)$id_supplier : '');
            return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }

        $sql = 'SELECT p.*, product_lab.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, MAX(product_attribute_lab.id_product_attribute) id_product_attribute, product_attribute_lab.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, MAX(image_lab.`id_image`) id_image,
					il.`legend`, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_lab.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
						DAY)) > 0 AS new, product_lab.price AS orderprice
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				'.Lab::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Lab::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_lab.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_lab', false, $context->lab).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (product_lab.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$lang_id.Lab::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$lang_id.Lab::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i
					ON (i.`id_product` = p.`id_product`)'.
            Lab::addSqlAssociation('image', 'i', false, 'image_lab.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (image_lab.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$lang_id.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE product_lab.`id_lab` = '.(int)$context->lab->id.'
					AND cp.`id_category` = '.(int)$this->id
            .($active ? ' AND product_lab.`active` = 1' : '')
            .($front ? ' AND product_lab.`visibility` IN ("both", "catalog")' : '')
            .($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '')
            .' GROUP BY product_lab.id_product';

        if ($random === true)
            $sql .= ' ORDER BY RAND() LIMIT '.(int)$random_number_products;
        else
            $sql .= ' ORDER BY '.(!empty($order_by_prefix) ? $order_by_prefix.'.' : '').'`'.bqSQL($order_by).'` '.pSQL($order_way).'
			LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;

        $db->setQuery($query);
        $result = $db->loadObjectList();
        if ($order_by == 'order_price')
            JeprolabTools::orderByPrice($result, $order_way);

        if (!$result)
            return array();

        /* Modify SQL result */
        return JeprolabProductModelProduct::getProductsProperties($lang_id, $result);
    }


    public static function getSimpleCategories($lang_id){
        $db = JFactory::getDBO();
        $query = "SELECT category." . $db->quoteName('category_id') . ", category_lang." . $db->quoteName('name') . " FROM " . $db->quoteName('#__jeprolab_category') . " AS category LEFT JOIN " .$db->quoteName('#__jeprolab_category_lang');
        $query .= " AS category_lang ON (category." . $db->quoteName('category_id') . " = category_lang." . $db->quoteName('category_id') . JeprolabLabModelLab::addSqlRestrictionOnLang('category_lang') . ") ";
        $query .= JeprolabLabModelLab::addSqlAssociation('category') . " WHERE category_lang." . $db->quoteName('lang_id') . " = " .(int)$lang_id . " AND category." . $db->quoteName('category_id') . " != " . JeprolabSettingModelSetting::getValue('root_category');
        $query .= "	GROUP BY category.category_id ORDER BY category." . $db->quoteName('category_id') . ", category_lab." . $db->quoteName('position');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getLabId()
    {
        return $this->lab_id;
    }

    /**
     * Recursively add specified category children to $to_delete array
     *
     * @param array &$to_delete Array reference where categories ID will be saved
     * @param integer $category_id Parent category ID
     */
    protected function recursiveDelete(&$to_delete, $category_id){
        if (!is_array($to_delete) || !$category_id)
            die(Tools::displayError());

        $db = JFactory::getDBO();
        $query = "SELECT " . $db->quoteName('category_id') . " FROM " . $db->quoteName('#__jeprolab_category') . " WHERE " . $db->quoteName('parent_id') . " = " .(int)$category_id;
        $db->setQuery($query);
        $result = $db->loadObjectList();
        foreach ($result as $row){
            $to_delete[] = (int)$row->category_id;
            $this->recursiveDelete($to_delete, (int)$row->category_id);
        }
    }

    public function deleteLite(){
        $db = JFactory::getDBO();

        // @hook actionObject*DeleteBefore
        Hook::exec('actionObjectDeleteBefore', array('object' => $this));
        Hook::exec('actionObject'.get_class($this).'DeleteBefore', array('object' => $this));

        $this->clearCache();
        $result = true;
        // Remove association to multilab table
        if (JeprolabLabModelLab::isTableAssociated('category')){
            $lab_list_ids = JeprolabLabModelLab::getContextListLabIds();
            if (count($this->lab_list_ids)) {
                $lab_list_ids = $this->lab_list_ids;
            }

            $query = "DELETE FROM " . $db->quoteName('#__jeprolab_category_lab') . " WHERE " . $db->quoteName('category_id') . " = " . (int)$this->category_id . " AND " . $db->quoteName('lab_id') . " IN(" . implode(', ', $lab_list_ids) . ")";
            $db->setQuery($query);
            $result &= $db->query();
        }

        // Database deletion
        $has_multilab_entries = $this->hasMultilabEntries();
        if ($result && !$has_multilab_entries) {
            $query = "DELETE FROM " . $db->quoteName('#__jeprolab_category') . " WHERE " . $db->quoteName('category_id') . " = " . (int)$this->category_id;
            $db->setQuery($query);
            $result &= $db->query();
        }

        if (!$result)
            return false;

        // Database deletion for multilingual fields related to the object
        if (!empty($this->def['multilang']) && !$has_multilab_entries) {
            $query = "DELETE FROM " . $db->quoteName('#__jeprolab_category_lang') . " WHERE " . $db->quoteName('category_id') . " = " . (int)$this->category_id;
            $db->setQuery($query);
            $result &= $db->query();
        }
        // @hook actionObject*DeleteAfter
        Hook::exec('actionObjectDeleteAfter', array('object' => $this));
        Hook::exec('actionObject'.get_class($this).'DeleteAfter', array('object' => $this));

        return $result;
    }

    public function delete(){
        if ((int)$this->category_id === 0 || (int)$this->category_id === 1)
            return false;

        $this->clearCache();

        $allCategories = $this->getAllChildren();
        $allCategories[] = $this;
        foreach ($allCategories as $category){
            $category->deleteLite();
            if (!$this->hasMultilabEntries()){
                $category->deleteImage();
                $category->cleanGroups();
                $category->cleanAssoProducts();
                // Delete associated restrictions on cart rules
                JeprolabCartRuleModelCartRule::cleanProductRuleIntegrity('categories', array($category->category_id));
                JeprolabCategoryModelCategory::cleanPositions($category->parent_id);
                /* Delete Categories in GroupReduction */
                if (JeprolabGroupReductionModelGroupReduction::getGroupsReductionByCategoryId((int)$category->category_id)) {
                    JeprolabGroupReductionModelGroupReduction::deleteCategory($category->category_id);
                }
            }
        }

        /* Rebuild the nested tree */
        if (!$this->hasMultilabEntries() && (!isset($this->doNotRegenerateNTree) || !$this->doNotRegenerateNTree))
            JeprolabCategoryModelCategory::regenerateEntireNtree();

        Hook::exec('actionCategoryDelete', array('category' => $this));

        return true;
    }

    /**
     * Delete several categories from database
     *
     * return boolean Deletion result
     * @param $categories
     * @return bool|int
     */
    public function deleteSelection($categories){
        $return = 1;
        foreach ($categories as $category_id){
            $category = new JeprolabCategoryModelCategory($category_id);
            if ($category->isRootCategoryForALab())
                return false;
            else
                $return &= $category->delete();
        }
        return $return;
    }

    /**
     * @see ObjectModel::toggleStatus()
     */
    public function toggleStatus(){
        $result = parent::toggleStatus();
        Hook::exec('actionCategoryUpdate');
        return $result;
    }

    /**
     * Recursive scan of subcategories
     *
     * @param integer $max_depth Maximum depth of the tree (i.e. 2 => 3 levels depth)
     * @param integer $current_depth specify the current depth in the tree (don't use it, only for recursive!)
     * @param integer $lang_id Specify the id of the language used
     * @param array $excluded_ids_array specify a list of ids to exclude of results
     *
     * @return array Subcategories lite tree
     */
    public function recurseLiteCategoryTree($max_depth = 3, $current_depth = 0, $lang_id = null, $excluded_ids_array = null){
        $lang_id = is_null($lang_id) ? JeprolabContext::getContext()->language->lang_id : (int)$lang_id;

        $children = array();
        $subCategories = $this->getSubCategories($lang_id, true);
        if (($max_depth == 0 || $current_depth < $max_depth) && $subCategories && count($subCategories)) {
            foreach ($subCategories as &$subCategory) {
                if (!$subCategory->category_id)
                    break;
                else if (!is_array($excluded_ids_array) || !in_array($subCategory->category_id, $excluded_ids_array)) {
                    $category = new JeprolabCategoryModelCategory($subCategory->category_id, $lang_id);
                    $children[] = $category->recurseLiteCategoryTree($max_depth, $current_depth + 1, $lang_id, $excluded_ids_array);
                }
            }
        }

        if (is_array($this->description))
            foreach ($this->description as $language_id => $description)
                $this->description[$language_id] = JeprolabTools::getDescriptionClean($description);
        else
            $this->description = JeprolabTools::getDescriptionClean($this->description);

        return array(
            'category_id' => (int)$this->category_id,
            'link' => JRoute::_('index.php?option=com_jeprolab&view=category&category_id=' . $this->category_id . '&link_rewrite=' . $this->link_rewrite . '&' . JeprolabTools::getCategoryToken() . '=1'),
            'name' => $this->name,
            'desc'=> $this->description,
            'children' => $children
        );
    }

    public static function recurseCategory($categories, $current, $category_id = 1, $selected_id = 1){
        echo '<option value="'.$category_id .'"'.(($selected_id == $category_id) ? ' selected="selected"' : '').'>'.
            str_repeat('&nbsp;', $current['infos']['depth_level'] * 5).stripslashes($current['infos']['name']).'</option>';
        if (isset($categories[$category_id]))
            foreach (array_keys($categories[$category_id]) as $key)
                JeprolabCategoryModelCategory::recurseCategory($categories, $categories[$category_id][$key], $key, $selected_id);
    }
}