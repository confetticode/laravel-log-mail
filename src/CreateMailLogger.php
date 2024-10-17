<?php

namespace ConfettiCode\LaravelLog;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;
use Monolog\Handler\SymfonyMailerHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class CreateMailLogger
{
    /**
     * Create a monolog Logger with a mail handler.
     *
     * @throws InvalidArgumentException
     */
    public function __invoke(array $config): LoggerInterface
    {
        if (!isset($config['from'])) {
            throw new InvalidArgumentException('Missing "from" parameter.');
        }

        if (!isset($config['to'])) {
            throw new InvalidArgumentException('Missing "to" parameter.');
        }

        $transport = $this->createSymfonyTransport($config);
        $email = $this->createSymfonyEmail($config);
        $level = $config['level'] ?? Level::Error;
        $bubble = $config['bubble'] ?? true;

        $handler = new SymfonyMailerHandler($transport, $email, $level, $bubble);

        if (isset($config['processors']) && is_array($config['processors'])) {
            foreach ($config['processors'] as $processor) {
                $handler->pushProcessor(
                    App::make($processor)
                );
            }
        }

        $logger = new Logger($config['name'] ?? App::environment());

        $logger->pushHandler($handler);

        return $logger;
    }

    protected function createSymfonyTransport(array $config): TransportInterface
    {
        $mailer = Mail::mailer($config['mailer'] ?? null);

        return $mailer->getSymfonyTransport();
    }

    protected function createSymfonyEmail(array $config): Email
    {
        $email = new Email;

        $email->subject(Config::get('app.name') . ' | %message%');

        if (is_string($config['from'])) {
            $email->sender($config['from']);
        } elseif (is_array($config['from'])) {
            $email->sender(
                new Address($config['from']['address'], $config['from']['name'])
            );
        } else {
            throw new InvalidArgumentException('"from" parameter must be a string or an array');
        }

        if (is_string($config['to'])) {
            $email->to($config['to']);
        } elseif (is_array($config['to'])) {
            $email->to(
                new Address($config['to']['address'], $config['to']['name'])
            );
        } else {
            throw new InvalidArgumentException('"to" parameter must be a string or an array');
        }

        return $email;
    }
}
