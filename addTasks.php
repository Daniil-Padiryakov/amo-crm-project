<?php
require_once 'index.php';

$link = 'https://' . $subdomain . '.amocrm.ru/api/v4/tasks'; //Формируем URL для запроса

/** Формируем заголовки */
$headers = [
    'Authorization: Bearer ' . $access_token,
    'Content-Type: application/json'
];

// добавляем задачи
foreach ($id_contacts_without_leads as $id) {

    // дата задачи(1 день)
    $time_task = time() + 3600 * 24;

    $myCurl = curl_init();

    /** Устанавливаем необходимые опции для сеанса cURL  */
    curl_setopt_array($myCurl, array(

        CURLOPT_URL => $link,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([[
            'task_type_id' => 1,
            "text" => 'Контакт без сделок',
            "complete_till" => $time_task, // time
            "entity_id" => $id, // id
            "entity_type" => "contacts",
        ]]),
        CURLOPT_USERAGENT => 'amoCRM-oAuth-client/1.0',
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_HEADER => false,
        CURLOPT_SSL_VERIFYPEER => 1,
        CURLOPT_SSL_VERIFYHOST => 2,

    ));

    $response = curl_exec($myCurl);
    $data = json_decode($response, true);
    curl_close($myCurl);
}