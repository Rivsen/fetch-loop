<?php

namespace Rswork;

use Doctrine\DBAL\Query\QueryBuilder;

class Entity
{
    protected static $table = '';
    protected $app;

    public function __construct( \Pimple $app, $params = array() )
    {
        $this->app = $app;
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
                    ;
            }

            $qbuilder = $qbuilder->execute();
            return $result = $qbuilder->fetchAll( \PDO::FETCH_CLASS, $entityname, array( $app, $params ) );
        }

        return false;
    }
}
