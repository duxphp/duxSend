<?php
/**
 * 发送类驱动接口
 */
namespace dux\send;

Interface SendInterface {

    /**
     * 检测接收号码
     * @param $receive
     * @return mixed
     */
    public function check($receive);

    /**
     * 发送通知
     * @param $receive
     * @param string $title
     * @param string $content
     * @param array $params
     * @return mixed
     */
    public function send($receive, string $title, string $content, array $params);

}