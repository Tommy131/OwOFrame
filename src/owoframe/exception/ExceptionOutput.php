<?php

/*********************************************************************
     _____   _          __  _____   _____   _       _____   _____
    /  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___|
    | | | | | |  __   / /  | | | | | |_| | | |     | | | | | |
    | | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _
    | |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| |
    \_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/

    * Copyright (c) 2015-2021 OwOBlog-DGMT.
    * Developer: HanskiJay(Tommy131)
    * Telegram:  https://t.me/HanskiJay
    * E-Mail:    support@owoblog.com
    * GitHub:    https://github.com/Tommy131

**********************************************************************/

declare(strict_types=1);
namespace owoframe\exception;

use Throwable;

use owoframe\System;
use owoframe\application\View;
use owoframe\http\Response;
use owoframe\utils\Logger;

class ExceptionOutput
{

    public static function debugTrace2String() : string
    {
        $template = "#%d %s(%d): %s%s%s(%s)\n";
        $trace    = debug_backtrace();
        array_shift($trace); // 移除调用本函数的栈追踪;
        $output = '';
        unset($trace[0], $temp);
        $trace = array_values($trace);

        foreach($trace as $k => $d) {
            $output .= sprintf($template, $k, $d['file'] ?? '', $d['line'] ?? '', $d['class'] ?? '', $d['type'] ?? '', $d['function'] ?? '', @implode(', ', $d['args']));
        }
        return $output . "{main}\n";
    }

    /**
     * 错误处理方法
     *
     * @return void
     */
    public static function ErrorHandler(int $errorLevel, string $errorMessage, string $errorFile, int $errorLine) : void
    {
        if(error_reporting() === 0) return;
        $errorConversion =
        [
            E_ERROR             => 'E_ERROR',
            E_WARNING           => 'E_WARNING',
            E_PARSE             => 'E_PARSE',
            E_NOTICE            => 'E_NOTICE',
            E_CORE_ERROR        => 'E_CORE_ERROR',
            E_CORE_WARNING      => 'E_CORE_WARNING',
            E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
            E_USER_ERROR        => 'E_USER_ERROR',
            E_USER_WARNING      => 'E_USER_WARNING',
            E_USER_NOTICE       => 'E_USER_NOTICE',
            E_STRICT            => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED        => 'E_DEPRECATED',
            E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
        ];
        $errorLevel = $errorConversion[$errorLevel] ?? $errorLevel;
        if(($position = strpos($errorMessage, "\n")) !== false) {
            $errorMessage = substr($errorMessage, 0, $position);
        }

        $toString = "{$errorLevel} happened: {$errorMessage} in {$errorFile} at line {$errorLine}";
        $trace    = self::debugTrace2String();
        self::execute([
            'PHP',
            $errorLevel,
            $errorMessage,
            $errorFile,
            $errorLine,
            $trace,
            $toString . "\nStack trace:\n" . $trace
        ]);
    }

    /**
     * 异常处理方法
     *
     * @return void
     */
    public static function ExceptionHandler(Throwable $exception) : void
    {
        if($type = $exception instanceof OwOFrameException) {
            $fileName = $exception->getRealFile();
            $realName = $exception->getRealLine();
        } else {
            $fileName = $exception->getFile();
            $realName = $exception->getLine();
        }

        self::execute([
            $type ? 'OwO' : 'PHP',
            System::getShortClassName($exception),
            $exception->getMessage(),
            $fileName,
            $realName,
            $exception->getTraceAsString(),
            $exception->__toString()
        ]);
    }


    private static function execute(array $args) : void
    {
        if(System::isRunningWithCGI()) {
            $view = self::getTemplate();
            $view->assign([
                'type'    => $args[0] ?? 'PHP',
                'subtype' => $args[1] ?? 'None',
                'message' => $args[2] ?? 'None',
                'file'    => $args[3] ?? 'None',
                'line'    => $args[4] ?? 'None',
                'trace'   => $args[5] ?? 'None',
                'runTime' => System::getRunTime(),
            ]);
            $output   = $view->render();
            $response = new Response(function() use ($output) {
                return $output;
            });
            $response->setResponseCode(502);
            $response->sendResponse();
        }
        self::log($args[6]);
        exit(1);
    }


    /**
     * 返回OwOView对象
     *
     * @return View
     */
    public static function getTemplate() : View
    {
        $view = new View('ExceptionOutputTemplate', FRAMEWORK_PATH . 'template');
        return $view->assign('debugMode', System::isDebugMode() ? '<span id="debugMode">DebugMode</span>' : '');
    }

    /**
     * 日志写入
     *
     * @param  string $msg
     * @return void
     */
    private static function log(string $msg) : void
    {
        $logger   = new Logger;
        $selected = System::isRunningWithCGI() ? 'run' : 'cli';
        $logger->fileName  = "owoblog_{$selected}_error.log";
        $logger->logPrefix = 'OwOBlogErrorHandler';
        $logger->emergency(trim(str2UTF8($msg)));
    }
}
?>