<?php
/**
 * Created by PhpStorm.
 * Author: 查路
 * Date: 2020/4/24
 * Time: 11:48
 */

namespace youranyese\EsQuery\Builder;


abstract class SortScriptBuilder
{
    /**
     * Author: 查路
     * Date: 2020/4/24 14:10
     *
     * @var array 排序
     */
    protected $sort = [];

    /**
     * 创建一个类
     * Author: 查路
     * Date: 2020/4/24 15:26
     *
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * 设置字段排序
     * Author: 查路
     * Date: 2020/4/24 16:40
     *
     * @param array $sort ["field"=>"desc","field"=>"asc"]
     *
     * @return $this
     */
    public function setSort(array $sort)
    {
        foreach ($sort as $k => $v) {
            $this->sort[] = [$k => ["order"=> strtolower($v)]];
        }

        return $this;
    }

    /**
     * 设置自定义排序脚本
     * Author: 查路
     * Date: 2020/4/24 15:26
     *
     * @param string $name 脚本名称
     * @param string $sort 排序 asc：升序，desc降序
     * @param array  $param 脚本参数
     *
     * @return $this
     */
    public function setSortScript(string $name, string $sort, array $param = [])
    {
        if (!method_exists($this, $name)) {
            throw new \InvalidArgumentException(static::class.'未定义脚本方法：'.$name);
        }

        if (!in_array(strtolower($sort), ['desc', 'asc'])) {
            throw new \InvalidArgumentException(static::class.'->'.$name.'()：排序参数错误,eg:desc|asc');
        }

        $data = call([$this, $name], [$param]);
        if (!isset($data['script']) || empty($data['script'])) {
            throw new \InvalidArgumentException(static::class.'->'.$name.'()：返回值错误，必含script');
        }
        if (!isset($data['type']) || empty($data['type'])) {
            throw new \InvalidArgumentException(static::class.'->'.$name.'()：返回值错误，必含type');
        }

        $script = [
            'lang' => 'painless',
            'source' => $data['script'],
        ];
        if (isset($data['param']) && !empty($data['param'])) {
            $script['params'] = $param;
        }
        $this->sort[] = [
            '_script' => [
                'script' => $script,
                'order' => strtolower($sort),
                'type' => $data['type']
            ]
        ];


        return $this;
    }

    /**
     * 获取自定义评分脚本
     * Author: 查路
     * Date: 2020/4/24 15:28
     *
     * @return array
     */
    public function getsort()
    {
        return $this->sort;
    }
}