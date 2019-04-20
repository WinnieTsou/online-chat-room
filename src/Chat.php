<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $names;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->names = [];

        $log = "Web Socket Starts\n";
        $this->log($log);
    }

    public function onOpen(ConnectionInterface $conn) {
        $log = $conn->resourceId . ' is connected\n';
        $this->log($log);
    }

    public function onMessage(ConnectionInterface $from, $msg) {

        $msg = json_decode($msg);
        $name = $msg->name;

        if (strcmp($msg->message, 'Name Setting') == 0) {
            if (array_search($name, $this->names)) {
                $msg = Array('sender' => $name,
                             'setting' => 'Name Exists',
                             'message' => '',
                             'systemInfo' => '',
                             'visitors' => $this->names);
                $msg = json_encode($msg);

                $from->send($msg);
            } else {
                $this->clients->attach($from);
                $this->names[$from->resourceId] = $name;

                $msg = 'Name OK';
                $msg = Array('sender' => $name,
                             'setting' => $msg,
                             'message' => '',
                             'systemInfo' => '',
                             'visitors' => $this->names);
                $msg = json_encode($msg);
                $from->send($msg);

                $msg = 'New visitor! Say Hi to ' . $name . '!';
                $msg = Array('sender' => $name,
                             'setting' => '',
                             'message' => '',
                             'systemInfo' => $msg,
                             'visitors' => $this->names);
                $msg = json_encode($msg);

                foreach ($this->clients as $client) {
                    $client->send($msg);
                }
            }
        } else {
            $msg = $msg->message;
            $msg = Array('sender' => $name, 
                         'setting' => '',
                         'message' => $msg, 
                         'systemInfo' => '',
                         'visitors' => $this->names);
            $msg = json_encode($msg);


            foreach ($this->clients as $client) {
                $client->send($msg);
            }

            $log = "({$from->resourceId}, {$name}) send: {$msg} to others\n";
            $this->log($log);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        if (array_key_exists($conn->resourceId, $this->names)) {
            $name = $this->names[$conn->resourceId];
            unset($this->names[$conn->resourceId]);
            $msg = $name . " left.";
            $msg = Array('sender' => $name, 
                         'setting' => '',
                         'message' => '', 
                         'systemInfo' => $msg,
                         'visitors' => $this->names);
            $msg = json_encode($msg);

            foreach ($this->clients as $client) {
                $client->send($msg);
            }

            $log = "({$conn->resourceId}, {$name}) is disconnected\n";
            $this->log($log);
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $log = "An error has occurred: {$e->getMessage()}\n";
        $this->log($log);

        $conn->close();
    }

    protected function getName(ConnectionInterface $conn) {
        $nameString = $conn->httpRequest->getHeaders()['Cookie'][0];
        $i = strpos($nameString, '=');
        $nameString = substr($nameString, ($i + 1));
        return $nameString;
    }

    protected function log($msg) {
        $date = \DateTime::createFromFormat('U.u', microtime(TRUE));
        $log = $date->format('Y-m-d H:i:s:v ');
        $log = $log . $msg;
        echo $log;
    }
}
