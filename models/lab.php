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

class JeprolabLabModelLab extends JModelLegacy
{
    public $lab_id;

    public $lab_group_id;

    public $category_id;

    public $name;
    public $theme_id;
    public $theme_name;

    /** @var string Lab theme directory (read only) */
    public $theme_directory = 'default';

    /** @var string Physical uri of main url (read only) */
    public $physical_uri;

    /** @var string Virtual uri of main url (read only) */
    public $virtual_uri;

    /** @var string Domain of main url (read only) */
    public $domain;

    /** @var string Domain SSL of main url (read only) */
    public $ssl_domain;

    public $published;

    public $deleted;

    protected $lab_group;

    /** @var array List of labs cached */
    protected static $labs;

    protected static $associated_tables = array();
    protected static $default_lab_tables_id = array();
    protected static $initialized = false;

    /** @var int Store the current context of lab (CONTEXT_ALL, CONTEXT_GROUP, CONTEXT_lab) */
    protected static $lab_context;

    /** @var int ID lab in the current context (will be empty if context is not CONTEXT_lab) */
    protected static $context_lab_id;

    /** @var int ID lab group in the current context (will be empty if context is CONTEXT_ALL) */
    protected static $context_lab_group_id;

    /**
     * Some data can be shared between labs, like customers or orders
     */
    const SHARE_CUSTOMER = 'share_customer';
    const SHARE_ORDER = 'share_order';
    const SHARE_STOCK = 'share_stock';

    const CONTEXT_LAB = 1;
    const CONTEXT_GROUP = 2;
    const CONTEXT_ALL = 4;

