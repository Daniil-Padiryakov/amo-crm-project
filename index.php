<?php

//ПОДДОМЕН НУЖНОГО АККАУНТА
$subdomain = '';

$link = 'https://' . $subdomain . '.amocrm.ru/api/v4/contacts?with=leads'; //Формируем URL для запроса

//ТОКЕН НУЖНОГО ПОЛЬЗОВАТЕЛЯ
$access_token = '';

/** Формируем заголовки */
$headers = [
    'Authorization: Bearer ' . $access_token
];

$curl = curl_init();
/** Устанавливаем необходимые опции для сеанса cURL  */
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-oAuth-client/1.0');
curl_setopt($curl, CURLOPT_URL, $link);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

$out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
$data = json_decode($out, true); // json -> array
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

$code = (int)$code;
$errors = [
    400 => 'Bad request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Not found',
    500 => 'Internal server error',
    502 => 'Bad gateway',
    503 => 'Service unavailable',
];

try {
    /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
    if ($code < 200 || $code > 204) {
        throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
    }
} catch (\Exception $e) {
    die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
}
echo '<pre>';

$id_contacts_without_leads = []; //идентификаторы контактов без сделок

foreach ($data as $leads) {
    // проходимся по всем контактам
    for ($i = 0; isset($leads['contacts'][$i]['id']); $i++) {
        // проверка есть ли сделка
        if (!$leads['contacts'][$i]['_embedded']['leads']) {
            // добавляем id контакта без сделки в массив
            $id_contacts_without_leads[] = $leads['contacts'][$i]['id'];
        }
    }

}
// добавление задач
include_once 'addTasks.php';