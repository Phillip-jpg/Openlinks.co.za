<?php 

//system
define("five_Day_wait_admin_SELECT", array(
    "SELECT sr.Legal_name as SL, sr.Email as SE, cr.Legal_name as CL, cr.Email CE, ea.SMME_ID as SMME_ID, ea.COMPANY_ID as COMPANY_ID,
    ss.typeOfEntity SW, cs.typeOfEntity CW
        FROM smme_company_events ea, openlink_smmes.register sr, openlink_companies.register cr, openlink_smmes.signup ss, openlink_companies.signup cs
        WHERE ea.SMME_ID=sr.SMME_ID
        AND ea.COMPANY_ID=cr.COMPANY_ID
        AND ss.SMME_ID=sr.SMME_ID
        AND cs.COMPANY_ID=cr.COMPANY_ID
        AND TIMESTAMPDIFF(SECOND, event_Start, CURRENT_TIMESTAMP)>=?
        AND event_Completed=0
        AND ea.EVENT_ID=?;","ii"));

define("after_set_date_admin_SELECT", array(
    "SELECT sr.Legal_name as SL, sr.Email as SE, cr.Legal_name as CL, cr.Email CE, ea.SMME_ID, ea.COMPANY_ID,
    ss.typeOfEntity SW, cs.typeOfEntity CW, event_date
        FROM smme_company_events ea, openlink_smmes.register sr, openlink_companies.register cr, openlink_smmes.signup ss, openlink_companies.signup cs
        WHERE ea.SMME_ID=sr.SMME_ID
        AND ea.COMPANY_ID=cr.COMPANY_ID
        AND ss.SMME_ID=sr.SMME_ID
        AND cs.COMPANY_ID=cr.COMPANY_ID
    AND TIMESTAMPDIFF(DAY, event_date, CURRENT_TIMESTAMP)>=1
    AND event_Completed=0
    AND ea.EVENT_ID=?;","i"));

//smme
//Select iteration
define("SMME_EVENT_SELECT", array(
    "SELECT 1
     FROM openlink_association_db.smme_company_events
     WHERE SMME_ID=?
    AND COMPANY_ID=?
    AND EVENT_ID=?
    OR
    SMME_ID=?
    AND COMPANY_ID=?
    AND EVENT_ID=?
    ;
     ","iiiiii"));

define("COMPANY_EVENT_SELECT", array(
    "SELECT 1
    FROM openlink_association_db.smme_company_events
    WHERE SMME_ID=?
    AND COMPANY_ID=?
    AND EVENT_ID=?
    OR
    SMME_ID=?
    AND COMPANY_ID=?
    AND EVENT_ID=?
    ;
    ","iiiiii"));

//select emails
define("SMME_EMAIL_SELECT", array(
"SELECT sr.Legal_name AS 'SMME NAME', 
sr.Email AS 'SMME EMAIL', 
cr.Legal_name AS 'COMPANY NAME', 
cr.Email AS 'COMPANY EMAIL' 
FROM openlink_smmes.register sr, openlink_companies.register cr 
WHERE sr.SMME_ID=? 
AND cr.COMPANY_ID=?;
","ii"));

//Select iteration
define("SMME_ITERATION_SELECT", array(
   "SELECT event_iteration, event_Completed
    FROM openlink_association_db.smme_company_events
    WHERE SMME_ID=?
    AND COMPANY_ID=?
    AND EVENT_ID=?;
    ","iii"));

    // Select SMME READ
define("SMME_READ_SELECT", array(
    "SELECT ?
    FROM ?
    WHERE SMME_ID=?
    AND COMPANY_ID=?
    AND EVENT_ID=?;
    ","iii"));
    

     // Select NPO READ
define("NPO_READ_SELECT", array(
    "SELECT ?
    FROM ?
    WHERE SMME_ID=?
    AND COMPANY_ID=?
    AND EVENT_ID=?;
    ","iii"));

//update reccuring event
define("SMME_EVENT_UPDATE", array(
    "UPDATE openlink_association_db.smme_company_events
    SET 
    event_Start=CURRENT_TIMESTAMP(),
    event_iteration=?
    WHERE SMME_ID=?
    AND COMPANY_ID=?
    AND EVENT_ID=?;
    ","iiii"));

define("SMME_EVENT_COMPLETED_UPDATE", array(
    "UPDATE openlink_association_db.smme_company_events
    SET 
    event_Start=CURRENT_TIMESTAMP(),
    event_Completed=?
    WHERE SMME_ID=?
    AND COMPANY_ID=?
    AND EVENT_ID=?;
    ","iiii"));

    //update date reccuring event
define("SMME_DATE_UPDATE", array(
    "UPDATE openlink_association_db.smme_company_events
    SET 
    event_Start=CURRENT_TIMESTAMP(),
    event_iteration=?,
    event_date= ?
    WHERE SMME_ID=?
    AND COMPANY_ID=?
    AND EVENT_ID=?;
    ","isiii"));



define("SMME_PROGRESS_UPDATE", array(
    "UPDATE openlink_association_db.smme_company_events
    SET 
    Progress=?
    WHERE SMME_ID=?
    AND COMPANY_ID=?

    ","iii"));

define("SMME_PROGRESS_SELECT", array(
    "SELECT `EVENT_ID` 
    FROM openlink_association_db.smme_company_events
    WHERE `COMPANY_ID` = ?
    AND `SMME_ID` = ?
    ORDER BY `EVENT_ID`;
    -- LIMIT 1;
    ","ii"));
//101010101101041
define("NPO_PROGRESS_UPDATE", array(
    "UPDATE openlink_association_db.smme_company_events
    SET 
    Progress=?
    WHERE SMME_ID=?
    AND COMPANY_ID=?
    AND EVENT_ID=?;
    ","iiii"));






//insert reccuring event
define("SMME_EVENT_INSERT", array(
    "INSERT INTO openlink_association_db.smme_company_events
    (
        `Progress`,
        `EVENT_ID`, 
        `COMPANY_ID`, 
        `SMME_ID`
    )
    VALUES 
    (
        ?,
        ?,
        ?,
        ?
    );
    ","iiii"));

    //insert date reccuring event
define("SMME_DATE_INSERT", array(
    "INSERT INTO openlink_association_db.smme_company_events
    (
        `event_date`,
        `Progress`, 
        `EVENT_ID`, 
        `COMPANY_ID`, 
        `SMME_ID`
    )
    VALUES 
    (
        ?,
        ?,
        ?,
        ?,
        ?
    );
    ","siiii"));

//insert non-reccuring event
define("SMME_NONRECCURING_INSERT", array(
"INSERT INTO openlink_association_db.smme_company_events (event_Completed, Progress, EventID, SMME_ID, Company_ID)
Values(1,?,?,?,?)","iiii"));

define("SMME_COMPANY_DIRECTOR_SELECT", array(
    "SELECT Name, Surname, Ethnic_Group, Gender
    FROM `company_director` 
    WHERE SMME_ID=?;","i"));

define("SMME_EXPENSE_SUMMARY_SELECT", array(
    "SELECT * 
    FROM `expense_summary` 
    WHERE SMME_ID = ?;","i"));




//npo
//select emails
define("NPO_EMAIL_SELECT", array(
    "SELECT nr.Legal_name AS 'NPO NAME', 
    nr.Email AS 'NPO EMAIL', 
    cr.Legal_name AS 'COMPANY NAME', 
    cr.Email AS 'COMPANY EMAIL' 
    FROM nops.register nr, openlink_companies.register cr 
    WHERE nr.NPO_ID=? 
    AND cr.COMPANY_ID=?;
    ","ii"));

    //select iteration
    define("NPO_ITERATION_SELECT", array(
        "SELECT event_iteration
        FROM openlink_association_db.npo_company_events
        WHERE NPO_ID=?
        AND COMPANY_ID=?
        AND EVENT_ID=?;
        ","iii"));

    //update reccuring event
    define("NPO_EVENT_UPDATE", array(
        "UPDATE npo_company_events
        SET 
        event_iteration=?
        WHERE NPO_ID=?
        AND COMPANY_ID=?
        AND EVENT_ID=?;
        ","iiii"));

            //update date reccuring event
define("NPO_DATE_UPDATE", array(
    "UPDATE npo_company_events
    SET 
    event_iteration=?,
    event_date= ?
    WHERE NPO_ID=?
    AND COMPANY_ID=?
    AND EVENT_ID=?;
    ","isiii"));

//insert reccuring event
define("NPO_EVENT_INSERT", array(
    "INSERT INTO npo_company_events
    (
        `Progress`, //3
        `EVENT_ID`, 
        `COMPANY_ID`, 
        `NPO_ID`
    )
    VALUES 
    (
        ?,
        ?,
        ?,
        ?
    );
    ","iiii"));

        //insert date reccuring event
define("NPO_DATE_INSERT", array(
    "INSERT INTO npo_company_events
    (
        `event_date`,
        `Progress`, 
        `EVENT_ID`, 
        `COMPANY_ID`, 
        `NPO_ID`
    )
    VALUES 
    (
        ?,
        ?,
        ?,
        ?,
        ?
    );
    ","siiii"));
    

    //insert non-reccuring event
    define("NPO_NONRECCURING_INSERT", array(
    "INSERT INTO npo_company_events(event_Completed, Progress, EventID, SMME_ID, Company_ID)
    Values(1,?,?,?,?)","iiii"));
define("NOTIFICATION_VIEWED_UPDATE", array("DELETE FROM `notifications` WHERE NOTIFICATION_ID = ?", "i"));

//Consultants
//select emails
define("CONSULTANTS_EMAIL_SELECT", array(
    "SELECT CONCAT(cs.`First_Name`, ' ', cs.`Surname`) AS 'CONSULTANT NAME', 
    cs.Email AS 'CONSULTANT EMAIL', 
    cr.Legal_name AS 'COMPANY NAME', 
    cr.Email AS 'COMPANY EMAIL' 
    FROM openlink_consultants.signup cs, openlink_companies.register cr 
    WHERE cs.CONSULTANT_ID=?
    AND cr.COMPANY_ID=?;
    ","ii"));
//EMAIL DETAILS

define("EMAIL_NAMES_SELECT", array("
SELECT s.Trade_Name as 'SMME NAME', c.Trade_Name as 'COMPANY NAME', s.email as 'SMME EMAIL', c.email as 'COMPANY EMAIL'
FROM openlink_companies.register as c, openlink_smmes.register as s 
WHERE s.SMME_ID = ?
AND c.COMPANY_ID = ?
", "ii"));