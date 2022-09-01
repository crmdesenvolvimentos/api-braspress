<?php


namespace Crmdesenvolvimentos\ApiBraspress;

use GuzzleHttp\Psr7\Response;

class Braspress
{
    /**
     * Production Environment
     * Ambiente de Produção
     */
    const PRODUCTION = 'production';

    /**
     * Approval Environment
     * Ambiente de Homologação
     */
    const DEVELOPMENT = 'development';

    /**
     * Environment Options
     * Ambientes possíveis
     */
    const ENVIRONMENT = [self::PRODUCTION, self::DEVELOPMENT];

    /**
     * Freight Quote Mode
     * Modo de uso para cotação de frete
     */
    const MODE_FREIGHT = 'freight';

    /**
     * Object Tracking Mode
     * Modo de uso para restreamento de objetos
     */
    const MODE_TRACKING = 'tracking';

    /**
     * Api Usage Options
     * Possibilidades de uso da Api
     */
    const API_MODE = [self::MODE_FREIGHT, self::MODE_TRACKING];

    /**
     * URL for Approval Environment
     * Url para Ambiente de Homologação
     */
    const BASE_DEVELOPMENT = 'https://hml-api.braspress.com/';

    /**
     * URL for Production Environment
     * Url para Ambiente de Produção
     */
    const BASE_PRODUCTION = 'https://api.braspress.com/';

    /**
     * Set environment
     * Setar ambiente
     *
     * @var string $environment
     */
    public $environment = 'production';

    /**
     * @var int $timeout
     */
    public $timeout = 5;

    /**
     * Set API call mode
     * Setar modo de chamadas da api
     *
     * @var string $mode
     */
    public $mode = 'freight';

    /**
     * Sender's taxvat
     * Cnpj do Remetente
     *
     * @var string $cnpjRemetente
     */
    public $cnpjRemetente;

    /**
     * Api User
     * Usuário da Api
     *
     * @var string $user
     */
    public $user;

    /**
     * Api user password
     * Senha do usuário da Api
     *
     * @var string $password
     */
    public $password;

    /**
     * @var Freight $freight
     */
    public $freight;

    /**
     * @var Tracking $tracking
     */
    public $tracking;

    /**
     * @var Response
     */
    public $response;


    /**
     * Braspress constructor.
     * @throws \ReflectionException
     */
    public function __construct()
    {
        $this->freight = new Freight($this);
        $this->tracking = new Tracking($this);
    }

    /**
     * production|development
     *
     * @param string $environment
     * @return Braspress
     * @throws \Exception
     */
    public function setEnvironment($environment)
    {
        $value = strtolower($environment);
        if (in_array($value, self::ENVIRONMENT)) {
            $this->environment = $value;
        } else {
            throw new \Exception('Ambiente inválido');
        }

        return $this;
    }

    /**
     * @param int $timeout
     * @return Braspress
     */
    public function setTimeout($timeout)
    {
        $this->timeout = (int)$timeout;
        return $this;
    }

    /**
     * freight|tracking
     *
     * @param string $mode
     * @return Braspress
     * @throws \Exception
     */
    public function setMode($mode)
    {
        $value = strtolower($mode);
        if (in_array($value, self::API_MODE)) {
            $this->mode = $value;
        } else {
            throw new \Exception('Modo de utilização da api inválido');
        }

        return $this;
    }

    /**
     * @param string $cnpjRemetente
     * @return Braspress
     * @throws \Exception
     */
    public function setCnpjRemetente($cnpjRemetente)
    {
        $value = Util::onlyNumbers($cnpjRemetente);
        if (Util::validateCnpj($value)) {
            $this->cnpjRemetente = $value;
            $this->freight->setCnpjRemetente($value);
            $this->tracking->setCnpjRemetente($value);
        } else {
            throw new \Exception('Cnpj inválido');
        }

        return $this;
    }

    /**
     * @param $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }


    /**
     * Trigger the Api call
     * Dispara a chamada da api
     *
     * @return array|mixed
     */
    public function send()
    {
        switch ($this->mode) {
            case 'freight':
                return $this->freight->send();
            case 'tracking' :
                return $this->tracking->send();
        }
    }

}
