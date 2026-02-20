

<?php 


define("CHECK_F_EMAIL_SELECT", array(
    "SELECT SMME_ID FROM
    `signup` 
    WHERE Email = ?;","s"));

define("INSERT_TOKEN", array(
    "INSERT INTO `pass_r`(`selector`, `validator`, `expiryDate`, `SMME_ID`) VALUES (?, ?, ?, ?)","sssi"));

define("CHECK_EXISTS_SELECT", array(
    "SELECT `expiryDate`, `SMME_ID` FROM `pass_r` WHERE `selector` = ? AND `validator` = ?;","ss"));

define("UPDATE_TOKEN", array(
    "UPDATE `signup` SET `Pwd`= ? WHERE SMME_ID = ?;","si"));

define("DELETE_TOKEN", array(
    "DELETE FROM `pass_r` WHERE selector = ?;","s"));

    define("CHECK_F_EMAIL_SELECT_C", array(
        "SELECT COMPANY_ID FROM
        `signup` 
        WHERE Email = ?;","s"));
    define("CHECK_F_EMAIL_SELECT_CC", array(
        "SELECT CONSULTANT_ID FROM
        `signup` 
        WHERE Email = ?;","s"));

define("INSERT_TOKEN_C", array(
    "INSERT INTO pass_r(selector, validator, expiryDate, COMPANY_ID) VALUES (?, ?, ?, ?)","sssi"));
    define("INSERT_TOKEN_CC", array(
        "INSERT INTO pass_r(selector, validator, expiryDate, CONSULTANT_ID) VALUES (?, ?, ?, ?)","sssi"));
    

    define("CHECK_EXISTS_SELECT_C", array(
        "SELECT `expiryDate`, `COMPANY_ID` FROM `pass_r` WHERE `selector` = ? AND `validator` = ?;","ss"));

        define("CHECK_EXISTS_SELECT_CC", array(
            "SELECT `expiryDate`, `CONSULTANT_ID` FROM `pass_r` WHERE `selector` = ? AND `validator` = ?;","ss"));

    define("UPDATE_TOKEN_C", array(
            "UPDATE `signup` SET `Pwd`= ? WHERE COMPANY_ID = ?;","si"));
            define("UPDATE_TOKEN_CC", array(
                "UPDATE `signup` SET `Pwd`= ? WHERE CONSULTANT_ID = ?;","si"));
    define("DELETE_TOKEN_C", array(
        "DELETE FROM `pass_r` WHERE selector = ?;","s"));
        define("DELETE_TOKEN_CC", array(
            "DELETE FROM `pass_r` WHERE selector = ?;","s"));

        define("SAVE_PASSWORD_UPDATE", array(
            "SELECT `expiryDate`, `COMPANY_ID` FROM `pass_r` WHERE `selector` = ? AND `validator` = ?;","ss"));  