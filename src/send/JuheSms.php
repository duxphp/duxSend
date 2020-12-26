<?php

namespace dux\send;

/**
 * 聚合短信
 */
class JuheSms implements \dux\send\SendInterface
{

    protected $config = [
        'key' => '',
    ];

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function check($receive)
    {
        if (filter_var($receive, \FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => "/^1\d{10}$/"]]) === false) {
            return false;
        }
        return true;
    }

    public function send($receive, string $title, string $content, array $params)
    {
        if (!$params['tpl']) {
            throw new \Exception('JuheSms the template does not exist');
        }
        $apiParams = [];
        $apiParams["mobile"] = $receive; //手机号
        $apiParams["tpl_id"] = $params['tpl']; //模板id
        $params = [];
        foreach ($params['data'] as $key => $vo) {
            $params[] = "#{$key}#=" . $vo;
        }
        $apiParams["tpl_value"] = urlencode(implode('&', $params));  //模版内容
        $apiParams["key"] = $this->config['key']; //key
        try {
            $response = (new \GuzzleHttp\Client())->request('POST', $url, [
                'form_params' => $apiParams,
            ]);
            $reason = $response->getStatusCode();
            if ($reason <> 200) {
                throw new \Exception("JuheSms Send failed!");
            }
            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);
            if (empty($data)) {
                throw new \Exception("JuheSms Send failed!");
            }
            if($data['error_code']) {
                throw new \Exception("JuheSms Error: [{$data['error_code']}] {$data['reason']}");
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $data = $e->getResponse()->getBody()->getContents();
            $data = json_decode($data, true);
            if (empty($data)) {
                throw new \Exception("JuheSms Send failed!");
            }
            throw new \Exception("JuheSms Error: " . $data['Message']);
        }
        return true;
    }

}
