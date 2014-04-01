<?php

namespace Rswork;

use Doctrine\DBAL\Query\QueryBuilder;

abstract class Entity
{
    const ISTREE = false;
    const TREECOL = null;
    const PRIKEY = null;

    protected static $table = '';
    protected $app;
    protected $data = array();
    protected $savedData = array();

    public function __construct( \Pimple $app, $params = array() )
    {
        $this->savedData = $this->data;

        $this->app = $app;

        $called = get_called_class();

        $prikey = $called::PRIKEY;
        $treecol = $called::TREECOL;

        $calledClass = join(
            '',
            array_slice( explode( '\\', $called ), -1 )
        );

        if( $called::ISTREE === true ) {
            if( isset( $params['parent'] ) AND $params['parent']->$prikey == $this->$treecol ) {
                $this->parent = $params['parent'];
            } else {
                if( $this->$treecol > 0 ) {
                    /*
                     * get parent node
                     */
                    $qbuilder = $app['db']->createQueryBuilder();
                    $qbuilder
                        ->select( '*' )
                        ->from( $app['db']->quoteIdentifier( $called::$table ), $app['db']->quoteIdentifier('a') )
                        ->where( 'a.'.$prikey.'=?' )
                        ->setParameter( 0, $this->$treecol )
                        ;

                    $this->parent = $app['entity']->getEntity( $calledClass, array(
                        'qbuilder' => $qbuilder,
                    ) );
                } else {
                    $this->parent = null;
                }
            }

            /*
             * get children nodes
             */
            $qbuilder = $app['db']->createQueryBuilder();
            $qbuilder
                ->select( '*' )
                ->from( $app['db']->quoteIdentifier( $called::$table ), $app['db']->quoteIdentifier('a') )
                ->where( 'a.'.$treecol.'=?' )
                ->setParameter( 0, $this->$prikey )
                ;

            $this->children = $app['entity']->getEntity( $calledClass, array(
                'qbuilder' => $qbuilder,
                'getchild' => true,
                'parent' => $this,
            ) );
        }
    }

    public abstract function save();

    public function __get( $name )
    {
        if( isset( $this->data[$name] ) ) {
            return $this->data[$name];
        }

        return false;
    }

    public function __set( $name, $val )
    {
        $this->data[$name] = $val;
        return true;
    }

    public function __isset( $name )
    {
        return isset( $this->data[$name] );
    }

    public function __unset( $name )
    {
        unset( $this->data[$name] );
        return true;
    }

    public static function getTableName( $entityname )
    {
        $entityname = __NAMESPACE__ . '\\Entity\\' . $entityname;

        if( is_subclass_of( $entityname, __CLASS__ ) ) {
            return $entityname::$table;
        }

        return false;
    }

    public static function getInstance( \Pimple $app, $entityname, $params = array() )
    {
        if( is_subclass_of( $entityname, __CLASS__ ) ) {

            if( isset( $params['qbuilder'] ) AND $params['qbuilder'] instanceof QueryBuilder ) {
                $hastable = false;

                foreach( $params['qbuilder']->getQueryPart('from') as $from ) {
                    if( trim( $from['table'], $app['db']->getDatabasePlatform()->getIdentifierQuoteCharacter() ) == $entityname::$table ) {
                        $hastable = true;
                        break;
                    }
                }

                if( $hastable === true ) {
                    $qbuilder = $params['qbuilder'];
                }
            }

            if( !isset( $qbuilder ) ) {
                $qbuilder = $app['db']->createQueryBuilder();
                $qbuilder
                    ->select( '*' )
                    ->from( $app['db']->quoteIdentifier( $entityname::$table ), $app['db']->quoteIdentifier('p') )
                    ->where( 1 )
                    ;

                if( $entityname::ISTREE === true ) {
                    $qbuilder->andWhere( 'p.'.$entityname::TREECOL.'=:'.$entityname::TREECOL );
                    $qbuilder->setParameter( ':'.$entityname::TREECOL, @$params[$entityname::TREECOL] ?: 0 );
                }
            }

            $qbuilder = $qbuilder->execute();
            return $result = $qbuilder->fetchAll( \PDO::FETCH_CLASS, $entityname, array( $app, $params ) );
        }

        return false;
    }
}
