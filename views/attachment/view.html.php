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

class JeprolabAttachmentViewAttachment extends JViewLegacy
{
    public function renderDetails($tpl = null){
        $this->addToolBar();
        parent::display($tpl);
    }

    private function addToolBar(){
        JeprolabHelper::sideBarRender('catalog');
        $this->sideBar = JHtmlSideBar::render();
    }

    protected function createFormSubMenu(){
        $script = '<div class="box_wrapper jeproshop_sub_menu_wrapper"><fieldset class="btn-group">';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=analyze'). '" class="btn jeproshop_sub_menu" ><i class="icon-analyze" ></i> ' . JText::_('COM_JEPROLAB_ANALYSES_LABEL'). '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=category') . '" class="btn jeproshop_sub_menu" ><i class="icon-category" ></i> ' . JText::_('COM_JEPROLAB_CATEGORIES_LABEL'). '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=attachment') . '" class="btn jeproshop_sub_menu btn-success" ><i class="icon-attachment" ></i> ' . JText::_('COM_JEPROLAB_ATTACHMENTS_LABEL'). '</a>';
        $script .= '</fieldset></div>';

        return $script;
    }
}