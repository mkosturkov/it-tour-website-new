<?php

declare(strict_types=1);

namespace ItTour;

/** An outgoing response: status, headers and the finished body. */
final class Response
{
    public function __construct(
        public int $status = 200,
        public string $body = '',
        public array $headers = ['Content-Type' => 'text/html; charset=utf-8'],
    ) {
    }

    public static function redirect(string $url, int $status = 301): self
    {
        return new self($status, '', ['Location' => $url]);
    }

    public static function text(string $body, int $status = 200, string $type = 'text/plain'): self
    {
        return new self($status, $body, ['Content-Type' => "$type; charset=utf-8"]);
    }

    public function send(bool $head = false): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        if (!$head) {
            echo $this->body;
        }
    }
}
