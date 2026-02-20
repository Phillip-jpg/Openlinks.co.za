<?php
define("COMPANY_BOOKMARK_INSERT", array("INSERT INTO `company_consultant_bookmarks`(`CONSULTANT_ID`, `date`) 
VALUES (?, CURRENT_TIMESTAMP);","i"));

define("TOKEN_INSERT", array("INSERT INTO `company_consultant_association`(`COMPANY_ID`, `link`) 
VALUES ","is"));


define("SEND_MAIL_SELECT", array(
    "SELECT concat(cs.First_Name, ' ', cs.Surname) AS 'CONSULTANT NAME', 
    cs.Email AS 'CONSULTANT EMAIL', 
    cr.Legal_name AS 'COMPANY NAME', 
    cr.Email AS 'COMPANY EMAIL' 
    FROM openlink_consultants.signup cs, openlink_companies.register cr 
    WHERE cs.CONSULTANT_ID=?
    AND cr.COMPANY_ID=?;
    ","ii"));

// define("LINK_SELECT", array(
//     "SELECT 1 FROM `company_consultant_association` WHERE `link` = ?;
//     ","s")); 


// define("INITIATE_FORM_INSERT", array("INSERT INTO `company_consultant_association`
// (`COMPANY_ID`, `CONSULTANT_ID`, `stage`, `link`)
// VALUES ( ?, ?, ?, ?)","iiis"));

define("GET_ALL_SELECT", array(
    "SELECT COMPANY_ID, c.CONSULTANT_ID, `date_started`, `link`,
    concat(First_Name, ' ', Surname) AS name, Gender, Ethnic_Group, pp.ext
    FROM openlink_association_db.company_consultant_association AS c, openlink_consultants.signup AS cs, 
    openlink_consultants.consultant_information AS ci, openlink_consultants.pimg AS pp
    WHERE c.CONSULTANT_ID = cs.CONSULTANT_ID
    AND cs.CONSULTANT_ID = ci.CONSULTANT_ID
    AND ci.CONSULTANT_ID = pp.CONSULTANT_ID
    AND stage = 1
    AND COMPANY_ID = ?
    ORDER BY `date_started`;
    ","i"));

define("GET_INDIVIDUAL_SELECT", array(
    "SELECT COMPANY_ID, c.CONSULTANT_ID, `date_started`, `link`,
    concat(First_Name, ' ', Surname) AS name, Gender, Ethnic_Group, pp.ext
    FROM openlink_association_db.company_consultant_association AS c, openlink_consultants.signup AS cs, 
    openlink_consultants.consultant_information AS ci, openlink_consultants.pimg AS pp
    WHERE c.CONSULTANT_ID = cs.CONSULTANT_ID
    AND cs.CONSULTANT_ID = ci.CONSULTANT_ID
    AND ci.CONSULTANT_ID = pp.CONSULTANT_ID
    AND stage = 1
    AND COMPANY_ID = ?
    AND c.CONSULTANT_ID = ?
    ORDER BY `date_started`;
    ","ii"));




define("CANDIDATE_UPDATE", array(
    "UPDATE `company_consultant_association` 
    SET `stage`= ?
    WHERE COMPANY_ID = ?
    AND CONSULTANT_ID = ?;
    ","iii"));

define("CANDIDATE_DELETE", array(
    "DELETE FROM `company_consultant_association` 
    WHERE `COMPANY_ID` = ? 
    AND `stage` = 1;
    ","ii"));


