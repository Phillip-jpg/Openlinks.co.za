<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/posting.class.php');
include_once($filepath.'/../config/sql.posting.php');
include_once($filepath.'/../config/config.php');


class comp_post extends posting {
    protected $who = "comp";
    protected $db = DB_NAME_3;
    protected $id;
    protected $idname = "COMPANY_ID";
    protected $POSTING_INSERT = COMPANY_POSTING_INSERT;
    protected $POSTING_SELECT = COMPANY_POSTING_SELECT;
}
