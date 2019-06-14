<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace byinti\queue;

use byinti\queue\serializers\JsonSerializer;
use byinyi\queue\SqsPushEvent;
use Yii;
use yii\base\Component;
use yii\base\InvalidParamException;
use yii\di\Instance;
use yii\helpers\VarDumper;

/**
 * Base Queue
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
abstract class Queue extends Component
{
    /**
     * @var Serializer|array
     */
    public $serializer = JsonSerializer::class;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->serializer = $this->serializer;
    }

    /**
     * Pushes job into queue
     *
     * @param SqsPushEvent $message
     * @return string|null id of a job message
     */
    public function push(SqsPushEvent $message)
    {
        $serialized = $this->serializer->serialize($message);

        return $this->pushMessage($serialized, $message->ttr, $message->delay);
    }

    /**
     * @param string $message
     * @param int $ttr time to reserve in seconds
     * @param int $delay
     * @return string|null id of a job message
     */
    abstract protected function pushMessage($message, $ttr, $delay);
}