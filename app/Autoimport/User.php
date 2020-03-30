<?php

namespace App\Autoimport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;


define('_JEXEC', 1);
define('JPATH_BASE', getenv('JPATH_BASE'));
define('DS', DIRECTORY_SEPARATOR);
require_once(JPATH_BASE . DS . 'includes' . DS . 'defines.php');
require_once(JPATH_BASE . DS . 'includes' . DS . 'framework.php');
//defined('_JEXEC') or defined('_VALID_MOS') or die("Direct Access Is Not Allowed");

class User extends Model
{
    protected $table = 'user';
    protected $connection = 'db_no_prefix';
    //protected $primaryKey = 'itemid';
    public $timestamps = false;

    static public function getCurrent()
    {
        /*$jAp = &JFactory::getApplication();
        $user = &JFactory::getUser();
        $user->get('id');
        return $user;*/
        //return scandir('/var/www/antara');
        return '123';
    }
}
