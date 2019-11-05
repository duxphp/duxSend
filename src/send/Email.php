<?php

namespace dux\send;

/**
 * 服务器邮件
 */
class Email implements \dux\send\SendInterface {

    protected $config = [
        'host' => '',
        'username' => '',
        'password' => '',
        'port' => '',
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
        if (empty($title) || empty($content)) {
            throw new \Exception("Please fill in the email title and content!");
        }
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->CharSet = "UTF-8";
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = "ssl";
            $mail->Port = $this->config['port'];
            $mail->setFrom($this->config['mail']);
            $mail->addAddress($receive);
            $mail->addReplyTo($this->config['mail']);
            $mail->isHTML(true);
            $mail->Subject = $title;
            $mail->Body = $content;
            $mail->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            throw new \Exception("Mail Error: {$mail->ErrorInfo}");
        }
        return true;
    }

}
