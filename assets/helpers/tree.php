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

/**** ----------------  TREE ---------- ***/
class JeprolabTree
{
    const DEFAULT_TEMPLATE = 'tree';
    const DEFAULT_HEADER_TEMPLATE = 'tree_header';
    const DEFAULT_NODE_FOLDER_TEMPLATE = 'tree_node_folder';
    const DEFAULT_NODE_ITEM_TEMPLATE = 'tree_node_item';

    protected $_attributes;
    private $_context;
    protected $_data;
    protected $_header_template;
    private $_tree_id;
    protected $_lang;
    protected $_use_checkbox;
    protected $_use_search;
    protected $_node_folder_template;
    protected $_node_item_template;
    protected $_template;
    private $_tree_title;
    private $_toolbar;

    public function __construct($id, $data = null){
        $this->setTreeId($id);

        if (isset($data)){ 	$this->setTreeData($data); }
    }

    /** ------ SETTERS -------- ***/

    /**
     * @param $value
     * @return $this
     */
    public function setTreeId($value){
        $this->_tree_id = $value;
        return $this;
    }

    public function setTreeData($value){
        if (!is_array($value) && !$value instanceof Traversable){
            JError::raiseWarning(500, JText::_('Data value must be an traversable array'));
        }
        $this->_data = $value;
        return $this;
    }

    public function setTreeTitle($value){
        $this->_tree_title = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setTreeLang($value){
        $this->_lang = $value;
        return $this;
    }


    public function setTreeTemplate($value){
        $this->_template = $value;
        return $this;
    }

    public function setUseCheckBox($value){
        $this->_use_checkbox = (bool)$value;
        return $this;
    }

    public function setUseSearch($value){
        $this->_use_search = (bool)$value;
        return $this;
    }
}

/**** ---------------- CATEGORY TREE ---------- ***/
class JeprolabCategoriesTree extends JeprolabTree
{
    const DEFAULT_TEMPLATE = 'tree_categories';
    const DEFAULT_NODE_FOLDER_TEMPLATE = 'tree_node_folder_radio';
    const DEFAULT_NODE_ITEM_TEMPLATE = 'tree_node_item_radio';

    private $_disabled_categories;
    private $_input_name;
    private $_root_category;
    private $_selected_categories;
    private $_shop;

    private $_use_shop_restriction;

    public function __construct($tree_id, $title = null, $root_category = null, $lang = null, $use_shop_restriction = true){
        parent::__construct($tree_id);

        if (isset($title)){ $this->setTreeTitle($title); }

        if (isset($root_category)){ $this->setRootCategory($root_category); }

        $this->setTreeLang($lang);
        $this->setUseLabRestriction($use_shop_restriction);
    }

    public function setRootCategory($value){
        if (!JeprolabTools::isInt($value)){
            JError::raiseWarning(500, JText::_('Root category must be an integer value'));
        }
        $this->_root_category = $value;
        return $this;
    }

    public function setUseLabRestriction($value){
        $this->_use_shop_restriction = (bool)$value;
        return $this;
    }


    public function setSelectedCategories($value){
        if (!is_array($value))
            throw new JException('Selected categories value must be an array');

        $this->_selected_categories = $value;
        return $this;
    }

    public function setInputName($value) {
        $this->_input_name = $value;
        return $this;
    }

    /**
     * @param null $data
     * @return string
     */
    public function render($data = NULL){

    }

}