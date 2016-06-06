<?php
/**
 * 若快验证码人工打码接口
 * post data=bas64.
 */

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'http://api.ruokuai.com/create.json');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 65);

curl_custom_postfields($ch, [
    // 'username' => '填写你的用户名',
    // 'password' => '填写你的密码',
    'typeid' => 3000, // 4位的字母数字混合码   类型表http://www.ruokuai.com/pricelist.aspx
    'timeout' => 60,  // 中文以及选择题类型需要设置更高的超时时间建议90以上
    'softid' => 53089,
    'softkey' => '1e3caa0b568b450ebd25e33526a7c294',
], [
    // 'image' => file_get_contents('img.jpg'),
    'image' => base64_decode($_POST['data']),
]);

$result = curl_exec($ch);
curl_close($ch);

die($result);


/**
 * For safe multipart POST request for PHP5.3 ~ PHP 5.4.
 *
 * @param resource $ch    cURL resource
 * @param array    $assoc "name => value"
 * @param array    $files "name => path"
 *
 * @return bool
 *
 * @link http://php.net/manual/en/class.curlfile.php
 */
function curl_custom_postfields(&$ch, array $assoc = array(), array $files = array())
{

    // invalid characters for "name" and "filename"
    static $disallow = array("\0", '"', "\r", "\n");

    // build normal parameters
    foreach ($assoc as $k => $v) {
        $k = str_replace($disallow, '_', $k);
        $body[] = implode("\r\n", array(
            "Content-Disposition: form-data; name=\"{$k}\"",
            '',
            filter_var($v),
        ));
    }

    // build file parameters
    foreach ($files as $k => $v) {
        $k = str_replace($disallow, '_', $k);
        $body[] = implode("\r\n", array(
            "Content-Disposition: form-data; name=\"{$k}\"; filename=\"{$k}\"",
            'Content-Type: application/octet-stream',
            '',
            $v,
        ));
    }

    // generate safe boundary
    do {
        $boundary = '---------------------'.md5(mt_rand().microtime());
    } while (preg_grep("/{$boundary}/", $body));

    // add boundary for each parameters
    array_walk($body, function (&$part) use ($boundary) {
        $part = "--{$boundary}\r\n{$part}";
    });

    // add final boundary
    $body[] = "--{$boundary}--";
    $body[] = '';

    // die(implode("\r\n", $body));

    // set options
    return curl_setopt_array($ch, array(
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => implode("\r\n", $body),
        CURLOPT_HTTPHEADER => array(
            'Expect: 100-continue',
            "Content-Type: multipart/form-data; boundary={$boundary}", // change Content-Type
        ),
    ));
}
