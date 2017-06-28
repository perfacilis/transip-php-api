<?php

/**
 * A class for generating an Access Token.
 *
 * @author: TransIP <support@transip.nl>
 * @version 1.0
 */
class TransIP_AccessToken
{
    /**
     * Your login name on the TransIP website.
     *
     * @var string
     */
    const LOGIN = '';

    /**
     * One of your private keys; these can be requested via your Controlpanel
     *
     * @var string
     */
    const PRIVATE_KEY = '';

    /**
     * The URL for authentication, this should be formatted with the endpoint URL
     */
    const AUTH_URL = 'https://%s/%s/auth';

    /**
     * TransIP API endpoint to connect to.
     *
     * e.g.:
     *
     *        'api.transip.nl'
     *        'api.transip.be'
     *        'api.transip.eu'
     *
     * @var string
     */
    const ENDPOINT = 'api.transip.nl';

    /**
     * API version number
     *
     * @var string
     */
    const VERSION = 'v6';

    /**
     * Read only mode
     */
    const READ_ONLY = false;

    /**
     * Default expiration time.
     * The maximum expiration time is one month.
     */
    const EXPIRATION_TIME = '30 minutes';

    /**
     * The label for the new access token
     * @var string
     */
    private $label = '';

    /**
     * @var string
     */
    private $signature;

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Creates a new Access Token
     *
     * @return string
     * @throws Exception
     */
    public function createToken()
    {
        $requestBody = $this->getRequestBody();

        // Create signature using the JSON encoded request body and your private key.
        $this->createSignature($requestBody);

        $responseJson = $this->performRequest($requestBody);

        if (!isset($responseJson->token)) {
            throw new Exception("An error occurred: {$responseJson->error}");
        }

        return $responseJson->token;
    }

    /**
     * @return string
     */
    private function getAuthUrl()
    {
        return sprintf(self::AUTH_URL, self::ENDPOINT, self::VERSION);
    }

    /**
     * Creates a JSON encoded string of the request body
     *
     * @return string
     */
    private function getRequestBody()
    {
        $requestBody = [
            'login'             => self::LOGIN,
            'nonce'             => uniqid(),
            'read_only'         => self::READ_ONLY,
            'expiration_time'   => self::EXPIRATION_TIME,
            'label'             => $this->label,
        ];
        return json_encode($requestBody);
    }

    /**
     * @param string $requestBody
     * @return string
     */
    private function performRequest($requestBody)
    {
        // Set up CURL request
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->getAuthUrl(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $requestBody,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Signature: ' . $this->signature
            ]
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        $responseJson = json_decode($response);
        return $responseJson;
    }

    /**
     * Method for creating a signature based on
     * Same sign method as used in SOAP API.
     *
     * @param string $parameters
     * @return string
     * @throws Exception
     */
    private function createSignature($parameters)
    {
        // Fixup our private key, copy-pasting the key might lead to whitespace faults
        if (!preg_match('/-----BEGIN (RSA )?PRIVATE KEY-----(.*)-----END (RSA )?PRIVATE KEY-----/si', self::PRIVATE_KEY,
            $matches)
        ) {
            throw new Exception('Could not find a valid private key');
        }

        $key = $matches[2];
        $key = preg_replace('/\s*/s', '', $key);
        $key = chunk_split($key, 64, "\n");

        $key = "-----BEGIN PRIVATE KEY-----\n" . $key . "-----END PRIVATE KEY-----";

        $digest = $this->sha512Asn1($parameters);
        if (!@openssl_private_encrypt($digest, $signature, $key)) {
            throw new Exception('Could not sign your request, please set a valid private key in the PRIVATE_KEY constant.');
        }

        $this->signature = base64_encode($signature);
    }

    /**
     * Creates a SHA512 ASN.1 header.
     *
     * @param $data
     * @return string
     */
    private function sha512Asn1($data)
    {
        $digest = hash('sha512', $data, true);

        // this ASN1 header is sha512 specific
        $asn1 = chr(0x30) . chr(0x51);
        $asn1 .= chr(0x30) . chr(0x0d);
        $asn1 .= chr(0x06) . chr(0x09);
        $asn1 .= chr(0x60) . chr(0x86) . chr(0x48) . chr(0x01) . chr(0x65);
        $asn1 .= chr(0x03) . chr(0x04);
        $asn1 .= chr(0x02) . chr(0x03);
        $asn1 .= chr(0x05) . chr(0x00);
        $asn1 .= chr(0x04) . chr(0x40);
        $asn1 .= $digest;

        return $asn1;
    }
}
