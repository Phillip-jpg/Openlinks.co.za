<?php
define("GEN_LINK_SELECT", array("SELECT COMPANY_ID FROM `consultant_links` WHERE `link` = ?;","i"));

define("GEN_LINK_INSERT", array("INSERT INTO `consultant_links`(`link`, `COMPANY_ID`) VALUES (?, ?)","si"));

define("GEN_LINK_DELETE", array("DELETE FROM `consultant_links` WHERE `COMPANY_ID` =?;","i"));



define("GET_CONTROLLABLE_SELECT", array(
"SELECT 
    link, Legal_name, cca.COMPANY_ID
FROM
    openlink_association_db.company_consultant_association AS cca
INNER JOIN openlink_companies.register AS cr
ON
    cca.COMPANY_ID = cr.COMPANY_ID
WHERE stage = ?
AND
    CONSULTANT_ID = ?;
","ii"));

define("ENABLE_CONTROL_UPDATE", array(
    "UPDATE
    openlink_association_db.company_consultant_association
SET
    `stage` = ?
WHERE
    `COMPANY_ID` = ? 
AND `CONSULTANT_ID` = ?
    ","iii"));

define("ENABLE_CONTROL_DELETE", array(
    "DELETE
    FROM
        openlink_association_db.company_consultant_association
    WHERE
        `COMPANY_ID` = ? 
    AND `CONSULTANT_ID` = ?
    ","ii"));

define("ENABLE_CONTROL_CONSULTANT_LINK_DELETE", array(
    "DELETE
    FROM
        openlink_companies.consultant_links
    WHERE
        `COMPANY_ID` = ?
    ","i"));




define("CONTROL_LINK_SELECT", array(
"SELECT COMPANY_ID FROM openlink_association_db.company_consultant_association
WHERE `link` = ?
AND CONSULTANT_ID = ?;
","si"));

define("CONNECTION_INSERT", array("INSERT INTO openlink_association_db.company_consultant_association
(`COMPANY_ID`, `CONSULTANT_ID`, `stage`, `link`)
VALUES ( ?, ?, ?, ?)","iiis"));


define("CREATE_NEW_LINK_SELECT", array(
"SELECT COMPANY_ID FROM openlink_association_db.company_consultant_association
WHERE `link` = ? ","s"));

