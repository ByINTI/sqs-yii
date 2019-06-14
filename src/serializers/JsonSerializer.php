<?php

namespace byinti\queue\serializers;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Object;
use yii\helpers\Json;

class JsonSerializer extends Object
{
    /**
     * @var string
     */
    public $classKey = 'class';
    /**
     * @var int
     */
    public $options = 0;

    /**
     * @inheritdoc
     */
    public function serialize($message)
    {
        return Json::encode($this->toArray($message), $this->options);
    }

    /**
     * @param mixed $data
     * @return array|mixed
     * @throws InvalidConfigException
     */
    protected function toArray($data)
    {
        if (is_object($data)) {
            $result = [$this->classKey => get_class($data)];
            foreach (get_object_vars($data) as $property => $value) {
                if ($property === $this->classKey) {
                    throw new InvalidConfigException("Object cannot contain $this->classKey property.");
                }
                $result[$property] = $this->toArray($value);
            }

            return $result;
        }
        
        if (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                if ($key === $this->classKey) {
                    throw new InvalidConfigException("Array cannot contain $this->classKey key.");
                }
                $result[$key] = $this->toArray($value);
            }

            return $result;
        }
        
        return $data;
    }
}
