<?php

namespace understeam\multimailer;

use yii\mail\BaseMailer;
use yii\mail\MailerInterface;
use yii\mail\MessageInterface;
use Yii;

/**
 * Mailer class
 * @author Anatoly Rugalev <anatoly.rugalev@gmail.ru>
 */
class Mailer extends BaseMailer
{

    /**
     * @var array|MailerInterface[]
     */
    public $mailers = [];

    /**
     * MultiSender does not send any messages by itself
     * @param MessageInterface $message
     * @return bool
     */
    protected function sendMessage($message)
    {
        return false;
    }

    public function send($message)
    {
        $this->clearCounter();
        while ($mailer = $this->getNextMailer()) {
            if ($result = $mailer->send($message)) {
                return $result;
            }
        }
        return false;
    }

    private $_currentMailer;

    private function clearCounter()
    {
        unset($this->_currentMailer);
    }

    /**
     * @return MailerInterface
     */
    private function getNextMailer()
    {
        if (!isset($this->_currentMailer)) {
            $this->_currentMailer = 0;
        } else {
            $this->_currentMailer++;
        }
        $keys = array_keys($this->mailers);
        if (!isset($keys[$this->_currentMailer])) {
            return null;
        }
        $mailerId = $keys[$this->_currentMailer];
        if (!is_object($this->mailers[$mailerId])) {
            $this->mailers[$mailerId] = Yii::createObject($this->mailers[$mailerId]);
        }
        return $this->mailers[$mailerId];
    }
}