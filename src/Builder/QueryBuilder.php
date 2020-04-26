<?php
/**
 * Created by PhpStorm.
 * Author: 查路
 * Date: 2020/4/23
 * Time: 14:48
 */

namespace youranyese\EsQuery\Builder;

use youranyese\EsQuery\ConnectInterface;
use youranyese\EsQuery\Model\Model;

/**
 * Author: 查路
 * Date: 2020/4/24 17:13
 * Class QueryBuilder
 *
 * @package youranyese\EsQuery\Builder
 */
class QueryBuilder
{
    /**
     * Author: 查路
     * Date: 2020/4/24 11:21
     *
     * @var Model 被查询的model
     */
    protected $model;

    /**
     * Author: 查路
     * Date: 2020/4/26 10:27
     *
     * @var ConnectInterface
     */
    protected $connect;

    protected $binds = [
        'query' => [],
        'sort' => [],
        'from' => 0,
        'size' => 15,
        '_source' => [],
        'script_fields' => [],
    ];

    /**
     * Author: 查路
     * Date: 2020/4/24 15:47
     *
     * @var array 查询表达式
     */
    private $ex = [
        '=',
        '<',
        '<=',
        '>',
        '>=',
        'in',
        'between',
        'like',
        'match',
        'distance',
        'or'
    ];

    /**
     * Author: 查路
     * Date: 2020/4/24 15:47
     *
     * @var array 查询条件
     */
    protected $condition = [
        'must' => [],
        'must_not' => [],
        'should' => [],
    ];

