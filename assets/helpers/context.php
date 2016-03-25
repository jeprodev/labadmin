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


class JeprolabContext
{
    /** @var JeprolabContext **/
    protected static $instance;

    public $controller;

    /** @var JeprolabCartModelCart Description **/
    public $cart;

    /** @var JeprolabCustomerModelCustomer Description **/
    public $customer;

    /** @var JeprolabCountryModelCountry Description **/
    public $country;

    /** @var JeprolabEmployeeModelEmployee Description **/
    public $employee;

    /** @var JeprolabLanguageModelLanguage Description **/
    public $language;

    /** @var JeprolabCurrencyModelCurrency Description **/
    public $currency;

    /** @var JeprolabLabModelLab Description **/
    public $lab;

    /** @var Mobile_Detect Description **/
    public $mobile_detect;

    /** @var boolean Description **/
    public $mobile_device;
	
	public $is_phone = false;

    /** @var JeprolabCookie Description **/
    public $cookie;

    public function getMobileDetect(){
        if ($this->mobile_detect === null){
            require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Mobile_Detect.php');
            $this->mobile_detect = new Mobile_Detect();
        }
        return $this->mobile_detect;
    }

    public function getMobileDevice(){
        if ($this->mobile_device === null){
            $this->mobile_device = false;
            if ($this->checkMobileContext()){
                if (isset(JeprolabContext::getContext()->cookie->no_mobile) && JeprolabContext::getContext()->cookie->no_mobile == false AND (int)  JeprolabSettingModelSetting::getValue('allow_mobile_device') != 0){
                    $this->mobile_device = true;
                }else{
                    $mobile_detect = $this->getMobileDetect();
                    switch ((int)JeprolabSettingModelSetting::getValue('allow_mobile_device')){
                        case 1: // Only for mobile device
                            if ($mobile_detect->isMobile() && !$mobile_detect->isTablet()){
                                $this->mobile_device = true;
                            }
                            break;
                        case 2: // Only for touch pads
                            if ($mobile_detect->isTablet() && !$mobile_detect->isMobile()){
                                $this->mobile_device = true;
                            }
                            break;
                        case 3: // For touch pad or mobile devices
                            if ($mobile_detect->isMobile() || $mobile_detect->isTablet()){
                                $this->mobile_device = true;
                            }
                            break;
                    }
                }
            }
        }
        return $this->mobile_device;
    }

    public function getDevice(){
        static $device = null;

        if ($device === null){
            $mobile_detect = $this->getMobileDetect();
            if ($mobile_detect->isTablet()){
                $device = JeprolabContext::DEVICE_TABLET;
            }elseif ($mobile_detect->isMobile()){
                $device = JeprolabContext::DEVICE_MOBILE;
            }else{
                $device = JeprolabContext::DEVICE_COMPUTER;
            }
        }
        return $device;
    }

    protected function checkMobileContext(){
        // Check mobile context
        $app = JFactory::getApplication();
        if ($app->input->get('no_mobile_theme')){
            JeprolabContext::getContext()->cookie->no_mobile = true;
            if (JeprolabContext::getContext()->cookie->guest_id){
                $guest = new JeprolabGuestModelGuest(JeprolabContext::getContext()->cookie->guest_id);
                $guest->mobile_theme = false;
                $guest->update();
            }
        }elseif ($app->input->get('mobile_theme_ok')){
            JeprolabContext::getContext()->cookie->no_mobile = false;
            if (JeprolabContext::getContext()->cookie->guest_id){
                $guest = new JeprolabGuestModelGuest(JeprolabContext::getContext()->cookie->guest_id);
                $guest->mobile_theme = true;
                $guest->update();
            }
        }

        return isset($_SERVER['HTTP_USER_AGENT']) && isset(JeprolabContext::getContext()->cookie)
            && (bool)  JeprolabSettingModelSetting::getValue('allow_mobile_device')
            && !JeprolabContext::getContext()->cookie->no_mobile;
    }

    /**
     * Get a singleton JeprolabContext
     * @return JeprolabContext
     */
    public static function getContext(){
        if(!isset(self::$instance)){
            self::$instance = new JeprolabContext();
        }
        return self::$instance;
    }

    /**
     * Clone current context
     *
     * @return JeprolabContext
     */
    public function cloneContext(){
        return clone($this);
    }
}