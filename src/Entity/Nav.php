<?php

namespace Rswork\Entity;

class Nav extends \Rswork\Entity
{
    protected static $table = 'nav';

    public function __construct( \Pimple $app, $params = array() )
    {
        parent::__construct( $app, $params );
    }

    public function getChildren()
    {
        if( !$this->id ) {
            return false;
        }

        $qbuilder = $this->app['db']->createQueryBuilder();
        $qbuilder
            ->select( '*' )
            ->from( $this->app['db']->quoteIdentifier( self::$table ), $this->app['db']->quoteIdentifier('a') )
            ->where( 'a.pid=?' )
            ->setParameter( 0, $this->id )
        ;

        $this->children = $this->app['entity']->getEntity( 'Nav', array(
            'qbuilder' => $qbuilder
        ) );

        unset( $qbuilder );

        return $this->children;
    }

    public function getParents()
    {
        if( !$this->id ) {
            return false;
        }

        if( $this->pid <= 0 ) {
            return null;
        }

        $qbuilder = $this->app['db']->createQueryBuilder();
        $qbuilder
            ->select( '*' )
            ->from( $this->app['db']->quoteIdentifier( self::$table ), $this->app['db']->quoteIdentifier('a') )
            ->where( 'a.id=?' )
            ->setParameter( 0, $this->pid )
        ;

        $this->parent = $this->app['entity']->getEntity( 'Nav', array(
            'qbuilder' => $qbuilder
        ) );

        unset( $qbuilder );

        return $this->parent;
    }
}
