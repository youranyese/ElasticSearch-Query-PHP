<?php
/**
 * Created by PhpStorm.
 * Author: 查路
 * Date: 2020/4/23
 * Time: 14:06
 */

namespace youranyese\EsQuery\Model;


use youranyese\EsQuery\Builder\Builder;

abstract class Model
{
    /**
     * Author: 查路
     * Date: 2020/4/23 14:44
     *
     * @var string 索引名称
     */
    protected $index = 'manbang_member_relation';

    protected $clinet;

    public function __construct()
    {
    }

    /**
     * Author: 查路
     * Date: 2020/4/23 14:26
     *
     * @param string $method
     * @param mixed $parameters
     *
     * @return mixed|null
     */
    public function __call($method, $parameters)
    {
        return call([$this->newQuery(), $method], $parameters);
    }

    /**
     * 处理静态方法调用
     *
     * @param string $method
     * @param array $parameters
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static())->{$method}(...$parameters);
    }

    /**
     * 获取一个查询构造器
     * Author: 查路
     * Date: 2020/4/23 14:55
     *
     * @return Builder
     */
    public function newQuery() : Builder
    {
        return new Builder();
    }

    public function getClient()
    {
    }
}