<?php 


define("COMPANY_SEARCH_INSERT", array(
    "INSERT INTO `search`(`hits`, `COMPANY_ID`) VALUES (?,?);","ii"));

define("P_COMPANY_SEARCH_INSERT", array(
    "INSERT INTO `search`(`hits`, `COMPANY_ID`, Who) VALUES (?,?,?);","iis"));

define("SMME_SEARCH_INSERT", array(
    "INSERT INTO `search`(`hits`, `SMME_ID`) VALUES (?,?);","ii"));

define("SEARCH_TERM_INSERT", array(
    "INSERT INTO `search_terms`(`term_name`, `term_category`, `SEARCH_ID`) VALUES (?, ?, ?);","ssi"));

define("SIMPLE_SEARCH_SELECT", array(
    "SELECT
    Legal_name,
    s.SMME_ID AS ID,
    s.typeOfEntity,
    p.ext,
    Address,
    i.title,
    Y.rating
FROM
    (
        (
            (
                (
                    openlink_smmes.register AS r
                INNER JOIN openlink_smmes.signup AS s
                ON
                    r.SMME_ID = s.SMME_ID
                )
            INNER JOIN openlink_smmes.pimg AS p
            ON
                s.SMME_ID = p.SMME_ID
            )
        INNER JOIN openlink_association_db.industry_title AS i
        ON
            r.INDUSTRY_ID = i.TITLE_ID
        )
    LEFT JOIN(
        SELECT(
                (
                    (
                        a.expense_score + b.status_score
                    ) / 20
                ) * 100
            ) AS rating,
            b.SMME_ID
        FROM
            (
            SELECT CASE WHEN
                e.total > 18000 THEN 10 WHEN((e.total / 18000) * 100) > 75 THEN 7.51 WHEN((e.total / 18000) * 100) > 68 THEN 6.83 WHEN((e.total / 18000) * 100) > 59 THEN 5.92 WHEN((e.total / 18000) * 100) > 51 THEN 5.13 WHEN((e.total / 18000) * 100) > 46 THEN 4.67 WHEN((e.total / 18000) * 100) > 38 THEN 3.86 WHEN((e.total / 18000) * 100) >= 23 THEN 2.39 ELSE 0
        END AS expense_score,
        e.SMME_ID
    FROM
        (
        SELECT
            SUM(e.rand_value) AS total,
            e.SMME_ID
        FROM
            openlink_smmes.expense_summary e
        GROUP BY
            SMME_ID
    ) e
    ) a
JOIN(
    SELECT CASE WHEN
        BBBEE_Status = 1 THEN 10 WHEN BBBEE_Status = 2 THEN 7.513148009 WHEN BBBEE_Status = 3 THEN 6.830134554 WHEN BBBEE_Status = 4 THEN 5.92025254 WHEN BBBEE_Status = 5 THEN 5.131581182 WHEN BBBEE_Status = 6 THEN 4.665073802 WHEN BBBEE_Status = 7 THEN 3.855432894 WHEN BBBEE_Status = 8 THEN 2.393920494 ELSE 0
END AS status_score,
SMME_ID
FROM
    openlink_smmes.company_documentation c
GROUP BY
    SMME_ID
    ) b
ON
    b.SMME_ID = a.SMME_ID
) AS Y
ON
    s.SMME_ID = Y.SMME_ID)
WHERE
    Legal_name LIKE CONCAT('%', ?, '%')
UNION
SELECT
    Legal_name,
    s.COMPANY_ID AS ID,
    s.typeOfEntity,
    p.ext,
    Address,
    i.title,
    NULL AS rating
FROM
    (
        (
            (
                openlink_companies.register AS r
            INNER JOIN openlink_companies.signup AS s
            ON
                r.COMPANY_ID = s.COMPANY_ID
            )
        INNER JOIN openlink_companies.pimg AS p
        ON
            s.COMPANY_ID = p.COMPANY_ID
        )
    INNER JOIN openlink_association_db.industry_title AS i
    ON
        r.INDUSTRY_ID = i.TITLE_ID
    )
WHERE
    Legal_name LIKE CONCAT('%', ?, '%');","ss"));



define("SMME_SEARCH_SELECT", array(
    "SELECT DISTINCT r.Legal_name, s.SMME_ID AS ID, s.typeOfEntity, p.ext, r.Address, IT.title
    FROM openlink_smmes.register AS r, 
    openlink_smmes.signup AS s, 
    openlink_smmes.pimg AS p, 
    openlink_smmes.products AS pp,
    openlink_association_db.industry_title AS IT,
    openlink_association_db.industry AS I
    WHERE r.SMME_ID = s.SMME_ID
    AND s.SMME_ID = p.SMME_ID
    AND p.SMME_ID = pp.SMME_ID
    AND r.INDUSTRY_ID = IT.TITLE_ID
    AND IT.INDUSTRY_ID = I.INDUSTRY_ID
    AND s.typeOfEntity = 'SMME'
    AND (r.Legal_name LIKE CONCAT('%',?,'%')
    AND pp.product_name LIKE CONCAT('%',?,'%')
    AND IT.title LIKE CONCAT('%',?,'%')
    AND r.foo LIKE CONCAT('%',?,'%')
    AND I.office LIKE CONCAT('%',?,'%'))","sssss"));


define("NPO_SEARCH_SELECT", array(
    "SELECT DISTINCT r.Legal_name, s.SMME_ID AS ID, s.typeOfEntity, p.ext, r.Address, IT.title
    FROM openlink_smmes.register AS r, 
    openlink_smmes.signup AS s, 
    openlink_smmes.pimg AS p, 
    openlink_smmes.products AS pp,
    openlink_association_db.npo_industry_title AS IT,
    openlink_association_db.npo_industry AS I
    WHERE r.SMME_ID = s.SMME_ID
    AND s.SMME_ID = p.SMME_ID
    AND p.SMME_ID = pp.SMME_ID
    AND r.INDUSTRY_ID = IT.NPO_TITLE_ID
    AND IT.NPO_INDUSTRY_ID = I.NPO_INDUSTRY_ID
    AND s.typeOfEntity = 'NPO'
    AND (r.Legal_name LIKE CONCAT('%',?,'%')
    AND pp.product_name LIKE CONCAT('%',?,'%')
    AND IT.title LIKE CONCAT('%',?,'%')
    AND r.foo LIKE CONCAT('%',?,'%')
    AND I.office LIKE CONCAT('%',?,'%'))","sssss"));


define("COMPANY_SEARCH_SELECT", array(
    "SELECT DISTINCT r.Legal_name, s.COMPANY_ID AS ID, s.typeOfEntity, p.ext, r.Address, IT.title
    FROM openlink_companies.register AS r,
    openlink_companies.signup AS s,
    openlink_companies.pimg AS p,
    openlink_association_db.industry_title AS IT,
    openlink_association_db.industry AS I
    WHERE r.COMPANY_ID = s.COMPANY_ID
    AND s.COMPANY_ID = p.COMPANY_ID
    AND r.INDUSTRY_ID = IT.TITLE_ID
    AND IT.INDUSTRY_ID = I.INDUSTRY_ID
    AND (Legal_name LIKE CONCAT('%',?,'%')
        AND r.foo LIKE CONCAT('%',?,'%')
        AND IT.title LIKE CONCAT('%',?,'%')
        AND I.office LIKE CONCAT('%',?,'%')
        )","ssss"));

define("ENTITY_RATING_SELECT",array("SELECT
(
    (
        (
            a.expense_score + b.status_score
        ) / 20
    ) * 100
) as rating
FROM
(
SELECT CASE WHEN
    e.total > 18000 THEN 10 WHEN((e.total / 18000) * 100) > 75 THEN 7.51 WHEN((e.total / 18000) * 100) > 68 THEN 6.83 WHEN((e.total / 18000) * 100) > 59 THEN 5.92 WHEN((e.total / 18000) * 100) > 51 THEN 5.13 WHEN((e.total / 18000) * 100) > 46 THEN 4.67 WHEN((e.total / 18000) * 100) > 38 THEN 3.86 WHEN((e.total / 18000) * 100) >= 23 THEN 2.39 ELSE 0
END AS expense_score
FROM
(
SELECT
    SUM(e.rand_value) AS total
FROM
    openlink_smmes.expense_summary e
WHERE
    e.SMME_ID = ?
) e
) a
JOIN(
SELECT CASE WHEN
    BBBEE_Status = 1 THEN 10 WHEN BBBEE_Status = 2 THEN 7.513148009 WHEN BBBEE_Status = 3 THEN 6.830134554 WHEN BBBEE_Status = 4 THEN 5.92025254 WHEN BBBEE_Status = 5 THEN 5.131581182 WHEN BBBEE_Status = 6 THEN 4.665073802 WHEN BBBEE_Status = 7 THEN 3.855432894 WHEN BBBEE_Status = 8 THEN 2.393920494 ELSE 0
END AS status_score
FROM
openlink_smmes.company_documentation c
WHERE
c.SMME_ID = ?
) b;", "iI"));








//final rating sql
// SELECT
//     Legal_name,
//     s.SMME_ID AS ID,
//     s.typeOfEntity,
//     p.ext,
//     Address,
//     i.title,
//     Y.rating
// FROM
//     (
//         (
//             (
//                 (
//                     openlink_smmes.register AS r
//                 INNER JOIN openlink_smmes.signup AS s
//                 ON
//                     r.SMME_ID = s.SMME_ID
//                 )
//             INNER JOIN openlink_smmes.pimg AS p
//             ON
//                 s.SMME_ID = p.SMME_ID
//             )
//         INNER JOIN openlink_association_db.industry_title AS i
//         ON
//             r.INDUSTRY_ID = i.TITLE_ID
//         )
//     LEFT JOIN(
//         SELECT(
//                 (
//                     (
//                         a.expense_score + b.status_score
//                     ) / 20
//                 ) * 100
//             ) AS rating,
//             b.SMME_ID
//         FROM
//             (
//             SELECT CASE WHEN
//                 e.total > 18000 THEN 10 WHEN((e.total / 18000) * 100) > 75 THEN 7.51 WHEN((e.total / 18000) * 100) > 68 THEN 6.83 WHEN((e.total / 18000) * 100) > 59 THEN 5.92 WHEN((e.total / 18000) * 100) > 51 THEN 5.13 WHEN((e.total / 18000) * 100) > 46 THEN 4.67 WHEN((e.total / 18000) * 100) > 38 THEN 3.86 WHEN((e.total / 18000) * 100) >= 23 THEN 2.39 ELSE 0
//         END AS expense_score,
//         e.SMME_ID
//     FROM
//         (
//         SELECT
//             SUM(e.rand_value) AS total,
//             e.SMME_ID
//         FROM
//             openlink_smmes.expense_summary e
//         GROUP BY
//             SMME_ID
//     ) e
//     ) a
// JOIN(
//     SELECT CASE WHEN
//         BBBEE_Status = 1 THEN 10 WHEN BBBEE_Status = 2 THEN 7.513148009 WHEN BBBEE_Status = 3 THEN 6.830134554 WHEN BBBEE_Status = 4 THEN 5.92025254 WHEN BBBEE_Status = 5 THEN 5.131581182 WHEN BBBEE_Status = 6 THEN 4.665073802 WHEN BBBEE_Status = 7 THEN 3.855432894 WHEN BBBEE_Status = 8 THEN 2.393920494 ELSE 0
// END AS status_score,
// SMME_ID
// FROM
//     openlink_smmes.company_documentation c
// GROUP BY
//     SMME_ID
//     ) b
// ON
//     b.SMME_ID = a.SMME_ID
// ) AS Y
// ON
//     s.SMME_ID = Y.SMME_ID)
// WHERE
//     Legal_name LIKE CONCAT('%', ?, '%')
// UNION
// SELECT
//     Legal_name,
//     s.COMPANY_ID AS ID,
//     s.typeOfEntity,
//     p.ext,
//     Address,
//     i.title,
//     NULL AS rating
// FROM
//     (
//         (
//             (
//                 openlink_companies.register AS r
//             INNER JOIN openlink_companies.signup AS s
//             ON
//                 r.COMPANY_ID = s.COMPANY_ID
//             )
//         INNER JOIN openlink_companies.pimg AS p
//         ON
//             s.COMPANY_ID = p.COMPANY_ID
//         )
//     INNER JOIN openlink_association_db.industry_title AS i
//     ON
//         r.INDUSTRY_ID = i.TITLE_ID
//     )
// WHERE
//     Legal_name LIKE CONCAT('%', ?, '%')




















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
//     openlink_companies.search_terms
// WHERE
//     MATCH(`term_name`) AGAINST(? IN BOOLEAN MODE) >= 0.5
// ) a


