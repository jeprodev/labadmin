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


class JeprolabCountryModelCountry extends  JModelLegacy
{
    public $country_id;

    public $lang_id;

    public $lab_id;

    public $zone_id;

    public $currency_id;

    public $states = array();

    public $name = array();
    public $iso_code;

    public $call_prefix;

    public $published;

    public $contains_states;

    public $need_identification_number;

    public $need_zip_code;

    public $zip_code_format;

    public $display_tax_label;

    public $country_display_tax_label;
    public $get_lab_from_context = false;

    public $multiLangLab = true;
    public $multiLang = true;

    private $pagination = null;

    public function __construct($country_id = null, $lang_id = null, $lab_id = NULL){
        $db = JFactory::getDBO();

        if($lang_id !== NULL){
            $this->lang_id = (JeprolabLanguageModelLanguage::getLanguage($lang_id) ? (int)$lang_id : JeprolabSettingModelSetting::getValue('default_lang'));
        }

        if($lab_id  && $this->isMultiLab()){
            $this->lab_id = (int)$lab_id;
            $this->get_lab_from_context = FALSE;
        }

        if($this->isMultiLab() && !$this->lab_id){
            $this->lab_id = JeprolabContext::getContext()->lab->lab_id;
        }

        if($country_id){
            $cache_id = 'jeprolab_country_model_' . (int)$country_id . '_' . (int)$lang_id . '_' . (int)$lab_id;
            if(!JeprolabCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_country') . " AS country ";

                //Get language data
                if($lang_id){
                    $query .= " LEFT JOIN " . $db->quoteName('#__jeprolab_country_lang') . " AS country_lang ON (country_lang." . $db->quoteName('country_id') . " = country." . $db->quoteName('country_id') . " AND country_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ")";
                }

                if(JeprolabLabModelLab::isTableAssociated('country')){
                    $query .= " LEFT JOIN " . $db->quoteName('#__jeprolab_country_lab') . " AS country_lab ON (country_lab." . $db->quoteName('country_id') . " = country." . $db->quoteName('country_id') . " AND country_lab." . $db->quoteName('lab_id') . " = " . (int)$lab_id . ") ";
                }

                $db->setQuery($query);
                $country_data = $db->loadObject();

                if($country_data){
                    if(!$lang_id){
                        $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_country_lang') . " WHERE " . $db->quoteName('country_id') . " = " . (int)$country_id;

                        $db->setQuery($query);
                        $country_lang_data = $db->loadObjectList();
                        if($country_lang_data){
                            foreach($country_lang_data as $row){
                                foreach($row as $key => $value){
                                    if(array_key_exists($key, $this) && $key != 'country_id'){
                                        if(!isset($country_data->{$key}) || !is_array($country_data->{$key})){
                                            $country_data->{$key} = array();
                                        }
                                        $country_data->{$key}[$row->lang_id] = $value;
                                    }
                                }
                            }
                        }
                    }
                    JeprolabCache::store($cache_id, $country_data);
                }
            }else{
                $country_data = JeprolabCache::retrieve($cache_id);
            }

            if($country_data){
                $country_data->country_id = (int)$country_id;
                foreach($country_data as $key => $value){
                    if(array_key_exists($key, $this)){
                        $this->{$key} = $value;
                    }
                }
            }
        }
    }

    /**
     * @brief Return available countries
     *
     * @param integer $lang_id Language ID
     * @param boolean $published return only active countries
     * @param boolean $contain_states return only country with states
     * @param boolean $list_states Include the states list with the returned list
     *
     * @return Array Countries and corresponding zones
     */
    public static function getStaticCountries($lang_id, $published = false, $contain_states = false, $list_states = true) {
        $countries = array();
        $db = JFactory::getDBO();

        $query = "SELECT country_lang.*, country.*, country_lang." . $db->quoteName('name') . " AS country_name, zone." . $db->quoteName('name');
        $query .= " AS zone_name FROM " . $db->quoteName('#__jeprolab_country') . " AS country " . JeprolabLabModelLab::addSqlAssociation('country');
        $query .= "	LEFT JOIN " . $db->quoteName('#__jeprolab_country_lang') . " AS country_lang ON (country." . $db->quoteName('country_id') ;
        $query .= " = country_lang." . $db->quoteName('country_id') . " AND country_lang." . $db->quoteName('lang_id') . " = " .(int)$lang_id;
        $query .= ") LEFT JOIN " . $db->quoteName('#__jeprolab_zone') . " AS zone ON (zone." . $db->quoteName('zone_id') . " = country.";
        $query .= $db->quoteName('zone_id') . ") WHERE 1 " .($published ? " AND country.published = 1" : "") ;
        $query .= ($contain_states ? " AND country." . $db->quoteName('contains_states') . " = " .(int)$contain_states : "")." ORDER BY country_lang.name ASC";

        $db->setQuery($query);
        $result = $db->loadObjectList();
        foreach ($result as $row){ $countries[$row->country_id] = $row; }

        if ($list_states){
            $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_state') . " ORDER BY " . $db->quoteName('name') . " ASC";

            $db->setQuery($query);
            $result = $db->loadObjectList();
            foreach ($result as $row)
                if (isset($countries[$row->country_id]) && $row->published == 1) /* Does not keep the state if its country has been disabled and not selected */
                    $countries[$row->country_id]->states[] = $row;
        }
        return $countries;
    }

    public function isMultiLab(){
        return JeprolabLabModelLab::isTableAssociated('country') || !empty($this->multiLangLab);
    }

    public function getCountryList(JeprolabContext $context = NULL){
        jimport('joomla.html.pagination');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        if(!$context){ $context = JeprolabContext::getContext(); }

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limit_start = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
        $lab_id = $app->getUserStateFromRequest($option. $view. '.lab_id', 'lab_id', $context->lab->lab_id, 'int');
        $lab_group_id = $app->getUserStateFromRequest($option. $view. '.lab_group_id', 'lab_group_id', $context->lab->lab_group_id, 'int');
        $category_id = $app->getUserStateFromRequest($option. $view. '.cat_id', 'cat_id', 0, 'int');
        $order_by = $app->getUserStateFromRequest($option. $view. '.order_by', 'order_by', 'date_add', 'string');
        $order_way = $app->getUserStateFromRequest($option. $view. '.order_way', 'order_way', 'ASC', 'string');
        $published = $app->getUserStateFromRequest($option. $view. '.published', 'published', 0, 'string');
        $product_attribute_id = $app->getUserStateFromRequest($option. $view. '.product_attribute_id', 'product_attribute_id', 0, 'int');

        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        do{
            $query = "SELECT SQL_CALC_FOUND_ROWS country." . $db->quoteName('country_id') . ", country_lang." . $db->quoteName('name');
            $query .= " AS name, country." . $db->quoteName('iso_code') . ", country." . $db->quoteName('call_prefix') . ", country.";
            $query .= $db->quoteName('published') . ",zone." . $db->quoteName('zone_id'). " AS zone, zone." . $db->quoteName('name');
            $query .= " AS zone_name FROM " . $db->quoteName('#__jeprolab_country') . " AS country LEFT JOIN ";
            $query .= $db->quoteName('#__jeprolab_country_lang') . " AS country_lang ON (country_lang." . $db->quoteName('country_id');
            $query .= " = country." . $db->quoteName('country_id') . " AND country_lang." . $db->quoteName('lang_id') . " = " . $lang_id;
            $query .= ") LEFT JOIN " . $db->quoteName('#__jeprolab_zone') . " AS zone ON (zone." . $db->quoteName('zone_id') . " = country.";
            $query .= $db->quoteName('zone_id') .") WHERE 1 ORDER BY country." . $db->quoteName('country_id');

            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");

            $db->setQuery($query);
            $countries = $db->loadObjectList();

            if($use_limit == true){
                $limit_start = (int)$limit_start -(int)$limit;
                if($limit_start < 0){ break; }
            }else{ break; }
        }while(empty($countries));

        $this->pagination = new JPagination($total, $limit_start, $limit);
        return $countries;
    }

    public function getPagination(){
        return $this->pagination;
    }
}

