<?php

namespace AEngine\Memory\Interfaces;

interface DriverInterface
{
    /**
     * Writes a value to an external storage key
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $expire
     * @param string $tag
     *
     * @return bool
     */
    public function set($key, $value, $expire = 0, $tag = null) : bool;

    /**
     * Return value from external storage and returns
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Removes specified key from the external storage
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete($key) : bool;

    /**
     * Remove all keys from an external storage
     *
     * @return bool
     */
    public function flush() : bool;

    /**
     * Return values for a given tag
     *
     * @param string $tag
     *
     * @return array
     */
    public function getByTag($tag) : array;

    /**
     * Deletes values for a given tag
     *
     * @param string $tag
     *
     * @return bool
     */
    public function deleteByTag($tag) : bool;
}