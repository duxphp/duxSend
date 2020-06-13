<?php

namespace dux\send;

/**
 * 聊天推送
 */
class DuxChats implements \dux\send\SendInterface {

    protected $config = [
        'appid'  => '',
        'secret' => '',
        'url'    => ''
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

        $data = $params['data'];

        $header = [
            'CHATS-AUTHORIZATION' => $params['token']
        ];

        try {
            $response = (new \GuzzleHttp\Client())->request('POST', $this->url(), [
                'json'    => $data,
                'headers' => $header
            ]);
            $reason = $response->getStatusCode();
            if ($reason <> 200) {
                throw new \Exception("DuxChats Send failed!");
            }
            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);
            if (empty($data)) {
                throw new \Exception("DuxChats Send failed!");
            }
            if($data['code'] <> 200) {
                throw new \Exception("DuxChats Error: [{$data['code']}] {$data['message']}");
            }

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new \Exception($e->getResponse()->getBody()->getContents());
        }

        return true;
    }

    private function url(){
        return $this->config['url'] . '/a/chats/Message/send';
    }

}