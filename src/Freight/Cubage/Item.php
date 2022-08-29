<?php


namespace Crmdesenvolvimentos\ApiBraspress\Freight\Cubage;

use Crmdesenvolvimentos\ApiBraspress\AbstractLibrary;

class Item extends AbstractLibrary
{
    /**
     * Length of the volume in meters
     * Comprimento do volume em metros
     *
     * @var float $comprimento
     */
    protected $comprimento;

    /**
     * Volume width in meters
     * Largura do volume em metros
     *
     * @var float $largura
     */
    protected $largura;

    /**
     * Height of the volume in meters
     * Altura do volume em metros
     *
     * @var float $altura
     */
    protected $altura;

    /**
     * Total amount of volumes. If the volumes have the same measurement, here you can specify the total amount
     * Quantidade total de volumes. Caso os volumes tenham a mesma medida, aqui você pode especificar o valor total
     *
     * @var int $volumes
     */
    protected $volumes;

    /**
     * @return float
     */
    public function getComprimento()
    {
        return $this->comprimento;
    }

    /**
     * @param float $comprimento
     */
    public function setComprimento($comprimento)
    {
        $this->comprimento = (float)$comprimento;
    }

    /**
     * @return float
     */
    public function getLargura()
    {
        return $this->largura;
    }

    /**
     * @param float $largura
     */
    public function setLargura($largura)
    {
        $this->largura = $largura;
    }

    /**
     * @return float
     */
    public function getAltura()
    {
        return $this->altura;
    }

    /**
     * @param float $altura
     */
    public function setAltura($altura)
    {
        $this->altura = (float)$altura;
    }

    /**
     * @return int
     */
    public function getVolumes()
    {
        return $this->volumes;
    }

    /**
     * @param int $volumes
     */
    public function setVolumes($volumes)
    {
        $this->volumes = (int)$volumes;
    }


}
