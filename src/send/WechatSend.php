<?php

namespace dux\send;

/**
 * 微信模板消息
 */
class WechatSend implements \dux\send\SendInterface {

    protected $config = [
        'appid' => '',
        'secret' => '',
        'token' => '',
        'aeskey' => '',
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
            'data' => $params['param'],
        ];
        if ($params['minapp']) {
            $data['miniprogram'] = [
                'appid' => $params['minapp']['appid'],
                'pagepath' => $params['minapp']['path'],
            ];
        }
        $wechat->template_message->send($data);
        return true;
    }

    public function wechat() {
        $options = [
            'app_id' => $this->config['appid'],
            'secret' => $this->config['secret'],
            'token' => $this->config['token'],
            'aes_key' => $this->config['aeskey'],
            'response_type' => 'array',
            'log' => [
                'level' => 'error',
                'permission' => 0775,
                'file' => DATA_PATH . 'log/wechat/send_' . date('y-m-d') . '.log',
            ],
            'http' => [
                'timeout' => 20,
            ],
        ];
        return \EasyWeChat\Factory::officialAccount($options);
    }

}
