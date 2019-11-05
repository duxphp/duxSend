
<p align="center">
  <a href="https://github.com/duxphp/duxfiles">
   <img alt="DuxShop" src="https://github.com/duxphp/duxphp/raw/master/docs/logo.png?raw=true">
  </a>
</p>

<p align="center">
  为快速开发而生
</p>

<p align="center">
  <a href="https://github.com/duxphp/duxfiles">
    <img alt="maven" src="https://img.shields.io/badge/DuxSend-v1-blue.svg">
  </a>

  <a href="http://zlib.net/zlib_license.html">
    <img alt="code style" src="https://img.shields.io/badge/zlib-licenses-brightgreen.svg">
  </a>
</p>

# 简介

DuxSend 是一款支持多种平台接口的短信、邮件等推送类

# 支持平台

- SMTP邮件
- 阿里云邮件
- 阿里云短信
- 小米推送
- 极光推送
- 云片短信

# 环境支持

- 语言版本：PHP 7.1+

# 讨论

QQ群：131331864

> 本系统非盈利产品，为防止垃圾广告和水群已开启收费入群，收费入群并不代表我们可以无条件回答您的问题，入群之前请仔细查看文档，常见安装等问题通过搜索引擎解决，切勿做伸手党

# bug反馈

[issues反馈](https://github.com/duxphp/duxFiles/issues)
    
# 版权说明

本项目使用MIT开源协议，您可以在协议允许范围内进行进行商业或非商业项目使用

# 开发团队

湖南聚匠信息科技有限公司


# 安装说明

   ```
   composer require duxphp/duxsend
   ```
   
# 使用方法

实例化操作类

   ```
    $driver = \dux\send\Email::class;  //驱动类名
    $config = []; //驱动配置
    $send = new \dux\send\Send($driver, $config);
   ```
   
配置信息

   ```
    // 阿里邮件
    $driver = \dux\send\AliMail::class;
    $config = [
        'api_id' => '',   //接口账号
        'apy_key' => '',  //接口秘钥
        'mail' => '',     //发件邮箱
    ];
   ```

   ```
    // 阿里云短信
    $driver = \dux\send\AliSms::class;
    $config = [
        'api_id' => '',   //AccessKey ID
        'apy_key' => '',  //Access Key Secret
        'sign' => '',     //短信签名
    ];
   ```

   ```
    // SMTP邮件
    $driver = \dux\send\Email::class;
    $config = [
        'host' => '',       //SMTP地址
        'username' => '',   //邮箱账号
        'password' => '',   //邮箱密码
        'port' => '',       //SMTP端口
        'mail' => '',       //发件邮箱
    ];
   ```

   ```
    // 极光推送
    $driver = \dux\send\Jpush::class;
    $config = [
        'app_key' => '',          //接口密钥
        'master_kecret' => '',
    ];
   ```
    
   ```
    // 小米推送
    $driver = \dux\send\Xiaomi::class;
    $config = [
        'ios_key' => '',         //IOS密钥
        'android_key' => '',     //安卓密钥
        'android_name' => '',    //安卓包名
    ];
   ```

   ```
    // 云片短信
    $driver = \dux\send\Yunpian::class;
    $config = [
        'api_key' => '',         //接口密钥
    ];
   ```
   
检测接收账号
    
   ```
    /**
     * @param $receive  //接收账号、号码或推送别名
     * @return bool
     * @throws \Exception
     */
    $send->check($receive);
   ```
    
发送消息
    
   ```
    /**
     * @param $receive          //接收账号、号码或推送别名
     * @param string $title     //发信标题，邮件有效，短信、推送等无效
     * @param string $content   //发信内容，邮件可为 Html,其他为字符串
     * @param array $params     //附件参数，模板短信传递 "tpl" 键名为模板 ID，其他参数为模板值
     * @return mixed
     * @throws \Exception
     */
    $send->send($receive, string $title, string $content, array $params = []);
   ```
    
异常捕获

   ```
    try {
        ...
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
   ```