<?php

namespace app\components;

class DbUtils
{
    public static function enableQueryStats() {
        $doctrineConnection = DB::getInstance();
        $stack = new \Doctrine\DBAL\Logging\DebugStack();
        $doctrineConnection->getConfiguration()->setSQLLogger($stack);
        return $stack;
    }

    public static function getQueryStats($stack) {
        return $stack->queries;
    }
}