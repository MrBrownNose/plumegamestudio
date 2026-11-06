<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: support.html');
    exit;
}

if (trim((string) ($_POST['company'] ?? '')) !== '') {
    header('Location: support.html?sent=1');
    exit;
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$game = trim((string) ($_POST['game'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

$allowedGames = ['Corner', 'Something else'];
if (!in_array($game, $allowedGames, true)) {
    $game = 'Corner';
}

$hasLineBreak = static function (string $value): bool {
    return preg_match('/[\r\n]/', $value) === 1;
};

$valid = $name !== ''
    && strlen($name) <= 100
    && $message !== ''
    && strlen($message) <= 5000
    && filter_var($email, FILTER_VALIDATE_EMAIL)
    && strlen($email) <= 200
    && !$hasLineBreak($name)
    && !$hasLineBreak($email);

if (!$valid) {
    header('Location: support.html?error=1');
    exit;
}

$to = 'info@theplumegroup.com';
$subject = 'Support form: ' . $game;
$body = "Name: {$name}\nEmail: {$email}\nGame: {$game}\n\n{$message}\n";
$headers = implode("\r\n", [
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'From: Plume Game Studio <noreply@plumegamestudio.com>',
    'Reply-To: ' . $email,
]);

$sent = mail($to, $subject, $body, $headers);
header('Location: support.html?' . ($sent ? 'sent=1' : 'error=1'));
exit;
