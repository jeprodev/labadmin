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

class JeproLabFeedBackModelFeedBack extends JModelLegacy
{
    public $feedback_id;

    public $enjoy_working_with_us;

    public  $staff_courtesy;

    public $team_abilities;

    public $team_availability;

    public $problem_support;

    public $general_comment;

    public $online_services;

    public $global_quality;

    public $analyze_speed;

    public $submission;

    public $sample_delivery_speed;

    public $service_speed;

    public $recommend_our_services;

    public $reuse_our_services;

    public $help_us_improve_our_service;

    public $how_do_you_learn_about_us;

    public $service_comment_or_suggestion;

    public $customer_name;

    public $customer_phone;

    public $customer_email;

    public $customer_company;

    public $reports_quality;

    private $pagination;

    public function __construct($feedback_id = null){
        $db = JFactory::getDBO();
        if($feedback_id){
            $cache_id = 'jeprolab_feedback_model_' . $feedback_id;
            if(!JeprolabCache::isStored($cache_id)){
                $query = "SELECT * FROM " . $db->quoteName('#__jeprolab_feedback') . " AS feedback WHERE feedback." . $db->quoteName('feedback_id') . " = " . (int)$feedback_id;

                $db->setQuery($query);
                $feedBackData = $db->loadObject();

                if($feedBackData){
                    JeprolabCache::store($cache_id, $feedBackData);
                }
            }else{
                $feedBackData = JeprolabCache::retrieve($cache_id);
            }

            if($feedBackData){
                $this->feedback_id = (int)$feedback_id;
                foreach($feedBackData as $key => $value){
                    if(array_key_exists($key, $this)) $this->{$key} = $value;
                }
            }
        }
    }

    public function getFeedsBack(JeproLabContext $context = null){
        jimport('joomla.html.pagination');
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        $feedsBack = null;

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limit_start = $app->getUserStateFromRequest($option. $view. '.limitstart', 'limitstart', 0, 'int');

        /* Manage default params values */
        $use_limit = true;
        if ($limit === false)
            $use_limit = false;

        do{
            $query = "SELECT feedback.feedback_id, feedback.request_service_id, feedback.enjoy_working_with_us, feedback.staff_courtesy, feedback.team_abilities, ";
            $query .= "feedback.problem_support, feedback.team_availability, feedback.reuse_our_services, feedback.recommend_our_services, feedback.services_speed, ";
            $query .= "feedback.sample_delivery_speed, feedback.submission, feedback.reports_quality, feedback.analyze_speed, feedback.online_services, feedback.";
            $query .= "customer_id, feedback.global_quality, CONCAT(customer.firstname, ' ', customer.lastname) AS customer_name FROM " . $db->quoteName('#__jeprolab_feedback') . " AS feedback LEFT JOIN ";
            $query .= $db->quoteName('#__jeprolab_customer') . " AS customer ON (customer.customer_id = feedback.customer_id)";

            $db->setQuery($query);
            $total = count($db->loadObjectList());

            $query .= (($use_limit === true) ? " LIMIT " .(int)$limit_start . ", " .(int)$limit : "");

            $db->setQuery($query);
            $feedsBack = $db->loadObjectList();
            if($use_limit == true){
                $limit_start = (int)$limit_start - (int)$limit;
                if($limit_start < 0){ break; }
            }else{ break; }
        }while(empty($feedsBack));

        $this->pagination = new JPagination($total, $limit_start, $limit);
        return $feedsBack;
    }

    public function getCustomer(){
        return "";
    }

    public function getPagination(){
        return $this->pagination;
    }
}