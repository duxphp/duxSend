<?php

namespace dux\send;

/**
 * 微信模板消息
 */
class Wechat implements \dux\send\SendInterface {

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
        $data = [
            'touser' => $receive,
            'template_id' => $params['tpl'],
            'url' => $params['url'],
            'data' => $params['data'],
        ];
        if ($params['url_other']) {
            $other = explode('//', $params['url_other'], 2);
            $other = end($other);
            $otherArray = explode('@', $other, 2);
            $data['miniprogram'] = [
                'appid' => $otherArray[0],
                'pagepath' => $otherArray[1],
            ];
        }
        $status = $wechat->template_message->send($data);
        if ($status['errcode']) {
            throw new \Exception('WechatSend:' . $status['errmsg']);
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
                'file' => './data/log/wechat/send_' . date('y-m-d') . '.log',
            ],
            'http' => [
                'timeout' => 20,
            ],
        ];
        return \EasyWeChat\Factory::officialAccount($options);
    }

}
