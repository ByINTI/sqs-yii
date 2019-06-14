Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

```
php composer.phar require --prefer-dist byinti/sqs-yii
```

Usage
------------
Update `main-local.php` 

    'bootstrap' => [
        'queue'
    ],
    'components' => [
            'queue' => [
                'class' => 'byinti\sqs\Queue',
                'url' => <URL>,
                'region' => <REGION>,
                'key' => <USERID>,
                'secret' => <SECRET>,
                'messageGroupId' => <MESSAGEGROUPID>
            ]
    ]

Get the pushed message id:    
```php
$id = Yii::$app->queue->push(new byinti\sqs\SqsPushEvent($message));```