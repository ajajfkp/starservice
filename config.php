<?php

/* User defined constants to be used in application

*	It will autometically get the all path relative to root folder of webserver.
*	Written By	:- Aijaz Ahmad
*	DATE		:- 11/12/2017
*/

/************************************************************/
/*************************production*************************/
/************************************************************/
/*							    							*/
/************************************************************/
/********************|Database Configuration|****************/
/************************************************************/
/*
define("CH_DB_HOST", "localhost");
define("CH_DB_USER", "x8u1f4p4_aijaz");
define("CH_DB_PASSWORD", "!@#dev@db");
define("CH_DB_NAME", "x8u1f4p4_local");

define('BASE_URL', 'http://collegebooksrus.com/mybooksrus/');

*/
/************************************************************/
/*********************|Email Configuration|******************/
/************************************************************/
/*
define("PROTOCOL", "SMTP");
define("SMTP_HOST", "cloud1022.hostgator.com");
define("SMTP_PORT", "465");
define("SMTP_USER", "help@collegebooksrus.com");
define("SMTP_PASS", "!@#dev@email");
define("MAILTYPE", "HTML");
define("CHARSET", "iso-8859-1");
define("WORDWRAP", "TRUE");
define("EMAIL_FROM", "admin@collegebooksrus.com");
define("EMAIL_FROM_NAME", "Admin Tean");
define("EMAIL_VARIFY_URL", "http://collegebooksrus.com/mybooksrus/verifyaccount/");
define("FORGET_PASSWD_URL", "http://collegebooksrus.com/mybooksrus/changepasswd/");

/****************************************************************/
/*********************|RECAPTCHA Configuration|******************/
/****************************************************************/
/*	
define("RE_CAPTCHA_SITEKEY", "6LfbXRIUAAAAADCFVh5aCTkRXLrJU1GZbjRiSw1F");
define("RE_CAPTCHA_SECRET", "6LfbXRIUAAAAAEhpct9JGOvBRxx1UxxmA4R7SyMV");
define("REMOTE_IP", $_SERVER['REMOTE_ADDR']);

*/

/************************************************************/
/*************************   LOCAL  *************************/
/************************************************************/
/*															*/
/************************************************************/
/********************|Database Configuration|****************/
/************************************************************/
date_default_timezone_set('Asia/Kolkata');
define("CH_DB_HOST", "localhost");
define("CH_DB_USER", "root");
define("CH_DB_PASSWORD", "");
define("CH_DB_NAME", "starsdb");
define('BASE_URL', '/starservice/');

/************************************************************/
/*********************|Email Configuration|******************/
/************************************************************/

/* define("PROTOCOL", "SMTP");
define("SMTP_HOST", "smtp.gmail.com");
define("SMTP_PORT", "465");
define("SMTP_USER", "aijaz.fkp@gmail.com");
define("SMTP_PASS", "!@#romio_75");
define("MAILTYPE", "HTML");
define("CHARSET", "iso-8859-1");
define("WORDWRAP", "TRUE");
define("EMAIL_FROM", "admin@yourdomain.com");
define("EMAIL_FROM_NAME", "Admin Tean");
define("EMAIL_VARIFY_URL", "http://localhost/booksrus/mybooksrus/verifyaccount/");
define("FORGET_PASSWD_URL", "http://localhost/booksrus/mybooksrus/changepasswd/");
define("MSG_CONVER_URL", "http://localhost/booksrus/mybooksrus/message/showmessages/"); */
/****************************************************************/
/*********************|RECAPTCHA Configuration|******************/
/****************************************************************/

/* define("RE_CAPTCHA_SITEKEY", "6LcGQRIUAAAAAFIizfT0hjD2dfraJd43OsvZdEO9");
define("RE_CAPTCHA_SECRET", "6LcGQRIUAAAAAM-9FuOVnew2LDgJkrw5Wn3E-I71");
define("REMOTE_IP", $_SERVER['REMOTE_ADDR']); */



/****************************************************************/
/*********************|Common Configuration|*********************/
/****************************************************************/

define("CH_DB_PORT", "3306");
define("CH_SITE_KEY", "_dev");
define("DEFAULT_DATE_FORMATE", "d-m-Y H:i:s");
/*define("CH_DB_UPDATE", "1");*/












