<?php


namespace Crmdesenvolvimentos\ApiBraspress;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class Request
{
    /**
     * @var string $url
     */
    protected $url;

    /**
     * @var string $method
     */
    protected $method = 'post';

    /**
     * @var Client $client
     */
    protected $client;

    /**
     * @var Braspress $braspress
     */
    protected $braspress;

    /**
     * @var Response;
     */
    protected $response;

    /**
     * Request constructor.
     *
     * @param string $url
     * @param string $method
     */
    public function __construct(Braspress $braspress, $url, $method = 'post')
    {
        $this->braspress = $braspress;
        $this->url = $url;
        $this->method = $method;
        $this->client = new Client();
    }


    /**
     * Make the call with the api and return the response
     * Faz a chamada junto a api e retorna o response
     *
     * @param array $data
     * @return Response|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function call(array $data = [])
    {
        $this->response = $this->client->request(
            $this->method,
            $this->url,
            [
                'auth' => [$this->braspress->user, $this->braspress->password],
                'Content-Type' => 'application/json',
                'timeout' => $this->braspress->timeout,
                'json' => $data
            ]
        );

        return $this->response;
    }

}
