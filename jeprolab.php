<?php
/**
 * @version         1.0.3
 * @package         components
 * @sub package     com_jeproshop
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

if(!file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'load.php')){
    JError::raiseError(500, JText::_(''));
}
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets' .  DIRECTORY_SEPARATOR . 'load.php';

$context = JeprolabContext::getContext();

/** initialize the shop **/
$context->lab = JeprolabLabModelLab::initialize();

/** load configuration  */
JeprolabSettingModelSetting::loadSettings();


/** load languages  */
JeprolabLanguageModelLanguage::loadLanguages();

/** set context cookie */
$life_time = time() + (max(JeprolabSettingModelSetting::getValue('bo_life_time'), 1) * 3600);
$context->cookie = new JeprolabCookie('jeprolab_admin', '', $life_time);

/** @var  JeprolabEmployeeModelEmployee */
$context->employee = new JeprolabEmployeeModelEmployee(JFactory::getUser()->id);
$context->cookie->employee_id = $context->employee->employee_id;

/** Loading default country */
$context->country = new JeprolabCountryModelCountry(JeprolabSettingModelSetting::getValue('default_country'), JeprolabSettingModelSetting::getValue('default_lang'));

/** if the cookie stored language is not an available language, use default language */
if(isset($context->cookie->lang_id) && $context->cookie->lang_id){
    $language = new JeprolabLanguageModelLanguage($context->cookie->lang_id);
}

if(!isset($language) || !JeprolabTools::isLoadedObject($language, 'lang_id')){
    $language = new JeprolabLanguageModelLanguage(JeprolabSettingModelSetting::getValue('default_lang'));
}

$context->language = $language;


$currency_id = ($context->cookie->currency_id ) ? $context->cookie->currency_id : JeprolabSettingModelSetting::getValue('default_currency');
$context->currency = new JeprolabCurrencyModelCurrency($currency_id);

/** controller and redirection */
$controller = JFactory::getApplication()->input->get('view');
if($controller == 'orders'){ $controller = 'order'; }
if($controller) {
    if(file_exists(dirname(__FILE__). DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controller . '.php')){
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'controller.php';
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controller . '.php';
    }else{
        $controller = '';
    }
    $context->controller = JControllerLegacy::getInstance('Jeprolab' . $controller);
    $context->controller->initialize();
    $context->controller->initContent();
}else{
    $context->controller = JControllerLegacy::getInstance('Jeprolab' . $controller);
    $task = JFactory::getApplication()->input->get('task') != '' ? JFactory::getApplication()->input->get('task') : 'display';
    $context->controller->execute($task);
    $context->controller->redirect();
}
