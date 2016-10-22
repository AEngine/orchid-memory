<?php

use AEngine\Memory\Exception\CacheException;
use AEngine\Memory\Interfaces\DriverInterface;

/**
 * Mem is user-friendly wrap around storage
 */
class Mem
{
    /**
     * Prevents reading from external storage
     *
     * @var bool
     */
    public static $disabled = false;

    /**
     * Prefix keys
     *
     * @var string
     */
    public static $prefix = '';

    /**
     * List of keys that are stored in the buffer
     *
     * @var array
     */
    public static $cachedKeys = [];

    /**
     * Internal storage
     *
     * @var array
     */
    protected static $buffer = [];

    /**
     * Array of connections
     *
     * @var array
     */
    protected static $connection = [
        'master' => [],
        'slave'  => [],
    ];

    /**
     * Setup the Memory driver
     *
     * @param array $configs
     *
     * @throws RuntimeException
     * @throws CacheException
     */
    public static function setup(array $configs = [])
    {
        if ($configs) {
            $default = [
                'driver'  => 'memcache',
                'host'    => '',
                'port'    => '',
                'timeout' => 10,
                'role'    => 'master',
            ];

            foreach ($configs as $index => $config) {
                $config = array_merge($default, $config);

                switch (strtolower($config['driver'])) {
                    case 'memcache':
                        static::$connection[$config['role'] == 'master' ? 'master' : 'slave'][] = function () use ($config) {
                            return new AEngine\Memory\Driver\Memcache(
                                $config['host'],
                                $config['port'],
                                $config['timeout']
                            );
                        };
                        break;
                }
            }

            return;
        }

        throw new RuntimeException('There are no settings to connect to the memory');
    }

    /**
     * Opens and returns a connection to external storage
     *
     * @param bool $useMaster
     *
     * @return DriverInterface
     * @throws CacheException
     */
    public static function getInstance($useMaster = false)
    {
        $pool = [];
        $role = $useMaster ? 'master' : 'slave';

        switch (true) {
            case !empty(static::$connection[$role]):
                $pool = static::$connection[$role];
                break;
            case !empty(static::$connection['master']):
                $pool = static::$connection['master'];
                $role = 'master';
                break;
            case !empty(static::$connection['slave']):
                $pool = static::$connection['slave'];
                $role = 'slave';
                break;
        }

        if ($pool) {
            if (is_array($pool)) {
                return static::$connection[$role] = $pool[array_rand($pool)]();
            } else {
                return $pool;
            }
        }

        throw new CacheException('Unable to establish connection');
    }

    /**
     * Generate key
     *
     * @param string $key
     *
     * @return string
     */
    protected static function getKey($key)
    {
        return static::$prefix ? static::$prefix . ':' . $key : $key;
    }

    /**
     * Return value from external storage
     *
     * @param string  $key
     * @param Closure $payload
     *
     * @return mixed
     */
    public static function get($key, Closure $payload = null)
    {
        if (!static::$disabled) {
            if (isset(static::$buffer[$key])) {
                $value = static::$buffer[$key];
            } else {
                $value = static::getInstance(false)->get(static::getKey($key));

                foreach (static::$cachedKeys as $k) {
                    if (strpos($key, $k) === 0) {
                        static::$buffer[$key] = $value;
                    }
                }
            }

            return $value !== false ? $value : ($payload ? call_user_func($payload) : false);
        }

        return $payload;
    }

    /**
     * Writes a value to external storage
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $expire
     * @param string $tag
     *
     * @return bool
     */
    public static function set($key, $value, $expire = 0, $tag = null)
    {
        if (isset(static::$cachedKeys[$key])) {
            unset(static::$buffer[$key]);
        }

        return static::getInstance(true)->set(static::getKey($key), $value, $expire, $tag);
    }

    /**
     * Removes specified key from external storage
     *
     * @param string $key
     *
     * @return bool
     */
    public static function delete($key)
    {
        if (isset(static::$cachedKeys[$key])) {
            unset(static::$buffer[$key]);
        }

        return static::getInstance(true)->delete(static::getKey($key));
    }

    /**
     * Remove all keys from external storage
     *
     * @return bool
     */
    public static function flush()
    {
        static::$buffer = [];

        return static::getInstance(true)->flush();
    }

    /**
     * Return values for a given tag
     *
     * @param string $tag
     *
     * @return array
     */
    public static function getByTag($tag)
    {
        return static::getInstance(false)->getByTag(static::getKey($tag));
    }

    /**
     * Deletes values for a given tag
     *
     * @param string $tag
     *
     * @return bool
     */
    public static function deleteByTag($tag)
    {
        return static::getInstance(true)->deleteByTag(static::getKey($tag));
    }
}