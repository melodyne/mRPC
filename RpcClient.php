<?php
/**
 * Created by PhpStorm.
 * User: Wanzhou Chen
 * Date: 2020/12/24
 * Time: 15:30
 */

class RpcClient
{
    private $urlInfo = array();

    /**
     * RpcClient constructor.
     */
    public function __construct($url)
    {
        $this->urlInfo = parse_url($url);
    }

    public static function instance($url)
    {
        return new RpcClient($url);
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        //创建一个客户端
        $client = stream_socket_client("tcp://{$this->urlInfo['host']}:{$this->urlInfo['port']}", $errno, $errstr);
        if (!$client) {
            exit("{$errno} : {$errstr} \n");
        }
        $data = [
            'class' => basename($this->urlInfo['path']),
            'method' => $name,
            'params' => $arguments
        ];
        //向服务端发送我们自定义的协议数据
        fwrite($client, json_encode($data));
        //读取服务端传来的数据
        $data = fread($client, 2048);
        //关闭客户端
        fclose($client);
        return $data;
    }
}

$cli = new RpcClient('http://127.0.0.1:8888/test');
echo $cli->tuzisir1() . "\n";
echo $cli->tuzisir2(array('name' => 'tuzisir', 'age' => 23));