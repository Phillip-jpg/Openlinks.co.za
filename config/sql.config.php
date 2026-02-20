<?php
define("SIGNUP_SELECT", array("SELECT Username FROM signup WHERE Username=?;","s"));
define("SIGNUP_INSERT", array("INSERT INTO signup (First_Name, Surname, Username, Email, Pwd, Terms_Policies) VALUES (?, ?, ?, ?, ?, ?);","sssssi"));
define("NPO_SIGNUP_INSERT", array('INSERT INTO signup (First_Name, Surname, Username, Email, Pwd, Terms_Policies, typeOfEntity) VALUES (?, ?, ?, ?, ?, ?, "NPO");',"sssssi"));


define("SMME_DEFAULTPROFILE_SELECT", array("SELECT SMME_ID FROM signup WHERE Username=?;","s"));
define("NPO_DEFAULTPROFILE_SELECT", array("SELECT NPO_ID FROM signup WHERE Username=?;","s"));
define("COMPANY_DEFAULTPROFILE_SELECT", array("SELECT COMPANY_ID FROM signup WHERE Username=?;","s"));
define("CONSULTANT_DEFAULTPROFILE_SELECT", array("SELECT CONSULTANT_ID FROM signup WHERE Username=?;","s"));
define("SMME_DEFAULTPROFILE_INSERT", array("INSERT INTO pimg (SMME_ID) VALUES (?);","i"));
define("NPO_DEFAULTPROFILE_INSERT", array("INSERT INTO pimg (NPO_ID) VALUES (?);","i"));
define("COMPANY_DEFAULTPROFILE_INSERT", array("INSERT INTO pimg (COMPANY_ID) VALUES (?);","i"));
define("CONSULTANT_DEFAULTPROFILE_INSERT", array("INSERT INTO pimg (CONSULTANT_ID) VALUES (?);","i"));


define("LOGIN_SELECT", array('SELECT * FROM signup WHERE Username=? AND typeOfEntity=?;',"ss"));


define("ADMIN_PIMG_SELECT", array("SELECT * FROM pimg WHERE ADMIN_ID=?;","i"));
define("SMME_PIMG_SELECT", array("SELECT * FROM pimg WHERE SMME_ID=?;","i"));
define("NPO_PIMG_SELECT", array("SELECT * FROM pimg WHERE NPO_ID=?;","i"));
define("COMPANY_PIMG_SELECT", array("SELECT * FROM pimg WHERE COMPANY_ID=?;","i"));
define("CONSULTANT_PIMG_SELECT", array("SELECT * FROM pimg WHERE CONSULTANT_ID=?;","i"));

define("SMME_ADMIN_SELECT", array("SELECT SMME_ID FROM admin WHERE SMME_ID=?","i"));
define("NPO_ADMIN_SELECT", array("SELECT NPO_ID FROM admin WHERE NPO_ID=?","i"));
define("COMPANY_ADMIN_SELECT", array("SELECT COMPANY_ID FROM admin WHERE COMPANY_ID=?","i"));
define("SMME_ADMIN_INSERT", array("INSERT INTO admin (first_name, Surname, Identification_Type, ID_Number, Gender, Email, Ethnic_Group, SMME_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?);","sssisssi"));
define("COMPANY_ADMIN_INSERT", array("INSERT INTO admin (first_name, Surname, Identification_Type, ID_Number, Gender, Email, Ethnic_Group, COMPANY_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?);","sssisssi"));

define("SMME_ADDCOMPANYSTATEMENTS_INSERT", array("INSERT INTO `company_profile`(`introduction`, `vision`, `mission`, `values_`, `goals_objectives`,`SMME_ID`)  VALUES (?, ?, ?, ?, ?, ?)","sssssi"));
define("NPO_ADDCOMPANYSTATEMENTS_INSERT", array("INSERT INTO company_profile ( introduction, vision, mission, values_, goals_objectives, products_services, SMME_ID) VALUES (?, ?, ?, ?, ?, ?, ?)","ssssssi"));

define("SMME_DIRECTORS_SELECT", array("SELECT SMME_ID FROM company_director WHERE SMME_ID=?","i"));
define("NPO_DIRECTORS_SELECT", array("SELECT NPO_ID FROM company_director WHERE SMME_ID=?","i"));
define("SMME_DIRECTORS_INSERT", array("INSERT INTO company_director (Name, Surname, Identification_Type, ID_Number, Ethnic_Group, Gender, SMME_ID) VALUES ","sssissi"));
define("NPO_DIRECTORS_INSERT", array("INSERT INTO company_director (Name, Surname, Identification_Type, ID_Number, Ethnic_Group, Gender, NPO_ID) VALUES ","sssissi"));

