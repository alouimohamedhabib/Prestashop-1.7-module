<?php

class MymoduleClass extends ObjectModelCore
{
    /** @var string reassurance text */
    public $parameter;

    public $id_mymodule;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'mymodule',
        'primary' => 'id_mymodule',
        'multilang' => false,
        'fields' => array(
            'id_mymodule' => array('type' => self::TYPE_NOTHING, 'validate' => 'isUnsignedId'),
            'parameter' => array('type' => self::TYPE_STRING,'validate' => 'isGenericName', 'required' => true),
        )
    );

}