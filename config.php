<?php

/**
 * Direct URL of the funnel step
 */
$member_access_url    = 'http://demo.devquickie.com/test2?page_id=234234&page_key=asdasd&page_hash=234234&login_redirect=1';

/**
 * Which product id purchase should grant access?
 * @var array
 */
$accessible_products  = ["1132163"];

/**
 * Email details
 */
$email_from_name     = "Sahan";

$email_from_email    = "sahan@devquickie.com";

$email_subject       = "Membership access to DevQuickie";

$email_smtp_host     = "smtp.email.com";

$email_smtp_port     = 25;

$email_smtp_username = "login@smtp.com";

$email_smtp_password = "smtppass";


/**
 * Specify message below you can use below merge fields
 * {name} for name
 * {email} for email
 * {password} for password
 */
$message = <<<EOD
Hello {name},

Please find your login details below

Link - http://devquickie.com
Username - {email}
Password - {password}
EOD;

