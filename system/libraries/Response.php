<?php

class Response {

    public static $HTTP_OK = 200;
    public static $HTTP_Created = 201;
    public static $HTTP_Accepted = 202;
    public $headers;
    private $content;
    private $version;
    private $statusCode;
    private $statusText;
    private $contentType;
    private $charset;
    private $cookies = array();
    public static $statusTexts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    );

    public function __construct($content = '', $status = 200, $headers = array()) {
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->headers = $headers;
    }

    /**
     * Returns the response content as it will be sent (with the headers).
     *
     * @return string The response content
     */
    public function __toString() {
        $this->prepare();

        return
                sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText) . "\r\n" .
                $this->headers . "\r\n" .
                $this->getContent();
    }

    /**
     * Clones the current Response instance.
     */
    public function __clone() {
        $this->headers = clone $this->headers;
    }

    /**
     * Sends HTTP headers.
     */
    public function sendHeaders() {
        // headers have already been sent by the developer
        if (headers_sent()) {
            return;
        }

        // status
        header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText));

        // headers
        foreach ($this->headers as $name => $values) {
            foreach ($values as $value) {
                header($name . ': ' . $value, false);
            }
        }

        // cookies
        foreach ($this->getCookies() as $cookie) {
            setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
        }
    }

    /**
     * Sends content for the current web response.
     */
    public function sendContent() {
        echo $this->content;
    }

    /**
     * Sends HTTP headers and content.
     */
    public function send() {
        $this->sendHeaders();
        $this->sendContent();
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function getContent() {
        return $this->content;
    }

    public function setStatusCode($code) {
        $this->statusCode = $code;
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    public function setStatusText($text) {
        $this->statusText = $text;
    }

    public function getStatusText() {
        return $this->statusText;
    }

    public function setContentType($contentType) {
        $this->contentType = $contentType;
    }

    public function getContentType() {
        return $this->contentType;
    }

    public function setCharset($charset) {
        $this->charset = $charset;
    }

    public function getCharset() {
        return $this->charset;
    }

    /**
     * Sets the HTTP protocol version (1.0 or 1.1).
     *
     * @param string $version The HTTP protocol version
     *
     * @api
     */
    public function setProtocolVersion($version) {
        $this->version = $version;
    }

    /**
     * Gets the HTTP protocol version.
     *
     * @return string The HTTP protocol version
     *
     * @api
     */
    public function getProtocolVersion() {
        return $this->version;
    }

    /**
     * Sets a cookie.
     *
     * @param Cookie $cookie
     *
     * @return void
     *
     * @api
     */
    public function setCookie(Cookie $cookie) {
        $this->cookies[$cookie->getDomain()][$cookie->getPath()][$cookie->getName()] = $cookie;
    }

    /**
     * Removes a cookie from the array, but does not unset it in the browser
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     *
     * @return void
     *
     * @api
     */
    public function removeCookie($name, $path = '/', $domain = null) {
        if (null === $path) {
            $path = '/';
        }

        unset($this->cookies[$domain][$path][$name]);

        if (empty($this->cookies[$domain][$path])) {
            unset($this->cookies[$domain][$path]);

            if (empty($this->cookies[$domain])) {
                unset($this->cookies[$domain]);
            }
        }
    }

    /**
     * Returns an array with all cookies
     *
     * @param string $format
     *
     * @throws \InvalidArgumentException When the $format is invalid
     *
     * @return array
     *
     * @api
     */
    public function getCookies($format = self::COOKIES_FLAT) {
        if (!in_array($format, array(self::COOKIES_FLAT, self::COOKIES_ARRAY))) {
            //throw new \InvalidArgumentException(sprintf('Format "%s" invalid (%s).', $format, implode(', ', array(self::COOKIES_FLAT, self::COOKIES_ARRAY))));
        }

        if (self::COOKIES_ARRAY === $format) {
            return $this->cookies;
        }

        $flattenedCookies = array();
        foreach ($this->cookies as $path) {
            foreach ($path as $cookies) {
                foreach ($cookies as $cookie) {
                    $flattenedCookies[] = $cookie;
                }
            }
        }

        return $flattenedCookies;
    }

    /**
     * Clears a cookie in the browser
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     *
     * @return void
     *
     * @api
     */
    public function clearCookie($name, $path = '/', $domain = null) {
        $this->setCookie(new Cookie($name, null, 1, $path, $domain));
    }

}
