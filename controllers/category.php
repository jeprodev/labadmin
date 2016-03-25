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

class JeprolabCategoryController extends JeprolabController
{
    public $category = null;

    public function initialize(){
        $app = JFactory::getApplication();
        $context = JeprolabContext::getContext();

        parent::initialize();
        $category_id = $app->input->get('category_id');
        $task = $app->input->get('task');
        if($category_id && $task != 'delete'){
            $this->category = new JeprolabCategoryModelCategory($category_id);
        }else{
            if(JeprolabLabModelLab::isFeaturePublished() && JeprolabLabModelLab::getLabContext() == JeprolabLabModelLab::CONTEXT_LAB){
                $this->category = new JeprolabCategoryModelCategory($context->lab->category_id);
            }elseif(count(JeprolabCategoryModelCategory::getCategoriesWithoutParent()) > 1 && JeprolabSettingModelSetting::getValue('multi_lab_feature_active') && count(JeprolabLabModelLab::getLabs(true, null, true)) != 1){
                $this->category = JeprolabCategoryModelCategory::getTopCategory();
            }else{
                $this->category = new JeprolabCategoryModelCategory(JeprolabSettingModelSetting::getValue('root_category'));
            }
        }

        if(JeprolabTools::isLoadedObject($this->category, 'category_id') && !$this->category->isAssociatedToLab() && JeprolabLabModelLab::getLabContext() == JeprolabLabModelLab::CONTEXT_LAB){
            $app->redirect('index.php?option=com_jeprolab&view=category&task=edit&category_id=' . (int)$context->lab->getCategoryId() . '&' . JeprolabTools::getCategoryToken() . '=1');
        } 
    }


}