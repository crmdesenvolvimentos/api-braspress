<?php


namespace Crmdesenvolvimentos\ApiBraspress;

use Crmdesenvolvimentos\ApiBraspress\Freight\Cubage;

class Freight extends AbstractLibrary
{
    /**
     * Path to freight quote
     */
    const PATH_FREIGHT = 'v1/cotacao/calcular/json';

    /**
     * HTTP METHOD
     */
    const METHOD = 'post';

    /**
     * @var Braspress $braspress
     */
    protected $braspress;

    /**
     * CNPJ of the sender - client must be registered with BRASPRESS
     * CNPJ do remetente - cliente precisa estar cadastrado na BRASPRESS
     *
     * @var string $cnpjRemetente
     */
    protected $cnpjRemetente;

    /**
     * Beneficiary's CNPJ - if the beneficiary's CNPJ or CPF is not registered with Braspress
     * quotation will be based on zip code (except for 2-FOB freight)
     *
     * CNPJ do destinatário – caso o CNPJ ou CPF do destinatário não esteja cadastrado na Braspress,
     * será realizada a cotação com base no CEP (exceto para tipo de frete 2-FOB)
     *
     * @var string $cnpjDestinatario
     */
    protected $cnpjDestinatario;

    /**
     * Consignee's CNPJ - client must be registered with BRASPRESS
     *
     * CNPJ do consignatário – cliente precisa estar cadastrado na BRASPRESS
     * (campo obrigatório para tipo de frete 3-Consignado)
     *
     * @var string $cnpjConsignado
     */
    protected $cnpjConsignado;

    /**
     * Modal type: 'R' for road, 'A' for air
     * Tipo de modal: 'R' para rodoviário, 'A' para aéreo
     *
     * @var string $modal
     */
    protected $modal;

    /**
     * 1 for CIF (the payer is the Sender), 2 for FOB (the payer is the recipient) and 3 for the Consignee (the payer is a third party)
     * 1 para CIF (pagante é o Remetente), 2 para FOB(pagante é o destinatário) e 3 para Consignado (pagante é um terceiro)
     *
     * @var int $tipoFrete
     */
    protected $tipoFrete;

    /**
     * Shipping origin zip code
     * CEP de origem do frete
     *
     * @var string $cepOrigem
     */
    protected $cepOrigem;

    /**
     * Shipping destination zip code
     * CEP de destino do frete
     *
     * @var string $cepDestino
     */
    protected $cepDestino;

    /**
     * Total freight value
     * Valor total da mercadoria do frete
     *
     * @var float $vlrMercadoria
     */
    protected $vlrMercadoria;

    /**
     * Total weight value of all packages
     * Valor total do peso de todos os volumes
     *
     * @var float $peso
     */
    protected $peso;

    /**
     * Total amount of volumes
     * Quantidade total de volumes
     *
     * @var int $volumes
     */
    protected $volumes;

    /**
     * List item
     * Lista de Items
     *
     * @var Cubage $cubagem
     */
    protected $cubagem;

    /**
     * @var Request $request
     */
    protected $request;

    /**
     * Data to be sent to api
     * Dados a serem subetidos a api
     *
     * @var array $data
     */
    public $data = [];

