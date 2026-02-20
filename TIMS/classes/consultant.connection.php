<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/connection.class.php');
include_once($filepath.'/../config/sql.mode.php');
include_once($filepath.'/../config/sql.connection.php');
include_once($filepath.'/../config/config.php');


class consultant_connection extends connection {
    protected $who = "CONSULTANT";
    protected $who1 = "COMPANY";
    protected $db = DB_NAME_4;
    protected $db1 = DB_NAME_3;
    protected $id;
    protected $idname = "CONSULTANT_ID";

    protected $CONNECTION_INSERT = CONNECTION_INSERT;
    protected $GEN_LINK_SELECT = GEN_LINK_SELECT;
    protected $CREATE_NEW_LINK_SELECT=CREATE_NEW_LINK_SELECT;
}