<?php


namespace console\controllers\log;

use DateTime;
use yii\base\Action;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Trait ConsoleStdoutLogTrait
 * @package console\controllers\log
 * @method void stderr(string $message, int $color)
 * @method stdout(string $message, int $color)
 */
trait ConsoleStdoutLogTrait
{
    /**
     * Method error
     * @param string $message
     */
    public function error(string $message): void
    {
        $this->getTraitOwner()->stderr('ERROR[' . $this->getTime() . '] => ' . $message . PHP_EOL, Console::FG_RED);
    }

    /**
     * @return Controller
     */
    private function getTraitOwner(): Controller
    {
        if ($this instanceof Controller) {
            return $this;
        }
        if ($this instanceof Action) {
            return $this->controller;
        }
    }

    /**
     * Method getTime
     * @return string
     */
    public function getTime(): string
    {
        $date = DateTime::createFromFormat('U.u', microtime(true));
        if ($date) {
            return $date->format('Y-m-d G:i:s.u');
        }
        return $this->getTime();
    }

    /**
     * Method info
     * @param string $message
     */
    public function info(string $message): void
    {
        $this->getTraitOwner()->stdout('INFO[' . $this->getTime() . '] => ' . $message . PHP_EOL, Console::FG_GREEN);
    }
}