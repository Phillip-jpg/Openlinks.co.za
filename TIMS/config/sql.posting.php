<?php
define("COMPANY_POSTING_INSERT", array("INSERT INTO `posting`(`COMPANY_ID`, `description`) VALUES (?, ?)","is"));
define("COMPANY_POSTING_SELECT", array("SELECT p.COMPANY_ID, description, Legal_name
FROM posting AS p, register AS r
WHERE p.COMPANY_ID = r.COMPANY_ID",""));

define("CONSULTANT_POSTING_INSERT", array("INSERT INTO `posting`(`CONSULTANT_ID`, `description`) VALUES (?, ?)","is"));
define("CONSULTANT_POSTING_SELECT", array("SELECT p.CONSULTANT_ID, description, First_Name
FROM posting AS p, signup AS s
WHERE p.CONSULTANT_ID = s.CONSULTANT_ID",""));