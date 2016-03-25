<?php
/**
 * @version         1.0.3
 * @package         components
 * @sub package      com_jeprolab
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

class JeprolabCache
{
    /** @var array store local cache  */
    protected static $local = array();

    /** @var JeprolabCache instance */
    protected static $instance;

    /**
     * @return JeprolabCache instance
     */
    public static function getInstance(){
        if(self::$instance){
            $caching_system = COM_JEPROSHOP_CACHING_SYSTEM;
            self::$instance = new $caching_system();
        }
        return self::$instance;
    }

    /**
     * Store a given value as key value in an array
     **/
    public static function store($key, $value){
        JeprolabCache::$local[$key] = $value;
    }

    /**
     * Retrieve a value if stored in cache
     */
    public static function retrieve($key){
        return isset(JeprolabCache::$local[$key]) ? JeprolabCache::$local[$key] : null;
    }

    /**
     * Retrieve all data from cache
     */
    public static function retrieveAll(){
        return JeprolabCache::$local;
    }

    /**
     * Check if a key exist in an array
     */
    public static function isStored($key){
        return isset(JeprolabCache::$local[$key]);
    }
}