<?php

namespace dux\send;

/**
 * 极光APP推送
 */
class Yunpian implements \dux\send\SendInterface {

    protected $config = [
        'api_key' => '',
    ];

    public function __construct($config) {
        $this->config = $config;
    }

    public function check($receive) {
        if (filter_var($receive, \FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => "/^1\d{10}$/"]]) === false) {
            return false;
        }
        return true;
    }

    public function send($receive, string $title, string $content, array $params) {
        $data = [
            'apikey' => $this->config['api_key'],
            'mobile' => $receive,
            'text' => $content,
        ];
        try {
            $response = (new \GuzzleHttp\Client())->request('POST', 'https://sms.yunpian.com/v2/sms/single_send.json', [
                'form_params' => $data,
            ]);
            $reason = $response->getStatusCode();
            if ($reason <> 200) {
                throw new \Exception("Yunpian Send failed!");
            }
            $data = $e->getResponse()->getBody()->getContents();
            $data = json_decode($data, true);
            if (empty($data)) {
                throw new \Exception("Yunpian Send failed!");
            }
            if ($data['code'] <> 0) {
                throw new \Exception("Yunpian Error: [{$data['code']}] {$data['msg']}");
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $data = $e->getResponse()->getBody()->getContents();
            $data = json_decode($data, true);
            if (empty($data)) {
                throw new \Exception("Yunpian Send failed!");
            }
            throw new \Exception("Yunpian Error: [{$data['code']}] {$data['msg']} {$data['detail']}");
        }
        return true;
    }

}