    public function __construct($lab_id = null, $lang_id = null){
        parent::__construct();

        $db = JFactory::getDBO();

        if($lang_id != null){
            $this->lang_id = (JeprolabLanguageModelLanguage::getLanguage($lang_id) != false) ? $lang_id : JeprolabSettingModelSetting::getSettingValue('default_lang');
        }

        if($lab_id){
            /** Load lab from database if  id is supplied */
            $cache_id = 'jeprolab_lab_model_' . (int) $lab_id . (($lang_id > 0) ? '_' . $lang_id : '');
            if(!JeprolabCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_lab') . " AS lab ";
                if($lang_id){
                    $query .= " LEFT JOIN " . $db->quoteName('#__jeprolab_lab_lang') ;
                    $query .= " ON(lab." . $db->quoteName('lab_id') . " = lab_lang." . $db->quoteName('lab_id') ;
                    $query .= " AND lab_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ")";
                }
                $query .= " WHERE lab." . $db->quoteName('lab_id') . " = " . (int)$lab_id;

                $db->setQuery($query);
                $lab_data = $db->loadObject();
                if($lab_data){
                    if($lang_id && isset($this->multilang) && $this->multilang){
                        $query = " SELECT * FROM " . $db->quoteName('#__jeprolab_lab_lang') . " WHERE ";
                        $query .= $db->quoteName('lab_id') . " = " . $lab_id;
                        $db->setQuery($query);
                        $lab_lang_data = $db->loadObjectList();
                        if($lab_lang_data){
                            foreach($lab_lang_data as $row){
                                foreach($row as $key => $value){
                                    if(array_key_exists($key, $this) && $key != 'lab_id'){
                                        if(!isset($lab_data->{$key}) || !is_array($lab_data->{$key})){
                                            $lab_data->{$key} = array();
                                        }
                                        $lab_data->{$key}[$row->lang_id]  = $value;
                                    }
                                }
                            }
                        }
                    }
                    JeprolabCache::store($cache_id, $lab_data);
                }
            }else{
                $lab_data = JeprolabCache::retrieve($cache_id);
            }

            if($lab_data){
                $this->lab_id = (int)$lab_id;
                foreach($lab_data as $key => $value){
                    if(array_key_exists($key, $this)){
                        if($key != 'name'){
                            $this->{$key} = $value;
                        }else{
                            $prop = 'lab_' . $key;
                            $this->{$prop} = $value;
                        }
                    }
                }
            }
        }
        if ($this->lab_id){  $this->setLabUrl(); }
    }

    public function setLabUrl(){
        $db = JFactory::getDBO();

        $query = "SELECT lab_url.physical_uri, lab_url.virtual_uri, lab_url.domain, lab_url.ssl_domain, theme.theme_id, theme.theme_name, theme.directory FROM ";
        $query .= $db->quoteName('#__jeprolab_lab') .  " AS lab LEFT JOIN " . $db->quoteName('#__jeprolab_lab_url') . " lab_url ON (lab.lab_id";
        $query .= " = lab_url.lab_id) LEFT JOIN " . $db->quoteName('#__jeprolab_theme') . " AS theme ON (theme.theme_id = lab.theme_id) WHERE lab.lab_id = ";
        $query .= (int)$this->lab_id . " AND lab.published = 1 AND lab.deleted = 0 AND lab_url.main = 1";

        $db->setQuery($query);
        if (!$row = $db->loadObject()){ return; }

        $this->theme_id = $row->theme_id;
        $this->theme_name = $row->theme_name;
        $this->theme_directory = $row->directory;
        $this->physical_uri = $row->physical_uri;
        $this->virtual_uri = $row->virtual_uri;
        $this->domain = $row->domain;
        $this->ssl_domain = $row->ssl_domain;

        return true;
    }

    /**
     * Find the lab from current domain / uri and get an instance of this lab
     * if INSTALL_VERSION is defined, will return an empty lab object
     *
     * @return Lab
     */
    public static function initialize(){
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();

        $lab_id = (int)$app->input->get("lab_id");

        //find current lab from url
        if(!$lab_id){
            $found_uri = '';
            $host = '';
            $request_uri = rawurldecode($_SERVER['REQUEST_URI']);

            $query = "SELECT lab." . $db->quoteName('lab_id') . ", CONCAT(lab_url.";
            $query .= $db->quoteName('physical_uri') . ", lab_url." .  $db->quoteName('virtual_uri');
            $query .= ") AS uri, lab_url." .  $db->quoteName('domain') . ", lab_url." ;
            $query .= $db->quoteName('main') . " FROM " .  $db->quoteName('#__jeprolab_lab_url');
            $query .= " AS lab_url LEFT JOIN " .  $db->quoteName('#__jeprolab_lab') . " AS lab ON ";
            $query .= "(lab.lab_id = lab_url.lab_id) WHERE (lab_url.domain = " . $db->quote($db->escape($host));
            $query .= " OR lab_url.ssl_domain = " . $db->quote($db->escape($host)) . ") AND lab.published = 1 AND ";
            $query .= "lab.deleted = 0 ORDER BY LENGTH (CONCAT(lab_url.physical_uri, lab_url.virtual_uri)) DESC";

            $db->setQuery($query);
            $results = $db->loadObjectList();
            $throuth = false;
            foreach($results as $result){
                if(preg_match('#^' . preg_quote($result->uri, '#') . '#i', $request_uri)){
                    $throuth = true;
                    $lab_id = $result->lab_id;
                    $found_uri =  $result->uri;
                    if($result->main){
                        $is_main_uri = true;
                    }
                    break;
                }
            }

            /** If an URL was found and it's not the main URL, redirect to main url  **/
            if($throuth && $lab_id &&!$is_main_uri){
                foreach ($results as $result){
                    if($result->lab_id == $lab_id && $result->main){
                        $request_uri = substr($request_uri, strlen($found_uri));
                        $url = str_replace('//', '/', $result->domain. $result->uri . $request_uri);
                        $redirect_type = JeprolabSettingModelSetting::getValue('canonical_redirect') == 2 ? '301' : '302';

                        exit();
                    }
                }
            }
        }

        if((!$lab_id) || JeprolabTools::isPHPCLI() || in_array(JeprolabTools::getHttpHost(), array(COM_JEPROLAB_MEDIA_SERVER_1, COM_JEPROLAB_MEDIA_SERVER_2, COM_JEPROLAB_MEDIA_SERVER_3))){
            if((!$lab_id && JeprolabTools::isPHPCLI())){
                $lab_id = (int)JeprolabSettingModelSetting::getValue('default_lab');
            }
            $lab = new JeprolabLabModelLab($lab_id);

            if(!JeprolabTools::isLoadedObject($lab, 'lab_id')){
                $lab = new JeprolabLabModelLab((int)JeprolabSettingModelSetting::getValue('default_lab'));
            }

            $lab->physical_uri = preg_replace('#/+#', '/', str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME']))) . '/');
            $lab->virtual_uri = '';

            //Define some $_SERVER variables like HTTP_HOST
            if(JeprolabTools::isPHPCLI()){
                if(!isset($_SERVER['HTTP_HOST']) || empty($_SERVER['HTTP_HOST'])){
                    $_SERVER['HTTP_HOST'] = $lab->domain;
                }
                if(!isset($_SERVER['SERVER_NAME']) || empty($_SERVER['SERVER_NAME'])){
                    $_SERVER['SERVER_NAME'] = $lab->domain;
                }
                if(!isset($_SERVER['REMOTE_ADDR']) || empty($_SERVER['REMOTE_ADD'])){
                    $_SERVER['REMOTE_ADD'] =  '127.0.0.1';
                }
            }
        }else{
            $lab = new JeprolabLabModelLab($lab_id);
            if(!JeprolabTools::isLoadedObject($lab, 'lab_id') || !$lab->published){
                // No lab found too bad let's redirect to default lab
                $default_lab = new JeprolabLabModelLab((int)JeprolabSettingModelSetting::getValue('default_lab'));

                if(!JeprolabTools::isLoadedObject($default_lab, 'lab_id')){
                    JError::raiseError(500, JText::_('COM_JEPROLAB_NO_LAB_FOUND_MESSAGE'));
                }

                $inputs = $app->input;
                $inputs->set('lab_id', NULL);
                $url = $default_lab->domain;
                if(!JeprolabSettingModelSetting::getValue('rewrite_settings')){
                    $url .= $default_lab->getBaseUrl() . 'index.php?option=com_jeprolab' . JeprolabTools::buildHttpQuery($inputs);
                }else{
                    /** catch sub domain url "www" **/
                    if(strpos($url, 'www.') === 0 && 'www.' . $_SERVER['HTTP_HOST'] === $url || $_SERVER['HTTP_HOST'] === 'www.' . $url){
                        $url .= $_SERVER['REQUEST_URI'];
                    }else{
                        $url .= $default_lab->getBaseUrl();
                    }

                    if(count($inputs)){
                        $url .= '?option=com_jeprolab' . JeprolabTools::httpBuildQuery($inputs);
                    }
                }
                $redirect_type = JeprolabSettingModelSetting::getValue('canonical_redirect') == 2 ? '301' : '302';
                exit();
            }elseif(empty($lab->physical_uri)) {
                $default_lab  = new JeprolabLabModelLab((int)JeprolabSettingModelSetting::getValue('default_lab'));
                $lab->physical_uri = $default_lab->physical_uri;
                $lab->virtual_uri = $default_lab->virtual_uri;
            }
        }

        self::$context_lab_id =  $lab->lab_id;
        self::$context_lab_group_id =  $lab->lab_group_id;
        self::$lab_context = self::CONTEXT_LAB;

        return $lab;
    }

    /**
     * Check if given table is associated to lab
     *
     * @param string $table
     * @return bool
     */
    public static function isTableAssociated($table){
        if(!JeprolabLabModelLab::$initialized){
            JeprolabLabModelLab::init();
        }
        return isset(JeprolabLabModelLab::$associated_tables[$table]) && JeprolabLabModelLab::$associated_tables[$table]['type'] == 'lab';
    }

    /**
     * Add table associated to lab
     * @param string $table_name
     * @param array $table_details
     * @return boolean
     */
    private static function addTableAssociation($table_name, $table_details){
        if(!isset(JeprolabLabModelLab::$associated_tables[$table_name])){
            JeprolabLabModelLab::$associated_tables[$table_name] = $table_details;
        }else{
            return false;
        }
        return true;
    }

    protected static function init(){
        JeprolabLabModelLab::$default_lab_tables_id = array('product', 'category');

        $associated_tables = array(
            'category' => array('type' => 'lab'),
            'category_lang' => array('type' => 'fk_lab'),
            'contact' => array('type' => 'lab'),
            'country' => array('type' => 'lab'),
            'currency' => array('type' => 'lab'),
            'employee' => array('type' => 'lab'),
            'image' => array('type' => 'lab'),
            'lang' => array('type' => 'lab'),
            'meta_lang' => array('type' => 'fk_lab'),
            'product' => array('type' => 'lab'),
            'product_attribute' => array('type' => 'lab'),
            'product_lang' => array('type' => 'fk_lab'),
            'referrer' => array('type' => 'lab'),
            'attribute' => array('type' => 'lab'),
            'feature' => array('type' => 'lab'),
            'group' => array('type' => 'lab'),
            'attribute_group' => array('type' => 'lab'),
            'tax_rules_group' => array('type' => 'lab'),
            'zone' => array('type' => 'lab'),
            'developer' => array('type' => 'lab')
        );

        foreach($associated_tables as $tale_name => $table_details){
            JeprolabLabModelLab::addTableAssociation($tale_name, $table_details);
        }

        JeprolabLabModelLab::$initialized = true;
    }

    public static function getLabContext(){
        return self::$lab_context;
    }

    public function getBaseUrl(){
        if($this->domain){
            return FALSE;
        }
        return 'http://';
    }

    public static function getContextListLabIds($share = false){
        if(JeprolabLabModelLab::getLabContext() == JeprolabLabModelLab::CONTEXT_LAB){
            $list = ($share) ? JeprolabLabModelLab::getSharedLabs(JeprolabLabModelLab::getContextLabId(), $share) : array(JeprolabLabModelLab::getContextLabId());
        } elseif(JeprolabLabModelLab::getLabContext() == JeprolabLabModelLab::CONTEXT_GROUP) {
            $list = JeprolabLabModelLab::getLabs(true, JeprolabLabModelLab::getContextLabGroupId(), true);
        }else{
            $list = JeprolabLabModelLab::getLabs(TRUE, null, true);
        }
        return $list;
    }

    public static function getContextLabId($null_value_without_multilab = false){
        if($null_value_without_multilab && !JeprolabLabModelLab::isFeaturePublished()){
            return null;
        }
        return self::$context_lab_id;
    }

    public static function checkDefaultLabId($table){
        if(!JeprolabLabModelLab::$initialized){
            JeprolabLabModelLab::init();
        }
        return in_array($table, self::$default_lab_tables_id);
    }

    public static function getCompleteListOfLabsId(){
        $db = JFactory::getDBO();
        $list = array();
        $query = "SELECT lab_id FROM " . $db->quoteName('#__jeprolab_lab');

        $db->setQuery($query);
        $result = $db->loadObjectList();
        foreach ($result as $row){ $list[] = $row->lab_id; }

        return $list;
    }

    /*
     * Get group  of the current lab
    * @return JeprolabLabGroupModelLabGroup
    */
    public function getLabGroup(){
        if(!$this->lab_group){
            $this->lab_group = new JeprolabLabGroupModelLabGroup($this->lab_group_id);
        }
        return $this->lab_group;
    }

    public static function getContextLabGroup(){
        static $context_lab_group = null;
        if ($context_lab_group === null)
            $context_lab_group = new JeprolabLabGroupModelLabGroup((int)self::$context_lab_group_id);
        return $context_lab_group;
    }

    /**
     * Add an sql join in query between a table and its associated table in multi-lab
     *
     * @param string $table Table name (E.g. product, module, etc.
     * @param bool $inner_join
     * @param null $on
     * @param null $force_not_default
     * @return string
     */
    public static function addSqlAssociation($table, $inner_join = true, $on = null, $force_not_default = null){
        $db = JFactory::getDBO();
        $table_alias = $table. '_lab';
        if(strpos($table, '.') !== false){
            list($table_alias, $table) = explode('.', $table);
        }

        if($table == 'group'){ $output_alias = 'grp'; }
        else{ $output_alias = $table; }

        $associated_table = JeprolabLabModelLab::getAssociatedTable($table);
        if($associated_table === false || $associated_table['type'] != 'lab'){ return; }

        $query = (($inner_join) ? " INNER " : " LEFT ") . "JOIN " . $db->quoteName('#__jeprolab_' . $table .'_lab') . " AS ";
        $query .= $table_alias . " ON( " . $table_alias . ".". $table . "_id = " . $output_alias . "." . $table . "_id";

        if((int)self::$context_lab_id){
            $query .= " AND " . $table_alias . ".lab_id = " . (int)self::$context_lab_id;
        }elseif(JeprolabLabModelLab::checkDefaultLabId($table) && !$force_not_default){
            $query .= " AND " . $table_alias . ".lab_id = " . $output_alias . ".default_lab_id";
        }else{
            $query .= " AND " . $table_alias . ".lab_id IN (" . implode(', ', JeprolabLabModelLab::getContextListLabIds()) . ")" ;
        }
        $query .= (($on) ? " AND " . $on : "" ). ")";

        return $query;
    }

    /**
     * Get labs list
     *
     * @param bool $published
     * @param int $lab_group_id
     * @param bool $get_as_list_id
     * @return array
     */
    public static function getLabs($published = true, $lab_group_id = null, $get_as_list_id = false){
        JeprolabLabModelLab::cacheLabs();

        $results = array();
        foreach (self::$labs as $group_id => $group_data){
            foreach ($group_data['labs'] as $lab_id => $lab_data){
                if((!$published || $lab_data->published) && (!$lab_group_id || $lab_group_id == $group_id)){
                    if ($get_as_list_id){
                        $results[$lab_id] = $lab_id;
                    }else{
                        $results[$lab_id] = $lab_data;
                    }
                }
            }
        }
        return $results;
    }

    /**
     * Load list of groups and labs, and cache it
     *
     * @param bool $refresh
     */
    public static function cacheLabs($refresh = false){
        if (!is_null(self::$labs) && !$refresh){
            return;
        }

        self::$labs = array();
        $db = JFactory::getDBO();
        $from = "";
        $where = "";

        $employee = JeprolabContext::getContext()->employee;

        // If the profile isn't a superAdmin
        if (JeprolabTools::isLoadedObject($employee, 'employee_id') && $employee->profile_id != _PS_ADMIN_PROFILE_){
            $from .= " LEFT JOIN ". $db->quoteName('#__jeprolab_employee_lab') . " AS employee_lab ON employee_lab.lab_id = lab.lab_id";
            $where .= " AND employee_lab.employee_id = " . (int)$employee->employee_id;
        }

        $query = "SELECT lab_group.*, lab.*, lab_group.lab_group_name AS group_name, lab.lab_name AS lab_name, ";
        $query .= " lab.published, lab_url.domain, lab_url.ssl_domain, lab_url.physical_uri, lab_url.";
        $query .= "virtual_uri FROM " . $db->quoteName('#__jeprolab_lab_group') . " AS lab_group LEFT JOIN ";
        $query .= $db->quoteName('#__jeprolab_lab') . " AS lab ON lab.lab_group_id = lab_group.lab_group_id ";
        $query .= " LEFT JOIN " . $db->quoteName('#__jeprolab_lab_url') . " AS lab_url ON lab.lab_id =";
        $query .= " lab_url.lab_id AND lab_url.main = 1 " . $from . " WHERE lab.deleted = 0 AND lab_group.";
        $query .= "deleted = 0 " . $where . " ORDER BY lab_group.lab_group_name, lab.lab_name";

        $db->setQuery($query);
        $results = $db->loadObjectList();

        if($results ){
            foreach ($results as $row){
                if (!isset(self::$labs[$row->lab_group_id])){
                    self::$labs[$row->lab_group_id] = array(
                        'lab_group_id' => $row->lab_group_id,
                        'name' => $row->group_name,
                        'share_customers' => $row->share_customers,
                        'share_requests' => $row->share_requests,
                        'share_results' => $row->share_results,
                        'labs' => array()
                    );

                    self::$labs[$row->lab_group_id]['labs'][$row->lab_id] = $row; /*array(
							'lab_id' => $row->lab_id,
							'lab_group_id' => $row->lab_group_id,
							'name' => $row->lab_name,
							'theme_id' => $row->theme_id,
							'category_id' => $row->category_id,
							'domain' => $row->domain,
							'ssl_domain' =>	$row->ssl_domain,
							'uri' =>  $row->physical_uri . $row->virtual_uri,
							'published' => $row->published
					);*/
                }
            }
        }
    }

    /**
     * If the lab group has the option $type activated, get all labs ID of this group, else get current lab ID
     *
     * @param int $lab_id
     * @param int $type Lab::SHARE_CUSTOMER | Lab::SHARE_ORDER
     * @return array
     */
    public static function getSharedLabs($lab_id, $type){
        if (!in_array($type, array(JeprolabLabModelLab::SHARE_CUSTOMER, JeprolabLabModelLab::SHARE_ORDER, JeprolabLabModelLab::SHARE_STOCK))){
            die('Wrong argument ($type) in Lab::getSharedLabs() method');
        }

        JeprolabLabModelLab::cacheLabs();
        foreach (self::$labs as $group_data){
            if (array_key_exists($lab_id, $group_data['labs']) && $group_data[$type]){
                return array_keys($group_data['labs']);
            }
        }
        return array($lab_id);
    }

    /**
     * Get root category of current lab
     * @return int
     */
    public function getCategoryId(){
        return ($this->category_id ? $this->category_id : 1 );
    }

    /**
     * Retrieve group ID of a lab
     *
     * @param int $lab_id Lab ID
     * @param bool $as_id
     * @return int Group ID
     */
    public static function getLabGroupFromLab($lab_id, $as_id = true){
        JeprolabLabModelLab::cacheLabs();
        foreach (self::$labs as $group_id => $group_data)
            if (array_key_exists($lab_id, $group_data['labs']))
                return ($as_id) ? $group_id : $group_data;
        return false;
    }


    /**
     * Add an sql restriction for labs fields
     *
     * @param boolean $share If false, dont check share datas from group. Else can take a Lab::SHARE_* constant value
     * @param string $alias
     * @return string
     */
    public static function addSqlRestriction($share = false, $alias = null){
        if ($alias){
            $alias .= '.';
        }

        $group = JeprolabLabModelLab::getLabGroupFromLab(JeprolabLabModelLab::getContextLabID(), false);
        if ($share == JeprolabLabModelLab::SHARE_CUSTOMER && JeprolabLabModelLab::getLabContext() == JeprolabLabModelLab::CONTEXT_LAB && $group['share_customers']){
            $restriction = " AND ".$alias."lab_group_id = ".(int)  JeprolabLabModelLab::getContextLabGroupId();
        }else{
            $restriction = " AND ".$alias."lab_id IN (".implode(', ', JeprolabLabModelLab::getContextListLabIds($share)).") ";
        }
        return $restriction;
    }

    public static function addSqlRestrictionOnLang($alias = NULL, $lab_id = NULL){
        if(isset(JeprolabContext::getContext()->lab) && is_null($lab_id)){
            $lab_id = (int)  JeprolabContext::getContext()->lab->lab_id;
        }

        if(!$lab_id){
            $lab_id = JeprolabSettingModelSetting::getValue('default_lab');
        }
        $db = JFactory::getDBO();
        return " AND " . ($alias ? $alias . "." : "") . $db->quoteName('lab_id') . " = " . (int)$lab_id;
    }

    /**
     * Get the associated table if available
     *
     * @param $table
     * @return array
     */
    public static function getAssociatedTable($table){
        if (!JeprolabLabModelLab::$initialized){
            JeprolabLabModelLab::init();
        }
        return (isset(JeprolabLabModelLab::$associated_tables[$table]) ? JeprolabLabModelLab::$associated_tables[$table] : false);
    }

    public static function isFeaturePublished(){
        static $feature_published = null;

        if ($feature_published === null){
            $db = JFactory::getDBO();
            $query = "SELECT COUNT(*) FROM " . $db->quoteName('#__jeprolab_lab');
            $db->setQuery($query);
            $feature_published = JeprolabSettingModelSetting::getValue('multi_lab_feature_active') && (($db->loadResult()) > 1);
        }
        return $feature_published;
    }

    /**
     * Get current ID of lab group if context is CONTEXT_LAB or CONTEXT_GROUP
     *
     * @param bool $null_value_without_multilab
     * @return int
     */
    public static function getContextLabGroupId($null_value_without_multilab = false){
        if ($null_value_without_multilab && !JeprolabLabModelLab::isFeaturePublished()){
            return null;
        }
        return self::$context_lab_group_id;
    }
}


