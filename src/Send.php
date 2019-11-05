<?php

namespace dux;

/**
 * 发送类
 */
class Send {

    protected $config = [];
    protected $driver = null;
    protected $object = null;

    /**
     * 实例化类
     * @param string $driver
     * @param array $config
     * @throws \Exception
     */
    public function __construct(string $driver, array $config = []) {
        $this->driver = $driver;
        if (!class_exists($this->driver)) {
            throw new \Exception('The send driver class does not exist', 500);
        }
        $this->config = $config;
        if (empty($this->config)) {
            throw new \Exception($this->driver . ' send config error', 500);
        }
    }

    /**
     * 检测接收号码
     * @param $receive
     * @return bool
     * @throws \Exception
     */
    public function check($receive) {
        return $this->getObj()->check($receive);
    }

    /**
     * 发送信息
     * @param $receive
     * @param string $title
     * @param string $content
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function send($receive, string $title, string $content, array $params = []) {
        if(empty($receive)) {
            throw new \Exception($this->driver . ' accept the account does not exist', 500);
        }
        return $this->getObj()->send($receive, $title, $content, $params);
    }

    /**
     * 驱动对象
     * @return send\SendInterface
     * @throws \Exception
     */
    public function getObj() {
        if ($this->object) {
            return $this->object;
        }
        $this->object = new $this->driver($this->config);
        if (!$this->object instanceof \dux\send\SendInterface) {
            throw new \Exception('The send class must interface class inheritance', 500);
        }
        return $this->object;
    }

}