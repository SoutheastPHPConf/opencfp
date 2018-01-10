<?php

declare(strict_types=1);

/**
 * Copyright (c) 2013-2018 OpenCFP
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/opencfp/opencfp
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use OpenCFP\Environment;
use OpenCFP\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

$basePath    = \realpath(\dirname(__DIR__));
$environment = Environment::fromServer($_SERVER);

if (!$environment->isProduction()) {
    Debug::enable();
}

if ($environment->isProduction()) {
    $monolog = new Logger('OpenCfp Log');
    $syslog = new \Monolog\Handler\SyslogHandler('papertrail');
    $formatter = new \Monolog\Formatter\LineFormatter('%channel%.%level_name%: %message% %extra%');
    $syslog->setFormatter($formatter);

    $monolog->pushHandler($syslog);
}

$kernel   = new Kernel((string) $environment, !$environment->isProduction());
$request  = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
