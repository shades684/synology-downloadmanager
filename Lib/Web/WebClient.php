<?php
/**
 * Omines-CMF
 * (c) Omines Internetbureau B.V.
 *
 * User: Niels Keurentjes
 * Date: 24-9-13
 * Time: 18:42
 */

namespace Lib\Web;

/**
 * Wrapper around CURL to easily retrieve remote web resources via HTTP.
 */
class WebClient
{
    /**
     * Construct object.
     *
     * @param array $headers Optional headers for the request.
     */
    public function __construct($headers = array())
    {
        $this->curl = curl_init();
        $this->headers = $headers;
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
    }

    /**
     * Sets a specific HTTP header for the request.
     *
     * @param string $key
     * @param string $value
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * Sets HTTP Basic Authentication headers.
     *
     * @param string $username
     * @param string $password
     */
    public function setHTTPBasicAuth($username, $password)
    {
        curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($this->curl, CURLOPT_USERPWD, "$username:$password");
    }

    /**
     * Retrieves the specified URL using GET.
     *
     * @param string $url
     * @param array $parameters Optional extra GET parameters.
     * @return string
     */
    public function get($url, array $parameters = array())
    {
        curl_setopt($this->curl, CURLOPT_HTTPGET, 1);
        if(!empty($parameters))
            $url = URL::mergeGetParameters($url, $parameters, true);
        return $this->send($url);
    }

    /**
     * Retrieves the specified URL using POST.
     *
     * @param string $url
     * @param array $parameters Optional extra POST parameters.
     * @return string
     */
    public function post($url, array $postFields = array())
    {
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postFields);
        return $this->send($url);
    }

    /**
     * Sends a request to the remote URL.
     *
     * @param string $url URL to call.
     * @return string The result of the request.
     * @throws \Exception When a problem occurred.
     */
    public function send($url)
    {
        $headers = array();
        foreach($this->headers as $key => $value)
            $headers[] = "$key: $value";
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        if(FALSE === ($result = curl_exec($this->curl)))
            throw new \Exception('WebClient request failed ('.curl_errno($this->curl).'): '.curl_error($this->curl));
        return $result;
    }

    /**
     * Shortcut function for retrieving a remote URL by POST.
     *
     * @param string $url
     * @param array $values
     * @param array $headers
     * @return string
     */
    public static function postURL($url, array $values, $headers = array())
    {
        $client = new WebClient($headers);
        return $client->post($url, $values);
    }

    /** @var array */
    private $headers = array();
}