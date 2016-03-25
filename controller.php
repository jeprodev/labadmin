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

class JeprolabController extends JControllerLegacy
{
    public $use_ajax = true;

    public $default_form_language;

    public $allow_employee_form_language;

    public $allow_link_rewrite;

    public $multilab_context = -1;

    public static $_current_index;
    protected static $_initialized = false;

    public function display($cachable = FALSE, $urlParams = FALSE){
        $view = $this->input->get('view', 'dashboard');
        $layout = $this->input->get('layout', 'default');
        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        $viewClass->setLayout($layout);
        $viewClass->display();
    }

    public function catalogs(){
        $app = JFactory::getApplication();
        $app->redirect('index.php?option=com_jeprolab&view=analyze');
    }

    public function orders(){
        $app = JFactory::getApplication();
        $app->redirect('index.php?option=com_jeprolab&view=order');
    }

    public function customers(){
        $app = JFactory::getApplication();
        $app->redirect('index.php?option=com_jeprolab&view=customer');
    }

    public function initialize(){
        if(self::$_initialized){ return; }
        $app = JFactory::getApplication();
        $context = JeprolabContext::getContext();

        if($app->input->get('use_ajax')){
            $this->use_ajax = true;
        }

        /* Server Params */
        //$protocol_link = (JeprolabTools::usingSecureMode() && JeprolabSettingModelSetting::getValue('enable_ssl')) ? 'https://' : 'http://';
        //$protocol_content = (JeprolabTools::usingSecureMode() && JeprolabSettingModelSetting::getValue('enable_ssl')) ? 'https://' : 'http://';

        if (isset($_GET['logout'])){ $context->employee->logout(); }

        if (isset(JeprolabContext::getContext()->cookie->last_activity)){
            if ($context->cookie->last_activity + 900 < time())
                $context->employee->logout();
            else
                $context->cookie->last_activity = time();
        }

        $controllerName = $app->input->get('view');
        if ($controllerName != 'authenticate' && (!isset($context->employee) || !$context->employee->isLoggedBack())){
            if (isset($context->employee)) {
                $context->employee->logout();
            }
            $email = false;
            if ($app->input->get('email') && JeprolabTools::isEmail($app->inpt->get('email'))){ $email = $app->input->get('email'); }

            //$app->redirect($this->getAdminLink('AdminLogin').((!isset($_GET['logout']) && $controllerName != 'AdminNotFound' && $app->input->get('view')) ? '&redirect=' . $controllerName : '').($email ? '&email='.$email : ''));
        }

        $current_index = 'index.php?option=com_jeproshop' . (($controllerName) ? 'view=' . $controllerName : '');
        if($app->input->get('return')){ $current_index .= '&return=' . urlencode($app->input->get('return')); }
        self::$_current_index = $current_index;
        if($this->use_ajax && method_exists($this, 'ajaxPreProcess')){ $this->ajaxPreProcess(); }

        self::$_initialized = true;

        //$this->initProcess();
    }

    public function initContent(){
        if(!$this->viewAccess()){
            JError::raiseWarning(500, JText::_('COM_JEPROSHOP_YOU_DO_NOT_HAVE_PERMISSION_TO_VIEW_THIS_PAGE_MESSAGE'));
        }

        $this->getLanguages();
        $app = JFactory::getApplication();

        $task = $app->input->get('task');
        $view = $app->input->get('view');
        $viewClass = $this->getView($view, JFactory::getDocument()->getType());

        if($task == 'edit'){
            if(!$viewClass->loadObject(true)){ return; }
            $viewClass->setLayout('edit');
            $viewClass->renderEditForm();
        }elseif($task == 'add'){
            $viewClass->setLayout('add');
            $viewClass->renderAddForm();
        }elseif($task == 'view'){
            $viewClass->setLayout('view');
            $viewClass->renderView();
        }elseif($task == 'display' || $task  == ''){
            $viewClass->renderDetails();
        }elseif(!$this->use_ajax){

        }else{
            $this->execute($task);
        }
    }

    public function getLanguages(){
        $cookie = JeprolabContext::getContext()->cookie;
        $this->allow_employee_form_language = (int)JeprolabSettingModelSetting::getValue('allow_employee_form_lang');
        if($this->allow_employee_form_language && !$cookie->employee_form_lang){
            $cookie->employee_form_lang = (int)JeprolabSettingModelSetting::getValue('default_lang');
        }

        $lang_exists = false;
        $languages = JeprolabLanguageModelLanguage::getLanguages(false);
        foreach($languages as $language){
            if(isset($cookie->employee_form_language) && $cookie->employee_form_language == $language->lang_id){
                $lang_exists = true;
            }
        }

        $this->default_form_language = $lang_exists ? (int)$cookie->employee_form_language : (int)JeprolabSettingModelSetting::getValue('default_lang');

        return $languages;
    }

    function viewAccess($disabled = false){
        if($disabled){ return true; }
        return true;
    }
}