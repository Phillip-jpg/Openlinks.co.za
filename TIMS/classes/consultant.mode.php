<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/posting.class.php');
include_once($filepath.'/../config/sql.mode.php');
include_once($filepath.'/../config/sql.notification.php');
include_once($filepath.'/../config/config.php');


class consultant_mode extends mode {
    protected $who = "CONSULTANT";
    protected $who1 = "COMPANY";
    protected $db = DB_NAME_4;
    protected $db1 = DB_NAME_3;
    protected $id;
    protected $idname = "CONSULTANT_ID";
    protected $POSTING_INSERT = CONSULTANT_MODE_INSERT;
    protected $POSTING_SELECT = CONSULTANT_MODE_SELECT;
}