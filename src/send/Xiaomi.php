<?php

namespace dux\send;

/**
 * 小米APP推送
 */
class Xiaomi implements \dux\send\SendInterface {

    protected $config = [
        'ios_key' => '',
        'android_key' => '',
        'android_name' => '',
    ];

    public function __construct($config) {
        $this->config = $config;
    }

    public function check($receive) {
        if (!$receive) {
            return false;
        }
        return true;
    }

    public function send($receive, string $title, string $content, array $params) {

        if(is_array($receive)) {
            $receive = implode(',', $receive);
        }
        $iosHeader = [
            'Authorization' => 'key=' . $this->config['ios_key']
        ];
        $androidHeader = [
            'Authorization' => 'key=' . $this->config['android_key']
        ];

        $iosData = [
            'alias' => $receive,
            'description' => $content,
        ];

        $androidData = [
            'alias' => $receive,
            'title' => $title,
            'description' => $content,
            'restricted_package_name' => $this->config['android_name'],
            'pass_through' => 0,
            'notify_type' => -1,
        ];

        try {
            $response = (new \GuzzleHttp\Client())->request('POST', 'https://api.xmpush.xiaomi.com/v3/message/alias', [
                'form_params' => $androidData,
                'headers' => $androidHeader
            ]);
            $reason = $response->getStatusCode();
            if ($reason <> 200) {
                throw new \Exception("Xiaomi Send failed!");
            }
            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);
            if (empty($data)) {
                throw new \Exception("Xiaomi Send failed!");
            }
            if($data['result'] == 'error') {
                throw new \Exception("Xiaomi Error: [{$data['code']}] {$data['description']}");
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new \Exception($e->getMessage());
        }

        try {
            $response = (new \GuzzleHttp\Client())->request('POST', 'https://api.xmpush.xiaomi.com/v3/message/alias', [
                'form_params' => $iosData,
                'headers' => $iosHeader
            ]);
            $reason = $response->getStatusCode();
            if ($reason <> 200) {
                throw new \Exception("Xiaomi Send failed!");
            }
            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);
            if (empty($data)) {
                throw new \Exception("Xiaomi Send failed!");
            }
            if($data['result'] == 'error') {
                throw new \Exception("Xiaomi Error: [{$data['code']}] {$data['description']}");
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new \Exception($e->getMessage());
        }
        return true;
    }

}