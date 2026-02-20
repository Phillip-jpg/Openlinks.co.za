<?PHP
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../config/sql.config.php');
include_once($filepath.'/../config/sql.notify.config.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/../classes/NOTIFICATION.php');
class COMPANY_notify extends NOTIFICATION{
    protected $classname="COMPANY";
    protected $id="COMPANY_ID";
    protected $var=DB_NAME_3;
    protected $var2=DB_NAME_1;
    protected $var3=DB_NAME_2;
    Protected $var4=DB_NAME_5;
    protected $LOGIN_SELECT=LOGIN_SELECT;
    protected $PIMG_SELECT=COMPANY_PIMG_SELECT;
    protected $EMAIL_SELECT=SMME_EMAIL_SELECT;
    protected $ITERATION_SELECT=SMME_ITERATION_SELECT;
    protected $EVENT_UPDATE=SMME_EVENT_UPDATE;
    protected $EVENT_COMPLETED_UPDATE=SMME_EVENT_COMPLETED_UPDATE;
    protected $DATE_UPDATE=SMME_DATE_UPDATE;
    protected $DATE_INSERT=SMME_DATE_INSERT;
    protected $NONRECCURING_INSERT=SMME_NONRECCURING_INSERT;
    protected $EVENT_INSERT=SMME_EVENT_INSERT;
    protected $EVENT_SELECT=COMPANY_EVENT_SELECT;
    protected $EMAIL_NAMES_SELECT=EMAIL_NAMES_SELECT;
    protected $READ_SELECT =SMME_RREAD_SELECT ;
    protected $PROGRESS_UPDATE = SMME_PROGRESS_UPDATE;
    protected $PROGRESS_SELECT = SMME_PROGRESS_SELECT;
    protected $NOTIFICATION_VIEWED=NOTIFICATION_VIEWED_UPDATE;
}
