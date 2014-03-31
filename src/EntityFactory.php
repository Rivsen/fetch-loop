<?php

namespace Rswork;

class EntityFactory
{
    protected $app;

    public function __construct( \Pimple $app )
    {
        $this->app = $app;
    }

    public function getEntity( $entityname, array $params = array() )
    {
        $entityname = __NAMESPACE__ . '\\Entity\\' . $entityname;

        if( class_exists( $entityname ) ) {

            $result = $entityname::getInstance( $this->app, $entityname, $params );

            if( $result AND count($result) > 0 ) {
                if( count($result) == 1 ) {
                    return $result[0];
                } else {
                    return $result;
                }
            }
        }

        return false;
    }
}
