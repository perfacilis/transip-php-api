<?php

namespace Transip;

/**
 * @author Perfacilis <support@perfacilis.com>
 * @version 0.1
 */
abstract class Rest
{

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    public function __construct(\TransIP_AccessToken $accesstoken)
    {
        $this->accesstoken = $accesstoken;

        if (!$this->accesstoken::LOGIN) {
            throw new \Exception('Login name not set in Access Token, please set it with the username you\'re using to login to the cp.');
        }

        if (!$this->accesstoken::PRIVATE_KEY) {
            throw new \Exception('Private key not set in Acces Token, please set it with a private key from a Key Pair retrieved from the cp.');
        }
    }

    final public function get($uri, $params = [])
    {
        return $this->doRequest(self::METHOD_GET, $uri, $params);
    }

    final public function post($uri, $params = [])
    {
        return $this->doRequest(self::METHOD_POST, $uri, $params);
    }

    final public function put($uri, $params = [])
    {
        return $this->doRequest(self::METHOD_PUT, $uri, $params);
    }

    final public function delete($uri, $params = [])
    {
        return $this->doRequest(self::METHOD_DELETE, $uri, $params);
    }

    final public function getHttpCode()
    {
        return $this->http_code;
    }

    protected $version = 0.1;
    private $accesstoken = null;
    private $accesstoken_string = '';
    // https://ENDPOINT/VERSION/URI
    private $url_format = 'https://%1$s/%2$s/%3$s';
    private $http_code = 0;

    /**
     * Execute the actual request and return an array with the results
     * @param type $method
     * @param type $uri
     * @param array $params
     * @return array
     */
    private function doRequest(string $method, string $uri, array $params)
    {
        // Build endpoint URL
        $url = $this->buildUrl($uri);

        // Setup cURL request
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->getAccessToken(),
            ],
            CURLOPT_USERAGENT, 'Transip Rest API PHP Wrapper ' . $this->version
        ]);

        // Add params as JSON body
        if ($params) {
            $params = json_encode($params);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        // Yo hey, yo ho, yo f*cking he!
        $output = curl_exec($ch);
        $this->http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $output = json_decode($output, true);
        if (!$output) {
            throw new \Exception('Returned JSON could not be parsed: ' . json_last_error_msg());
        }

        if (isset($output['error'])) {
            throw new \Exception('TransIP API returned an error: ' . $output['error']);
        }

        return $output;
    }

    private function buildUrl($uri)
    {
        $token = &$this->accesstoken;
        $uri = ltrim($uri, '/');
        return sprintf($this->url_format, $token::ENDPOINT, $token::VERSION, $uri);
    }

    private function getAccessToken()
    {
        if ($this->accesstoken_string) {
            return $this->accesstoken_string;
        }

        $this->accesstoken_string = $this->accesstoken->createToken();
        return $this->accesstoken_string;
    }

}
