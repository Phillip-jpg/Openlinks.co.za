<?PHP
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../config/sql.config.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/../classes/NOTIFICATION.php');
class SMME_notify extends NOTIFICATION{
    protected $classname="SMME";
    protected $id="SMME_ID";
    protected $var=DB_NAME_1;
    protected $var2=DB_NAME_3;
    Protected $var4=DB_NAME_5;
    protected $LOGIN_SELECT=LOGIN_SELECT;
    protected $PIMG_SELECT=SMME_PIMG_SELECT;
    protected $EMAIL_SELECT=SMME_EMAIL_SELECT;
    protected $ITERATION_SELECT=SMME_ITERATION_SELECT;
    protected $EVENT_COMPLETED_UPDATE=SMME_EVENT_COMPLETED_UPDATE;
    protected $EVENT_UPDATE=SMME_EVENT_UPDATE;
    protected $DATE_UPDATE=SMME_DATE_UPDATE;
    protected $DATE_INSERT=SMME_DATE_INSERT;
    protected $NONRECCURING_INSERT=SMME_NONRECCURING_INSERT;
    protected $EMAIL_NAMES_SELECT=EMAIL_NAMES_SELECT;
    protected $EVENT_SELECT=SMME_EVENT_SELECT;
    protected $EVENT_INSERT=SMME_EVENT_INSERT;
    protected $READ_SELECT =COMPANY_RREAD_SELECT;
    protected $PROGRESS_UPDATE = SMME_PROGRESS_UPDATE;
    protected $PROGRESS_SELECT = SMME_PROGRESS_SELECT;
    protected $NOTIFICATION_VIEWED=NOTIFICATION_VIEWED_UPDATE;
}