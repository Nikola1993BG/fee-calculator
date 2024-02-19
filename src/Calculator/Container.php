<?php

namespace App\Calculator;

use App\Calculator\Interfaces\DataProcessorInterface;
use App\Calculator\Interfaces\ExchangeRatesProviderInterface;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $services = [];
    public function get(string $id)
    {
        if ($this->has($id)) {
            $service = $this->services[$id];

            return $service($this);
        }
    }

    public function set(string $id, callable $callback): void
    {
        $this->services[$id] = $callback;
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}