class JeprolabStateModelState extends JModelLegacy
{
    public $state_id;

    /** @var integer Country id which state belongs */
    public $country_id;

    /** @var integer Zone id which state belongs */
    public $zone_id;

    /** @var string 2 letters iso code */
    public $iso_code;

    /** @var string Name */
    public $name;

    /** @var boolean Status for delivery */
    public $published = true;

    public function __construct($state_id = null){
        $db = JFactory::getDBO();
        /*
        if ($lang_id !== null)
            $this->lang_id = (JeprolabLanguageModelLanguage::getLanguage($lang_id) !== false) ? $lang_id : JeprolabSettingModelSetting::getValue('default_lang');
        
        if ($lab_id && $this->isMultilab()){
            $this->lab_id = (int)$lab_id;
            $this->get_lab_from_context = false;
        }
        
        if ($this->isMultilab() && !$this->lab_id){
            $this->lab_id = JeprolabContext::getContext()->lab->lab_id;
        } */

        if ($state_id){
            // Load object from database if object id is present
            $cache_id = 'jeprolab_model_state_'.(int)$state_id.'_'; //.(int)$this->lab_id . '_'.(int)$lang_id;
            if (!JeprolabCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_state') . " AS state WHERE state.state_id = " . (int)$state_id;


                /*/ Get lang informations
                if ($lang_id)
                {
                    $sql->leftJoin($this->def['table'].'_lang', 'b', 'a.'.$this->def['primary'].' = b.'.$this->def['primary'].' AND b.id_lang = '.(int)$id_lang);
                    if ($this->id_lab && !empty($this->def['multilang_lab']))
                        $sql->where('b.id_lab = '.$this->id_lab);
                }
        
                // Get lab informations
                if (Lab::isTableAssociated($this->def['table']))
                    $sql->leftJoin($this->def['table'].'_lab', 'c', 'a.'.$this->def['primary'].' = c.'.$this->def['primary'].' AND c.id_lab = '.(int)$this->id_lab);
                */

                $db->setQuery($query);
                $state_data = $db->loadObject();

                if ($state_data){
                    /*if (!$id_lang && isset($this->def['multilang']) && $this->def['multilang'])
                    {
                        $sql = 'SELECT * FROM `'.pSQL(_DB_PREFIX_.$this->def['table']).'_lang`
                                WHERE `'.bqSQL($this->def['primary']).'` = '.(int)$id
                                        .(($this->id_lab && $this->isLangMultilab()) ? ' AND `id_lab` = '.$this->id_lab : '');
                        if ($object_datas_lang = ObjectModel::$db->executeS($sql))
                            foreach ($object_datas_lang as $row)
                                foreach ($row as $key => $value)
                                {
                                    if (array_key_exists($key, $this) && $key != $this->def['primary'])
                                    {
                                        if (!isset($object_datas[$key]) || !is_array($object_datas[$key]))
                                            $object_datas[$key] = array();
                                        $object_datas[$key][$row['id_lang']] = $value;
                                    }
                                }
                    }*/
                    JeprolabCache::store($cache_id, $state_data);
                }
            }else{
                $state_data = JeprolabCache::retrieve($cache_id);
            }

            if ($state_data){
                $this->state_id = (int)$state_id;
                foreach ($state_data as $key => $value){
                    if (array_key_exists($key, $this)){
                        $this->{$key} = $value;
                    }
                }
            }
        }
    }

