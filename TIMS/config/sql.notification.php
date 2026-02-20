<?php 

    define("NOTIFICATION_INSERT", array(
    "INSERT INTO openlink_association_db.notifications
    (
     `EVENT_ID`,
     `From_`,
     `To_`,
      From_entity,
      To_entity
    ) 
    VALUES (
      ?,
      ?,
      ?,
      ?,
      ?
    );","iiiss"));



define("NOTIFICATION_INSERT_DESCRIPTION", array(
  "INSERT INTO openlink_association_db.notifications
  (
   `EVENT_ID`,
   `From_`,
   `To_`,
   From_entity,
   To_entity,
   `Description`
  ) 
  VALUES (
    ?,
    ?,
    ?,
    ?,
    ?,
    ?
  );","iiisss"));

    define("NOTIFICATION_SELECT_EVENT", array(
      "SELECT event_date FROM `smme_company_events` 
      WHERE `COMPANY_ID`=? 
      AND `SMME_ID`=?
      AND `EVENT_ID`=?;","iii"));

define("NOTIFICATION_SELECT_WHO", array(
  "SELECT s.* FROM (SELECT `typeOfEntity` FROM openlink_smmes.signup WHERE `SMME_ID` = ? UNION
  SELECT `typeOfEntity` FROM openlink_companies.signup WHERE `COMPANY_ID` = ? UNION
  SELECT `typeOfEntity` FROM openlink_consultants.signup WHERE `CONSULTANT_ID` = ? UNION
  SELECT `typeOfEntity` FROM openlink_admin_db.signup WHERE `ADMIN_ID` = ?) s
  LIMIT 1","iiii"));




    define("NOTIFICATION_SELECT_ALL", array(
    "SELECT * FROM `notifications` WHERE To_=? ORDER BY NOTIFICATION_ID DESC;","i"));

    define("NOTIFICATION_SELECT_P_COMPANY", array(
      "SELECT * FROM `notifications` WHERE To_=? AND EVENT_ID NOT IN(45, 47, 49) ORDER BY NOTIFICATION_ID DESC;","i"));

    define("NOTIFICATION_SELECT", array(
        "SELECT * FROM `notifications` WHERE To_=?
        AND EVENT_ID=?;","ii"));

define("SMME_NAME_SELECT", array(
  "SELECT Legal_name FROM openlink_smmes.register WHERE SMME_ID=? UNION
   SELECT Legal_name FROM openlink_companies.register WHERE COMPANY_ID=? UNION
   SELECT concat(cs.First_Name, ' ', cs.Surname) AS 'Legal_name' 
      FROM openlink_consultants.signup cs WHERE CONSULTANT_ID=?;","iii"));
  define("COMPANY_NAME_SELECT", array(
    "SELECT Legal_name FROM openlink_smmes.register WHERE SMME_ID=? UNION
    SELECT Legal_name FROM openlink_companies.register WHERE COMPANY_ID=? UNION
    SELECT concat(cs.First_Name, ' ', cs.Surname) AS 'Legal_name' 
       FROM openlink_consultants.signup cs WHERE CONSULTANT_ID=?;","iii"));
    define("CONSULTANT_NAME_SELECT", array(
      "SELECT Legal_name FROM openlink_smmes.register WHERE SMME_ID=? UNION
      SELECT Legal_name FROM openlink_companies.register WHERE COMPANY_ID=? UNION
      SELECT concat(cs.First_Name, ' ', cs.Surname) AS 'Legal_name' 
         FROM openlink_consultants.signup cs WHERE CONSULTANT_ID=?;","iii"));


      

define("SMME_RREAD_SELECT", array(
  "SELECT
  register.Legal_name,
  city,
  Province,
  BBBEE_Status,
  register.SMME_ID AS ID,
  introduction,
  vision,
  mission,
  values_,
  goals_objectives,
  register.Email,
  register.Contact,
  register.foo,
  ai.title
FROM
  (
          (
              (
                  register
              INNER JOIN company_documentation ON register.SMME_ID = company_documentation.SMME_ID
              )
          INNER JOIN company_profile ON company_documentation.SMME_ID = company_profile.SMME_ID
          )
  LEFT JOIN openlink_association_db.industry_title ai
  ON
      register.INDUSTRY_ID = ai.INDUSTRY_ID
  ) WHERE register.SMME_ID = ?;","i"));

define("NPO_RREAD_SELECT", array(
  "SELECT register.Legal_name, city, Province, BBBEE_Status, register.NPO_ID AS ID, introduction, vision, mission, values_, goals_objectives
  FROM register, company_documentation, company_profile, company_data
  WHERE register.NPO_ID=company_documentation.NPO_ID
  AND company_documentation.NPO_ID=company_profile.NPO_ID
  AND company_profile.NPO_ID=company_data.NPO_ID
  AND register.NPO_ID = ? ;","i"));

define("COMPANY_RREAD_SELECT", array(
  "SELECT Legal_name, city, Province, r.COMPANY_ID AS ID, introduction, vision, mission, values_ , goals_objectives, r.Email , r.Contact, r.foo, ai.title
  FROM openlink_companies.register AS r, openlink_companies.company_profile, openlink_association_db.industry_title ai
  WHERE r.COMPANY_ID = company_profile.COMPANY_ID
  AND ai.TITLE_ID = r.INDUSTRY_ID
  AND r.COMPANY_ID =?
  ORDER BY Legal_name;","i"));
