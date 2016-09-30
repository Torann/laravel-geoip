<?php

namespace Torann\GeoIP\Support;

use Illuminate\Support\Arr;

class HttpClient
{
    /**
     * Request configurations.
     *
     * @var array
     **/
    private $config = [
        'base_uri' => '',
        'headers' => [],
        'query' => [],
    ];

    /**
     * Last request http status.
     *
     * @var int
     **/
    protected $http_code = 200;

    /**
     * Last request error string.
     *
     * @var string
     **/
    protected $errors = null;

    /**
     * Array containing headers from last performed request.
     *
     * @var array
     */
    private $headers = [];

    /**
     * HttpClient constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Perform a get request.
     *
     * @param  string $url
     * @param  array  $query
     * @param  array  $headers
     *
     * @return array
     */
    public function get($url, array $query = [], array $headers = [])
    {
        return $this->execute('GET', $this->buildGetUrl($url, $query), [], $headers);
    }

    /**
     * Execute the curl request
     *
     * @param  string $method
     * @param  string $url
     * @param  array  $query
     * @param  array  $headers
     *
     * @return array
     */
    public function execute($method, $url, array $query = [], array $headers = [])
    {
        // Merge global and request headers
        $headers = array_merge(
            Arr::get($this->config, 'headers', []),
            $headers
        );

        // Merge global and request queries
        $query = array_merge(
            Arr::get($this->config, 'query', []),
            $query
        );

        $this->errors = null;

        $curl = curl_init();

        // Set options
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->getUrl($url),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HEADER => 1,
            CURLINFO_HEADER_OUT => 1,
            CURLOPT_VERBOSE => 1,
        ]);

        // Setup method specific options
        switch ($method) {
            case 'PUT':
            case 'PATCH':
            case 'POST':
                curl_setopt_array($curl, [
                    CURLOPT_CUSTOMREQUEST => $method,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $query,
                ]);
                break;

            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;

            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
                break;
        }

        // Make request
        curl_setopt($curl, CURLOPT_HEADER, true);
        $response = curl_exec($curl);

        // Set HTTP response code
        $this->http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Set errors if there are any
        if (curl_errno($curl)) {
            $this->errors = curl_error($curl);
        }

        // Parse body
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close($curl);

        return [$body, $this->parseHeaders($header)];
    }

    /**
     * Check if the curl request ended up with errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return is_null($this->errors) === false;
    }

    /**
     * Get curl errors
     *
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get last curl HTTP code.
     *
     * @return int
     */
    public function getHttpCode()
    {
        return $this->http_code;
    }

    /**
     * Parse string headers into array
     *
     * @param string $headers
     *
     * @return array
     */
    private function parseHeaders($headers)
    {
        $result = [];

        foreach (preg_split("/\\r\\n|\\r|\\n/", $headers) as $row) {
            $header = explode(':', $row, 2);

            if (count($header) == 2) {
                $result[$header[0]] = trim($header[1]);
            }
            else {
                $result[] = $header[0];
            }
        }

        return $result;
    }

    /**
     * Get request URL.
     *
     * @param  string $url
     *
     * @return string
     */
    private function getUrl($url)
    {
        // Check for URL scheme
        if (parse_url($url, PHP_URL_SCHEME) === null) {
            $url = Arr::get($this->config, 'base_uri') . $url;
        }

        return $url;
    }

    /**
     * Build a GET request string.
     *
     * @param  string $url
     * @param  array  $query
     *
     * @return string
     */
    private function buildGetUrl($url, array $query = [])
    {
        // Merge global and request queries
        $query = array_merge(
            Arr::get($this->config, 'query', []),
            $query
        );

        // Append query
        if ($query = http_build_query($query)) {
            $url .= strpos($url, '?') ? $query : "?{$query}";
        }

        return $url;
    }
}