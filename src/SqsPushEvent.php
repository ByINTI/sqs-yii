<?php

namespace byinti\queue;

use yii\base\Event;
/**
 * Class PushEvent
 *
 */
abstract class SqsPushEvent extends Event
{
    /**
     * @var string|null unique id of a job
     */
    public $id;
    /**
     * @var int
     */
    public $delay = 0;
    /**
     * @var int time to reserve in seconds of the job
     */
    public $ttr = 0;

    /**
     * @var string message to queue
     */
    public $message;
}