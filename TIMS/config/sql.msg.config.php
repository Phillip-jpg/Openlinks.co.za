<?php
define("GETCHAT_SELECT", array(
"SELECT messages.*, sp.ext, sp.SMME_ID
 FROM messages INNER JOIN yasccoza_openlink_smmes.pimg as sp
ON sp.SMME_ID = messages.From_
WHERE (From_ = ? AND To_ = ?)
OR (From_ = ? AND To_ = ?)
UNION
SELECT messages.*, cp.ext, cp.COMPANY_ID FROM messages INNER JOIN yasccoza_openlink_companies.pimg as cp
ON cp.COMPANY_ID = messages.From_
WHERE (From_ = ? AND To_ = ?)
OR (From_ = ? AND To_ = ?)
UNION
SELECT messages.*, cp.avatar, cp.id FROM messages INNER JOIN yasccoza_tms_db.users as cp
ON cp.id = messages.From_
WHERE (From_ = ? AND To_ = ?)
OR (From_ = ? AND To_ = ?)
UNION
SELECT messages.*, cp.ext, cp.CONSULTANT_ID FROM messages INNER JOIN yasccoza_openlink_consultants.pimg as cp
ON cp.CONSULTANT_ID = messages.From_
WHERE (From_ = ? AND To_ = ?)
OR (From_ = ? AND To_ = ?) ORDER BY MESSAGE_ID;"
,"iiiiiiiiiiiiiiii"));

define("INSERTCHAT_INSERT", array(
    "INSERT INTO messages (To_, From_, message)
VALUES ( ?, ?, ?);"
,"iis"));

define("SEARCHCHAT_SELECT", array(
    "SELECT DISTINCT To_, sr.Legal_name, sp.ext, From_
    FROM messages, yasccoza_openlink_smmes.register AS sr, yasccoza_openlink_smmes.pimg AS sp
    WHERE (To_ = sr.SMME_ID OR From_ = sr.SMME_ID)
    AND sr.SMME_ID = sp.SMME_ID
    AND (From_ = ? OR To_=?)
    AND NOT sr.SMME_ID = ?
    AND NOT To_ = From_
    AND sr.Legal_name LIKE CONCAT('%',?,'%')
    UNION
    SELECT DISTINCT To_, cr.Legal_name, cp.ext, From_
    FROM messages, yasccoza_openlink_companies.register AS cr, yasccoza_openlink_companies.pimg AS cp
    WHERE (To_ = cr.COMPANY_ID OR From_ = cr.COMPANY_ID)
    AND cr.COMPANY_ID = cp.COMPANY_ID
    AND (From_ = ? OR To_=?)
    AND NOT cr.COMPANY_ID = ?
    AND NOT To_ = From_
    AND cr.Legal_name LIKE CONCAT('%',?,'%')
    UNION
    SELECT DISTINCT To_, CONCAT(cs.`First_Name`, ' ', cs.`Surname`) as 'Legal_name', cp.ext, From_
    FROM messages, yasccoza_openlink_consultants.signup AS cs, yasccoza_openlink_consultants.pimg AS cp
    WHERE (To_ = cs.CONSULTANT_ID OR From_ = cs.CONSULTANT_ID)
    AND cs.CONSULTANT_ID = cp.CONSULTANT_ID
    AND (From_ = ? OR To_=?)
    AND NOT cs.CONSULTANT_ID = ?
    AND NOT To_ = From_
    AND CONCAT(cs.`First_Name`, ' ', cs.`Surname`) LIKE CONCAT('%',?,'%')
    UNION
    SELECT DISTINCT To_, CONCAT('ADMIN ', ads.firstname) as 'Legal_name', ads.avatar, From_
    FROM messages, yasccoza_tms_db.users AS ads
    WHERE (To_ = ads.id OR From_ = ads.id)
    AND (From_ = ? OR To_=?)
    AND NOT ads.id = ?
    AND NOT To_ = From_
    AND CONCAT('ADMIN ', ads.firstname) LIKE CONCAT('%',?,'%')
    ORDER BY From_ DESC;"
    ,"iiisiiisiiisiiis"));
    
