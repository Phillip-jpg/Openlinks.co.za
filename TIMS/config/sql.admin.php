<?php
define("POOL_SELECT", array(
"SELECT `ENQ_ID`, `ADMIN_ID`, `message`, `time`, `urgency`, `stage` 
FROM `enquiries`
WHERE `ADMIN_ID` = NULL
AND `stage` = 1;",""));

define("MY_POOL_SELECT", array(
    "SELECT `ENQ_ID`, `ADMIN_ID`, `message`, `time`, `urgency`, `stage`
    FROM `enquiries`
    WHERE `ADMIN_ID` = ?
    AND `stage` = 2;","i"));

define("", array(

));
define("ALL_CLICKED_EMAILS_SELECT", array(
"
SELECT COUNT(`EL_ID`) as people_clicked FROM `email_link`
"
));
    
define("ALL_EMAILS_SENT_SELECT", array(
"
SELECT COUNT(`NOTIFICATION_ID`) as emails FROM notifications
"
));
    
define("PROGRESS_PROCESS_SELECT", array(
"
SELECT
    *
FROM
    (
        (
            (
            SELECT
                COUNT(`ASSOCIATION_ID`) AS requests
            FROM
                yasccoza_openlink_association_db.smme_company_events a
            WHERE
                a.EVENT_ID IN(1, 2, 3)
        ) r
    JOIN(
        SELECT COUNT(`ASSOCIATION_ID`) AS connections
        FROM
            yasccoza_openlink_association_db.smme_company_events a
        WHERE
            a.EVENT_ID IN(4, 5, 6)
    ) c
        )
    JOIN(
        SELECT COUNT(`ASSOCIATION_ID`) AS finalized
        FROM
            yasccoza_openlink_association_db.smme_company_events a
        WHERE
            a.EVENT_ID IN(42)
    ) f
    )
"
)


);
define("PROCESS_AVERAGE_TIME_SELECT", array(
"
SELECT TIME(ROUND(AVG(a.time))) as Average_time
FROM (
SELECT
    TIMEDIFF(f.`event_Start`, c.`event_Start`) AS time
FROM
    (
    SELECT
        *
    FROM
        `smme_company_events`
    WHERE
        EVENT_ID IN(4, 5, 6)
) c
JOIN(
    SELECT
        *
    FROM
        `smme_company_events`
    WHERE
        EVENT_ID IN(42)
) f
ON
    f.`SMME_ID` = c.`SMME_ID` 
    AND f.`COMPANY_ID` = c.`COMPANY_ID`
) a

"
)


);
define("PAGE_VISITS_GRAPGH", array("
SELECT
    `Page`,
    COUNT(`CTP_ID`) as Visits,
    `time`
FROM
    `come_to_page`
GROUP BY
    `Page`
HAVING
    MONTH(`time`) = MONTH(CURRENT_TIMESTAMP)
")


);
define("MAX_PAGE_VISITS_SELECT", array("
    SELECT
    `Page`,
    COUNT(`CTP_ID`) as Visits,
    `time`
FROM
    `come_to_page`
GROUP BY
    `Page`
HAVING
    MONTH(`time`) = MONTH(CURRENT_TIMESTAMP)
ORDER BY Visits DESC
LIMIT 1;"
));
define("MIN_PAGE_VISITS_SELECT", array("
SELECT
    `Page`,
    COUNT(`CTP_ID`) as Visits,
    `time`
FROM
    `come_to_page`
GROUP BY
    `Page`
HAVING
    MONTH(`time`) = MONTH(CURRENT_TIMESTAMP)
ORDER BY Visits
LIMIT 1
"));
define("AVERAGE_PAGE_VISITS_SELECT", array("
SELECT
AVG(s.Visits) AS 'average_visits'
FROM
(
SELECT
    `Page`,
    COUNT(`CTP_ID`) AS Visits,
    `time`
FROM
    `come_to_page`
GROUP BY
    `Page`
HAVING
    MONTH(`time`) = MONTH(CURRENT_TIMESTAMP)
) as s
"
)


);

define("TOTAL_NUMBER_USERS", array(
    "
    SELECT
    SUM(e.entities)
FROM
    (
    SELECT
        COUNT(`SMME_ID`) AS entities
    FROM
        yasccoza_openlink_smmes.signup
    UNION
        SELECT
            COUNT(`COMPANY_ID`) AS entities
        FROM
        yasccoza_openlink_companies.signup) e
    "
    ));
    
    define("TOTAL_SMME_USERS", array(
        "
        SELECT
        COUNT(`SMME_ID`) AS entities
        FROM
            yasccoza_openlink_smmes.signup
        "
        ));
        define("TOTAL_COMPANY_USERS", array(
            "
            SELECT
            COUNT(`COMPANY_ID`) AS entities
        FROM
            yasccoza_openlink_companies.signup
            "
            ));





define("SEARCH_GRAPGH_SELECT", array(
"
SELECT
    s.term_name,
    SUM(s.Searches) 
FROM
    (
        SELECT
        `term_name`,
        COUNT(`TERM_ID`) as Searches
    FROM
            yasccoza_openlink_smmes.search_terms
        WHERE term_category = 'Legal Name'
        GROUP BY
            `term_name`
    UNION
        SELECT
        `term_name`,
        COUNT(`TERM_ID`) as Searches
    FROM
        yasccoza_openlink_companies.search_terms
    WHERE term_category = 'Legal Name'
        GROUP BY
        `term_name`
    ) s
GROUP BY
    `term_name`
;

"
)


);
define("MOST_SEARCHED_NAME_SELECT", array(
"
SELECT
    s.term_name,
    SUM(s.Searches)
FROM
    (
    SELECT
        `term_name`,
        COUNT(`TERM_ID`) AS Searches
    FROM
        yasccoza_openlink_smmes.search_terms
    WHERE
        term_category = 'Legal Name'
    GROUP BY
        `term_name`
    UNION
SELECT
    `term_name`,
    COUNT(`TERM_ID`) AS Searches
FROM
    yasccoza_openlink_companies.search_terms
WHERE
    term_category = 'Legal Name'
GROUP BY
    `term_name`
ORDER BY
    Searches
DESC
) s
GROUP BY
    `term_name`
ORDER BY
    Searches DESC
LIMIT 1;

    

"
)


);
define("MOST_SEARCHED_INDUSTRY", array(
"
SELECT
    s.term_name,
    SUM(s.Searches)
FROM
    (
        SELECT
        `term_name`,
        COUNT(`TERM_ID`) as Searches
    FROM
        yasccoza_openlink_smmes.search_terms
    WHERE term_category = 'Industry'
    GROUP BY
        `term_name`
    UNION
    SELECT
        `term_name`,
        COUNT(`TERM_ID`) as Searches
    FROM
        yasccoza_openlink_companies.search_terms
    WHERE term_category = 'Industry'
    GROUP BY
        `term_name`
    ORDER BY Searches DESC
) s
GROUP BY
    `term_name`
ORDER BY
    Searches DESC
LIMIT 1;



"
)


);
define("MOST_SEARCHED_PRODUCT", array(
"
SELECT
    s.term_name,
    SUM(s.Searches)
FROM
    (
        SELECT
        `term_name`,
        COUNT(`TERM_ID`) as Searches
    FROM
        yasccoza_openlink_smmes.search_terms
    WHERE term_category = 'Products'
    GROUP BY
        `term_name`
    UNION
    SELECT
        `term_name`,
        COUNT(`TERM_ID`) as Searches
    FROM
        yasccoza_openlink_companies.search_terms
    WHERE term_category = 'Products'
    GROUP BY
        `term_name`
    ORDER BY Searches DESC
) s
GROUP BY
    `term_name`
ORDER BY
    Searches DESC
LIMIT 1;





"));
define("CURRENT_DAY_SEARCHES_SELECT", array(
"
SELECT
    SUM(e.todays_searches) as 'current_searches'
FROM
    (
        SELECT 
            COUNT(`SEARCH_ID`) AS todays_searches
        FROM yasccoza_openlink_smmes.search
        WHERE DAY(`time`) = DAY(CURRENT_TIMESTAMP)
    UNION
        SELECT 
            COUNT(`SEARCH_ID`) AS todays_searches
        FROM yasccoza_openlink_companies.search
        WHERE DAY(`time`) = DAY(CURRENT_TIMESTAMP)
    ) e

"
)


);