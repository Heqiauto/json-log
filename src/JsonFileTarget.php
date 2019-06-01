<?php
/**
 * @link https://heqiauto.com/
 * @copyright Copyright (c) 2019 Heqiauto, Inc.
 */

namespace heqiauto\jsonlog;

use Yii;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\log\FileTarget;
use yii\log\Logger;

/**
 * Class JsonFileTarget
 *
 * @package heqiauto\jsonlog
 * @author Panlatent <panlatent@gmail.com>
 */
class JsonFileTarget extends FileTarget
{
    // Properties
    // =========================================================================

    /**
     * @var array
     */
    public $fields = [];

    /**
     * @var string
     */
    public $applicationEnvelope = 'application';

    /**
     * @var string
     */
    public $timestampEnvelope = '@timestamp';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;
        $level = Logger::getLevelName($level);
        if (!is_string($text)) {
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $text = (string) $text;
            } else {
                $text = VarDumper::export($text);
            }
        }

        $prefix = $this->getMessagePrefix($message);

        $ret = [
            $this->timestampEnvelope => $timestamp,
            'prefix' => $prefix,
            'level' => $level,
            'category' => $category,
            'message' => $text,
            'traces' => isset($message[4]) ? $message[4] : [],
            $this->applicationEnvelope => [
                'id' => Yii::$app->id,
                'name' => Yii::$app->name,
            ]
        ];

        if (!empty($this->fields)) {
            $ret['fields'] = $this->fields;
        }

        return Json::encode($ret);
    }
}