define("SMME_ADDCOMPANYDOCUMENTS_INSERT", array("INSERT INTO company_documentation (Number_Shareholders, Number_Black_Shareholders, Number_White_Shareholders, Black_Ownership_Percentage, Black_Female_Percentage, White_Ownership_Percentage, BBBEE_Status, Date_Of_Issue, Expiry_Date, SMME_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)","iiiiiiissi"));
define("NPO_ADDCOMPANYDOCUMENTS_INSERT", array("INSERT INTO company_documentation (Number_shareholders, Number_Black_Shareholders, Number_White_Shareholders, Black_Ownership_Percentage, Black_Female_Percentage, White_Ownership_percentage, BBBEE_Status, Date_Of_Issue, Expiry_Date, NPO_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)","iiiiiiissi"));

// AND S.typeOfEntity = 'SMME'


define("SMME_PRODUCT_INSERT", array("INSERT INTO products (product_name,product_description,SMME_ID) VALUES "));

define("SMME_EXPENSESUMMARY_INSERT", array("INSERT INTO expense_summary (service_provider, product_name, product_specification, rand_value, frequency, type_of_expense, SMME_ID) VALUES "));
define("NPO_EXPENSESUMMARY_INSERT", array("INSERT INTO expense_summary (service_provider, product_name, product_specification, rand_value, frequency, type_of_expense, NPO_ID) VALUES "));
define("SMME_EXPENSESUMMARY_SELECT", array("SELECT `EXPENSE_NUMBER`, `service_provider`, `product_name`, `product_specification`, `rand_value`, `frequency`, `type_of_expense`, E.SMME_ID
FROM expense_summary AS E, signup AS S 
WHERE E.SMME_ID = S.SMME_ID 
AND S.SMME_ID = ? 
ORDER BY type_of_expense;", "i"));
define("NPO_EXPENSESUMMARY_SELECT", array("SELECT `EXPENSE_NUMBER`, `service_provider`, `product_name`, `product_specification`, `rand_value`, `frequency`, `type_of_expense`, `SMME_ID`
FROM expense_summary AS E, signup AS S 
WHERE E.SMME_ID = S.SMME_ID 
AND S.typeOfEntity = 'NPO'
AND S.SMME_ID=?;", "i"));
define("SMME_TEMP_UPDATE", array("UPDATE pimg SET  ext='?'  WHERE SMME_ID='?';","si"));
define("NPO_TEMP_UPDATE", array("UPDATE pimg SET  ext='?'  WHERE NPO_ID='?';","si"));
define("COMPANY_TEMP_UPDATE", array("UPDATE pimg SET  ext='?'  WHERE COMPANY_ID='?';","si"));
define("CONSULTANT_TEMP_UPDATE", array("UPDATE pimg SET  ext='?'  WHERE CONSULTANT_ID='?';","si"));



define("SMME_REGISTER_SELECT", array("SELECT SMME_ID FROM register WHERE SMME_ID=?","i"));
define("NPO_REGISTER_SELECT", array("SELECT NPO_ID FROM register WHERE NPO_ID=?","i"));
define("COMPANY_REGISTER_SELECT", array("SELECT COMPANY_ID FROM register WHERE COMPANY_ID=?","i"));
define("SMME_REGISTER_INSERT", array("INSERT INTO register (Legal_name, CC_Registration_Number, Address, Post_Code, city, Province, Contact, Email, INDUSTRY_ID, SMME_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)","sisissisii"));
define("NPO_REGISTER_INSERT", array("INSERT INTO register (Legal_name, CC_Registration_Number, Address, Post_Code, city, Province, Contact, Email, INDUSTRY_ID, NPO_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)","sisissisii"));
define("COMPANY_REGISTER_INSERT", array("INSERT INTO register (Legal_name, CC_Registration_Number, Address, Post_Code, city, Province, Contact, Email, INDUSTRY_ID, COMPANY_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)","sisissisii"));

define("CONSULTANT_REGISTER_SELECT", array("SELECT CONSULTANT_ID FROM consultant_information WHERE CONSULTANT_ID=?","i"));
define("CONSULTANT_REGISTER_INSERT", array("INSERT INTO consultant_information (Ethnic_Group, Identification_Type, ID_Number, Gender, CONSULTANT_ID) VALUES (?, ?, ?, ?, ?)","ssisi"));


define("SMME_REGISTERCOMPANYDATA_SELECT", array("SELECT SMME_ID FROM company_data WHERE SMME_ID=?","i"));
define("NPO_REGISTERCOMPANYDATA_SELECT", array("SELECT SMME_ID FROM company_data WHERE NPO_ID=?","i"));
define("SMME_REGISTERCOMPANYDATA_INSERT", array("INSERT INTO company_data (Legal_name, CC_Registration_Number, Trading_Name, Financial_Year, SMME_ID) VALUES (?, ?, ?, ?, ?)","sissi"));
define("NPO_REGISTERCOMPANYDATA_INSERT", array("INSERT INTO company_data (Legal_name, CC_Registration_Number, Trading_Name, Financial_Year,  NPO_ID) VALUES (?, ?, ?, ?, ?, ?, ?)","sissssi"));

define("CONSULTANT_ADMIN_SELECT", array("SELECT CONSULTANT_ID FROM consultant_information WHERE CONSULTANT_ID=?","i"));
define("CONSULTANT_ADMIN_INSERT", array("INSERT INTO admin (first_name, Surname, Identification_Type, ID_Number, Gender, Email, Ethnic_Group, CONSULTANT_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?);","sssisssi"));

// display all companies
define("COMPANY_TOVIEW1_SELECT1", array("SELECT s.typeOfEntity as T, Legal_name, city, Province, p.ext , r.COMPANY_ID AS ID
FROM ((signup as s
INNER JOIN register AS r
ON s.COMPANY_ID=r.COMPANY_ID)
INNER JOIN pimg AS p 
ON r.COMPANY_ID=p.COMPANY_ID)
ORDER BY Legal_name"));

define("COMPANY_TOVIEW1_SELECT2", array("SELECT Legal_name, Address, p.ext, a.Progress, a.EVENT_ID, r.COMPANY_ID AS ID,
s.typeOfEntity, i.title
FROM openlink_companies.register r, openlink_companies.pimg p, openlink_association_db.smme_company_events a,
signup AS s, openlink_association_db.industry_title as i
WHERE r.COMPANY_ID=p.COMPANY_ID
AND p.COMPANY_ID=a.COMPANY_ID
AND r.COMPANY_ID = s.COMPANY_ID
AND r.INDUSTRY_ID= i.TITLE_ID
AND a.EVENT_ID=?
AND a.SMME_ID=?;" , "ii"));

define("COMPANY_TOVIEW1_SELECT3", array(" " , " "));
// define("TOVIEW1_SELECT", array("SELECT Legal_name, city, Province FROM register"));// LIMIT 

//we need more information from companies

//displays all smmes
define("SMME_TOVIEW1_SELECT1", array("SELECT DISTINCT register.Legal_name, city, Province, ext , BBBEE_Status, register.SMME_ID AS ID, s.typeOfEntity as T
FROM signup as s, register, company_documentation, company_profile, pimg, openlink_association_db.smme_company_events AS sce
WHERE s.SMME_ID=register.SMME_ID
AND register.SMME_ID=company_documentation.SMME_ID 
AND company_documentation.SMME_ID=company_profile.SMME_ID
AND company_profile.SMME_ID=pimg.SMME_ID
AND NOT pimg.SMME_ID=sce.SMME_ID;"));

define("SMME_TOVIEW1_SELECT2", array("SELECT register.Legal_name, Address, ext, BBBEE_Status, a.Progress, a.EVENT_ID, register.SMME_ID AS ID,
s.typeOfEntity, i.title
FROM register, company_documentation, company_profile, pimg p, openlink_association_db.smme_company_events a,
signup AS s, openlink_association_db.industry_title as i
WHERE register.SMME_ID=company_documentation.SMME_ID 
AND company_documentation.SMME_ID=company_profile.SMME_ID  
AND company_profile.SMME_ID=p.SMME_ID 
AND p.SMME_ID=a.SMME_ID
AND register.SMME_ID = s.SMME_ID
AND register.INDUSTRY_ID= i.TITLE_ID
AND a.EVENT_ID=?
AND a.COMPANY_ID=?;", "ii"));



define("SMME_TOVIEW1_SELECT3", array(" ", " "));//comparative charts sql for smmes

define("NPO_TOVIEW1_SELECT1", array("SELECT register.Legal_name, city, Province, ext, products_services, BBBEE_Status, register.NPO_ID AS ID
FROM register, company_documentation, company_profile, pimg 
WHERE register.NPO_ID=company_documentation.NPO_ID 
AND company_documentation.NPO_ID=company_profile.NPO_ID
AND company_profile.NPO_ID=pimg.NPO_ID;"));

define("NPO_TOVIEW1_SELECT2", array("SELECT register.Legal_name, city, Province, ext, products_services, BBBEE_Status, a.EVENT_ID, register.NPO_ID AS ID
FROM register, company_documentation, company_profile, pimg p, openlink_association_db.npo_company_events a
WHERE register.NPO_ID=company_documentation.NPO_ID 
AND company_documentation.NPO_ID=company_profile.NPO_ID
AND company_profile.NPO_ID=p.NPO_ID 
AND p.NPO_ID=a.NPO_ID
AND a.EVENT_ID=?
AND a.COMPANY_ID=?;" , "ii"));

define("NPO_TOVIEW1_SELECT3", array(" " , " "));

// define("COMPANY_PEEK_SELECT", array("SELECT Legal_name, city, Province, p.ext, a.Progress 
// FROM (openlink_companies.register r 
// INNER JOIN openlink_companies.pimg p ON r.COMPANY_ID=p.COMPANY_ID) 
// INNER JOIN openlink_association_db.smme_company_association a ON p.COMPANY_ID=a.COMPANY_ID 
// WHERE a.SMME_ID=?;" , "i"));

define("SMME_STEP_SELECT", array("SELECT progress FROM smme_company_association WHERE SMME_ID=?","i"));
//smme_company_association

define("COMPARITIVE_CHART",array("SELECT
r.Legal_name,
BBBEE_Status,
SUM(rand_value) AS rand_value
FROM
openlink_smmes.register r,
openlink_smmes.expense_summary e,
openlink_smmes.company_documentation c,
openlink_association_db.smme_company_events a
WHERE
r.SMME_ID = e.SMME_ID 
AND e.SMME_ID = c.SMME_ID 
AND c.SMME_ID = a.SMME_ID
AND a.COMPANY_ID = ?
GROUP BY
r.legal_name;", "i"));//AND a.COMPANY_ID = ?

define("SMME_MOREINFO", array("SELECT ext, r.Legal_name, r.city, r.Province, r.foo, r.Contact, r.Email, p.vision, p.values_, p.mission, p.introduction, d.Number_Shareholders, d.Black_Ownership_Percentage, d.White_Ownership_Percentage, d.Black_Female_Percentage, a.title 
FROM openlink_smmes.register r,openlink_association_db.industry_title a, openlink_smmes.company_profile p, openlink_smmes.pimg, openlink_smmes.company_documentation d
WHERE r.INDUSTRY_ID= a.TITLE_ID
AND r.SMME_ID = p.SMME_ID
AND p.SMME_ID = d.SMME_ID 
AND d.SMME_ID = pimg.SMME_ID 
AND pimg.SMME_ID = ?", "i"));

define("SMME_MOREINFO_CHART", array("SELECT d.Number_Shareholders, d.White_Ownership_Percentage, d.Black_Ownership_Percentage, d.Black_Female_Percentage
FROM  openlink_smmes.company_documentation d, openlink_association_db.smme_company_events a
WHERE d.SMME_ID = a.SMME_ID
AND a.SMME_ID = ?
AND a.COMPANY_ID = ?", "ii"));

define("VALIDATE_CONNECTION_SELECT", array(
    "SELECT ASSOCIATION_ID FROM `smme_company_events` 
    WHERE COMPANY_ID = ?
    AND SMME_ID=?",
    "ii"));

define("COMPANY_MOREINFO", array("SELECT ext, r.Legal_name ,r.Contact, r.Email, r.city, r.Province, r.foo, a.title
FROM register r, openlink_association_db.industry_title a, pimg 
WHERE r.INDUSTRY_ID= a.TITLE_ID
AND pimg.COMPANY_ID=r.COMPANY_ID
AND r.COMPANY_ID = ?", "i"));
define("SMME_PRODUCT_SELECT", array("SELECT product_name as product FROM `products` WHERE SMME_ID = ? ", "i"));

define("SMME_BUSINESS_LINKS_INSERT", array("INSERT INTO `business_links`(`SMME_ID`, `LINK_ID`, `url`) VALUES "));
define("COMPANY_BUSINESS_LINKS_INSERT", array("INSERT INTO `business_links`(`COMPANY_ID`, `LINK_ID`, `url`) VALUES"));
define("SMME_BUSINESS_LINK_VISITS_INSERT", array("INSERT INTO `business_links`(`SMME_ID`, `LINK_ID`, `url`) VALUES (?, ?, ?)", "iii"));
define("COMPANY_BUSINESS_LINK_VISITS_INSERT", array("INSERT INTO `business_links`(`SMME_ID`, `LINK_ID`, `url`) VALUES (?, ?, ?)", "iii"));
define("SMME_VIEWS_INSERT", array("INSERT INTO `entity_clicks`(
    `TYPE`,
    `WHO_CLICKED`,
    `WHO_TO_VIEW`
)
VALUES(
   ?,
  ?,
  ?
)", "sii"));

define("COMPANY_VIEWS_INSERT", array("INSERT INTO `entity_clicks`(
    `TYPE`,
    `WHO_CLICKED`,
    `WHO_TO_VIEW`
)
VALUES(
   ?,
  ?,
  ?
)", "sii"));