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


class JeprolabCurrencyModelCurrency extends  JModelLegacy
{
    public $currency_id;

    /** @var string name */
    public $name;

    /** @var string Iso code */
    public $iso_code;

    /** @var  string Iso code numeric */
    public $iso_code_num;

    /** @var string symbol for short display */
    public $sign;

    /** @var int bool used for displaying blank between sign and price */
    public $blank;

    /**
     * contains the sign to display before price, according to its format
     * @var string
     */
    public $prefix;

    /**
     * contains the sign to display after price, according to its format
     * @var string
     */
    public $suffix;

    /** @var double conversion rate  */
    public $conversion_rate;

    /** @var int ID used for displaying prices */
    public $format;

    /** @var boolean True if currency has been deleted(staying in database as deleted) */
    public $deleted;

    /** @var int bool Display decimals on prices */
    public $decimals;

    /** @var int bool published  */
    public $published;

    public $lab_id;

    static protected $currencies = array();


    public function __construct($currency_id = null, $lab_id = null){
        $db = JFactory::getDBO();


        if($lab_id && $this->isMultiLab()){
            $this->lab_id = (int)$lab_id;
            $this->get_lab_from_context = false;
        }

        if($this->isMultiLab() && !$this->lab_id){
            $this->lab_id = JeprolabContext::getContext()->lab->lab_id;
        }

        if($currency_id){
            //load object from the  database if the currency id is provided
            $cache_id = 'jeprolab_currency_model_' . (int)$currency_id . '_' . (int)$lab_id;
            if(!JeprolabCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_currency') . " AS currency ";

                if(JeprolabLabModelLab::isTableAssociated('currency')){
                    $query .= " LEFT JOIN " . $db->quoteName('#__jeprolab_currency_lab') ." AS currency_lab ON( currency.currency_id = currency_lab.currency_id AND currency_lab.lab_id = " . (int)$this->lab_id . ")";
                }
                $query .= " WHERE currency.currency_id = " . (int)$currency_id ;

                $db->setQuery($query);
                $currency_data = $db->loadObject();
                if($currency_data){
                    JeprolabCache::store($cache_id, $currency_data);
                }
            }else{
                $currency_data = JeprolabCache::retrieve($cache_id);
            }

            if($currency_data){
                $currency_data->currency_id = $currency_id;
                foreach($currency_data as $key => $value){
                    if(array_key_exists($key, $this)){
                        $this->{$key} = $value;
                    }
                }
            }
        }

        /* prefix and suffix are convenient short cut for displaying price sign before or after the price number */
        $this->prefix = $this->format % 2 != 0 ? $this->sign . " " : "";
        $this->suffix = $this->format % 2 == 0 ? " " . $this->sign : "";
    }

    public function isMultiLab(){
        return JeprolabLabModelLab::isTableAssociated('currency') || !empty($this->multiLangLab);
    }

    /**
     * Return available currencies
     *
     * @param bool $object
     * @param bool $published
     * @param bool $group_by
     * @return array Currencies
     */
    public static function getStaticCurrencies($object = false, $published = true, $group_by = false) {
        $db = JFactory::getDBO();

        $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_currency') . " AS currency " . JeprolabLabModelLab::addSqlAssociation('currency');
        $query .= " WHERE " . $db->quoteName('deleted') . " = 0" . ($published ? " AND currency." . $db->quoteName('published') . " = 1" : "");
        $query .= ($group_by ? " GROUP BY currency." . $db->quoteName('currency_id') : ""). " ORDER BY " . $db->quoteName('name') . " ASC";

        $db->setQuery($query);
        $tab = $db->loadObjectList();
        if ($object){
            foreach ($tab as $key => $currency)
                $tab[$key] = JeprolabCurrencyModelCurrency::getCurrencyInstance($currency->currency_id);
        }
        return $tab;
    }

    public static function getCurrencyInstance($currency_id){
        if (!isset(self::$currencies[$currency_id])){
            self::$currencies[(int)($currency_id)] = new JeprolabCurrencyModelCurrency($currency_id);
        }
        return self::$currencies[(int)($currency_id)];
    }
}