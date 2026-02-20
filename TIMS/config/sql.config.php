<?php
define("SIGNUP_SELECT", array("SELECT Username FROM signup WHERE Username=?;","s"));
define("SIGNUP_INSERT", array("INSERT INTO signup (First_Name, Surname, Username, Email, Pwd, Terms_Policies) VALUES (?, ?, ?, ?, ?, ?);","sssssi"));
define("ADMIN_SIGNUP_INSERT", array("INSERT INTO signup (First_Name, Surname, Username, Email, Pwd, Terms_Policies, Role, City, Province, Industry) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);","sssssisssi"));
define("ADMIN_SECTOR_INSERT", array("INSERT INTO admin_sector (ADMIN_ID,INDUSTRY_ID) VALUES (?, ?);","ii"));
define("NPO_SIGNUP_INSERT", array('INSERT INTO signup (First_Name, Surname, Username, Email, Pwd, Terms_Policies, typeOfEntity) VALUES (?, ?, ?, ?, ?, ?, "NPO");',"sssssi"));
define("EMAIL_VERIFICATION_INSERT", array("INSERT INTO email_verification (LINK, Email) VALUES (?,?)","ss"));
define("ADMIN_VERIFY_ACCOUNT_SELECT", array("SELECT 1 FROM yasccoza_openlink_admin_db.email_verification WHERE LINK=? and Email=?;","ss"));
define("SMME_VERIFY_ACCOUNT_SELECT", array("SELECT 1 FROM yasccoza_openlink_smmes.email_verification WHERE LINK=? and Email=?;","ss"));
define("COMPANY_VERIFY_ACCOUNT_SELECT", array("SELECT 1 FROM yasccoza_openlink_companies.email_verification WHERE LINK=? and Email=?;","ss"));
define("CONSULTANT_VERIFY_ACCOUNT_SELECT", array("SELECT 1 FROM yasccoza_openlink_consultants.email_verification WHERE LINK=? and Email=?;","ss"));
define("ADMIN_VERIFY_ACCOUNT_UPDATE", array("UPDATE yasccoza_openlink_admin_db.signup SET verified = 1 WHERE Email=?;","s"));
define("SMME_VERIFY_ACCOUNT_UPDATE", array("UPDATE yasccoza_openlink_smmes.signup SET verified = 1 WHERE Email=?;","s"));
define("COMPANY_VERIFY_ACCOUNT_UPDATE", array("UPDATE yasccoza_openlink_companies.signup SET verified = 1 WHERE Email=?;","s"));
define("CONSULTANT_VERIFY_ACCOUNT_UPDATE", array("UPDATE yasccoza_openlink_consultants.signup SET verified = 1 WHERE Email=?;","s"));
define("SMME_DEFAULTPROFILE_SELECT", array("SELECT SMME_ID FROM signup WHERE Username=?;","s"));
define("ADMIN_DEFAULTPROFILE_SELECT", array("SELECT ADMIN_ID FROM signup WHERE Username=?;","s"));
define("NPO_DEFAULTPROFILE_SELECT", array("SELECT NPO_ID FROM signup WHERE Username=?;","s"));
define("COMPANY_DEFAULTPROFILE_SELECT", array("SELECT COMPANY_ID FROM signup WHERE Username=?;","s"));
define("CONSULTANT_DEFAULTPROFILE_SELECT", array("SELECT CONSULTANT_ID FROM signup WHERE Username=?;","s"));
define("SMME_DEFAULTPROFILE_INSERT", array("INSERT INTO pimg (SMME_ID) VALUES (?);","i"));
define("ADMIN_DEFAULTPROFILE_INSERT", array("INSERT INTO pimg (ADMIN_ID) VALUES (?);","i"));
define("COMPANY_DEFAULTPROFILE_INSERT", array("INSERT INTO pimg (COMPANY_ID) VALUES (?);","i"));
define("CONSULTANT_DEFAULTPROFILE_INSERT", array("INSERT INTO pimg (CONSULTANT_ID) VALUES (?);","i"));
define("DISPLAY_ADMIN_CHAT_SELECT", array("SELECT * FROM yasccoza_tms_db.users"));

