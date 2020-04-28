<?php
/**
 * Created by PhpStorm.
 * Author: 查路
 * Date: 2020/4/23
 * Time: 14:06
 */

namespace youranyese\EsQuery\Model;


use youranyese\EsQuery\Builder\FieldBuilder;
use youranyese\EsQuery\Builder\QueryBuilder;
use youranyese\EsQuery\Builder\ScoreScriptBuilder;
use youranyese\EsQuery\Builder\SortScriptBuilder;
use youranyese\EsQuery\ConnectInterface;

/**
 * Author: 查路
 * Date: 2020/4/24 16:57
 * Class Model
 *
 * @package youranyese\EsQuery\Model
 *
 * @method QueryBuilder where(array $where) and查询条件
 * @method QueryBuilder whereOr(array $where) 或查询条件
 * @method QueryBuilder whereNot(array $where) 非查询条件
 * @method QueryBuilder field(array|FieldBuilder $field) 查询字段
 * @method QueryBuilder scoreScript(ScoreScriptBuilder $scriptScore) 自定义评分脚本
 * @method QueryBuilder sort(array|SortScriptBuilder $sort)
 * @method QueryBuilder page(int $page, int $size) 分页
 * @method QueryBuilder select($params) 查询
 * @method QueryBuilder count() 记录总数
 */
abstract class Model
{
    /**
     * Author: 查路
     * Date: 2020/4/23 14:44
     *
     * @var string 索引名称
     */
    public $index = '';
    protected $type = '_doc';

    /**
     * Author: 查路
     * Date: 2020/4/26 10:11
     *
     * @var ConnectInterface
     */
    protected $connect = null;
    

    public function __construct()
    {
        if ($this->connect === null) {
            $this->connect = $this->setConnection();
        }
    }

    public function __set($name, $value)
    {
        // TODO: Implement __set() method.
        $this->$name = $value;
    }

    /**
     * 设置连接
     * Author: 查路
     * Date: 2020/4/26 11:27
     *
     * @return ConnectInterface
     */
    abstract protected function setConnection() : ConnectInterface;

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

    public function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->$name;
    }

    /**
     * 获取一个查询构造器
     * Author: 查路
     * Date: 2020/4/23 14:55
     *
     * @return Builder
     */
    public function newQuery() : QueryBuilder
    {
        return new QueryBuilder(new static());
    }
}