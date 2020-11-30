<?php

namespace lgdz\lib\workerman;

trait SendToUser
{
    use BuildTarget;

    /**
     * @param mixed $uid 可以是字符串、数字、或者包含uid的数组。如果为数组，则是给数组内所有uid发送数据
     * @param array $body
     * @param \Closure $callback ($dataset)
     * @return mixed
     */
    public function sendToUser($uid, array $body, \Closure $callback = null)
    {
        return $this->common('sendToUid', $uid, $body, $callback);
    }

    /**
     * @param mixed $group 可以是字符串、数字、或者数组。如果为数组，则是给数组内所有group发送数据
     * @param array $body
     * @param \Closure $callback ($dataset)
     * @return mixed
     */
    public function sendToGroup($group, array $body, \Closure $callback = null)
    {
        return $this->common('sendToGroup', $group, $body, $callback);
    }

    private function common(string $method, $target, array $body, \Closure $callback = null)
    {
        $args = [
            $this->target($target),
            json_encode([
                'fromUser'  => 'system',
                'body'      => $body,
                'timestamp' => time()
            ])
        ];
        return $this->send($method, $args, $callback);
    }
}