define("GETUSERSCHAT_SELECT", array(
    "SELECT DISTINCT To_, sr.Legal_name, sp.ext, From_
    FROM messages, yasccoza_openlink_smmes.register AS sr, yasccoza_openlink_smmes.pimg AS sp
    WHERE (To_ = sr.SMME_ID OR From_ = sr.SMME_ID)
    AND sr.SMME_ID = sp.SMME_ID
    AND (From_ = ? OR To_=?)
    AND NOT sr.SMME_ID = ?
    AND NOT To_ = From_
    UNION
    SELECT DISTINCT To_, cr.Legal_name, cp.ext, From_
    FROM messages, yasccoza_openlink_companies.register AS cr, yasccoza_openlink_companies.pimg AS cp
    WHERE (To_ = cr.COMPANY_ID OR From_ = cr.COMPANY_ID)
    AND cr.COMPANY_ID = cp.COMPANY_ID
    AND (From_ = ? OR To_=?)
    AND NOT cr.COMPANY_ID = ?
    AND NOT To_ = From_
    UNION
    SELECT DISTINCT To_, CONCAT(cs.`First_Name`, ' ', cs.`Surname`) as 'Legal_name', cp.ext, From_
    FROM messages, yasccoza_openlink_consultants.signup AS cs, yasccoza_openlink_consultants.pimg AS cp
    WHERE (To_ = cs.CONSULTANT_ID OR From_ = cs.CONSULTANT_ID)
    AND cs.CONSULTANT_ID = cp.CONSULTANT_ID
    AND (From_ = ? OR To_=?)
    AND NOT cs.CONSULTANT_ID = ?
    AND NOT To_ = From_
    UNION
    SELECT DISTINCT To_, CONCAT('ADMIN ', ads.firstname) as 'Legal_name', ads.avatar, From_
    FROM messages, yasccoza_tms_db.users AS ads
    WHERE (To_ = ads.id OR From_ = ads.id)
    AND (From_ = ? OR To_=?)
    AND NOT ads.id = ?
    AND NOT To_ = From_;"
    ,"iiiiiiiiiiii"));

define("DATACHAT_SELECT", array(
    "SELECT * FROM messages WHERE (To_ = ?
    OR From_ = ?) AND (From_ = ? OR To_ = ?)
    ORDER BY MESSAGE_ID DESC LIMIT 1"
    ,"iiii"));

define("DATA_UNREAD_SELECT", array(
    "SELECT COUNT(status) as status
    FROM `messages` 
    WHERE status = 0
    AND To_ = ?
    AND From_ = ?"
    ,"ii"));

    define("HEADER_UNREAD_SELECT", array(
        "SELECT COUNT(status) as status
        FROM `messages` 
        WHERE status = 0
        AND To_ = ?"
        ,"i"));

    define("SEEN_UPDATE", array(
        "UPDATE `messages` SET  status= ?
        WHERE status = ?
        AND To_ = ?
        AND From_ = ?","iiii"));

    define("MAAR_UPDATE", array(
        "UPDATE `messages` SET  status= ?
        WHERE status = ?
        AND To_ = ?","iii"));



    define("DYNAMICUSER_SELECT", array(
        "SELECT Legal_name, ext, r.SMME_ID AS ID
        FROM register AS r, pimg AS p
        WHERE r.SMME_ID = p.SMME_ID
        AND r.SMME_ID=?
        UNION
        SELECT Legal_name, ext, cr.COMPANY_ID AS ID
        FROM yasccoza_openlink_companies.register AS cr, yasccoza_openlink_companies.pimg AS cp
        WHERE cr.COMPANY_ID = cp.COMPANY_ID
        AND cr.COMPANY_ID=?
        UNION
        SELECT CONCAT(cs.`First_Name`, ' ', cs.`Surname`) as 'Legal_name', ext, cs.CONSULTANT_ID AS ID
        FROM yasccoza_openlink_consultants.signup AS cs, yasccoza_openlink_consultants.pimg AS cp
        WHERE cs.CONSULTANT_ID = cs.CONSULTANT_ID
        AND cs.CONSULTANT_ID=?
        UNION
        SELECT CONCAT('ADMIN ', ads.firstname) as 'Legal_name', avatar, ads.id AS ID
        FROM yasccoza_tms_db.users AS ads
        WHERE ads.id = ?;"
        ,"iiii"));
    








