<?php
// Minimal lead capture endpoint: saves to CSV and attempts email
// Accepts JSON body or application/x-www-form-urlencoded

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'method_not_allowed']);
  exit;
}

function getInput(): array {
  $ct = $_SERVER['CONTENT_TYPE'] ?? '';
  if (stripos($ct, 'application/json') !== false) {
    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
  }
  return $_POST;
}

function sanitize(string $s, int $max = 200): string {
  $s = trim($s);
  $s = preg_replace('/[\r\n]+/', ' ', $s);
  return mb_substr($s, 0, $max);
}

$in = getInput();
$name = sanitize((string)($in['name'] ?? ''));
$email = sanitize((string)($in['email'] ?? ''));
$phone = sanitize((string)($in['phone'] ?? ''), 40);
$city = sanitize((string)($in['city'] ?? ''), 80);
$role = sanitize((string)($in['role'] ?? ''), 32);

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'invalid_input']);
  exit;
}

$ts = time();
$row = [
  'name' => $name,
  'email' => $email,
  'phone' => $phone,
  'city' => $city,
  'role' => $role,
  'ts' => $ts,
];

// Append to CSV (create header if new file)
$csvPath = __DIR__ . DIRECTORY_SEPARATOR . 'leads.csv';
$isNew = !file_exists($csvPath);
$fp = fopen($csvPath, 'a');
if ($fp) {
  if ($isNew) {
    fputcsv($fp, array_keys($row));
  }
  fputcsv($fp, $row);
  fclose($fp);
}

// Attempt to send email (requires mail configured)
$to = 'ara100limite@gmail.com';
$subject = 'Novo cadastro FX Farma';
$body = "Novo cadastro FX Farma\n\n" .
        "Nome: {$name}\n" .
        "E-mail: {$email}\n" .
        "WhatsApp: {$phone}\n" .
        "Cidade/UF: {$city}\n" .
        "Perfil: {$role}\n" .
        "Timestamp: " . date('c', $ts) . "\n";

function sendMailSmart(string $to, string $subject, string $body, string $replyTo = ''): bool {
  $smtpHost = getenv('SMTP_HOST') ?: '';
  $smtpUser = getenv('SMTP_USER') ?: '';
  $smtpPass = getenv('SMTP_PASS') ?: '';
  $smtpPort = (int)(getenv('SMTP_PORT') ?: 587);
  $smtpFrom = getenv('SMTP_FROM') ?: ($smtpUser ?: 'no-reply@fxfarma.local');
  $smtpSecure = strtolower((string)(getenv('SMTP_SECURE') ?: 'tls'));

  if (class_exists('PHPMailer\\PHPMailer\\PHPMailer') && $smtpHost && $smtpUser && $smtpPass) {
    try {
      $pm = new PHPMailer\PHPMailer\PHPMailer(true);
      $pm->isSMTP();
      $pm->Host = $smtpHost;
      $pm->SMTPAuth = true;
      $pm->Username = $smtpUser;
      $pm->Password = $smtpPass;
      if ($smtpSecure === 'ssl') {
        $pm->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        if (!$smtpPort) { $smtpPort = 465; }
      } else {
        $pm->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
      }
      $pm->Port = $smtpPort ?: 587;
      $pm->CharSet = 'UTF-8';
      $pm->setFrom($smtpFrom, 'FX Farma');
      $pm->addAddress($to);
      if ($replyTo) { $pm->addReplyTo($replyTo); }
      $pm->Subject = $subject;
      $pm->Body = $body;
      return $pm->send();
    } catch (Throwable $e) {
      // Fall through to mail()
    }
  }
  $headers = 'From: ' . $smtpFrom;
  if ($replyTo) { $headers .= "\r\nReply-To: " . $replyTo; }
  try {
    return @mail($to, $subject, $body, $headers);
  } catch (Throwable $e) {
    return false;
  }
}

$mailSent = sendMailSmart($to, $subject, $body, $email ?: '');

echo json_encode(['ok' => true, 'mail_sent' => $mailSent]);
?>