/***** ---------- LAB GROUP --------******/
class JeprolabLabGroupModelLabGroup extends JModelLegacy
{
    public $name;
    public $lab_group_id;
    public $published = true;
    public $share_customer;
    public $share_stock;
    public $share_order;
    public $deleted;

    public function __construct($lab_group_id = NULL) {
        $db = JFactory::getDBO();
        if($lab_group_id){
            /** Load object from database if lab group id is present **/
            $cache_id = 'jeprolab_lab_group_model_' . $lab_group_id;
            if(!JeprolabCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_lab_group') . " AS lab_group ";
                $query .= " WHERE lab_group." . $db->quoteName('lab_group_id') . " = ". (int)$lab_group_id;

                $db->setQuery($query);
                $lab_group_data = $db->loadObject();
                if($lab_group_data){
                    JeprolabCache::store($cache_id, $lab_group_data);
                }
            }else{
                $lab_group_data = JeprolabCache::retrieve($cache_id);
            }

            if($lab_group_data){
                $lab_group_data->lab_group_id = $lab_group_id;
                foreach($lab_group_data as $key => $value){
                    if(array_key_exists($key, $this)){
                        $this->{$key} = $value;
                    }
                }
            }
        }
    }

    public static function getLabGroups($published = TRUE){
        $db = JFactory::getDBO();

        $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_lab_group') . " WHERE 1 ";
        if($published){
            $query .= " AND " . $db->quoteName('published') . " = " . $published;
        }

        $db->setQuery($query);
        $groups = $db->loadObjectList();
        return $groups;
    }
}