    /**
     * Shipping Return Data
     * Dados de retorno do Frete
     *
     * @var array $price
     */
    public $price = ['id' => null, 'prazo' => null, 'totalFrete' => 0, 'validade' => null, 'datetime' => null];

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
     * Freight constructor.
     *
     * @param Braspress $braspress
     * @throws \ReflectionException
     */
    public function __construct(Braspress $braspress)
    {
        $this->braspress = $braspress;
        $this->cubagem = new Cubage();
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
     * @return Freight
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
     * @param string $cnpjDestinatario
     * @return Freight
     * @throws \Exception
     */
    public function setCnpjDestinatario($cnpjDestinatario)
    {
        $value = Util::onlyNumbers($cnpjDestinatario);
        if (Util::validateCnpjOrCpf($value)) {
            $this->cnpjDestinatario = $value;
        } else {
            throw new \Exception('Cnpj ou Cpf inválido');
        }

        return $this;
    }

    /**
     * @param string $cnpjConsignado
     * @return Freight
     * @throws \Exception
     */
    public function setCnpjConsignado($cnpjConsignado)
    {
        $value = Util::onlyNumbers($cnpjConsignado);
        if (Util::validateCnpj($value)) {
            $this->cnpjConsignado = $value;
        } else {
            throw new \Exception('Cnpj inválido');
        }

        return $this;
    }

    /**
     * @param string $modal
     * @return Freight
     * @throws \Exception
     */
    public function setModal($modal = 'R')
    {
        $modal = strtoupper($modal);
        if ($modal === 'R' || $modal === 'A') {
            $this->modal = $modal;
        } else {
            throw new \Exception('Modal inválido, dever ser (R) para rodoviário ou (A) para aéreo');
        }

        return $this;
    }

    /**
     * @param int $tipoFrete
     * @return Freight
     * @throws \Exception
     */
    public function setTipoFrete($tipoFrete)
    {
        if (in_array((int)$tipoFrete, [1, 2, 3])) {
            $this->tipoFrete = (int)$tipoFrete;
        } else {
            throw new \Exception('Tipo de frete inválido, deve ser (1) remetente, (2) fob ou (3) consignado');
        }

        return $this;
    }

    /**
     * @param string $cepOrigem
     * @return Freight
     * @throws \Exception
     */
    public function setCepOrigem($cepOrigem)
    {
        $value = Util::onlyNumbers($cepOrigem);
        if (Util::validateCep($value)) {
            $this->cepOrigem = $value;
        } else {
            throw new \Exception('Cep inválido, deve conter 8 dígitos');
        }

        return $this;
    }

    /**
     * @param string $cepDestino
     * @return Freight
     * @throws \Exception
     */
    public function setCepDestino($cepDestino)
    {
        $value = Util::onlyNumbers($cepDestino);
        if (Util::validateCep($value)) {
            $this->cepDestino = $value;
        } else {
            throw new \Exception('Cep inválido, deve conter 8 dígitos');
        }

        return $this;
    }

    /**
     * @param float $vlrMercadoria
     * @return Freight
     */
    public function setVlrMercadoria($vlrMercadoria)
    {
        $this->vlrMercadoria = $vlrMercadoria;

        return $this;
    }

    /**
     * @param float $peso
     * @return Freight
     */
    public function setPeso($peso)
    {
        $this->peso = (float)$peso;

        return $this;
    }

    /**
     * @param int $volumes
     * @return Freight
     */
    public function setVolumes($volumes)
    {
        $this->volumes = (int)$volumes;

        return $this;
    }

    /**
     * @param Cubage $cubagem
     * @return Freight
     */
    public function setCubagem(Cubage $cubagem)
    {
        $this->cubagem = $cubagem;

        return $this;
    }

    /**
     * @return Cubage
     */
    public function getCubagem()
    {
        return $this->cubagem;
    }

    /**
     * Make a request to the API
     * Prepara os dados e executar o request para a api
     */
    public function send()
    {
        try {
            $this->request = new Request(
                $this->braspress,
                $this->getUrl(),
                'post'
            );

            $this->prepareData();

            return $this->prepareResponse($this->request->call($this->data));
        } catch (\Exception | \Throwable $e) {
            $this->error = $e->getMessage();
            $this->errorList[] = $e->getMessage();
        }
    }

    /**
     * @return string
     */
    protected function getUrl()
    {
        $url = $this->braspress->environment === Braspress::PRODUCTION
            ? Braspress::BASE_PRODUCTION
            : Braspress::BASE_DEVELOPMENT;
        $url .= self::PATH_FREIGHT;

        return $url;
    }

    /**
     * Generate array with quote data
     * Montar array com os dados da cotação
     *
     * @return array
     */
    protected function prepareData()
    {
        $data = $this->_toArray();

        if ($this->tipoFrete !== 3) {
            unset($data['cnpjConsignado']);
        }

        foreach ($this->getCubagem()->getItems() as $item) {
            $data['cubagem'][] = $item->_toArray();
        }

        $this->data = $data;

        return $this->data;
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
            $body['validade'] = date_create()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
            $body['datetime'] = date('Y-m-d H:i:s');
            $this->price = $body;

            return $this->price;
        } else {
            $this->error = Util::data_get($body, 'message');
            $this->errorList = array_merge($this->errorList, Util::data_get($body, 'errorList'));
            $this->errorList[] = 'Código de retorno da api = ' . Util::data_get($body, 'statusCode');
            $this->errorList[] = 'Código de retorno da requisição = ' . $response->getStatusCode();

            return $this->error;
        }
    }

    /**
     * Generate the data to be sent via class attributes
     * Gerar os dados a serem submetidos através dos atributos da classe
     *
     * @return array
     */
    public function _toArray()
    {
        $data = parent::_toArray();
        unset($data['braspress'], $data['data'], $data['cubagem'], $data['price'], $data['data'], $data['error'], $data['errorList']);
        return $data;
    }

}
