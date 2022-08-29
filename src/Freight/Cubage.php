<?php


namespace Crmdesenvolvimentos\ApiBraspress\Freight;

use Crmdesenvolvimentos\ApiBraspress\Freight\Cubage\Item;

class Cubage
{
    /**
     * List of items/volumes
     * Lista de items/volumes
     *
     * @var array $items
     */
    protected $items = [];

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Return items converted to array
     * Retorna os items convertidos para array
     *
     * @return array
     */
    public function getItemsToArray()
    {
        $result = [];

        foreach ($this->getItems() as $item) {
            $result[] = $item->_toArray();
        }

        return $result;
    }


    /**
     * Add a new item to the list
     * Adicionar um novo item a lista
     *
     * @param Item $item
     */
    public function addItem(Item $item)
    {
        $this->items[] = $item;
    }


    /**
     * Add new item to Cubages list
     * Adicionar um item a lista de Cubagens
     *
     * @param float $comprimento
     * @param float $largura
     * @param float $altura
     * @param int $volumes
     * @throws \ReflectionException
     */
    public function newItem($comprimento, $largura, $altura, $volumes)
    {
        $item = new Item();
        $item->setComprimento($comprimento);
        $item->setLargura($largura);
        $item->setAltura($altura);
        $item->setVolumes($volumes);

        $this->items[] = $item;
    }

}
