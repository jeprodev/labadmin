<?php
/**
 * @version         1.0.3
 * @package         components
 * @sub package     com_jeprolab
 * @link            http://jeprodev.net
 *
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

class JeprolabSettingModelSetting extends JModelLegacy
{
    public $setting_id;

    /** @var string value **/
    public $value;

    /** @var string Object creation date Description **/
    public $date_add;

    /** @var string Object last modification date Description **/
    public $date_upd;

    /** @var array Setting cache **/
    protected static $_SETTINGS;

    /** @var array Vars types **/
    protected static $types = array();

    /**
     * Load all setting data
     */
    public static function loadSettings(){
        self::$_SETTINGS = array();

        $db = JFactory::getDBO();

        $query = "SELECT setting." . $db->quoteName('name') . ", setting." . $db->quoteName('value') . " FROM " . $db->quoteName('#__jeprolab_setting') . " AS setting";

        $db->setQuery($query);
        if(!$settings = $db->loadObjectList()){ return; }

        foreach($settings as $setting){
            if(!isset(self::$_SETTINGS)){
                self::$_SETTINGS = array('global' => array(), 'group' => array(), 'lab' => array());
            }

            if(isset($setting->lab_id)){
                self::$_SETTINGS['lab'][$setting->lab_id][$setting->name] = $setting->value;
            }elseif(isset($setting->lab_group_id)){
                self::$_SETTINGS['group'][$setting->lab_group_id][$setting->name] = $setting->value;
            }else{
                self::$_SETTINGS['global'][$setting->name] = $setting->value ;
            }
        }
    }

    public static function getValue($key, $lab_group_id = NULL, $lab_id = NULL){
        /** If setting is not initialized, try manual query **/
        if(!self::$_SETTINGS){
            JeprolabSettingModelSetting::loadSettings();

            if(!self::$_SETTINGS){
                $db = JFactory::getDBO();
                $query = "SELECT " . $db->quoteName('value') . " FROM " . $db->quoteName('#__jeprolab_setting');
                $query .= " WHERE " . $db->quoteName('name') . " = " . $db->quote($db->escape($key));

                $db->setQuery($query);
                $settingValue = $db->loadResult();
                return ($settingValue ? $settingValue : $key);
            }
        }

        if($lab_id && JeprolabSettingModelSetting::hasKey($key, NULL, $lab_id)){
            return self::$_SETTINGS['lab'][$lab_id][$key];
        }elseif($lab_group_id && JeprolabSettingModelSetting::hasKey($key)){
            return self::$_SETTINGS['group'][$lab_group_id][$key];
        }elseif(JeprolabSettingModelSetting::hasKey($key)){
            return self::$_SETTINGS['global'][$key];
        }else {     echo $key;     exit();  }
        return FALSE;
    }

    public function getSettingsByGroup($group){
        $db = JFactory::getDBO();

        $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_setting') . " WHERE " . $db->quoteName('setting_group');
        $query .= " = " . $db->quote(htmlentities($group));

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     *
     * @param String $key the setting key to retrieve data
     * @param null $lab_group_id
     * @param null $lab_id
     * @internal param int $lang_id
     * @return boolean
     */
    public static function hasKey($key, $lab_group_id = NULL, $lab_id = NULL){
        if($lab_id){
            return isset(self::$_SETTINGS['lab'][$lab_id]) && array_key_exists($key, self::$_SETTINGS['lab'][$lab_id]);
        }elseif($lab_group_id){
            return isset(self::$_SETTINGS['group'][$lab_group_id]) && array_key_exists($key, self::$_SETTINGS['group'][$lab_group_id]);
        }
        return isset(self::$_SETTINGS['global']) && array_key_exists($key, self::$_SETTINGS['global']);
    }
}