    /**
     * Author: 查路
     * Date: 2020/4/24 15:47
     *
     * @var array 自定义评分脚本
     */
    protected $scoreScript = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->connect = $model->connect;
    }

    public function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->$name;
    }

    public function __call($method, $parameters = [])
    {
        if (method_exists($this->model, $method)) {
            $this->model->query = $this;
            return call([$this->model, $method], $parameters);;
        }

        call([$this, $method], $parameters);

        return $this;
    }

    /**
     * and查询条件
     * Author: 查路
     * Date: 2020/4/24 11:50
     *
     * @param array $where
     *
     * @return $this
     */
    public function where(array $where)
    {
        $condition = $this->analysisWhere($where);
        $this->condition['must'] = array_merge($this->condition['must'], $condition);

        return $this;
    }

    /**
     * 或查询条件
     * Author: 查路
     * Date: 2020/4/24 11:50
     *
     * @param array $where
     *
     * @return $this
     */
    public function whereOr(array $where)
    {
        $this->condition['should'][] = $this->analysisWhere($where);

        return $this;
    }

    /**
     * 非查询条件
     * Author: 查路
     * Date: 2020/4/24 11:51
     *
     * @param array $where
     *
     * @return $this
     */
    public function whereNot(array $where)
    {
        $condition = $this->analysisWhere($where);
        $this->condition['must_not'] = array_merge($this->condition['must_not'], $condition);

        return $this;
    }

    /**
     * 查询字段
     * Author: 查路
     * Date: 2020/4/24 15:39
     *
     * @param array|FieldBuilder $field
     *
     * @return $this
     */
    public function field($field)
    {
        if (is_array($field)) {
            $this->binds['_source'] = $field;
        } elseif (is_string($field)) {
            $this->binds['_source'] = explode(',', $field);
        } elseif ($field instanceof FieldBuilder) {
            $this->binds['_source'] = $field->getField() ?: [];
            $this->binds['script_fields'] = $field->getFieldsScript() ?: [];
        } else {
            throw new \InvalidArgumentException('查询字段参数错误');
        }

        return $this;
    }

    /**
     * 自定义评分脚本
     * Author: 查路
     * Date: 2020/4/24 15:48
     *
     * @param ScoreScriptBuilder $scoreScript
     *
     * @return $this
     */
    public function scoreScript(ScoreScriptBuilder $scoreScript)
    {
        $this->scoreScript = $scoreScript->getScoreScript();

        return $this;
    }

    /**
     * 排序
     * Author: 查路
     * Date: 2020/4/24 16:55
     *
     * @param array|SortScriptBuilder $sort
     *
     * @return $this
     */
    public function sort($sort)
    {
        if (is_array($sort)) {
            $sortBuilder = SortScriptBuilder::create()->setSort($sort);
        } elseif ($sort instanceof SortScriptBuilder) {
            $sortBuilder = $sort;
        } else {
            throw new \InvalidArgumentException('非法排序');
        }

        $this->binds['sort'] = $sortBuilder->getsort();
        
        return $this;
    }
    
    /**
     * 分页
     * Author: 查路
     * Date: 2020/4/23 16:17
     *
     * @param int $page
     * @param int $size
     *
     * @return $this
     */
    public function page(int $page, int $size)
    {
        $this->binds['size'] = $size;
        $this->binds['from'] = ($page-1) * $size;

        return $this;
    }

    public function select($params = [])
    {
        $this->handleQuery()->search($params);
    }

    /**
     * 条件分析
     * Author: 查路
     * Date: 2020/4/24 16:09
     *
     * @param $where
     *
     * @return array
     */
    protected function analysisWhere($where)
    {
        $res = [];
        foreach ($where as $k => $v) {
            if (is_string($k)) {
                $res[] = $this->getStandardWhere($k, $v);
            } else {
                if ($v[0] != 'or' || count($v) != 2) {
                    throw new \InvalidArgumentException('条件参数错误：'.json_encode($v));
                }
                $res[] = ['bool'=>['should' => $this->analysisWhere($v[1])]];
            }
        }

        return $res;
    }

    /**
     * 获取一个标准的查询条件
     * Author: 查路
     * Date: 2020/4/24 16:09
     *
     * @param string $field
     * @param        $where
     *
     * @return array
     */
    protected function getStandardWhere(string $field, $where)
    {
        if (is_array($where)) {
            $ex = $where[0];
            $value = $where[1];
            if (!in_array($ex, $this->ex)) {
                throw new \InvalidArgumentException('不支持的表达式：'.$ex);
            }
            switch ($ex) {
                case 'in';
                    $res = ['terms' => [$field => $value]];
                    break;
                case 'match';
                    $res = ['match' => [$field => $value]];
                    break;
                case 'like';
                    $res = ['match' => [$field => $value]];
                    break;
                case '<';
                    $res = ['range' => [$field => ['lt'=>$value]]];
                    break;
                case '<=';
                    $res = ['range' => [$field => ['lte'=>$value]]];
                    break;
                case '>';
                    $res = ['range' => [$field => ['gt'=>$value]]];
                    break;
                case '>=';
                    $res = ['range' => [$field => ['gte'=>$value]]];
                    break;
                case 'between';
                    $res = ['range' => [$field => ['gte'=>$value[0],'lte'=>$value[1]]]];
                    break;
                case 'not between';
                    $res = ['range' => [$field => ['gt'=>$value[1],'lt'=>$value[0]]]];
                    break;
                default:
                    $res = ['term' => [$field => $value]];
            }
        } else {
            $res = ['term' => [$field => $where]];
        }

        return $res;
    }

    /**
     * 处理query条件
     * Author: 查路
     * Date: 2020/4/26 9:55
     *
     * @return $this
     */
    protected function handleQuery()
    {
        $query = ['bool'=>[]];
        if (!empty($this->condition['must_not'])) {
            $query['bool']['must_not'] = $this->condition['must_not'];
        }
        if (!empty($this->condition['should'])) {
            $query['bool']['should'] = $this->condition['should'];
        }
        if (!empty($this->condition['must'])) {
            $query['bool']['must'] = $this->condition['must'];
        }
        if (empty($query['bool'])) {
            $query['match_all'] = new \stdClass();
        }
        if (empty($this->scoreScript)) {
            $this->binds['query'] = $query;
        } else {
            $this->binds['query'] = [
                'function_score' => [
                    'query' => $query,
                    'script_score' => ['script'=>$this->scoreScript]
                ]
            ];
        }

        return $this;
    }

    protected function search($params = [])
    {
        $builder = new Builder($this);
        $param = $builder->searchData($params);
        print_r($param);
        echo json_encode($param['body'], JSON_UNESCAPED_UNICODE);
        $res = $this->connect::getClient($param)
            ->search();
        //print_r($res);
    }
}