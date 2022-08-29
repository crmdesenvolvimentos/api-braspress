<?php


namespace Crmdesenvolvimentos\ApiBraspress;


class Tracking extends AbstractLibrary
{
    /**
     * Path for tracking by NFE
     * Path para rastreio por NFE
     */
    const PATH_TRACKING_NFE = '/tracking/byNf/{cnpj}/{notaFiscal}/json';

    /**
     * Path for tracking by order number
     * Path para rastreio pelo número do pedido
     */
    const PATH_TRACKING_PEDIDO = '/tracking/byNumPedido/{cnpj}/{numPedido}/json';

    /**
     * @var Braspress $braspress
     */
    protected $braspress;

    /**
     * @var $cnpjRemetente
     */
    protected $cnpjRemetente;

    /**
     * Api version
     * Versão da Api
     *
     * @var string $version
     */
    protected $version = 'v3';

    /**
     * @var Request $request
     */
    protected $request;

    /**
     * Response content
     * Retorno da consulta
     *
     * @var array $trakink
     */
    public $trackink = ['conhecimentos' => [], 'totalNf' => null, 'fluxoAtendimento' => null, 'datetime' => null];

    /**
     * Error messages
     * Mensagem em caso de erro
     *
     * @var string $error
     */
    public $error;

    /**
     * Error list
     * Lista de erros
     *
     * @var array $errorList
     */
    public $errorList = [];

    /**
     * Tracking constructor.
     * @param Braspress $braspress
     * @throws \ReflectionException
     */
    public function __construct(Braspress $braspress)
    {
        $this->braspress = $braspress;
    }

    /**
     * @return string
     */
    public function getCnpjRemetente()
    {
        return $this->cnpjRemetente;
    }

    /**
     * @param string $cnpjRemetente
     * @return Tracking
     * @throws \Exception
     */
    public function setCnpjRemetente($cnpjRemetente)
    {
        $value = Util::onlyNumbers($cnpjRemetente);
        if (Util::validateCnpj($value)) {
            $this->cnpjRemetente = $value;
        } else {
            throw new \Exception('Cnpj inválido');
        }

        return $this;
    }

    /**
     * Set Api version
     * Setar versão da Api
     *
     * @param $version
     * @return $this
     * @throws \Exception
     */
    public function setVersion($version)
    {
        if (in_array(strtolower($version), ['v1', 'v2', 'v3'])) {
            $this->version = strtolower($version);
            return $this;
        } else {
            throw new \Exception('Versão inválida da api');
        }
    }

    /**
     * Track delivery by NFe
     * Rastrear entrega pela NFe
     *
     * @param $notaFiscal
     * @return Tracking
     */
    public function trackingByNfe($notaFiscal)
    {
        $this->request = new Request(
            $this->braspress,
            $this->getUrl($notaFiscal, null),
            'get'
        );

        return $this;
    }

    /**
     * Track delivery per order - only api v3
     * Rastrear entrega pelo Pedido - somente v3 da api
     *
     * @param $numPedido
     * @return Tracking
     * @throws \Exception
     */
    public function trackingByNumPedido($numPedido)
    {
        $this->setVersion('v3');

        $this->request = new Request(
            $this->braspress,
            $this->getUrl(null, $numPedido),
            'get'
        );

        return $this;
    }

    /**
     * Make a request to the API
     * Faz o request para a api
     */
    public function send()
    {
        try {
            return $this->prepareResponse($this->request->call());
        } catch (\Exception | \Throwable $e) {
            $this->error = $e->getMessage();
            $this->errorList[] = $e->getMessage();
        }
    }

    /**
     * Return URL for request
     * Retorna url para requisição
     *
     * @param string|null $notaFiscal
     * @param string|null $numPedido
     * @return string
     */
    protected function getUrl(?string $notaFiscal, ?string $numPedido)
    {
        $url = $this->braspress->environment === Braspress::PRODUCTION
            ? Braspress::BASE_PRODUCTION
            : Braspress::BASE_DEVELOPMENT;

        $url .= $this->version;

        if ($notaFiscal) {
            $url .= str_replace(
                ['{cnpj}', '{notaFiscal}'],
                [$this->cnpjRemetente, $notaFiscal],
                self::PATH_TRACKING_NFE
            );
        } elseif ($numPedido) {
            $url .= str_replace(
                ['{cnpj}', '{numPedido}'],
                [$this->cnpjRemetente, $numPedido],
                self::PATH_TRACKING_NFE
            );
        }

        return $url;
    }


    /**
     * Handle Api response
     * Tratar a resposta da api
     *
     * @param \GuzzleHttp\Psr7\Response $response
     * @return array|mixed
     */
    protected function prepareResponse(\GuzzleHttp\Psr7\Response $response)
    {
        $this->braspress->response = $response;

        $body = json_decode($response->getBody(), true);

        if ($response->getStatusCode() === 200) {
            $body['datetime'] = date('Y-m-d H:i:s');
            $this->trackink = $body;

            return $this->trackink;
        } else {
            $this->error = Util::data_get($body, 'message');
            $this->errorList = array_merge($this->errorList, Util::data_get($body, 'errorList', []));
            $this->errorList[] = 'Código de retorno da api = ' . Util::data_get($body, 'statusCode');
            $this->errorList[] = 'Código de retorno da requisição = ' . $response->getStatusCode();

            return $this->error;
        }
    }

}
