<?php


namespace Crmdesenvolvimentos\ApiBraspress;


class AbstractLibrary
{

    /**
     * @param $name
     * @return |null
     */
    public function __get($name)
    {
        $function = 'get' . Util::camel_case($name);
        if (method_exists($this, $function)) {
            return $this->$function;
        }
        return null;
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        $function = 'set' . Util::camel_case($name);
        if (method_exists($this, $function)) {
            return $this->$function($value);
        }
    }

    /**
     * @return array
     */
    public function _toArray()
    {
        return (array)call_user_func('get_object_vars', $this);
    }

}
