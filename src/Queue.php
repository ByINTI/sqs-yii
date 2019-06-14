<?php
namespace byinti\sqs;

use Aws\Credentials\CredentialProvider;
use Aws\Sqs\SqsClient;
use yii\base\NotSupportedException;

/**
 * SQS Queue.
 */
class Queue
{
    /**
     * The SQS url.
     * @var string
     */
    public $url;
    /**
     * aws access key.
     * @var string|null
     */
    public $key;
    /**
     * aws secret.
     * @var string|null
     */
    public $secret;
    /**
     * region where queue is hosted.
     * @var string
     */
    public $region = '';
    /**
     * API version.
     * @var string
     */
    public $version = 'latest';
    /**
     * Message Group ID for FIFO queues.
     * @var string
     * @since 2.2.1
     */
    public $messageGroupId = 'default';

    /**
     * Json serializer by default.
     * @inheritdoc
     */
    public $serializer = JsonSerializer::class;
    /**
     * @var SqsClient
     */
    private $_client;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @param SqsPushEvent $event
     * @return string|null
     */
    public function push(SqsPushEvent $event)
    {
        $message = $this->serializer->serialize($event->message);

        return $this->pushMessage($message, $event->ttr, $event->delay);
    }
    /**
     * @inheritdoc
     */
    protected function pushMessage($message, $ttr, $delay)
    {
        $request = [
            'QueueUrl' => $this->url,
            'MessageBody' => $message,
            'DelaySeconds' => $delay,
            'MessageAttributes' => [
                'TTR' => [
                    'DataType' => 'Number',
                    'StringValue' => $ttr,
                ],
            ],
        ];
        if (substr($this->url, -5) === '.fifo') {
            $request['MessageGroupId'] = $this->messageGroupId;
            $request['MessageDeduplicationId'] = hash('sha256', $message);
        }
        $response = $this->getClient()->sendMessage($request);
        return $response['MessageId'];
    }

    /**
     * @return \Aws\Sqs\SqsClient
     */
    protected function getClient()
    {
        if ($this->_client) {
            return $this->_client;
        }

        if ($this->key !== null && $this->secret !== null) {
            $credentials = [
                'key' => $this->key,
                'secret' => $this->secret,
            ];
        } else {
            // use default provider if no key and secret passed
            //see - http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/credentials.html#credential-profiles
            $credentials = CredentialProvider::defaultProvider();
        }

        $this->_client = new SqsClient([
            'credentials' => $credentials,
            'region' => $this->region,
            'version' => $this->version,
        ]);
        return $this->_client;
    }
}