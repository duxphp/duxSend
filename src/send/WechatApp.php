<?php

namespace dux\send;

/**
 * 微信模板消息
 */
class WechatApp implements \dux\send\SendInterface {

    protected $config = [
        'appid' => '',
        'secret' => '',
    ];

    public function __construct($config) {
        $this->config = $config;
    }

    public function check($receive) {
        return true;
    }

    public function send($receive, string $title, string $content, array $params) {
        if (!$params['tpl']) {
            throw new \Exception('Wechat the template does not exist');
        }
        $wechat = $this->wechat();
        $tmpData = [];
        foreach ($params['data'] as $key => $vo) {
            $tmpData[$key] = [
                'value' => $vo
            ];
        }
        $data = [
            'touser' => $receive,
            'template_id' => $params['tpl'],
            'page' => $params['page'],
            'data' => $tmpData,
        ];
        $status = $wechat->subscribe_message->send($data);
        if ($status['errcode']) {
            throw new \Exception('WechatAppSend:' . $status['errmsg']);
        }
        return true;
    }

    public function wechat() {
        $options = [
            'app_id' => $this->config['appid'],
            'secret' => $this->config['secret'],
            'response_type' => 'array',
            'log' => [
                'level' => 'error',
                'permission' => 0775,
                'file' => './data/log/wechatapp/send_' . date('y-m-d') . '.log',
            ],
            'http' => [
                'timeout' => 20,
            ],
        ];
        return \EasyWeChat\Factory::miniProgram($options);
    }

}
