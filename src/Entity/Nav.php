<?php

namespace Rswork\Entity;

class Nav extends \Rswork\Entity
{
    const ISTREE = true;
    const TREECOL = 'pid';
    const PRIKEY = 'id';

    protected static $table = 'nav';

    public function __construct( \Pimple $app, $params = array() )
    {
        parent::__construct( $app, $params );

        $prikey = self::PRIKEY;
        $treecol = self::TREECOL;

        if( !isset( $this->$prikey ) ) {
            return;
        }
    }

    public function save()
    {
        return true;
    }
}
