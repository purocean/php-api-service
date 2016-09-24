<?php

/**
 * 使用 SendCloud 向自己发送邮件
 *
 * @param string $subject 邮件主题
 * @param string $html    邮件正文
 *
 * @return string 发送结果
 */
function sendMail2Me($subject, $html)
{
    $url = 'http://sendcloud.sohu.com/webapi/mail.send.json';
    $apiUser = '';
    $apiKey = '';

    //不同于登录SendCloud站点的帐号，您需要登录后台创建发信子帐号，使用子帐号和密码才可以进行邮件的发送。
    $param = [
        'api_user' => $apiUser,
        'api_key' => $apiKey,
        'from' => 'service@sendcloud.im',
        'fromname' => 'SendCloud测试邮件',
        'to' => '',
        'subject' => $subject,
        'html' => $html,
        'resp_email_id' => 'true'
    ];

    $data = http_build_query($param);

    $options = [
        'http' => [
            'method'  => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $data
        ]
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;
}

echo sendMail2Me($_GET['subject'], $_GET['html']);
