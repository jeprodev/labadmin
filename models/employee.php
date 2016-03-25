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


class JeprolabEmployeeModelEmployee extends  JModelLegacy
{
    public $employee_id;

    public $customer_id;

    /** @var string Lastname */
    public $lastname;

    /** @var string Firstname */
    public $firstname;

    /** @var string e-mail */
    public $email;

    /** @var string Password */
    public $passwd;

    public $profile_id;

    public $lang_id;

    public $shop_id;

    public $theme = 'default';

    public $stats_date_from;
    public $stats_date_to;

    /** @var datetime Password **/
    public $last_passwd_gen;
    public $stats_compare_from;
    public $stats_compare_to;
    public $stats_compare_option = 1;

    public $preselect_date_range;

    protected $associated_shops = array();

    public function __construct($employee_id = NULL, $lang_id = NULL, $shop_id = null) {

    }

    /**
     * Check employee informations saved into cookie and return employee validity
     *
     * @return boolean employee validity
     */
    public function isLoggedBack(){
        if (!JeprolabCache::isStored('jeprolab_is_logged_back_' . $this->employee_id)) {
            /* Employee is valid only if it can be load and if cookie password is the same as database one */
            JeprolabCache::store('jeprolab_is_logged_back_'.$this->employee_id, (
                $this->employee_id && JeprolabValidator::isUnsignedInt($this->employee_id) && JeprolabEmployeeModelEmployee::checkPassword($this->employee_id, JeprolabContext::getContext()->cookie->passwd)
                && (!isset(JeprolabContext::getContext()->cookie->remote_addr) || JeprolabContext::getContext()->cookie->remote_addr == ip2long(JeprolabValidator::getRemoteAddr()) || !JeprolabSettingModelSetting::getValue('cookie_check_ip'))
            ));
        }
        return JeprolabCache::retrieve('jeprolab_is_logged_back_' . $this->employee_id);
    }

    /**
     * Logout
     */
    public function logout(){
        if (isset(JeprolabContext::getContext()->cookie)) {
            JeprolabContext::getContext()->cookie->logout();
            JeprolabContext::getContext()->cookie->write();
        }
        $this->employee_id = null;
    }

    /**
     * Check if employee password is the right one
     *
     * @param int $employee_id
     * @param string $passwd Password
     * @return boolean result
     */
}