define("ADMINS_NAVIGATION_SELECT", array("SELECT i.office,i.INDUSTRY_ID, t.title,t.TITLE_ID, s.type, s.id
FROM  yasccoza_openlink_association_db.industry i, yasccoza_openlink_association_db.industry_title t, yasccoza_tms_db.users s, yasccoza_openlink_admin_db.admin_sector
WHERE yasccoza_openlink_admin_db.admin_sector.OFFICE_ID = i.INDUSTRY_ID
AND yasccoza_openlink_admin_db.admin_sector.INDUSTRY_ID= t.TITLE_ID
AND s.id = yasccoza_openlink_admin_db.admin_sector.ADMIN_ID"));

define("COM_PRODUCT_DELETE", array("UPDATE products SET Active = 1 WHERE products.PRODUCT_ID = ? AND products.COMPANY_ID=? ","ii"));

define("COMPANY_PRODUCT_UPDATE", array("UPDATE products SET product_name= ?, product_description=?, price=? WHERE COMPANY_ID=? AND PRODUCT_ID=?
","ssiii"));

define("LOGIN_SELECT", array('SELECT * FROM signup WHERE Username=? AND typeOfEntity=?;',"ss"));
define("ADMIN_LOGIN_SELECT", array('SELECT * FROM yasccoza_tms_db.users WHERE email=? ;',"s"));

define("ADMIN_PIMG_SELECT", array("SELECT * FROM pimg WHERE ADMIN_ID=?;","i"));
define("SMME_PIMG_SELECT", array("SELECT * FROM pimg WHERE SMME_ID=?;","i"));
define("NPO_PIMG_SELECT", array("SELECT * FROM pimg WHERE NPO_ID=?;","i"));
define("COMPANY_PIMG_SELECT", array("SELECT * FROM pimg WHERE COMPANY_ID=?;","i"));
define("CONSULTANT_PIMG_SELECT", array("SELECT * FROM pimg WHERE CONSULTANT_ID=?;","i"));

define("SMME_ADMIN_SELECT", array("SELECT SMME_ID FROM admin WHERE SMME_ID=?","i"));
define("SMME_ADMIN_ACTIVE", array ("SELECT Active FROM admin WHERE SMME_ID=?","i"));
define("SMME_COMPANY_ACTIVE", array ("SELECT Active FROM admin WHERE COMPANY_ID=?","i"));
define("SMME_REGISTER_ACTIVE", array ("SELECT Active FROM register WHERE SMME_ID=?","i"));
define("COMPANY_REGISTER_ACTIVE", array ("SELECT Active FROM register WHERE COMPANY_ID=?","i"));
define("SMME_SELECT_ACTIVE", array ("SELECT Active FROM company_documentation WHERE SMME_ID=?","i"));

define("NPO_ADMIN_SELECT", array("SELECT NPO_ID FROM admin WHERE NPO_ID=?","i"));
define("COMPANY_ADMIN_SELECT", array("SELECT COMPANY_ID FROM admin WHERE COMPANY_ID=?","i"));
define("SMME_ADMIN_INSERT", array("INSERT INTO admin (first_name, Surname, Identification_Type, ID_Number, Gender, Email, Ethnic_Group, SMME_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?);","sssisssi"));
define("COMPANY_ADMIN_INSERT", array("INSERT INTO admin (first_name, Surname, Identification_Type, ID_Number, Gender, Email, Ethnic_Group, COMPANY_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?);","sssisssi"));

define("SMME_ADDCOMPANYSTATEMENTS_SELECT", array("SELECT SMME_ID, Active FROM company_profile WHERE SMME_ID=?","i"));
define("COMPANY_ADDCOMPANYSTATEMENTS_SELECT", array("SELECT COMPANY_ID, Active FROM company_profile WHERE COMPANY_ID=?","i"));
define("SMME_ADDCOMPANYSTATEMENTS_INSERT", array("INSERT INTO `company_profile`(`introduction`, `vision`, `mission`, `values_`, `goals_objectives`,`SMME_ID`)  VALUES (?, ?, ?, ?, ?, ?)","sssssi"));
define("NPO_ADDCOMPANYSTATEMENTS_INSERT", array("INSERT INTO company_profile ( introduction, vision, mission, values_, goals_objectives, products_services, SMME_ID) VALUES (?, ?, ?, ?, ?, ?, ?)","ssssssi"));

define("SMME_DIRECTORS_SELECT", array("SELECT SMME_ID FROM company_director WHERE SMME_ID=?","i"));
define("NPO_DIRECTORS_SELECT", array("SELECT NPO_ID FROM company_director WHERE SMME_ID=?","i"));
define("SMME_DIRECTORS_INSERT", array("INSERT INTO company_director (Name, Surname, Identification_Type, ID_Number, Ethnic_Group, Gender, SMME_ID) VALUES ","sssissi"));
define("NPO_DIRECTORS_INSERT", array("INSERT INTO company_director (Name, Surname, Identification_Type, ID_Number, Ethnic_Group, Gender, NPO_ID) VALUES ","sssissi"));

define("SMME_ADDCOMPANYDOCUMENTS_INSERT", array("INSERT INTO company_documentation (Number_Shareholders, Number_Black_Shareholders, Number_White_Shareholders, Black_Ownership_Percentage, Black_Female_Percentage, White_Ownership_Percentage, BBBEE_Status, Date_Of_Issue, Expiry_Date, SMME_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)","iiiiiiissi"));
define("NPO_ADDCOMPANYDOCUMENTS_INSERT", array("INSERT INTO company_documentation (Number_shareholders, Number_Black_Shareholders, Number_White_Shareholders, Black_Ownership_Percentage, Black_Female_Percentage, White_Ownership_percentage, BBBEE_Status, Date_Of_Issue, Expiry_Date, NPO_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)","iiiiiiissi"));

define("SMME_FILEUPLOAD_INSERT", array("INSERT INTO file_uploads(`type`, `link`, `userID`) VALUES (?,?,?)","ssi"));

define("SMME_UPDATE_DOCUMENTATION", array("UPDATE company_documentation SET Number_Shareholders=?, Number_Black_Shareholders=?, Number_White_Shareholders=?, Black_Ownership_Percentage=?, Black_Female_Percentage=?, White_Ownership_Percentage=?, BBBEE_Status=?, Date_Of_Issue =?, Expiry_Date=?, Active=0 WHERE SMME_ID=? ","sssissssii"));

define("DIRECTOR_SMME_UPDATE", array("UPDATE company_director SET Name=?, Surname=?, Identification_Type=?, ID_Number=?, Ethnic_Group=?, Gender=? WHERE SMME_ID=? ","sssissi"));

define("STATEMENT_SMME_UPDATE", array("UPDATE company_profile SET introduction=?, vision=?, mission=?, values_=?, goals_objectives=?,Active=0 WHERE SMME_ID=? ","sssssi"));

define("STATEMENT_COMPANY_UPDATE", array("UPDATE company_profile SET introduction=?, vision=?, mission=?, values_=?, goals_objectives=?,Active=0 WHERE COMPANY_ID=? ","sssssi"));

define("SMME_PRODUCT_INSERT", array("INSERT INTO products (product_name,product_description,price,image,SMME_ID) VALUES "));
define("COMPANY_ADDCOMPANYSTATEMENTS_INSERT", array("INSERT INTO `company_profile`(`introduction`, `vision`, `mission`, `values_`, `goals_objectives`,`COMPANY_ID`)  VALUES (?, ?, ?, ?, ?, ?)","sssssi"));
define("COMPANY_PRODUCT_INSERT", array("INSERT INTO products (product_name,product_description,price,image,COMPANY_ID) VALUES "));

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
define("SMME_TEMP_UPDATE", array("UPDATE pimg SET  ext=?  WHERE SMME_ID=?;","si"));

define("COMPANY_TEMP_UPDATE", array("UPDATE pimg SET  ext=?  WHERE COMPANY_ID=?;","si"));
define("CONSULTANT_TEMP_UPDATE", array("UPDATE pimg SET  ext=?  WHERE CONSULTANT_ID=?;","si"));
define("FETCH_INDUUSTRY_ID", array("SELECT INDUSTRY_ID FROM yasccoza_openlink_association_db.industry_title WHERE TITLE_ID = ?;","i"));


define("COMPANY_PASSWORD_SELECT", array("SELECT * FROM yasccoza_openlink_companies.signup c WHERE c.COMPANY_ID=?", "i"));
define("SMME_PASSWORD_SELECT", array("SELECT * FROM yasccoza_openlink_smmes.signup c WHERE c.SMME_ID=?", "i"));
define("CONSULTANT_PASSWORD_SELECT", array("SELECT * FROM yasccoza_openlink_consultants.signup c WHERE c.CONSULTANT_ID=?", "i"));
define("ADMIN_PASSWORD_SELECT", array("SELECT * FROM yasccoza_openlink_admin_db.signup c WHERE c.ADMIN_ID=?", "i"));

define("ADMIN_PASSWORD_UPDATE", array("UPDATE yasccoza_openlink_admin_db.signup s SET s.Pwd=? WHERE s.ADMIN_ID=?","si"));
define("COMPANY_PASSWORD_UPDATE", array("UPDATE yasccoza_openlink_companies.signup s SET s.Pwd=? WHERE s.COMPANY_ID=?","si"));
define("SMME_PASSWORD_UPDATE", array("UPDATE yasccoza_openlink_smmes.signup s SET s.Pwd=? WHERE s.SMME_ID=?","si"));
define("CONSULTANT_PASSWORD_UPDATE", array("UPDATE yasccoza_openlink_consultants.signup s SET s.Pwd=? WHERE s.CONSULTANT_ID=?","si"));

define("ADMIN_SELECT", array("SELECT * FROM yasccoza_openlink_admin_db.signup s WHERE s.ADMIN_ID = ?","i"));
define("ADMINT_TEMP_UPDATE", array("UPDATE pimg SET  ext=?  WHERE ADMIN_ID=?;","si"));
define("SMME_REGISTER_SELECT", array("SELECT SMME_ID FROM register WHERE SMME_ID=?","i"));
define("NPO_REGISTER_SELECT", array("SELECT NPO_ID FROM register WHERE NPO_ID=?","i"));
define("COMPANY_REGISTER_SELECT", array("SELECT COMPANY_ID FROM register WHERE COMPANY_ID=?","i"));
define("SMME_REGISTER_INSERT", array("INSERT INTO register (Trade_name,Legal_name, CC_Registration_Number, Address, Post_Code, city, Province, Contact, Email, foo, OFFICE_ID, INDUSTRY_ID, Financial_Year,  SMME_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?)","ssisississiisi"));
define("NPO_REGISTER_INSERT", array("INSERT INTO register (Trade_name,Legal_name, CC_Registration_Number, Address, Post_Code, city, Province, Contact, Email, INDUSTRY_ID, NPO_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)","sisissisii"));
define("COMPANY_REGISTER_INSERT", array("INSERT INTO register (Trade_name,Legal_name, CC_Registration_Number, Address, Post_Code, city, Province, Contact, Email,foo, INDUSTRY_ID,Financial_Year, COMPANY_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?, ?)","ssisississisi"));

define("CONSULTANT_REGISTER_SELECT", array("SELECT CONSULTANT_ID FROM consultant_information WHERE CONSULTANT_ID=?","i"));
define("CONSULTANT_REGISTER_INSERT", array("INSERT INTO consultant_information (Ethnic_Group, Identification_Type, ID_Number, Gender, CONSULTANT_ID) VALUES (?, ?, ?, ?, ?)","ssisi"));


define("SMME_REGISTERCOMPANYDATA_SELECT", array("SELECT SMME_ID FROM company_data WHERE SMME_ID=?","i"));
define("NPO_REGISTERCOMPANYDATA_SELECT", array("SELECT SMME_ID FROM company_data WHERE NPO_ID=?","i"));
define("SMME_REGISTERCOMPANYDATA_INSERT", array("INSERT INTO company_data (Legal_name, CC_Registration_Number, Trading_Name, Financial_Year, SMME_ID) VALUES (?, ?, ?, ?, ?)","sissi"));
define("NPO_REGISTERCOMPANYDATA_INSERT", array("INSERT INTO company_data (Legal_name, CC_Registration_Number, Trading_Name, Financial_Year,  NPO_ID) VALUES (?, ?, ?, ?, ?, ?, ?)","sissssi"));

define("CONSULTANT_ADMIN_SELECT", array("SELECT CONSULTANT_ID FROM consultant_information WHERE CONSULTANT_ID=?","i"));
define("CONSULTANT_ADMIN_INSERT", array("INSERT INTO admin (first_name, Surname, Identification_Type, ID_Number, Gender, Email, Ethnic_Group, CONSULTANT_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?);","sssisssi"));


define("SMME_REVIEW_SELECT", array("","i"));

define("CONSULTANT_ADMIN_UPDATE", array("UPDATE consultant_information 
SET Identification_Type=?,ID_Number=?,Gender=?,Ethnic_Group=?
WHERE CONSULTANT_ID = ?
","sissi"));
define("CONSULTANT_SIGNUP_UPDATE", array("UPDATE signup 
SET First_Name=?,Surname=?,Email=?
WHERE CONSULTANT_ID = ?
","sssi"));

define("COMPANY_ADMIN_UPDATE", array("UPDATE admin 
SET first_name=?, Surname=?,Identification_Type=?,ID_Number=?,Gender=?,Email=?,Ethnic_Group=?, Active=0
WHERE COMPANY_ID = ?
","sssisssi"));
define("COMPANY_REGISTER_UPDATE", array("UPDATE `register` 
SET Legal_name=?, Trade_Name=?, CC_Registration_Number=?,`Address`=?,`Post_Code`=?,`city`=?,`Province`=?,`Contact`=?,`Email`=?,`foo`=?,`INDUSTRY_ID`=?, `Financial_Year`=?, `Active`=0
WHERE `COMPANY_ID`=? 
","ssisississisi"));


define("PRODUCT_DELETE", array("UPDATE products SET Active = 1 WHERE products.PRODUCT_ID = ? AND products.SMME_ID=? ","ii"));

define("COMPANY_LINKS_UPDATE", array("UPDATE business_links SET url= ?  WHERE COMPANY_ID=? AND LINK_ID=?
","sii"));

define("SMME_PRODUCT_UPDATE", array("UPDATE products SET product_name= ?, product_description=?, price=? WHERE SMME_ID=? AND PRODUCT_ID=?
","ssiii"));
// define("COMPANY_PRODUCT_UPDATE", array("UPDATE products SET product_name= ?, product_description=?, price=? WHERE COMPANY_ID=? AND PRODUCT_ID=?
// ","ssiii"));


define("COMPANY_KEYWORDS_UPDATE", array("UPDATE keywords SET keyword=? WHERE KEYWORD_ID=? AND COMPANY_ID =?
","sii"));

define("SMME_KEYWORDS_UPDATE", array("UPDATE keywords SET keyword=? WHERE KEYWORD_ID=? AND SMME_ID =?
","sii"));

define("SMME_ADMIN_UPDATE", array("UPDATE admin 
SET first_name=?, Surname=?,Identification_Type=?,ID_Number=?,Gender=?,Email=?,Ethnic_Group=?, Active=0
WHERE SMME_ID = ?
","sssisssi"));

define("REGSITER_SMME_UPDATE", array("UPDATE register 
SET Trade_Name=?, Legal_name=?,CC_Registration_Number=?, Address=? ,Post_Code=?, city=?,Province=?,Contact=?,Email=?, foo=?, INDUSTRY_ID=?, Financial_Year=?, Active=0
WHERE SMME_ID = ?
","ssisississisi"));


define("CONSULTANT_REVIEW_SELECT", array("SELECT i.Identification_Type,i.ID_Number,i.Ethnic_Group,i.Gender,i.Active, s.Email, s.First_Name, s.Surname
FROM yasccoza_openlink_consultants.consultant_information i, yasccoza_openlink_consultants.signup s  
WHERE i.CONSULTANT_ID = s.CONSULTANT_ID
AND s.CONSULTANT_ID =?", "i"));
define("CONSULTANT_ADMIN_DELETE", array("UPDATE consultant_information SET Active = 1 WHERE consultant_information.CONSULTANT_ID = ?","i"));


define("COMPANY_REVIEW_ADMIN_SELECT", array("SELECT * FROM admin WHERE admin.COMPANY_ID = ?","i"));
define("COMPANY_REVIEW_REGISTER_SELECT", array("SELECT * FROM register WHERE register.COMPANY_ID =?","i"));
define("COMPANY_REVIEW_KEYWORDS_SELECT", array("SELECT * FROM keywords WHERE keywords.COMPANY_ID =?","i"));
define("COMPANY_REVIEW_LINKS_SELECT", array("SELECT * FROM business_links WHERE business_links.COMPANY_ID =? order by LINK_ID ASC","i"));
define("COMPANY_REVIEW_STATEMENTS_SELECT", array("SELECT * FROM company_profile WHERE company_profile.COMPANY_ID =?","i"));
define("COMPANY_REVIEW_PRODUCTS_SELECT", array("SELECT * FROM products WHERE products.COMPANY_ID = ?","i"));

define("SMME_REVIEW_ADMIN_SELECT", array("SELECT * FROM admin WHERE admin.SMME_ID = ?","i"));
define("SMME_REGISTER_SELECTS", array("SELECT * FROM register WHERE register.SMME_ID =?","i"));
define("SMME_DIRECTOR_SELECT", array("SELECT * FROM company_director WHERE company_director.SMME_ID =?","i"));
define("SMME_STATEMENT_SELECT", array("SELECT * FROM company_profile WHERE company_profile.SMME_ID =?","i"));
define("SMME_COMPANY_DOCUMENTATION_SELECT", array("SELECT * FROM company_documentation WHERE company_documentation.SMME_ID =?","i"));
define("SMME_PRODUCTS_SELECT", array("SELECT * FROM products WHERE products.SMME_ID = ?","i"));
define("SMME_KEYWORDS_SELECT", array("SELECT * FROM keywords WHERE keywords.SMME_ID =?","i"));
define("SMME_LINKS_SELECT", array("SELECT * FROM business_links WHERE business_links.SMME_ID =?","i"));

define("COMPANY_LINKS_DELETE", array("UPDATE business_links SET Active = 1 WHERE business_links.COMPANY_ID = ?","i"));
define("COMPANY_KEYWORDS_DELETE", array("UPDATE keywords SET Active = 1 WHERE keywords.COMPANY_ID = ?","i"));
define("COMPANY_ADMIN_DELETE", array("UPDATE admin SET Active = 1 WHERE admin.COMPANY_ID = ?","i"));
define("COMPANY_REGISTER_DELETE", array("UPDATE register SET Active = 1 WHERE register.COMPANY_ID = ?","i"));


define("SMME_ADMIN_DELETE", array("UPDATE admin SET Active = 1 WHERE admin.SMME_ID = ?","i"));
define("SMME_REGISTER_DELETE", array("UPDATE register SET Active = 1 WHERE register.SMME_ID = ?","i"));
define("SMME_DIR_DELETE", array("UPDATE company_director SET Active = 1 WHERE company_director.SMME_ID = ?","i"));
define("SMME_STATE_DELETE", array("UPDATE company_profile SET Active = 1 WHERE company_profile.SMME_ID = ?","i"));
define("SMME_DOC_DELETE",array("UPDATE company_documentation SET Active = 1 WHERE company_documentation.SMME_ID = ?","i"));

define("COMPANY_STATE_DELETE", array("UPDATE company_profile SET Active = 1 WHERE company_profile.COMPANY_ID = ?","i"));

define("ALL_CONSULTANTS_SELECT", array("SELECT
c.`Identification_Type`,
c.`ID_Number`,
c.`Ethnic_Group`,
c.`Gender`,
c.`CONSULTANT_ID`,
s.First_Name,
s.Surname,
s.Email,
s.CONSULTANT_ID as ID
FROM
yasccoza_openlink_consultants.consultant_information c,
yasccoza_openlink_consultants.signup s
WHERE c.CONSULTANT_ID = s.CONSULTANT_ID
"));
// display all companies
define("COMPANY_TOVIEW1_SELECT1", array("SELECT DISTINCT register.Legal_name,register.foo, city, Province, ext , register.COMPANY_ID AS ID, s.typeOfEntity as T, IT.title, i.office 
FROM yasccoza_openlink_companies.signup as s, yasccoza_openlink_companies.register, yasccoza_openlink_companies.company_profile, yasccoza_openlink_companies.pimg, yasccoza_openlink_association_db.industry_title as IT, yasccoza_openlink_association_db.industry as i 
WHERE s.COMPANY_ID=register.COMPANY_ID 
AND register.INDUSTRY_ID = IT.TITLE_ID 
AND IT.INDUSTRY_ID = i.INDUSTRY_ID 
AND register.COMPANY_ID=company_profile.COMPANY_ID 
AND company_profile.COMPANY_ID=pimg.COMPANY_ID"));

define("COMPANY_CONSULTANTS_SELECT", array("SELECT
c.`Identification_Type`,
c.`ID_Number`,
c.`Ethnic_Group`,
c.`Gender`,
a.`CONSULTANT_ID`,
s.First_Name,
s.Surname,
s.Email,
s.CONSULTANT_ID as ID
FROM
yasccoza_openlink_consultants.consultant_information c,
yasccoza_openlink_association_db.company_consultant_association a,
yasccoza_openlink_consultants.signup s
WHERE
a.CONSULTANT_ID = c.CONSULTANT_ID 
AND c.CONSULTANT_ID = s.CONSULTANT_ID
AND a.COMPANY_ID =?","i"));

define("COMPANY_TOVIEW1_SELECT2", array("SELECT DISTINCT r.Legal_name,r.foo, a.Progress, city, Province, ext , r.COMPANY_ID AS ID, s.typeOfEntity as T, i.title, IT.office 
FROM yasccoza_openlink_companies.register r, yasccoza_openlink_companies.pimg p, yasccoza_openlink_association_db.smme_company_events a,
signup AS s, yasccoza_openlink_association_db.industry_title as i,yasccoza_openlink_association_db.industry  as IT
WHERE r.COMPANY_ID=p.COMPANY_ID
AND p.COMPANY_ID=a.COMPANY_ID
AND r.COMPANY_ID = s.COMPANY_ID
AND r.INDUSTRY_ID = i.TITLE_ID 
AND i.INDUSTRY_ID = IT.INDUSTRY_ID 
AND a.EVENT_ID=?
AND a.SMME_ID=?;" , "ii"));

define("COMPANY_TOVIEW1_SELECT3", array(" " , " "));
// define("TOVIEW1_SELECT", array("SELECT Legal_name, city, Province FROM register"));// LIMIT 

//we need more information from companies

//displays all smmes
define("SMME_TOVIEW1_SELECT1", array("SELECT DISTINCT register.Legal_name,register.foo, city, Province, ext , BBBEE_Status, register.SMME_ID AS ID, s.typeOfEntity as T,  IT.title, i.office
FROM signup as s, register, company_documentation, company_profile, pimg, yasccoza_openlink_association_db.industry_title as IT, yasccoza_openlink_association_db.industry as i
WHERE s.SMME_ID=register.SMME_ID 
AND register.INDUSTRY_ID = IT.TITLE_ID
AND IT.INDUSTRY_ID = i.INDUSTRY_ID
AND register.SMME_ID=company_documentation.SMME_ID 
AND company_documentation.SMME_ID=company_profile.SMME_ID 
AND company_profile.SMME_ID=pimg.SMME_ID"));

define("SMME_TOVIEW1_SELECT2", array("SELECT register.Legal_name,register.foo, city, Province, ext, BBBEE_Status, a.Progress, a.EVENT_ID, register.SMME_ID AS ID,
s.typeOfEntity, i.title, IT.office
FROM register, company_documentation, company_profile, pimg p, yasccoza_openlink_association_db.smme_company_events a,
signup AS s, yasccoza_openlink_association_db.industry_title as i,  yasccoza_openlink_association_db.industry as IT
WHERE register.SMME_ID=company_documentation.SMME_ID 
AND company_documentation.SMME_ID=company_profile.SMME_ID  
AND company_profile.SMME_ID=p.SMME_ID 
AND p.SMME_ID=a.SMME_ID
AND register.SMME_ID = s.SMME_ID
AND register.INDUSTRY_ID = i.TITLE_ID
AND i.INDUSTRY_ID = IT.INDUSTRY_ID
AND a.EVENT_ID=?
AND a.COMPANY_ID=?
;", "ii"));



define("SMME_TOVIEW1_SELECT3", array(" ", " "));//comparative charts sql for smmes

define("COMPANY_KEYWORD_INSERT", array("INSERT INTO keywords(COMPANY_ID, keyword) VALUES "));
define("SMME_KEYWORD_INSERT", array("INSERT INTO keywords(SMME_ID, keyword) VALUES "));
define("NPO_KEYWORD_INSERT", array("INSERT INTO keywords(NPO_ID, keyword) VALUES "));
define("NPO_TOVIEW1_SELECT1", array("SELECT register.Legal_name, city, Province, ext, products_services, BBBEE_Status, register.NPO_ID AS ID
FROM register, company_documentation, company_profile, pimg 
WHERE register.NPO_ID=company_documentation.NPO_ID 
AND company_documentation.NPO_ID=company_profile.NPO_ID
AND company_profile.NPO_ID=pimg.NPO_ID;"));

define("NPO_TOVIEW1_SELECT2", array("SELECT register.Legal_name, city, Province, ext, products_services, BBBEE_Status, a.EVENT_ID, register.NPO_ID AS ID
FROM register, company_documentation, company_profile, pimg p, yasccoza_openlink_association_db.npo_company_events a
WHERE register.NPO_ID=company_documentation.NPO_ID 
AND company_documentation.NPO_ID=company_profile.NPO_ID
AND company_profile.NPO_ID=p.NPO_ID 
AND p.NPO_ID=a.NPO_ID
AND a.EVENT_ID=?
AND a.COMPANY_ID=?;" , "ii"));

define("NPO_TOVIEW1_SELECT3", array(" " , " "));

// define("COMPANY_PEEK_SELECT", array("SELECT Legal_name, city, Province, p.ext, a.Progress 
// FROM (yasccoza_openlink_companies.register r 
// INNER JOIN yasccoza_openlink_companies.pimg p ON r.COMPANY_ID=p.COMPANY_ID) 
// INNER JOIN yasccoza_openlink_association_db.smme_company_association a ON p.COMPANY_ID=a.COMPANY_ID 
// WHERE a.SMME_ID=?;" , "i"));

define("SMME_STEP_SELECT", array("SELECT progress FROM smme_company_association WHERE SMME_ID=?","i"));
//smme_company_association

define("COMPARITIVE_CHART",array("SELECT
r.Legal_name,
BBBEE_Status,
SUM(rand_value) AS rand_value
FROM
yasccoza_openlink_smmes.register r,
yasccoza_openlink_smmes.expense_summary e,
yasccoza_openlink_smmes.company_documentation c,
yasccoza_openlink_association_db.smme_company_events a
WHERE
r.SMME_ID = e.SMME_ID 
AND e.SMME_ID = c.SMME_ID 
AND c.SMME_ID = a.SMME_ID
AND a.COMPANY_ID = ?
GROUP BY
r.legal_name;", "i"));//AND a.COMPANY_ID = ?
///SELECT ext, r.Legal_name, r.city, r.Province, r.foo, r.Contact, r.Email, p.vision, p.values_, p.mission, p.introduction, d.Number_Shareholders, d.Black_Ownership_Percentage, d.White_Ownership_Percentage, d.Black_Female_Percentage, a.title 
// FROM yasccoza_openlink_smmes.register r,yasccoza_openlink_association_db.industry_title a, yasccoza_openlink_smmes.company_profile p, yasccoza_openlink_smmes.pimg, yasccoza_openlink_smmes.company_documentation d,yasccoza_openlink_smmes.products prod, yasccoza_openlink_smmes.expense_summary ex
// WHERE r.INDUSTRY_ID= a.TITLE_ID
// AND r.SMME_ID = p.SMME_ID
// AND p.SMME_ID = d.SMME_ID 
// AND d.SMME_ID = pimg.SMME_ID 
// AND pimg.SMME_ID = prod.SMME_ID
// AND prod.SMME_ID = ex.SMME_ID
// AND ex.SMME_ID =101010101101034

define("SMME_MOREINFO", array("SELECT ext, r.Legal_name, r.city, r.Province, r.foo, r.Contact, r.Email, p.vision, p.values_, p.mission, p.introduction, d.Number_Shareholders, d.Black_Ownership_Percentage, d.White_Ownership_Percentage, d.Black_Female_Percentage, a.title
FROM yasccoza_openlink_smmes.register r,yasccoza_openlink_association_db.industry_title a, yasccoza_openlink_smmes.company_profile p, yasccoza_openlink_smmes.pimg, yasccoza_openlink_smmes.company_documentation d
WHERE r.INDUSTRY_ID= a.TITLE_ID
AND r.SMME_ID = p.SMME_ID
AND p.SMME_ID = d.SMME_ID 
AND d.SMME_ID = pimg.SMME_ID 
AND pimg.SMME_ID = ?", "i"));

define("ADMIN_MOREINFO", array("SELECT ext, r.Legal_name, r.city, r.Province, r.foo, r.Contact, r.Email, p.vision, p.values_, p.mission, p.introduction,  a.title
FROM yasccoza_openlink_smmes.register r,yasccoza_openlink_association_db.industry_title a, yasccoza_openlink_smmes.company_profile p, yasccoza_openlink_smmes.pimg
WHERE r.INDUSTRY_ID= a.TITLE_ID
AND r.SMME_ID = p.SMME_ID
AND p.SMME_ID = pimg.SMME_ID 
AND pimg.SMME_ID = ?
UNION 
SELECT ext, r.Legal_name ,r.Contact, r.Email, r.city, r.Province, r.foo, a.title,p.vision, p.values_, p.mission, p.introduction
FROM yasccoza_openlink_companies.register r, yasccoza_openlink_association_db.industry_title a, yasccoza_openlink_companies.pimg, yasccoza_openlink_companies.company_profile p
WHERE r.INDUSTRY_ID= a.TITLE_ID
AND r.COMPANY_ID = p.COMPANY_ID
AND p.COMPANY_ID = pimg.COMPANY_ID
AND pimg.COMPANY_ID=r.COMPANY_ID
AND r.COMPANY_ID = ?"
, "ii"));

define("SMME_VIEW_MORE_LINK_SELECT", array("SELECT b.url, b.LINK_ID, l.fav_icon_class , l.link_name 
FROM yasccoza_openlink_companies.business_links b, yasccoza_openlink_association_db.links l 
WHERE l.LINK_ID = b.LINK_ID
AND b.COMPANY_ID = ?", "i"));

define("SMME_MOREINFO_CHART", array("SELECT d.Number_Shareholders, d.White_Ownership_Percentage, d.Black_Ownership_Percentage, d.Black_Female_Percentage
FROM  yasccoza_openlink_smmes.company_documentation d, yasccoza_openlink_association_db.smme_company_events a
WHERE d.SMME_ID = a.SMME_ID
AND a.SMME_ID = ?
AND a.COMPANY_ID = ?", "ii"));

define("ADMIN_MOREINFO_CHART", array("SELECT d.Number_Shareholders, d.White_Ownership_Percentage, d.Black_Ownership_Percentage, d.Black_Female_Percentage
FROM  yasccoza_openlink_smmes.company_documentation d
WHERE d.SMME_ID = ?
", "i"));
define("FILE_UPLOADS_SELECT", array("SELECT DISTINCT f.type,f.userID, f.link, f.date, r.Trade_Name, r.Address, r.city, f.verified 
FROM yasccoza_openlink_companies.file_uploads f, yasccoza_openlink_companies.register r
WHERE r.COMPANY_ID = f.userID 
AND f.userID = ? 
UNION SELECT f.type, f.link, f.userID, f.date, r.Trade_Name, r.Address, r.city, f.verified 
FROM yasccoza_openlink_smmes.file_uploads f, yasccoza_openlink_smmes.register r 
WHERE r.SMME_ID = f.userID 
AND f.userID = ?", "ii"));

define("COMPANY_VIEW_MORE_LINK_SELECT", array("SELECT b.url, b.LINK_ID, l.fav_icon_class, l.link_name 
FROM yasccoza_openlink_smmes.business_links b, yasccoza_openlink_association_db.links l 
WHERE l.LINK_ID = b.LINK_ID
AND b.SMME_ID = ?", "i"));
define("COMPANY_URL_SELECT", array("SELECT b.url
FROM yasccoza_openlink_smmes.business_links b
WHERE b.LINK_ID = ?", "i"));
define("SMME_URL_SELECT", array("SELECT b.url
FROM yasccoza_openlink_companies.business_links b
WHERE b.LINK_ID = ?", "i"));
define("ADMIN_URL_SELECT", array("SELECT b.url
FROM yasccoza_openlink_smmes.business_links b
WHERE b.LINK_ID = ?
UNION
SELECT b.url
FROM yasccoza_openlink_companies.business_links b
WHERE b.LINK_ID = ?
", "ii"));
define("ADMIN_VIEW_MORE_LINK_SELECT", array("SELECT b.url, b.LINK_ID, l.fav_icon_class, l.link_name 
FROM yasccoza_openlink_smmes.business_links b, yasccoza_openlink_association_db.links l 
WHERE l.LINK_ID = b.LINK_ID
AND b.SMME_ID = ?
UNION
SELECT b.url, b.LINK_ID, l.fav_icon_class, l.link_name 
FROM yasccoza_openlink_companies.business_links b, yasccoza_openlink_association_db.links l 
WHERE l.LINK_ID = b.LINK_ID
AND b.COMPANY_ID = ?
", "ii"));

define("VALIDATE_CONNECTION_SELECT", array(
    "SELECT ASSOCIATION_ID FROM `smme_company_events` 
    WHERE COMPANY_ID = ?
    AND SMME_ID=?",
    "ii"));

define("COMPANY_MOREINFO", array("SELECT ext, r.Legal_name ,r.Contact, r.Email, r.city, r.Province, r.foo, a.title,p.vision, p.values_, p.mission, p.introduction
FROM register r, yasccoza_openlink_association_db.industry_title a, pimg, yasccoza_openlink_companies.company_profile p
WHERE r.INDUSTRY_ID= a.TITLE_ID
AND r.COMPANY_ID = p.COMPANY_ID
AND p.COMPANY_ID = pimg.COMPANY_ID
AND pimg.COMPANY_ID=r.COMPANY_ID
AND r.COMPANY_ID = ?", "i"));
define("SMME_PRODUCT_SELECT", array("SELECT *  FROM yasccoza_openlink_smmes.products WHERE SMME_ID = ? ", "i"));

define("SMME_LINK_SELECT", array("SELECT LINK_ID FROM yasccoza_openlink_smmes.business_links WHERE SMME_ID= ? ", "i"));

define("ADMIN_PRODUCT_SELECT", array("SELECT *  FROM yasccoza_openlink_smmes.products WHERE SMME_ID = ? UNION SELECT *  FROM yasccoza_openlink_companies.products WHERE COMPANY_ID = ?  ", "ii"));
define("COMPANY_PRODUCT_SELECT", array("SELECT *  FROM yasccoza_openlink_companies.products WHERE COMPANY_ID = ? ", "i"));

define("SMME_LINKNAME_SELECT", array("SELECT * FROM yasccoza_openlink_association_db.links WHERE LINK_ID= ? ", "i"));
define("COMPANY_LINKNAME_SELECT", array("SELECT * FROM yasccoza_openlink_association_db.links WHERE LINK_ID= ? ", "i"));

define("SMME_BUSINESS_LINKS_INSERT", array("INSERT INTO `business_links`(`SMME_ID`, `LINK_ID`, `url`) VALUES "));
define("COMPANY_BUSINESS_LINKS_INSERT", array("INSERT INTO `business_links`(`COMPANY_ID`, `LINK_ID`, `url`) VALUES"));
define("SMME_BUSINESS_LINK_VISITS_INSERT", array("INSERT INTO `business_links`(`SMME_ID`, `LINK_ID`, `url`) VALUES (?, ?, ?)", "iii"));
define("COMPANY_BUSINESS_LINK_VISITS_INSERT", array("INSERT INTO `business_links`(`SMME_ID`, `LINK_ID`, `url`) VALUES (?, ?, ?)", "iii"));
define("SMME_VIEWS_INSERT", array("INSERT INTO yasccoza_openlink_association_db.entity_clicks(
    `TYPE`,
    `WHO_CLICKED`,
    `WHO_TO_VIEW`
)
VALUES(
   ?,
  ?,
  ?
)", "sii"));

define("COMPANY_VIEWS_INSERT", array("INSERT INTO yasccoza_openlink_association_db.entity_clicks(
    `TYPE`,
    `WHO_CLICKED`,
    `WHO_TO_VIEW`
)
VALUES(
   ?,
  ?,
  ?
)", "sii"));
define("NOTIFICATION_VIEWED_UPDATE", array("DELETE FROM `notifications` WHERE NOTIFICATION_ID = ?", "i"));