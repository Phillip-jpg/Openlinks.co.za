<?PHP
$filepath = realpath(dirname(__FILE__));//might be a potential error or smart f
include_once($filepath.'/../config/sql.config.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/../classes/MOTHER.class.php');
include_once($filepath.'/../classes/Generic.php');
include_once($filepath.'/../config/sql.analytics.php');
class SMME extends MOTHER{
    public $classname="SMME";
    protected $SIGNUP_INSERT=SIGNUP_INSERT;
    protected $TOTAL_NUMBER_REQUESTS_SELECT=SMME_TOTAL_NUMBER_REQUESTS_SELECT;
    protected $KEYWORD_INSERT=SMME_KEYWORD_INSERT;
  protected $DISPLAY_ADMIN_CHAT_SELECT=DISPLAY_ADMIN_CHAT_SELECT;
    protected $ADMINS_NAVIGATION_SELECT=ADMINS_NAVIGATION_SELECT;
    
    protected $SMME_ADMIN_SELECT=SMME_REVIEW_ADMIN_SELECT;
    protected $SMME_REGISTER_SELECT=SMME_REGISTER_SELECTS;
    protected $SMME_DIRECTOR_SELECT=SMME_DIRECTOR_SELECT;
    protected $SMME_STATEMENT_SELECT=SMME_STATEMENT_SELECT;
    protected $SMME_DOCUMENTATION_SELECT=SMME_COMPANY_DOCUMENTATION_SELECT;
    protected $SMME_PRODUCTS_SELECT=SMME_PRODUCTS_SELECT;
    protected $SMME_KEYWORDS_SELECT=SMME_KEYWORDS_SELECT;
    protected $SMME_LINK_SELECT=SMME_LINKS_SELECT;
    protected $NOTIFICATION_VIEWED=NOTIFICATION_VIEWED_UPDATE;

}