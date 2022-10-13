<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */
namespace Mmi\Tests\Orm;

/**
 * Testowy rekord
 */
class TestRecord extends \Mmi\Orm\Record
{

    public $id;
    public $camelCase;
    public $anotherColumn;
    public $nullColumn;
    public $defaultValue;

}

/**
 * Testowe query
 */
class TestQuery extends \Mmi\Orm\Query
{

    protected $_tableName = 'test';

}
