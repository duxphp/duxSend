<?php

namespace dux\send;

/**
 * 阿里云邮件
 */
class AliMail implements \dux\send\SendInterface {

    protected $config = [
        'api_id' => '',
        'api_key' => '',
        'mail' => '',
    ];

    public function __construct($config) {
        $this->config = $config;
    }

    public function check($receive) {
        if (!filter_var($receive, \FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    public function send($receive, string $title, string $content, array $params) {
        $apiParams = [];
        //公共参数
        $apiParams["AccessKeyId"] = $this->config['api_id'];
        $apiParams["Format"] = 'JSON';
        $apiParams["SignatureMethod"] = 'HMAC-SHA1';
        $apiParams["SignatureVersion"] = '1.0';
        $apiParams["SignatureNonce"] = uniqid();
        $apiParams["Timestamp"] = gmdate('Y-m-d\TH:i:s\Z');
        $apiParams["Version"] = '2015-11-23';
        //接口参数
        $apiParams["Action"] = 'SingleSendMail';
        $apiParams["TagName"] = 'duxphp';
        $apiParams['AddressType'] = 0;
        $apiParams['AccountName'] = $this->config['mail'];
        $apiParams['ReplyToAddress'] = 'true';
        $apiParams['ToAddress'] = $receive;
        $apiParams['Subject'] = $title;
        $apiParams['HtmlBody'] = $content;
        $apiParams["Signature"] = $this->computeSignature($apiParams, $this->config['api_key']);
        try {
            $response = (new \GuzzleHttp\Client())->request('POST', "http://dm.aliyuncs.com/", [
                'form_params' => $apiParams
            ]);
            $reason = $response->getStatusCode();
            if ($reason <> 200) {
                throw new \Exception("AliMail Send failed!");
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $data = $e->getResponse()->getBody()->getContents();
            $data = json_decode($data, true);
            if (empty($data)) {
                throw new \Exception("AliMail Send failed!");
            }
            throw new \Exception("AliMail Error: " . $data['Message']);
        }
        return true;
    }

    protected function computeSignature($parameters, $accessKeySecret) {
        ksort($parameters);
        $canonicalizedQueryString = '';
        foreach ($parameters as $key => $value) {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key) . '=' . $this->percentEncode($value);
        }
        $stringToSign = 'POST&%2F&' . $this->percentEncode(substr($canonicalizedQueryString, 1));
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . "&", true));
        return $signature;
    }

    protected function percentEncode($str) {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }

}
