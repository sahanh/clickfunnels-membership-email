<?php
require __DIR__.'/vendor/autoload.php';

use Tx\Mailer;
use Goutte\Client;
use GuzzleHttp\Exception\ConnectException;

$headers = getallheaders();

if (!array_get($headers, "X-Clickfunnels-Webhook-Delivery-Id")) {
    echo "Not a valid CF webhook request";
    exit;
}

include_once __DIR__."/config.php";

/**
 * DO NOT EDIT Anything below
 */

$input    = json_decode(file_get_contents('php://input'), true);

$password = str_random(8);

$member_access_query  = parse_url($member_access_url, PHP_URL_QUERY);

$member_access_params = [];
parse_str($member_access_query, $member_access_params);

/**
 * Extract parameters
 */
$contact = array_get($input, 'contact');

$fields  = [
    'member[email]' => array_get($contact, 'email'),
    'member[password]' => $password,
    'member[password_confirmation]' => $password,
    'member[page_key]' => array_get($member_access_params, 'page_key'),
    'member[page_id]' => array_get($member_access_params, 'page_id')
];

$merge_fields = [
    '{name}' => array_get($contact, 'name'),
    '{email}' => array_get($contact, 'email'),
    '{password}' => $password,
];

$message = strtr($message, $merge_fields);

$email_to_name = array_get($contact, 'name');
$email_to_email = array_get($contact, 'email');

try {

    $client  = new Client();

    $crawler = $client->request('GET', $member_access_url);
    /**
     * In funnel step page, we are looking for a form
     */
    $form    = $crawler->filter('form[action="/members"]')->form();

    /**
     * Submitting the form with above fields
     */
    $crawler = $client->submit($form, $fields);

    if (str_contains($crawler->getIterator()[0]->nodeValue, "Existing Account Found")) {
        echo "Duplicate sign up";
        exit;
    }

    $ok = (new Mailer())
        ->setServer($email_smtp_host, $email_smtp_port)
        ->setAuth($email_smtp_username, $email_smtp_password)
        ->setFrom($email_from_name, $email_from_email)
        ->addTo($email_to_name, $email_to_email)
        ->setSubject($email_subject)
        ->setBody(nl2br($message))
        ->send();

    //Email has already been taken
    echo "Job done.";

} catch (InvalidArgumentException $e) {
   
    if ($e->getMessage() == 'The current node list is empty.') {
        echo "Got an unexpected page.";
    }

} catch (Exception $e) {
    echo $e->getMessage();
}

