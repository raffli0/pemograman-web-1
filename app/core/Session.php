<?php

class Session
{
    /**
     * Start the session if not already started.
     */
    public static function init()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set a session variable.
     *
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        self::init();
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session variable.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        self::init();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * Remove a session variable.
     *
     * @param string $key
     */
    public static function remove($key)
    {
        self::init();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy the entire session.
     */
    public static function destroy()
    {
        self::init();
        session_destroy();
    }

    /**
     * Set a flash message (one-time message).
     *
     * @param string $key
     * @param string $message
     */
    public static function setFlash($key, $message)
    {
        self::init();
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Get and clear a flash message.
     *
     * @param string $key
     * @return string|null
     */
    public static function getFlash($key)
    {
        self::init();
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }

    /**
     * Check if a flash message exists.
     * 
     * @param string $key
     * @return bool
     */
    public static function hasFlash($key)
    {
        self::init();
        return isset($_SESSION['flash'][$key]);
    }
}
