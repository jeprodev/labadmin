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

class JeprolabCountryViewCountry extends JViewLegacy
{
    public $context;

    protected $helper;

    protected $country;
    protected $countries;
    protected $zone;
    protected $state;

    protected $zones;
    protected $states;

    protected $pagination = null;

    public function renderDetails($tpl = NULL){
        $countryModel = new JeprolabCountryModelCountry();
        $this->countries = $countryModel->getCountryList();
        $zones = JeprolabZoneModelZone::getZones();
        $this->assignRef('zones', $zones);
        $this->pagination = $countryModel->getPagination();
        if($this->getLayout() != 'modal'){
            $this->addToolBar();
            $this->sideBar = JHtmlSidebar::render();
        }
        parent::display($tpl);
    }

    public function viewZones($tpl = null){
        $zoneModel = new JeprolabZoneModelZone();
        $this->zones = $zoneModel->getZoneList();

        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    private function addToolBar(){
        switch($this->getLayout()){
            case 'add' :
                JToolbarHelper::title(JText::_('COM_JEPROLAB_ADD_NEW_COUNTRY_LABEL'), 'country-jeprolab');
                JToolbarHelper::apply('save', JText::_('COM_JEPROLAB_SAVE_LABEL'));
                JToolbarHelper::cancel('cancel');
                break;
            case 'states':
                JToolbarHelper::title(JText::_('COM_JEPROLAB_STATES_LIST_TITLE'), 'country-jeprolab');
                JToolbarHelper::addNew('add_state');
                JToolbarHelper::editList('edit_state');
                JToolbarHelper::publish('publish_state');
                JToolbarHelper::unpublish('unpublish_state');
                JToolbarHelper::trash('trash_state');
                break;
            case 'add_state' :
                JToolbarHelper::title(JText::_('COM_JEPROLAB_ADD_STATE_TITLE'), 'country-jeprolab');
                JToolbarHelper::apply('save_state');
                break;
            case 'edit_state' :
                JToolbarHelper::title(JText::_('COM_JEPROLAB_ADD_STATE_TITLE'), 'country-jeprolab');
                JToolbarHelper::apply('update_state', JText::_('COM_JEPROLAB_UPDATE_LABEL'));
                break;
            case 'zones' :
                JToolbarHelper::title(JText::_('COM_JEPROLAB_ZONES_LIST_TITLE'), 'country-jeprolab');
                JToolbarHelper::addNew('add_zone');
                JToolbarHelper::editList('edit_zone');
                JToolbarHelper::publish('publish_zone');
                JToolbarHelper::unpublish('unpublish_zone');
                JToolbarHelper::trash('trash_zone');
                break;
            case 'edit_zone' :
                JToolbarHelper::title(JText::_('COM_JEPROLAB_EDIT_ZONE_TITLE'), 'country-jeprolab');
                JToolbarHelper::apply('update_zone', JText::_('COM_JEPROLAB_UPDATE_LABEL'));
                JToolbarHelper::cancel('cancel');
                break;
            case 'add_zone' :
                JToolbarHelper::title(JText::_('COM_JEPROLAB_ADD_NEW_ZONE_TITLE'), 'country-jeprolab');
                JToolbarHelper::apply('save_zone');
                JToolbarHelper::cancel('cancel');
                break;
            case 'edit' :
                JToolbarHelper::title(JText::_('COM_JEPROLAB_EDIT_COUNTRY_TITLE'), 'country-jeprolab');
                JToolbarHelper::apply('update', JText::_('COM_JEPROLAB_UPDATE_LABEL'));
                JToolbarHelper::cancel('cancel');
                break;
            default:
                JToolbarHelper::title(JText::_('COM_JEPROLAB_COUNTRIES_LIST_TITLE'), 'country-jeprolab');
                JToolbarHelper::addNew('add');
                JToolbarHelper::editList('edit');
                JToolbarHelper::publish('publish');
                JToolbarHelper::unpublish('unpublish');
                JToolbarHelper::trash('trash');

                $status_options = '<option value="1" >' . JText::_('JPUBLISHED') . '</option>';
                $status_options .= '<option value="0" >' . JText::_('JUNPUBLISHED') . '</option>';
                JHtmlSidebar::addFilter(JText::_('COM_JEPROLAB_SELECT_STATUS_LABEL'), 'jform[filter_state]', $status_options, FALSE);
                $zone_options = '';
                foreach ($this->zones as $zone){
                    $zone_options .= '<option value="'. $zone->zone_id . '" >' . $zone->name . '</option>';
                }
                JHtmlSidebar::addFilter(JText::_('COM_JEPROLAB_SELECT_ZONE_LABEL'), 'jform[filter_zone]', $zone_options, FALSE);
                break;
        }
        JeprolabHelper::sideBarRender('localisation');
    }

    protected function renderSubMenu($current = 'country'){
        $script = '<fieldset class="btn-group" >';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=country') . '" class="btn jeprolab_sub_menu' . (($current == 'country') ? ' btn-success' : '') . '" >' . ucfirst(JText::_('COM_JEPROLAB_COUNTRIES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=country&task=zone') . '" class="btn jeprolab_sub_menu' . (($current == 'zone') ? ' btn-success' : '') . '" >' . ucfirst(JText::_('COM_JEPROLAB_ZONES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=country&task=states') . '" class="btn jeprolab_sub_menu' . (($current == 'states') ? ' btn-success' : '') . '" >' . ucfirst(JText::_('COM_JEPROLAB_STATES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_languages') . '" class="btn jeprolab_sub_menu' . (($current == 'languages') ? ' btn-success' : '') . '" >' . ucfirst(JText::_('COM_JEPROLAB_LANGUAGES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=currency') . '" class="btn jeprolab_sub_menu' . (($current == 'currency') ? ' btn-success' : '') . '" >' . ucfirst(JText::_('COM_JEPROLAB_CURRENCIES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=tax') . '" class="btn jeprolab_sub_menu' . (($current == 'tax') ? ' btn-success' : '') . '" >' . ucfirst(JText::_('COM_JEPROLAB_TAXES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=tax&task=rules') . '" class="btn jeprolab_sub_menu' . (($current == 'rules') ? ' btn-success' : '') . '" >' . ucfirst(JText::_('COM_JEPROLAB_TAX_RULES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=tax&task=rule_group') . '" class="btn jeprolab_sub_menu' . (($current == 'rule_group') ? ' btn-success' : '') . '" >' . ucfirst(JText::_('COM_JEPROLAB_TAX_RULES_GROUP_LABEL')) . '</a>';
        $script .= '</fieldset>';

        return $script;
    }

    public function renderAddForm($tpl = NULL){
        $this->helper = new JeprolabHelper();
        $languages = JeprolabLanguageModelLanguage::getLanguages();
        $this->assignRef('languages', $languages);
        $currencies = JeprolabCurrencyModelCurrency::getStaticCurrencies();
        $this->assignRef('currencies', $currencies);
        $zones = JeprolabZoneModelZone::getZones();
        $this->assignRef('zones', $zones);
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    public function renderEditForm($tpl = NULL){
        if(!isset($this->context)){ $this->context = JeprolabContext::getContext(); }
        $this->helper = new JeprolabHelper();
        $languages = JeprolabLanguageModelLanguage::getLanguages();
        $this->assignRef('languages', $languages);
        $currencies = JeprolabCurrencyModelCurrency::getStaticCurrencies();
        $this->assignRef('currencies', $currencies);
        $zones = JeprolabZoneModelZone::getZones();
        $this->assignRef('zones', $zones);
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    public function renderEditZone($tpl = null){
        $this->loadZone();
        $this->helper = new JeprolabHelper();
        /*$zoneModel = new JeprolabZoneModelZone();
        $this->zones = $zoneModel->getZoneList();*/

        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    public function loadZone($opt = false){
        $app =JFactory::getApplication();

        $zone_id = (int)$app->input->get('zone_id');
        if ($zone_id && JeprolabTools::isUnsignedInt($zone_id)) {
            if (!$this->zone) {
                $this->zone = new JeprolabZoneModelZone($zone_id);
            }
            if (JeprolabTools::isLoadedObject($this->zone, 'zone_id'))
                return $this->zone;
            // throw exception
            JError::raiseError(500, 'The zone cannot be loaded (or not found)');
            return false;
        } elseif ($opt) {
            if (!$this->zone)
                $this->zone = new JeprolabZoneModelZone();
            return $this->zone;
        } else {
            $this->context->controller->has_errors = true;
            JError::raiseError('The zone cannot be loaded (the identifier is missing or invalid)');
            return false;
        }
    }

    /**
     * Load class supplier using identifier in $_GET (if possible)
     * otherwise return an empty supplier, or die
     *
     * @param boolean $opt Return an empty supplier if load fail
     * @return boolean
     */
    public function loadObject($opt = false){
        $app = JFactory::getApplication();

        $country_id = (int)$app->input->get('country_id');
        if ($country_id && JeprolabTools::isUnsignedInt($country_id)) {
            if (!$this->country) {
                $this->country = new JeprolabCountryModelCountry($country_id);
            }
            if (JeprolabTools::isLoadedObject($this->country, 'country_id'))
                return $this->country;
            // throw exception
            JError::raiseError(500, 'The country cannot be loaded (or not found)');
            return false;
        } elseif ($opt) {
            if (!$this->country)
                $this->country = new JeprolabCountryModelCountry();
            return $this->country;
        } else {
            $this->context->controller->has_errors = true;
            JError::raiseError('The country cannot be loaded (the identifier is missing or invalid)');
            return false;
        }
    }

    public function viewStates($tpl = null){
        $stateModel = new JeprolabStateModelState();
        $this->states = $stateModel->getStateList();

        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    public function renderAddState($tpl = null){
        if(!isset($this->context)){ $this->context = JeprolabContext::getContext(); }
        $this->helper = new JeprolabHelper();
        $countries = JeprolabCountryModelCountry::getStaticCountries($this->context->language->lang_id);
        $zones = JeprolabZoneModelZone::getZones();

        $this->assignRef('countries', $countries);
        $this->assignRef('zones', $zones);
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }
}