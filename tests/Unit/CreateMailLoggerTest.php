<?php

use ConfettiCode\LaravelLog\CreateMailLogger;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Mockery as m;
use Monolog\Handler\SymfonyMailerHandler;
use Monolog\Level;
use Monolog\Logger;
use Symfony\Component\Mailer\Transport\TransportInterface;

test('it requires "from" from the config parameter', function () {
    $creator = new CreateMailLogger();

    $this->expectException(InvalidArgumentException::class);

    call_user_func($creator, ['to' => 'devops@laravel-log-mail.test']);
});

test('it requires "to" from the config parameter', function () {
    $creator = new CreateMailLogger();

    $this->expectException(InvalidArgumentException::class);

    call_user_func($creator, ['from' => 'system@laravel-log-mail.test']);
});

test('it can create a mail logger with default mailer, name, level, bubble', function () {
    App::shouldReceive('environment')->once()->andReturn('local');

    Config::shouldReceive('get')->once()->with('app.name')->andReturn('Laravel Log Mail');

    $transport = m::mock(TransportInterface::class);

    $mailer = m::mock(Mailer::class);
    $mailer->shouldReceive('getSymfonyTransport')->once()->andReturn($transport);

    Mail::shouldReceive('mailer')->once()->andReturn($mailer);

    $creator = new CreateMailLogger();

    $logger = call_user_func($creator, [
        'from' => 'system@laravel-log-mail.test',
        'to' => 'devops@laravel-log-mail.test',
    ]);

    $this->assertInstanceOf(Logger::class, $logger);
    $this->assertSame('local', $logger->getName());
    $this->assertCount(1, $logger->getHandlers());

    $handler = $logger->getHandlers()[0];

    $this->assertInstanceOf(SymfonyMailerHandler::class, $handler);
    $this->assertSame(Level::Error, $handler->getLevel());
    $this->assertSame(true, $handler->getBubble());
});

test('it can create a mail logger with custom mailer, name, level, bubble', function () {
    App::shouldReceive('environment')->never();

    Config::shouldReceive('get')
        ->once()
        ->with('app.name')
        ->andReturn('Laravel Log Mail');

    $transport = m::mock(TransportInterface::class);

    $mailer = m::mock(Mailer::class);
    $mailer->shouldReceive('getSymfonyTransport')
        ->once()
        ->andReturn($transport);

    Mail::shouldReceive('mailer')
        ->once()
        ->with('smtp')
        ->andReturn($mailer);

    $creator = new CreateMailLogger();

    $logger = call_user_func($creator, [
        'mailer' => 'smtp',
        'name' => 'production',
        'level' => Level::Debug,
        'bubble' => false,
        'from' => 'system@laravel-log-mail.test',
        'to' => 'devops@laravel-log-mail.test',
    ]);

    $this->assertInstanceOf(Logger::class, $logger);
    $this->assertSame('production', $logger->getName());
    $this->assertCount(1, $logger->getHandlers());

    $handler = $logger->getHandlers()[0];

    $this->assertInstanceOf(SymfonyMailerHandler::class, $handler);
    $this->assertSame(Level::Debug, $handler->getLevel());
    $this->assertSame(false, $handler->getBubble());
});
