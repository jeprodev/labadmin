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

class JeprolabFeedController extends JeprolabController
{
    public function display($cachable = FALSE, $urlParams = FALSE) {
        parent::display();
    }

    public function add(){
        $view = $this->input->get('view', 'feed');
        $layout = $this->input->get('layout', 'add');

        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        $viewClass->setLayout($layout);
        $viewClass->addFeed();
    }

    public function save(){
        JSession::checkToken() or die(JText::_('COM_JEPROLAB_NOT_ALLOWED_TO_ACCESS_THIS_AREA_MESSAGE'));
        $user = JFactory::getUser();
        if($user->authorise('core.create', 'com_jeprolab')){
            $feedModel = new JeprolabFeedModelFeed();
            $feedModel->saveFeed();
        }
    }

    /*public function add(){
        echo 'bonjour jeff';
    }*/
}