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

        $log = "web socket starts\n";
        $this->log($log);
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        $name = $this->getName($conn);
        $this->names[$conn->resourceId] = $name;


        $msg = "New visitor! Say Hi to " . $name . "!";
        $msg = Array('systemInfo' => $msg, 'sender' => $name, 'message' => '', 'visitors' => $this->names);
        $msg = json_encode($msg);

        foreach ($this->clients as $client) {
            $client->send($msg);
        }        

        $log = "({$conn->resourceId}, {$name}) is connected\n";
        $this->log($log);
    }

    public function onMessage(ConnectionInterface $from, $msg) {

        $name = $this->names[$from->resourceId];
        $msg = Array('systemInfo' => '', 'sender' => $name, 'message' => $msg, 'visitors' => $this->names);
        $msg = json_encode($msg);


        foreach ($this->clients as $client) {
            $client->send($msg);
        }

        $log = "({$from->resourceId}, {$name}) send: {$msg} to others\n";
        $this->log($log);
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        $name = $this->names[$conn->resourceId];
        unset($this->names[$conn->resourceId]);
        $msg = $name . " left.";
        $msg = Array('systemInfo' => $msg, 'sender' => $name, 'message' => '', 'visitors' => $this->names);
        $msg = json_encode($msg);

        foreach ($this->clients as $client) {
            $client->send($msg);
        }

        $log = "({$conn->resourceId}, {$name}) is disconnected\n";
        $this->log($log);
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
