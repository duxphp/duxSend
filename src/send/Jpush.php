<?php

namespace dux\send;

/**
 * 极光APP推送
 */
class Jpush implements \dux\send\SendInterface {

    protected $config = [
        'app_key' => '',
        'master_kecret' => '',
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
        if (!is_array($receive)) {
            $receive = [$receive];
        }
        $params['type'] = $params['type'] ? $params['type'] : 'alias';
        $data = [
            'platform' => 'all',
            'audience' => [
                $params['type'] => $receive,
            ],
            'notification' => [
                'android' => [
                    'alert' => $content,
                ],
                'ios' => [
                    'alert' => $content,
                    "sound" => "sound.caf",
                    'badge' => 1,
                ]
            ]
        ];
        try {
            $response = (new \GuzzleHttp\Client())->request('POST', 'https://api.jpush.cn/v3/push', [
                'auth' => [$this->config['app_key'], $this->config['master_kecret']],
                'headers' => ['Authorization' => 'Basic ' . base64_encode($this->config['app_key'] . ':' . $this->config['master_kecret'])],
                'json' => $data
            ]);
            $reason = $response->getStatusCode();
            if ($reason <> 200) {
                throw new \Exception("Jpush Send failed!");
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $data = $e->getResponse()->getBody()->getContents();
            $data = json_decode($data, true);
            if (empty($data)) {
                throw new \Exception("Jpush Send failed!");
            }
            throw new \Exception("Jpush Error: [{$data['error']['code']}] {$data['error']['message']}");
        }
        return true;
    }

}