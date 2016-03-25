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

class JeprolabFeedModelFeed extends JModelLegacy
{
    public $feed_id;
    public $lang_id;

    public $feed_title;
    public $feed_link;
    public $feed_description;
    public $feed_author;

    public function __construct($feed_id = null, $lang_id = null){
        if($lang_id !== null){
            $this->lang_id = (JeprolabLanguageModelLanguage::getLanguage($lang_id) != false) ? $lang_id : JeprolabSettingModelSetting::getValue('default_lang');
        }

        $db = JFactory::getDBO();
        if($feed_id){
            $cache_id = 'jeprolab_feed_model_' . $feed_id . '_' . $this->lang_id;
            if(!JeprolabCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_feeds') . " AS feed WHERE feed." . $db->quoteName('feed_id') . " = " . (int)$feed_id;
                if($lang_id){
                    $query .= " LEFT JOIN " . $db->quoteName('#__jeprolab_feeds_lang') . " AS feed_lang ON (feed_lang" . $db->quoteName('feed_id') . " = feed.";
                    $query .= $db->quoteName('feed_id')  . " AND feed_lang." . $db->quoteName('lang_id') . " = " . (int)$lang_id . ")";
                }

                $db->setQuery($query);
                $feedData = $db->loadObject();

                if($feedData){
                    if(!$lang_id){
                        $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_feeds_lang') . " WHERE " . $db->quoteName('feed_id');
                        $query .= " = " . (int)$feed_id ;

                        $db->setQuery($query);
                        $feedDataLang = $db->loadObjectList();

                        if($feedDataLang){
                            foreach($feedDataLang as $row){
                                foreach($row as $key => $value){
                                    if(array_key_exists($key, $this) && $key != 'feed_id'){
                                        if (!isset($feedData->{$key}) || !is_array($feedData->{$key}))
                                            $feedData->{$key} = array();
                                        $feedData->{$key}[$row->lang_id] = $value;
                                    }
                                }
                            }
                        }
                    }
                }
            }else{
                $feedData = JeprolabCache::retrieve($cache_id);
            }

            if($feedData){
                $this->feed_id = (int)$feed_id;
                foreach($feedData as $key => $value){
                    if(array_key_exists($key, $this)) $this->{$key} = $value;
                }
            }
        }
    }

    public function saveFeed(){
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();
        $languages = JeprolabLanguageModelLanguage::getLanguages();
        $input = JRequest::get('post');
        $input_data = $input['jform'];
        foreach($languages as $language) {
            $query = "INSERT INTO " . $db->quoteName('#__jeprolab_feeds') . "(" . $db->quoteName('lang_id') . ", ";
            $query .= $db->quoteName('feed_title') . ", " . $db->quoteName('feed_link') . ", " . $db->quoteName('feed_description') . ", ";
            $query .= $db->quoteName('date_add') . ", " . $db->quoteName('feed_author') . ") VALUES (" . (int)$language->lang_id . ", ";
            $query .= $db->quote($db->escape($input_data['feed_title_' . $language->lang_id])) . ", " . $db->quote($db->escape($input_data['feed_link'])) . ", ";
            $query .= $db->quote($db->escape($input_data['feed_description_' . $language->lang_id])) . ", NOW(), " . $db->quote($db->escape($input_data['feed_author'])) . ") ";

            $db->setQuery($query);
            if (!$db->query()) {
                return false;
            }
        }
        return true;
    }

    public function getFeedsList(JeprolabContext $context = NULL)
    {
        jimport('joomla.html.pagination');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        if (!$context) {
            $context = JeprolabContext::getContext();
        }

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limit_start = $app->getUserStateFromRequest($option . $view . '.limit_start', 'limit_start', 0, 'int');
        $order_by = $app->getUserStateFromRequest($option . $view . '.order_by', 'order_by', 'feed_id', 'string');
        $order_way = $app->getUserStateFromRequest($option . $view . '.order_way', 'order_way', 'DESC', 'string');
        $lang_id = $app->getUserStateFromRequest($option . $view . '.lang_id', 'lang_id', $context->language->lang_id, 'int');

        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        do {
            $query = "SELECT SQL_CALC_FOUND_ROWS * FROM " . $db->quoteName('#__jeprolab_feeds') . " AS feed WHERE feed." . $db->quoteName('lang_id') . " = " . (int)$lang_id;

            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= " ORDER BY " . ((str_replace('`', '', $order_by) == 'feed_id') ? "feed." : "") . $order_by . " " . $order_way;
            $query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");

            $db->setQuery($query);
            $feeds = $db->loadObjectList();

            if($use_limit == true){
                $limit_start = (int)$limit_start -(int)$limit;
                if($limit_start < 0){ break; }
            }else{ break; }
        } while (empty($feeds));

        $this->pagination = new JPagination($total, $limit_start, $limit);
        return $feeds;
    }
}