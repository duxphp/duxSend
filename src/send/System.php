<?php

namespace dux\send;

/**
 * 聊天推送
 */
class System implements \dux\send\SendInterface {

    protected $config = [
        'app_id'     => '',
        'app_secret' => '',
        'url'        => ''
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

    /**
     * 签名
     * @param $time
     * @return string
     */
    protected function sign($time = 0){
        $url = explode('://',$this->url());
        $url = count($url) == 1 ? $url[0] : $url[1];
        $sign = sprintf('url=%s&timestamp=%s&key=%s',$url,$time,$this->config['app_secret']);
        return strtoupper(md5($sign));
    }

    public function send($receive, string $title, string $content, array $params) {

        $data = $params['data'];
        if(!isset($data['user_id'])){
            $data['user_id'] = $receive;
        }
        $data['user_id'] = (int)$data['user_id'];

        $time = time();
        $header = [
            'Content-MD5'  => $this->sign($time),
            'Content-Date' => (string)$time,
            'AccessKey'    => $this->config['app_id']
        ];

        try {

            $response = (new \GuzzleHttp\Client())->request('POST', $this->url(), [
                'json'    => $data,
                'headers' => $header
            ]);
            $reason = $response->getStatusCode();
            if ($reason <> 200) {
                throw new \Exception("SystemSend Send failed!");
            }
            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);
            if (empty($data)) {
                throw new \Exception("SystemSend Send failed!");
            }
            if($data['code'] <> 200) {
                throw new \Exception("SystemSend Error: [{$data['code']}] {$data['message']}");
            }

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new \Exception($e->getResponse()->getBody()->getContents());
        }

        return true;
    }

    private function url(){
        return $this->config['url'] . '/bridge/notice/send';
    }

}