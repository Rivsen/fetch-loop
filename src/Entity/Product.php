<?php

namespace Rswork\Entity;

class Product extends \Rswork\Entity
{
    const ISTREE = false;
    const PRIKEY = 'id';

    protected static $table = 'product';

    public function __construct( \Pimple $app, $params = array() )
    {
        parent::__construct( $app, $params );

        $prikey = self::PRIKEY;

        if( !isset( $this->$prikey ) ) {
            return;
        }
    }

    public function save()
    {
        return true;
    }
}
