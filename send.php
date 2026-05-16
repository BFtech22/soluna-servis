<?php
declare(strict_types=1);

$RECIPIENT      = 'info@bftechnology.cz';
$SUBJECT_PREFIX = '[soluna-servis.cz] ';
$REFERER        = $_SERVER['HTTP_REFERER'] ?? '/';
$ORIGIN_PATH    = parse_url($REFERER, PHP_URL_PATH) ?: '/';
$THANKS_URL     = $ORIGIN_PATH . '?sent=1#kontakt';
$ERROR_URL      = $ORIGIN_PATH . '?sent=0#kontakt';

function pf($data, string $key, int $maxlen = 1000): string {
    if (!is_array($data)) return '';
    if (!isset($data[$key])) return '';
    $v = is_string($data[$key]) ? $data[$key] : '';
    $v = trim($v);
    if (strlen($v) > $maxlen) $v = substr($v, 0, $maxlen);
    return $v;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

// Honeypot
$hp = pf($_POST, 'website');
if ($hp !== '') {
    header('Location: ' . $THANKS_URL);
    exit;
}

$name     = pf($_POST, 'name', 100);
$email    = pf($_POST, 'email', 150);
$phone    = pf($_POST, 'phone', 50);
$interest = pf($_POST, 'interest', 200);
$message  = pf($_POST, 'message', 5000);

if ($name === '' || $email === '' || $phone === '') {
    header('Location: ' . $ERROR_URL);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ' . $ERROR_URL);
    exit;
}

$subject = $SUBJECT_PREFIX . 'Servisní dotaz od ' . $name;

$body  = "Nový servisní dotaz z webu soluna-servis.cz\n";
$body .= str_repeat('-', 50) . "\n\n";
$body .= "Jméno:     $name\n";
$body .= "Telefon:   $phone\n";
$body .= "E-mail:    $email\n";
if ($interest !== '') {
    $body .= "Model:     $interest\n";
}
$body .= "\nPopis závady:\n" . ($message !== '' ? $message : '(bez popisu)') . "\n\n";
$body .= str_repeat('-', 50) . "\n";
$body .= "IP:        " . ($_SERVER['REMOTE_ADDR'] ?? 'n/a') . "\n";
$body .= "UA:        " . substr($_SERVER['HTTP_USER_AGENT'] ?? 'n/a', 0, 200) . "\n";
$body .= "Referer:   " . $REFERER . "\n";
$body .= "Čas:       " . date('Y-m-d H:i:s') . "\n";

$replyEmail = preg_replace('/[\r\n]/', '', $email);

$headers  = "From: web@soluna-servis.cz\r\n";
$headers .= "Reply-To: " . $replyEmail . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

$encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

$ok = @mail($RECIPIENT, $encodedSubject, $body, $headers);

header('Location: ' . ($ok ? $THANKS_URL : $ERROR_URL));
