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

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'defines.inc.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'settings.inc.php';
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'context.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cookie.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cache.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'image.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'tree.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'tools.php');


/** require models */
$modelDirectoryPath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR;
require_once $modelDirectoryPath . 'category.php';
require_once $modelDirectoryPath . 'country.php';
require_once $modelDirectoryPath . 'currency.php';
require_once $modelDirectoryPath . 'customer.php';
require_once $modelDirectoryPath . 'employee.php';
require_once $modelDirectoryPath . 'feed.php';
require_once $modelDirectoryPath . 'feedback.php';
require_once $modelDirectoryPath . 'group.php';
require_once $modelDirectoryPath . 'lab.php';
require_once $modelDirectoryPath . 'language.php';
require_once $modelDirectoryPath . 'setting.php';