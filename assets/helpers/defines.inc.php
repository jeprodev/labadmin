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


jimport('joomla.filesystem.folder');

if(!defined('COM_JEPROLAB_SSL_PORT')){
    define('COM_JEPROLAB_SSL_PORT', 443);
}

/* Debug only */
define('COM_JEPROLAB_MODE_DEV', false);
/* Compatibility warning */
define('COM_JEPROLAB_DISPLAY_COMPATIBILITY_WARNING', false);

define('COM_JEPROLAB_BASE_URI', JPATH_SITE . '/index.php?option=com_jeproshop');

$componentPath = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jeproshop' . DIRECTORY_SEPARATOR;

if(!defined('COM_JEPROLAB_IMAGE_DIR')){
    $imageDirectory = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_jeproshop' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
    if(!JFolder::exists($imageDirectory)){
        JFolder::create($imageDirectory);
    }
    define('COM_JEPROLAB_IMAGE_DIR', JPATH_SITE . '/media/com_jeproshop/images/');
}

if(!defined('COM_JEPROLAB_CATEGORY_IMAGE_DIR')){
    $categoryImageDirectory = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_jeproshop' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR;
    if(!JFolder::exists($categoryImageDirectory)){
        JFolder::create($categoryImageDirectory);
    }
    define('COM_JEPROLAB_CATEGORY_IMAGE_DIR', $categoryImageDirectory);
}

if(!defined('COM_JEPROLAB_EMPLOYEE_IMAGE_DIR')){

    $employeeImageDirectory = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_jeproshop' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'employee' . DIRECTORY_SEPARATOR;
    if(!JFolder::exists($employeeImageDirectory)){ JFolder::create($employeeImageDirectory); }
    define('COM_JEPROLAB_EMPLOYEE_IMAGE_DIR', $employeeImageDirectory);
}

if(!defined('COM_JEPROLAB_IMAGE_GENDERS_DIR')){
    jimport('joomla.filesystem.folder');
    $genderImageDirectory = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_jeproshop' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'genders' . DIRECTORY_SEPARATOR;
    if(!JFolder::exists($genderImageDirectory)){ JFolder::create($genderImageDirectory); }
    define('COM_JEPROLAB_IMAGE_GENDERS_DIR', $employeeImageDirectory);
}

if(!defined('COM_JEPROLAB_LANGUAGE_IMAGE_DIR')){
    jimport('joomla.filesystem.folder');
    $languageImageDirectory = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jeproshop' . DIRECTORY_SEPARATOR . 'assets'. DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'flags' . DIRECTORY_SEPARATOR;
    if(!JFolder::exists($languageImageDirectory)){
        JFolder::create($languageImageDirectory);
    }
    define('COM_JEPROLAB_LANGUAGE_IMAGE_DIR', $languageImageDirectory);
}

if(!defined('COM_JEPROLAB_PRODUCT_IMAGE_DIR')){
    jimport('joomla.filesystem.folder');
    $categoryImageDirectory = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_jeproshop' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR;
    if(!JFolder::exists($categoryImageDirectory)){
        JFolder::create($categoryImageDirectory);
    }
    define('COM_JEPROLAB_PRODUCT_IMAGE_DIR', $categoryImageDirectory);
}

if(!defined('COM_JEPROLAB_MANUFACTURER_IMAGE_DIR')){
    jimport('joomla.filesystem.folder');
    $manufacturerImageDirectory = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_jeproshop' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'manufacturers' . DIRECTORY_SEPARATOR;
    if(!JFolder::exists($manufacturerImageDirectory)){
        JFolder::create($manufacturerImageDirectory);
    }
    define('COM_JEPROLAB_MANUFACTURER_IMAGE_DIR', $manufacturerImageDirectory);
}

if(!defined('COM_JEPROLAB_SUPPLIER_IMAGE_DIR')){
    jimport('joomla.filesystem.folder');
    $supplierImageDirectory = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_jeproshop' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'suppliers' . DIRECTORY_SEPARATOR;
    if(!JFolder::exists($supplierImageDirectory)){
        JFolder::create($supplierImageDirectory);
    }
    define('COM_JEPROLAB_SUPPLIER_IMAGE_DIR', $categoryImageDirectory);
}

if(!defined('COM_JEPROLAB_SHIPPING_IMAGE_DIR')){
    jimport('joomla.filesystem.folder');
    $shippingImageDirectory = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_jeproshop' . DIRECTORY_SEPARATOR . 'shippings' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR;
    if(!JFolder::exists($shippingImageDirectory)){
        JFolder::create($shippingImageDirectory);
    }
    define('COM_JEPROLAB_SHIPPING_IMAGE_DIR', $shippingImageDirectory);
}

if(!defined('COM_JEPROLAB_COLOR_IMAGE_DIR')){
    jimport('joomla.filesystem.folder');
    $colorImageDirectory = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_jeproshop' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'color' . DIRECTORY_SEPARATOR;
    if(!JFolder::exists($colorImageDirectory)){
        JFolder::create($colorImageDirectory);
    }
    define('COM_JEPROLAB_COLOR_IMAGE_DIR', $categoryImageDirectory);
}

if(!defined('COM_JEPROLAB_STORE_IMAGE_DIR')){
    jimport('joomla.filesystem.folder');
    $storeImageDirectory = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_jeproshop' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'stores' . DIRECTORY_SEPARATOR;
    if(!JFolder::exists($storeImageDirectory)){
        JFolder::create($storeImageDirectory);
    }
    define('COM_JEPROLAB_STORE_IMAGE_DIR', $storeImageDirectory);
}

if(!defined('COM_JEPROLAB_DEVELOPER_IMAGE_DIR')){
    jimport('joomla.filesystem.folder');
    $developerImageDirectory = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_jeproshop' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'developers' . DIRECTORY_SEPARATOR;
    if(!JFolder::exists($developerImageDirectory)){
        JFolder::create($developerImageDirectory);
    }
    define('COM_JEPROLAB_DEVELOPER_IMAGE_DIR', $developerImageDirectory);
}
define('COM_JEPROLAB_THEME_GENDERS_DIR', '');
define('COM_JEPROLAB_MAGIC_QUOTES_GPC', get_magic_quotes_gpc());

define('COM_JEPROLAB_JS_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'javascript');

/* Tax behavior */
define('COM_JEPROLAB_PRODUCT_TAX', 0);
define('COM_JEPROLAB_STATE_TAX', 1);
define('COM_JEPROLAB_BOTH_TAX', 2);

define('COM_JEPROLAB_PRICE_DISPLAY_PRECISION', 2);
define('COM_JEPROLAB_TAX_EXCLUDED', 1);
define('COM_JEPROLAB_TAX_INCLUDED', 0);

define('COM_JEPROLAB_ROUND_UP_PRICE', 0);
define('COM_JEPROLAB_ROUND_DOWN_PRICE', 1);
define('COM_JEPROLAB_ROUND_HALF_PRICE', 2);