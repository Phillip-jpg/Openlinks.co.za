<?php
define("OFFICES_SELECT", array("SELECT TITLE_ID, title FROM `industry_title`, industry 
WHERE industry_title.INDUSTRY_ID = industry.INDUSTRY_ID
AND industry.INDUSTRY_ID = ?
ORDER BY title;","i"));
define("NPO_OFFICES_SELECT", array("SELECT TITLE_ID, title FROM npo_industry_title, npo_industry 
WHERE npo_industry_title.INDUSTRY_ID = npo_industry.INDUSTRY_ID
AND industry.INDUSTRY_ID = ?
ORDER BY title;","i"));
define("OFFICES_NR_SELECT", array("SELECT TITLE_ID, title FROM `industry_title`, industry 
WHERE industry_title.INDUSTRY_ID = industry.INDUSTRY_ID
AND industry.office LIKE ?
ORDER BY title;","s"));
