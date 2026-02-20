<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/connection.class.php');
include_once($filepath.'/../config/sql.mode.php');
include_once($filepath.'/../config/sql.connection.php');
include_once($filepath.'/../config/config.php');


class company_connection extends connection {
    protected $who = "COMPANY";
    protected $who1 = "CONSULTANT";
    protected $db = DB_NAME_3;
    protected $db1 = DB_NAME_4;
    protected $id;
    protected $idname = "COMPANY_ID";
    protected $GEN_LINK_SELECT = GEN_LINK_SELECT;
    protected $GEN_LINK_INSERT = GEN_LINK_INSERT;
    protected $GEN_LINK_DELETE = GEN_LINK_DELETE;
}