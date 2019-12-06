<?php

namespace dux\send;

/**
 * 阿里云短信
 */
class AliSms implements \dux\send\SendInterface {

    protected $config = [
        'api_id' => '',
        'api_key' => '',
        'sign' => '',
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
        if (!$params['tpl']) {
            throw new \Exception('AliSms the template does not exist');
        }
        $url = $this->url($receive, $this->config['api_id'], $this->config['api_key'], $this->config['sign'], $params['tpl'], $params);
        try {
            $response = (new \GuzzleHttp\Client())->request('GET', $url);
            $reason = $response->getStatusCode();
            if ($reason <> 200) {
                throw new \Exception("AliSms Send failed!");
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $data = $e->getResponse()->getBody()->getContents();
            $data = json_decode($data, true);
            if (empty($data)) {
                throw new \Exception("AliSms Send failed!");
            }
            throw new \Exception("AliSms Error: " . $data['Message']);
        }
        return true;

    }

    private function url($phone, $AccessKeyId, $accessKeySecret, $SignName, $TemplateCode, $TemplateParam, $domain = 'dysmsapi.aliyuncs.com') {
        $apiParams["PhoneNumbers"] = $phone; //手机号
        $apiParams["SignName"] = $SignName; //签名
        $apiParams["TemplateCode"] = $TemplateCode; //短信模版id
        $apiParams["TemplateParam"] = json_encode($TemplateParam);  //模版内容
        $apiParams["AccessKeyId"] = $AccessKeyId; //key
        $apiParams["RegionId"] = "cn-hangzhou"; //固定参数
        $apiParams["Format"] = "json";  //返回数据类型,支持xml,json
        $apiParams["SignatureMethod"] = "HMAC-SHA1"; //固定参数
        $apiParams["SignatureVersion"] = "1.0";  //固定参数
        $apiParams["SignatureNonce"] = uniqid(); //用于请求的防重放攻击，每次请求唯一
        $apiParams["Timestamp"] = gmdate('Y-m-d\TH:i:s\Z'); //格式为：yyyy-MM-dd’T’HH:mm:ss’Z’；时区为：GMT
        $apiParams["Action"] = 'SendSms'; //api命名 固定值
        $apiParams["Version"] = '2017-05-25'; //api版本 固定值
        $apiParams["Signature"] = $this->computeSignature($apiParams, $accessKeySecret);  //最终生成的签名结果值
        $requestUrl = "http://" . $domain . "/?";
        foreach ($apiParams as $apiParamKey => $apiParamValue) {
            $requestUrl .= "$apiParamKey=" . urlencode($apiParamValue) . "&";
        }
        return $requestUrl;
    }

    public function computeSignature($parameters, $accessKeySecret) {
        ksort($parameters);
        $canonicalizedQueryString = '';
        foreach ($parameters as $key => $value) {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key) . '=' . $this->percentEncode($value);
        }
        $stringToSign = 'GET&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . "&", true));;
        return $signature;
    }

    public function percentEncode($str) {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }

}
