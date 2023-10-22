<?php

namespace ChatBooster\Requests;

use ChatBooster\Setting;

class Requests
{
    protected $code;
    protected $status;
    protected $message;
    protected $data;
    protected $requests;
    protected $method;

    public function __construct()
    {
        $this->setRequest();    
        $this->setMethod();
    }

    public function handle()
    {
        header('Content-Type: application/json');
    }

    protected function setCode(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    protected function setStatus(bool $status): self
    {
        $this->status = $status;
        return $this;
    }

    protected function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    protected function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    protected function setRequest(): void
    {
        $this->requests = json_decode(json_encode(array_merge($_POST, $_GET)));
    }

    protected function methodNotExists(): void
    {
        $this->setStatus(false)
            ->setCode(404)
            ->setMessage('Specified method does not exist!');
    }

    protected function methodNotSpecified(): void
    {
        $this->setStatus(false)
            ->setCode(404)
            ->setMessage('No method specified!');
    }

    protected function notMigrated(): void
    {
        $this->setStatus(false)
            ->setCode(404)
            ->setMessage('The migrations have not been run yet!');
    }

    protected function fileSettingsNotDefined(): void
    {
        $this->setStatus(false)
            ->setCode(404)
            ->setMessage('Settings file not defined!');
    }

    protected function verifyMethod(): bool
    {
        if(isset($this->method)):
            return true;
        endif;

        return false;
    }

    private function setMethod(): void
    {
        $this->method = isset($this->requests->method) ? $this->requests->method : null;
    }
}