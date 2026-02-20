<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/posting.class.php');
include_once($filepath.'/../config/sql.posting.php');
include_once($filepath.'/../config/config.php');


class cons_post extends posting {
    protected $who = "cons";
    protected $db = DB_NAME_4;
    protected $id;
    protected $idname = "CONSULTANT_ID";
    protected $POSTING_INSERT = CONSULTANT_POSTING_INSERT;
    protected $POSTING_SELECT = CONSULTANT_POSTING_SELECT;
}