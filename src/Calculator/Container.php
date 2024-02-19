<?php

namespace App\Calculator;

class Container
{
    private static array $services = [];

    /**
     * Retrieves a service from the container by its ID.
     *
     * @param string $id The ID of the service to retrieve.
     * @return mixed|null The retrieved service or null if the service does not exist.
     */
    public static function get(string $id)
    {
        if (self::has($id)) {
            $service = self::$services[$id];

            return $service();
        }
    }

    /**
     * Sets a service in the container.
     *
     * @param string $id The identifier of the service.
     * @param callable $callback The callback function that creates the service.
     * @return void
     */
    public static function set(string $id, callable $callback): void
    {
        self::$services[$id] = $callback;
    }

    /**
     * Checks if a service with the given ID exists in the container.
     *
     * @param string $id The ID of the service.
     * @return bool Returns true if the service exists, false otherwise.
     */
    public static function has(string $id): bool
    {
        return isset(self::$services[$id]);
    }
}