    /**
     * Get a state name with its ID
     *
     * @param integer $state_id Country ID
     * @return string State name
     */
    public static function getNameById($state_id){
        if (!$state_id)
            return false;
        $cache_id = 'jeprolab_state_get_name_by_id_'. (int)$state_id;
        if (!JeprolabCache::isStored($cache_id)) {
            $db = JFactory::getDBO();
            $query = "SELECT " . $db->quoteName('name') . "	FROM " . $db->quoteName('#__jeprolab_state') . " WHERE " . $db->quoteName('state_id') . "= " . (int)$state_id;

            $db->setQuery($query);
            $result = $db->loadResult();
            JeprolabCache::store($cache_id, $result);
        }
        return JeprolabCache::retrieve($cache_id);
    }

    public function isMultiLab(){
        return (JeprolabLabModelLab::isTableAssociated('state') || !empty($this->multiLangLab));
    }

    public function getStateList(JeprolabContext $context = NULL){
        jimport('joomla.html.pagination');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        if(!$context){ $context = JeprolabContext::getContext(); }
    }

    public static function getStates($lang_id = false, $published = false) {
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('state_id') . ",  " . $db->quoteName('country_id') . ", " . $db->quoteName('zone_id') . ", " . $db->quoteName('iso_code') . ", " .  $db->quoteName('name');
        $query .= ", " . $db->quote('published') . " FROM " . $db->quoteName('#__jeprolab_state') .($published ? " WHERE " . $db->quoteName('published') . " = 1" : "") . " ORDER BY " . $db->quoteName('name') . " ASC ";

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Get a state id with its name
     *
     * @param string $state_id Country ID
     * @return integer state id
     */
    public static function getIdByName($state_id)
    {
        if (empty($state))
            return false;
        $cache_id = 'State::getNameById_'.pSQL($state);
        if (!Cache::isStored($cache_id))
        {
            $result = (int)Db::getInstance()->getValue('
				SELECT `id_state`
				FROM `'._DB_PREFIX_.'state`
				WHERE `name` LIKE \''.pSQL($state).'\'
			');
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Get a state id with its iso code
     *
     * @param string $iso_code Iso code
     * @return integer state id
     */
    public static function getIdByIso($iso_code, $id_country = null)
    {
        return Db::getInstance()->getValue('
		SELECT `id_state`
		FROM `'._DB_PREFIX_.'state`
		WHERE `iso_code` = \''.pSQL($iso_code).'\'
		'.($id_country ? 'AND `id_country` = '.(int)$id_country : ''));
    }

    /**
     * Delete a state only if is not in use
     *
     * @return boolean
     */
    public function delete()
    {
        if (!$this->isUsed())
        {
            // Database deletion
            $result = Db::getInstance()->delete($this->def['table'], '`'.$this->def['primary'].'` = '.(int)$this->id);
            if (!$result)
                return false;

            // Database deletion for multilingual fields related to the object
            if (!empty($this->def['multilang']))
                Db::getInstance()->delete(bqSQL($this->def['table']).'_lang', '`'.$this->def['primary'].'` = '.(int)$this->id);
            return $result;
        }
        else
            return false;
    }

    /**
     * Check if a state is used
     *
     * @return boolean
     */
    public function isUsed()
    {
        return ($this->countUsed() > 0);
    }

    /**
     * Returns the number of utilisation of a state
     *
     * @return integer count for this state
     */
    public function countUsed()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'address`
			WHERE `'.$this->def['primary'].'` = '.(int)$this->id
        );
        return $result;
    }

    public static function getStatesByIdCountry($id_country)
    {
        if (empty($id_country))
            die(Tools::displayError());

        return Db::getInstance()->executeS('
        SELECT *
        FROM `'._DB_PREFIX_.'state` s
        WHERE s.`id_country` = '.(int)$id_country
        );
    }

    public static function hasCounties($id_state)
    {
        return count(County::getCounties((int)$id_state));
    }

    public static function getZoneId($id_state)
    {
        if (!Validate::isUnsignedId($id_state))
            die(Tools::displayError());

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_zone`
			FROM `'._DB_PREFIX_.'state`
			WHERE `id_state` = '.(int)$id_state
        );
    }

    /**
     * @param $ids_states
     * @param $id_zone
     * @return bool
     */
    public function affectZoneToSelection($ids_states, $id_zone)
    {
        // cast every array values to int (security)
        $ids_states = array_map('intval', $ids_states);
        return Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'state` SET `id_zone` = '.(int)$id_zone.' WHERE `id_state` IN ('.implode(',', $ids_states).')
		');
    }
}

class JeprolabZoneModelZone extends JModelLegacy
{
    public $zone_id;

    /** @var string Name */
    public $name;

    public $allow_delivery;

    private $pagination;

    public function __construct($zone_id = null){
        $db = JFactory::getDBO();

        if($zone_id){
            $cache_id =  'jeprolab_zone_model_' . (int)$zone_id;
            if(!JeprolabCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_zone') . " AS zone WHERE " . $db->quoteName('zone_id') . " = " . (int)$zone_id;
                $db->setQuery($query);
                $zoneData = $db->loadObject();
                JeprolabCache::store($cache_id, $zoneData);
            }else{
                $zoneData = JeprolabCache::retrieve($cache_id);
            }

            if($zoneData){
                $zoneData->zone_id = (int)$zone_id;
                foreach($zoneData as $key => $value){
                    $this->{$key} = $value;
                }
            }
        }
    }

    public function saveZone(){
        $db = JFactory::getDBO();

        $input_data = '';

        $query = "INSERT INTO " . $db->quoteName('#__jeprolab_zone') . "(";
    }

    public function getZoneList(JeprolabContext $context = NULL){
        jimport('joomla.html.pagination');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        if(!$context){ $context = JeprolabContext::getContext(); }

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limit_start = $app->getUserStateFromRequest($option. $view. '.limit_start', 'limit_start', 0, 'int');
        $lang_id = $app->getUserStateFromRequest($option. $view. '.lang_id', 'lang_id', $context->language->lang_id, 'int');
        $lab_id = $app->getUserStateFromRequest($option. $view. '.lab_id', 'lab_id', $context->lab->lab_id, 'int');
        $lab_group_id = $app->getUserStateFromRequest($option. $view. '.lab_group_id', 'lab_group_id', $context->lab->lab_group_id, 'int');
        $allow_delivery = $app->getUserStateFromRequest($option. $view. '.allow_delivery', 'allow_delivery', 0, 'int');

        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        do{
            $query = "SELECT SQL_CALC_FOUND_ROWS zone." .  $db->quoteName('zone_id') . ", zone." .  $db->quoteName('name');
            $query .= " AS zone_name, zone." .  $db->quoteName('allow_delivery') . " FROM " . $db->quoteName('#__jeprolab_zone');
            $query .= ($allow_delivery ? " WHERE zone.allow_delivery = 1 " : "");
            $query .= " AS zone ORDER BY " . $db->quoteName('name') . " ASC ";

            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");

            $db->setQuery($query);
            $zones = $db->loadObjectList();

            if($use_limit == true){
                $limit_start = (int)$limit_start -(int)$limit;
                if($limit_start < 0){ break; }
            }else{ break; }
        }while(empty($zones));

        $this->pagination = new JPagination($total, $limit_start, $limit);
        return $zones;
    }

    public function getPagination(){
        return $this->pagination;
    }

    /**
     * Get all available geographical zones
     *
     * @param bool|type $allow_delivery
     * @return type
     */
    public static function getZones($allow_delivery = FALSE){
        $cache_id = 'jeprolab_zone_model_get_zones_' . (bool)$allow_delivery;
        if(!JeprolabCache::isStored($cache_id)) {
            $db = JFactory::getDBO();

            $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_zone') . ($allow_delivery ? " WHERE allow_delivery = 1 " : "");
            $query .= " ORDER BY " . $db->quoteName('name') . " ASC ";

            $db->setQuery($query);
            $result = $db->loadObjectList();
            JeprolabCache::store($cache_id, $result);
        }
        return JeprolabCache::retrieve($cache_id);
    }

    /**
     * Get a zone ID from its default language name
     *
     * @param string $name
     * @return integer id_zone
     */
    public static function getIdByName($name){
        $db = JFactory::getDBO();

        $query = "SELECT " . $db->quoteName('zone_id') . " FROM " . $db->quoteName('#__jeprolab_zone') . " WHERE " . $db->quoteName('name') . " = " . $db->quote($name);

        $db->setQuery($query);
        return $db->loadResult();
    }

    /**
     * Delete a zone
     *
     * @return boolean Deletion result
     */
    public function delete() {
        $db = JFactory::getDBO();
        if (parent::delete()) {
            // Delete regarding delivery preferences
            $query = "DELETE FROM " . $db->quoteName('#__jeprolab_carrier_zone') . " WHERE " . $db->quoteName('zone_id') . " = " .(int)$this->zone_id;
            $db->setQuery($query);
            $result = $db->query();
            $query = "DELETE FROM " . $db->quoteName('#__jeprolab_delivery') . " WHERE " . $db->quoteName('zone_id') . " = " . (int)$this->zone_id;
            $db->setQuery($query);
            $result &= $db->query();

            // Update Country & state zone with 0
            $query = "UPDATE " . $db->quoteName('#__jeprolab_country') . " SET " . $db->quoteName('zone_id') . " = 0 WHERE " . $db->quoteName('zone_id'). " = " . (int)$this->zone_id;
            $db->setQuery($query);
            $result &= $db->query();
            $query = "UPDATE " . $db->quoteName('#__jeprolab_state') ." SET " . $db->quoteName('zone_id') . " = 0 WHERE " . $db->quoteName('zone_id') . " = " . (int)$this->zone_id;
            $db->setQuery($query);
            $result &= $db->query();

            return $result;
        }

        return false;
    }
}