<?php 

    define("UNAUTH_SEARCH_INSERT", array(
    "INSERT INTO `search`(
        `hits`,
        `IP_ID`,
        `Term`
    )
    VALUES(?,?,?)","iss"));

define("UNAUTH_SEARCH_SELECT", array(
    "SELECT Legal_name, s.SMME_ID AS ID, s.typeOfEntity, p.ext, i.title
    FROM register AS r, signup AS s, pimg AS p, openlink_association_db.industry_title as i
    WHERE r.SMME_ID = s.SMME_ID
    AND s.SMME_ID = p.SMME_ID
    AND r.INDUSTRY_ID= i.TITLE_ID
    AND Legal_name LIKE CONCAT('%',?,'%')
    UNION
    SELECT Legal_name, s.COMPANY_ID AS ID, s.typeOfEntity, p.ext, i.title
    FROM openlink_companies.register AS r, openlink_companies.signup AS s, openlink_companies.pimg AS p, openlink_association_db.industry_title as i
    WHERE r.COMPANY_ID = s.COMPANY_ID
    AND s.COMPANY_ID = p.COMPANY_ID
    AND r.INDUSTRY_ID= i.TITLE_ID
    AND Legal_name LIKE CONCAT('%',?,'%');","ss"));

