<?php

define("COMPANY_TOTAL_NUMBER_CONNECTIONS_SELECT", array("SELECT COUNT(DISTINCT sme.SMME_ID) AS connections FROM yasccoza_openlink_association_db.smme_company_events sme WHERE (Progress = 3 OR Progress = 4) AND COMPANY_ID = ?;","i"));

define("COMPANY_TOTAL_NUMBER_REQUESTS_SELECT", array("SELECT COUNT(DISTINCT sme.SMME_ID)as requests 
FROM yasccoza_openlink_association_db.smme_company_events sme 
WHERE sme.EVENT_ID = 2 
AND sme.COMPANY_ID =?;","i"));

define("COMPANY_TOTAL_NUMBER_REQUESTS_RECEIVED_SELECT", array("SELECT COUNT(DISTINCT sme.SMME_ID)as requests FROM yasccoza_openlink_association_db.smme_company_events sme WHERE (EVENT_ID = 1 OR EVENT_ID =3) AND COMPANY_ID = ?;","i"));

define("COMPANY_FINALISED_SELECT", array("SELECT COUNT(DISTINCT sme.SMME_ID)as finalised FROM yasccoza_openlink_association_db.smme_company_events sme WHERE PROGRESS = 4 
AND COMPANY_ID = ?;","i"));


define("SMME_TOTAL_NUMBER_CONNECTIONS_SELECT", array("SELECT COUNT(DISTINCT sme.COMPANY_ID)as connections FROM yasccoza_openlink_association_db.smme_company_events sme WHERE (Progress = 3 OR Progress = 4)
AND SMME_ID = ?;","i"));

define("SMME_TOTAL_NUMBER_REQUESTS_SELECT", array("SELECT COUNT(DISTINCT sme.COMPANY_ID)as requests FROM yasccoza_openlink_association_db.smme_company_events sme WHERE EVENT_ID = 1 
AND SMME_ID = ?;","i"));
define("NPO_TOTAL_NUMBER_REQUESTS_SELECT", array("SELECT COUNT(DISTINCT sme.COMPANY_ID))as requests FROM yasccoza_openlink_association_db.smme_company_events sme WHERE EVENT_ID = 3 
AND SMME_ID = ?;","i"));
define("SMME_TOTAL_NUMBER_REQUESTS_RECEIVED_SELECT", array("SELECT COUNT(DISTINCT sme.COMPANY_ID)as requests FROM yasccoza_openlink_association_db.smme_company_events sme WHERE EVENT_ID = 2  AND SMME_ID = ?;","i"));

define("SMME_FINALISED_SELECT", array("SELECT COUNT(DISTINCT sme.COMPANY_ID)as finalised FROM yasccoza_openlink_association_db.smme_company_events sme WHERE PROGRESS = 4 
AND SMME_ID = ?;","i"));

define("SMME_MORE_INFO_VIEWS_SELECT", array("SELECT
COUNT(CLICK_ID) as views
FROM
yasccoza_openlink_association_db.entity_clicks
WHERE
TYPE = 'VIEW MORE'
AND WHO_TO_VIEW = ?;","i"));//number of times other entities clicked to view more of a certain entities information



define("COMPANY_MORE_INFO_VIEWS_SELECT", array("SELECT
COUNT(CLICK_ID) as views
FROM
yasccoza_openlink_association_db.entity_clicks
WHERE
TYPE = 'VIEW MORE'
AND WHO_TO_VIEW = ?;","i"));//number of times other entities clicked to view more of a certain entities information


define("SMME_WEBSITELINK_VIEWS_SELECT", array("SELECT
COUNT(CLICK_ID) as web_visits
FROM
yasccoza_openlink_association_db.entity_clicks
WHERE
TYPE = 'WEBSITE VISIT'
AND WHO_TO_VIEW = ?;","i"));//number of times other entities clicked to view more of a certain entities website

define("SMME_ENGAGED_ANALYTICS_SELECT", array("SELECT
COUNT(DISTINCT m.From_) as engaged
FROM
yasccoza_openlink_association_db.messages m
WHERE To_ = ?;","i"));//number of times other entities clicked to view more of a certain entities website

define("COMPANY_WEBSITELINK_VIEWS_SELECT", array("SELECT
COUNT(CLICK_ID) as web_visits
FROM
yasccoza_openlink_association_db.entity_clicks
WHERE
TYPE = 'WEBSITE VISIT'
AND WHO_TO_VIEW = ?;","i"));//number of times other entities clicked to view more of a certain entities website

define("COMPANY_ENGAGED_ANALYTICS_SELECT", array("SELECT
COUNT(DISTINCT m.From_) as engaged
FROM
yasccoza_openlink_association_db.messages m
WHERE To_ = ?;","i"));//number of times other entities clicked to view more of a certain entities website

define("SMME_KEYWORD_ANALYTICS_SELECT", array("SELECT keyword FROM keywords
WHERE `SMME_ID` = ?","i"));

define("COMPANY_KEYWORD_ANALYTICS_SELECT", array("SELECT keyword FROM keywords
WHERE `COMPANY_ID` = ?","i"));

define("KEYWORD_HITS_SELECT", array("SELECT
SUM(a.hits) AS hits
FROM
(
SELECT
    COUNT(`TERM_ID`) AS hits
FROM
yasccoza_openlink_smmes.search_terms
WHERE
`term_name`LIKE ?
UNION
SELECT
COUNT(`TERM_ID`) AS hits
FROM
yasccoza_openlink_companies.search_terms
WHERE
`term_name`LIKE ?
) a","ss"));


define("SEARCH_PERFORMANCE_HITS_SELECT", array("SELECT
SUM(a.hits) AS hits
FROM
(
SELECT
    COUNT(`TERM_ID`) AS hits
FROM
yasccoza_openlink_smmes.search_terms
WHERE
`term_name`LIKE ?
UNION
SELECT
COUNT(`TERM_ID`) AS hits
FROM
yasccoza_openlink_companies.search_terms
WHERE
`term_name`LIKE ?
) a","ss"));

define("COMPANY_CONNECTIONS_PER_MONTH_SELECT", array("SELECT 
COUNT(DISTINCT sme.SMME_ID) AS connections, MONTHNAME(`event_Start`) AS Month 
FROM yasccoza_openlink_association_db.smme_company_events sme
WHERE(Progress = 3 OR Progress = 4)
AND COMPANY_ID = ? 
AND YEAR(`event_Start`) = YEAR(CURRENT_TIMESTAMP()) 
GROUP BY MONTH(`event_Start`)
;","i"));

define("SMME_CONNECTIONS_PER_MONTH_SELECT", array("SELECT
COUNT(DISTINCT sme.COMPANY_ID) AS connections,
MONTHNAME(`event_Start`) AS Month
FROM
yasccoza_openlink_association_db.smme_company_events sme
WHERE
(Progress = 3 OR Progress = 4)
AND SMME_ID = ?
AND YEAR(`event_Start`) = YEAR(CURRENT_TIMESTAMP())
GROUP BY
MONTH(`event_Start`)
;","i"));

define("SYSTEM_CONNECTIONS_PER_MONTH_SELECT", array("SELECT
COUNT(DISTINCT sme.COMPANY_ID, sme.SMME_ID) AS connections,
MONTHNAME(`event_Start`) AS Month
FROM
yasccoza_openlink_association_db.smme_company_events sme
WHERE
(Progress = 3 OR Progress = 4)
AND YEAR(`event_Start`) = YEAR(CURRENT_TIMESTAMP())
GROUP BY
MONTH(`event_Start`)
;"));

define("COMPANY_PROFILE_STATS_SELECT", array("SELECT
a.COMPANY_ID signup,
b.COMPANY_ID register,
c.COMPANY_ID business_links,
d.COMPANY_ID company_profile,
e.COMPANY_ID admin,
f.COMPANY_ID keywords,
i.COMPANY_ID products
FROM
(
            (
                (
                    (
                        (
                            (
                                `signup` a
                            LEFT JOIN register b ON
                                a.COMPANY_ID = b.COMPANY_ID
                            )
                        LEFT JOIN business_links c ON
                            a.COMPANY_ID = c.COMPANY_ID
                        )
                    LEFT JOIN company_profile d ON
                        a.COMPANY_ID = d.COMPANY_ID
                    )
                LEFT JOIN admin e ON
                    a.COMPANY_ID = e.COMPANY_ID
                )
            LEFT JOIN keywords f ON
                a.COMPANY_ID = f.COMPANY_ID
            )
        LEFT JOIN(
            SELECT DISTINCT
                COMPANY_ID
            FROM
                products
            WHERE
                COMPANY_ID = ?
        ) i
ON
a.COMPANY_ID = i.COMPANY_ID
)
WHERE
a.COMPANY_ID = ?","ii"));

define("SMME_PROFILE_STATS_SELECT", array("SELECT
a.SMME_ID signup,
b.SMME_ID register,
c.SMME_ID company_documentation,
d.SMME_ID company_profile,
e.SMME_ID admin,
f.SMME_ID keywords,
g.SMME_ID company_director,
h.SMME_ID expense_summary,
i.SMME_ID products
FROM
(
    (
        (
            (
                (
                    (
                        (
                            (
                                `signup` a
                            LEFT JOIN register b ON
                                a.SMME_ID = b.SMME_ID
                            )
                        LEFT JOIN company_documentation c ON
                            a.SMME_ID = c.SMME_ID
                        )
                    LEFT JOIN company_profile d ON
                        a.SMME_ID = d.SMME_ID
                    )
                LEFT JOIN admin e ON
                    a.SMME_ID = e.SMME_ID
                )
            LEFT JOIN keywords f ON
                a.SMME_ID = f.SMME_ID
            )
        LEFT JOIN(
            SELECT DISTINCT
                SMME_ID
            FROM
                company_director
            WHERE
                SMME_ID = ?
        ) g
    ON
        a.SMME_ID = g.SMME_ID
        )
    LEFT JOIN(
        SELECT DISTINCT
            SMME_ID
        FROM
            expense_summary
        WHERE
            SMME_ID = ?
    ) h
ON
    a.SMME_ID = h.SMME_ID
    )
LEFT JOIN(
    SELECT DISTINCT
        SMME_ID
    FROM
        products
    WHERE
        SMME_ID = ?
) i
ON
a.SMME_ID = i.SMME_ID
)
WHERE
a.SMME_ID = ?","iiii"));

define("SMME_PRODUCT_NAME_SELECT",array("SELECT product_name as result FROM yasccoza_openlink_smmes.products p WHERE p.SMME_ID = ?","i"));
define("COMPANY_PRODUCT_NAME_SELECT",array("SELECT product_name as result FROM yasccoza_openlink_companies.products p WHERE p.COMPANY_ID = ?","i"));

define("COMPANY_INDUSTRY_NAME_SELECT",array("SELECT title as result FROM yasccoza_openlink_companies.register r, yasccoza_openlink_association_db.industry_title i WHERE r.INDUSTRY_ID = i.TITLE_ID AND r.COMPANY_ID = ?","i"));
define("SMME_INDUSTRY_NAME_SELECT",array("SELECT title as result FROM yasccoza_openlink_smmes.register r, yasccoza_openlink_association_db.industry_title i WHERE r.INDUSTRY_ID = i.TITLE_ID AND r.SMME_ID =?","i"));

define("SMME_LEGAL_NAME_SELECT",array("SELECT Legal_name as result FROM yasccoza_openlink_smmes.register r WHERE r.SMME_ID = ?","i"));
define("COMPANY_LEGAL_NAME_SELECT",array("SELECT Legal_name as result FROM yasccoza_openlink_companies.register r WHERE r.COMPANY_ID = ?","i"));


// SELECT
//     COUNT(ASSOCIATION_ID) AS connections,
//     MONTHNAME(`event_date`) AS Month
// FROM
//     yasccoza_openlink_association_db.smme_company_events
// WHERE
//     (Progress = 2 OR Progress = 3)
//     AND COMPANY_ID = 2000000006
//     AND YEAR(`event_date`) = YEAR(CURRENT_TIMESTAMP())
// GROUP BY
//     MONTH(`event_date`)
//     ;

// define("SMME_KEYWORD_ANALYTICS_SELECT", array("SELECT
// COUNT(ASSOCIATION_ID) AS connections,
// MONTHNAME(`event_date`) AS Month
// FROM
// yasccoza_openlink_association_db.smme_company_events
// WHERE
// Progress = 3
// AND COMPANY_ID = ?
// AND YEAR(`event_date`) = YEAR(CURRENT_TIMESTAMP())
// GROUP BY
// MONTH(`event_date`)
// ;","i"));

// SELECT
//     SUM(a.hits) AS hits
// FROM
//     (
//     SELECT
//         COUNT(`TERM_ID`) AS hits
//     FROM
//         search_terms
//     WHERE
//         MATCH(`term_name`) AGAINST(? IN BOOLEAN MODE) >= 0.5
//     UNION
// SELECT
//     COUNT(`TERM_ID`) AS hits
// FROM
//     yasccoza_openlink_companies.search_terms
// WHERE
//     MATCH(`term_name`) AGAINST(? IN BOOLEAN MODE) >= 0.5
// ) a


//     SELECT keyword FROM keywords
// WHERE `SMME_ID` = ?



// SELECT
//     COUNT(ASSOCIATION_ID) AS connections,
//     MONTHNAME(`event_date`) AS Month
// FROM
//     yasccoza_openlink_association_db.smme_company_events
// WHERE
//     (EVENT_ID >= 14
//     OR EVENT_ID < 42)
//     AND NOT IN(15)
//     AND COMPANY_ID = 2000000006
//     AND YEAR(`event_date`) = YEAR(CURRENT_TIMESTAMP())
// GROUP BY
//     MONTH(`event_date`)
//     ;