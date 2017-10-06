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
     * Direct Send Email
     *
     * @param $templateId
     * @param $from
     * @param $to
     * @param $data
     * @param string $language
     * @param array $replyTo
     * @param array $cc
     * @param array $bcc
     *
     * @return \Aws\Result
     * @throws \Exception
     */
    public static function directSend($templateId, $from, $to, $data, $language = "en", $replyTo = [], $cc = [], $bcc = [])
    {
        if (empty($templateId)) {
            throw new \Exception("Template Id is required.");
        }

        if (empty($language)) {
            throw new \Exception("Language Id is required.");
        }

        if (empty($from['emailAddress']) || !filter_var($from['emailAddress'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("The from email address is missing or invalid.");
        }

        foreach ($to as $recipient) {
            if (empty($recipient['emailAddress']) || !filter_var($recipient['emailAddress'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("The to email address is missing or invalid.");
            }
        }

        foreach (array($replyTo, $cc, $bcc) as $mailer) {
            if (!empty($mailer)) {
                foreach ($mailer as $mailee) {
                    if (empty($mailee['emailAddress']) || !filter_var($mailee['emailAddress'], FILTER_VALIDATE_EMAIL)) {
                        throw new \Exception("The email address is missing or invalid.");
                    }
                }
            }
        }

        $message = [
            "method" => "DIRECT",
            "templateId" => $templateId,
            "lang" => $language,
            "from" => $from,
            "to" => $to,
            "data" => $data
        ];

        return self::send($message);
    }


    /**
     * Send email by type
     *
     * @param $emailType
     * @param $from
     * @param $to
     * @param $data
     * @param string $language
     * @return \Aws\Result
     * @throws \Exception
     */
    public static function typeSend($emailType, $from, $to, $data, $language = "en")
    {
        if (!empty($to)) {
            foreach ($to as $recipient) {
                if (empty($recipient['emailAddress']) || !filter_var($recipient['emailAddress'], FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("The to email address is missing or invalid.");
                }
            }
        }

        if (!empty($from)) {
            if (empty($from['emailAddress']) || !filter_var($from['emailAddress'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("The from email address is missing or invalid.");
            }
        }

        $message = [
            "method" => "TYPE",
            "type" => $emailType,
            "lang" => $language,
            "to" => $to,
            "from" => $from,
            "data" => $data
        ];

        return self::send($message);
    }

    /**
     * Send SQS Message
     *
     * @param $message
     * @return \Aws\Result
     */
    public static function send($message)
    {
        if (self::$debug === true) {
            return $message;
        }

        return self::$client->sendMessage([
            'MessageBody' => json_encode($message),
            'QueueUrl' => self::$url
        ]);
    }
}