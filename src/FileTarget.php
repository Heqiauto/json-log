<?php
/**
 * @link https://heqiauto.com/
 * @copyright Copyright (c) 2019 Heqiauto, Inc.
 */

namespace heqiauto\jsonlog;

use Yii;
use yii\base\Application;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\log\Logger;
use yii\web\Request;
use yii\web\Response;

/**
 * Class JsonFileTarget
 *
 * @package heqiauto\jsonlog
 * @author Panlatent <panlatent@gmail.com>
 */
class FileTarget extends \yii\log\FileTarget
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
    public $requestIdHeader = 'X-Request-Id';

    /**
     * @var string
     */
    public $applicationEnvelope = 'application';

    /**
     * @var string
     */
    public $timestampEnvelope = '@timestamp';

    /**
     * @var string
     */
    public $requestEnvelope = 'request';

    /**
     * @var Application
     */
    public $app;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Response
     */
    public $response;

    /**
     * @var string|null
     */
    protected $requestId;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->app === null) {
            $this->app = Yii::$app;
        }

        if ($this->request === null) {
            $this->request = Yii::$app->getRequest();
        }

        if ($this->response === null) {
            $this->response = Yii::$app->getResponse();
        }
    }

    /**
     * @inheritdoc
     */
    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;

        if (!is_string($text)) {
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $exception = $text;
                $text = (string)$text;
            } else {
                $text = VarDumper::export($text);
            }
        }

        $ret = [
            $this->timestampEnvelope => $timestamp,
            'level' => Logger::getLevelName($level),
            'category' => $category,
            'message' => $text,
            'traces' => isset($message[4]) ? $message[4] : [],
            $this->applicationEnvelope => $this->getApplication(),
            $this->requestEnvelope => $this->getRequest(),
        ];

        if (isset($exception)) {
            $ret['errorCode'] = $exception->getCode();
        }

        if (!empty($this->fields)) {
            $ret['fields'] = $this->fields;
        }

        return Json::encode($ret);
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return array
     */
    protected function getApplication()
    {
        return [
            'id' => $this->app->id,
            'name' => $this->app->name,
            'version' => $this->app->getVersion(),
            'frameworkVersion' => Yii::getVersion(),
        ];
    }

    /**
     * @return array
     */
    protected function getRequest()
    {
        if ($this->request->getHeaders()->has($this->requestIdHeader)) {
            $requestId = $this->request->getHeaders()->get($this->requestIdHeader);
        } elseif ($this->requestId !== null) {
            $requestId = $this->requestId;
        } else {
            $requestId = $this->requestId = uniqid();
        }

        /* @var $user \yii\web\User */
        $user = $this->app->has('user', true) ? $this->app->get('user') : null;
        $userID = $user && ($identity = $user->getIdentity(false)) ?  $identity->getId() : null;

        /* @var $session \yii\web\Session */
        $session = $this->app->has('session', true) ? $this->app->get('session') : null;
        $sessionID = $session && $session->getIsActive() ? $session->getId() : null;

        return [
            'id' => $requestId,
            'ip' => $this->request->getUserIP(),
            'userID' => $userID,
            'sessionID' => $sessionID,
        ];
    }
}