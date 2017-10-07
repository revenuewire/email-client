<?php
namespace RW\Email;

use Aws\Sqs\SqsClient;

class Client
{
    /** @var $client SqsClient */
    public static $client;
    public static $url;
    public static $debug = false;

    /**
     * Config
     *
     * @param $config
     */
    public static function init($config)
    {
        $defaultConfig = [
            "region" => "us-west-1",
            "version" => "2012-11-05"
        ];
        $config = array_merge($defaultConfig, $config);

        self::$url = $config['url'];
        self::$client = new SqsClient([
            'region' => $config['region'],
            'version' => $config['version'],
        ]);
    }

    /**
     * Send SQS Message
     *
     * @param $data
     * @param string $type
     * @return \Aws\Result
     * @internal param $message
     */
    public static function send($data, $type = "DIRECT")
    {
        $message = [
            "Type" => "EmailClient",
            "Subject" => $type,
            "Message" => base64_encode(json_encode($data))
        ];

        if (self::$debug === true) {
            return $message;
        }

        return self::$client->sendMessage([
            'MessageBody' => json_encode($message),
            'QueueUrl' => self::$url
        ]);
    }
}