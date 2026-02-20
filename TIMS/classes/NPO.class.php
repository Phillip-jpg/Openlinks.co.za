<?PHP
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../config/sql.config.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/../classes/MOTHER.class.php');
include_once($filepath.'/../config/sql.analytics.php');
class NPO extends MOTHER{
    protected $classname="NPO";
    protected $SIGNUP_INSERT=NPO_SIGNUP_INSERT;
    protected $TOTAL_NUMBER_REQUESTS_SELECT=NPO_TOTAL_NUMBER_REQUESTS_SELECT;
    protected $KEYWORD_INSERT=NPO_KEYWORD_INSERT;
}