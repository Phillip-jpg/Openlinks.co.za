<?PHP
$filepath = realpath(dirname(__FILE__));//might be a potential error or smart f
include_once($filepath.'/../config/sql.config.php');
include_once($filepath.'/../config/sql.notify.config.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/../classes/NOTIFICATION.php');
class ADMIN_notify extends NOTIFICATION{
    protected $classname="M_ADMIN";
    protected $id="ADMIN_ID";
    protected $var2=DB_NAME_1;
    protected $var3=DB_NAME_2;
    Protected $var4=DB_NAME_5;
    protected $var=DB_NAME_3;
    protected $LOGIN_SELECT=ADMIN_LOGIN_SELECT;
    protected $EMAIL_SELECT=SMME_EMAIL_SELECT;
    protected $ITERATION_SELECT=SMME_ITERATION_SELECT;
    protected $EVENT_UPDATE=SMME_EVENT_UPDATE;
    protected $EVENT_COMPLETED_UPDATE=SMME_EVENT_COMPLETED_UPDATE;
    protected $DATE_UPDATE=SMME_DATE_UPDATE;
    protected $DATE_INSERT=SMME_DATE_INSERT;
    protected $NONRECCURING_INSERT=SMME_NONRECCURING_INSERT;
    protected $EVENT_INSERT=SMME_EVENT_INSERT;
    protected $EVENT_SELECT=COMPANY_EVENT_SELECT;
    protected $READ_SELECT =SMME_RREAD_SELECT ;
    protected $PROGRESS_UPDATE = SMME_PROGRESS_UPDATE;
    protected $PROGRESS_SELECT = SMME_PROGRESS_SELECT;
    protected $FIVE_DAY_WAIT_ADMIN_SELECT=five_Day_wait_admin_SELECT;
    protected $after_set_date_admin_SELECT=after_set_date_admin_SELECT;
}