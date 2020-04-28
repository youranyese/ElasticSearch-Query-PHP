<?php
/**
 * Created by PhpStorm.
 * Author: 查路
 * Date: 2020/4/26
 * Time: 10:46
 */

namespace youranyese\EsQuery\Builder;


class Builder
{
    /**
     * Author: 查路
     * Date: 2020/4/26 10:47
     *
     * @var QueryBuilder
     */
    protected $builder;
    /**
     * Author: 查路
     * Date: 2020/4/26 10:51
     *
     * @var 带前缀的索引名称
     */
    protected $indexName;

    protected $body = [];
    
    public function __construct(QueryBuilder $builder)
    {
        $this->builder = $builder;
        $prefix = $builder->connect->getIndexPrefix();
        $suffix = $builder->connect->getIndexSuffix();
        $index = $builder->model->index;
        if (empty($index)) {
            throw new \InvalidArgumentException(get_class($builder->model).'未设置索引名称');
        }
        $this->indexName = $prefix.$index.$suffix;
        foreach ($this->builder->binds as $k => $v) {
            if (!empty($v) || $v === 0) {
                $this->body[$k] = $v;
            }
        }
    }

    /**
     * 组装ES搜索参数
     * Author: 查路
     * Date: 2020/4/26 11:10
     *
     * @param array $params
     *
     * @return array
     */
    public function searchData($params = [])
    {
        return array_merge([
            'index' => $this->indexName,
            'type' => $this->builder->model->type,
            'body' => $this->body,
        ], $params);
    }

    /**
     * 组装ES搜索参数
     * Author: 查路
     * Date: 2020/4/26 11:10
     *
     * @param array $params
     *
     * @return array
     */
    public function countData($params = [])
    {
        return array_merge([
            'index' => $this->indexName,
            'type' => $this->builder->model->type,
            'body' => ['query'=>$this->builder->binds['query']],
        ], $params);
    }
}