<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/posting.class.php');
include_once($filepath.'/../config/sql.mode.php');
include_once($filepath.'/../config/sql.notification.php');
include_once($filepath.'/../config/config.php');


class company_mode extends mode {
    protected $who = "COMPANY";
    protected $who1 = "CONSULTANT";
    protected $db = DB_NAME_3;
    protected $db1 = DB_NAME_4;
    protected $id;
    protected $idname = "COMPANY_ID";
    protected $POSTING_INSERT = COMPANY_MODE_INSERT;
    protected $POSTING_SELECT = COMPANY_MODE_SELECT;
}