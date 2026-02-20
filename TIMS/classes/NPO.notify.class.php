<?PHP
$filepath = realpath(dirname(__FILE__));//might be a potential error or smart f
include_once($filepath.'/../config/sql.config.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/../classes/NOTIFICATION.php');
class NPO_notify extends NOTIFICATION{
    protected $classname="NPO";
    protected $id="NPO_ID";
    protected $var=DB_NAME_2;
    protected $var2=DB_NAME_3;
    Protected $var4=DB_NAME_5;
    protected $LOGIN_SELECT=LOGIN_SELECT;
    protected $PIMG_SELECT=NPO_PIMG_SELECT;
    protected $EMAIL_SELECT=NPO_EMAIL_SELECT;
    protected $ITERATION_SELECT=NPO_ITERATION_SELECT;
    protected $DATE_UPDATE=NPO_DATE_UPDATE;
    protected $DATE_INSERT=NPO_DATE_INSERT;
    protected $NONRECCURING_INSERT=NPO_NONRECCURING_INSERT;
    protected $EVENT_INSERT=NPO_EVENT_INSERT;
    protected $READ_SELECT =NPO_RREAD_SELECT;
    protected $PROGRESS_UPDATE = NPO_PROGRESS_UPDATE;
    protected $PROGRESS_SELECT = NPO_PROGRESS_SELECT;
    
}