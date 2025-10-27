<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\http;

use JsonSerializable;

/**
 * This interface defines the functionality required to send and receive data to a URL.
 */
interface RESTTemplate
{
    /**
     * Set the token to be used for the upcoming requests.
     *
     * @param string $token
     */
    public function setToken($token);

    /**
     * Set the user agent with the partner reference.
     */
    public function setUserAgent($userAgent);

    /**
     * Perform a GET call to the given path.
     *
     * @param string $path
     *
     * @return string Response body
     */
    public function get($path, array $parameters = []);

    /**
     * Perform a POST call to the given path.
     *
     * @param string $path
     *
     * @return string Response body
     */
    public function post($path, ?JsonSerializable $body = null);

    /**
     * Perform a DELETE call to the given path.
     *
     * @param string $path
     *
     * @return string Response body
     */
    public function delete($path, array $parameters = []);
}
