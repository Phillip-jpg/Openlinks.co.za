<?PHP
$filepath = realpath(dirname(__FILE__));//might be a potential error or smart f
include_once($filepath.'/../config/sql.config.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/../classes/ADMIN.class.php');
include_once($filepath.'/../lib/Session.php');
include_once($filepath.'/../helpers/val.php');
class GAdmin extends Admin{
    public $classname="G_ADMIN";
}