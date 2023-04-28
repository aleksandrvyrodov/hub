//title: mail_test

$to = 'web@master-it.spb.ru';
$subject = 'Top secret by Jack';
$message = 'Hello! It\'s time to have fun fun fun fuuuuuuuuuun!';

$headers = array(
    'From' => 'Jack <no-reply@truxor.ru>',
    'Reply-To' => 'info@truxor.ru'
);


if (mail($to, $subject, $message, $headers)) {
    echo 'Письмо отправлено успешно!';
} else {
    echo 'Ошибка при отправке письма';
}
