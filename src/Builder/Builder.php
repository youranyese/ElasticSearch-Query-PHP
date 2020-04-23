<?php
/**
 * Created by PhpStorm.
 * Author: 查路
 * Date: 2020/4/23
 * Time: 14:48
 */

namespace youranyese\EsQuery\Builder;


class Builder
{
    protected $binds = [
        'query' => [],
        'sort' => [],
        'from' => 0,
        'size' => 15,
        '_source' => [],
        'script_fields' => [],
    ];

    private $ex = [
        '<','<=','>','>=',
        'in','not in',
        'between', 'not between',
        'or','like','match','distance'
    ];

    public function where(array $where)
    {
        print_r($where);
        $this->analysisWhere($where);

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
        $this->binds['from'] = ($page-1)*$size;

        return $this;
    }

    public function sort(array $sort)
    {

    }

    protected function analysisWhere($where)
    {
        foreach ($where as $k => $v) {
            if (is_string($k)) {

            } else {

            }
        }
    }

    protected function getStandardWhere(string $field, $where)
    {
        if (is_array($where)) {
            
        } else {
            $res = [''];
        }
    }
}