<?php
/**
 * Created by PhpStorm.
 * Author: 查路
 * Date: 2020/4/24
 * Time: 11:48
 */

namespace youranyese\EsQuery\Builder;


abstract class FieldBuilder
{
    /**
     * Author: 查路
     * Date: 2020/4/24 14:10
     *
     * @var array 自定义查询字段
     */
    protected $fieldsScript = [];

    /**
     * 查询字段
     * Author: 查路
     * Date: 2020/4/24 15:34
     *
     * @var array
     */
    protected $field = [];

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
     * 设置查询字段
     * Author: 查路
     * Date: 2020/4/24 15:36
     *
     * @param $field
     *
     * @return $this
     */
    public function setField($field)
    {
        if (is_array($field)) {
            $this->field = $field;
        } elseif (is_string($field)) {
            $this->field = explode(',', $field);
        } else {
            throw new \InvalidArgumentException('查询字段参数错误');
        }

        return $this;
    }
    /**
     * 设置自定义查询字段
     * Author: 查路
     * Date: 2020/4/24 15:26
     *
     * @param string $name 脚本名称属性
     * @param array  $param 脚本参数
     * @param null   $fieldName 自定义查询字段名称
     *
     * @return $this
     */
    public function setFiledScript(string $name, array $param = [], $fieldName = null)
    {
        if (!method_exists($this, $name)) {
            throw new \InvalidArgumentException(static::class.'未定义脚本方法：'.$name);
        }

        $data = call([$this, $name], $param);
        if (!isset($data['script']) || empty($data['script'])) {
            throw new \InvalidArgumentException(static::class.'->'.$name.'()：返回值错误，必含script');
        }

        $script = [
            'lang' => 'painless',
            'source' => $data['script'],
        ];
        if (isset($data['param']) && !empty($data['param'])) {
            $script['params'] = $param;
        }
        $fieldName = $fieldName ? $fieldName : wordsToUnderline($name);
        $this->fieldsScript[$fieldName]['script'] = $script;

        return $this;
    }

    /**
     * 获取自定义查询字段
     * Author: 查路
     * Date: 2020/4/24 15:28
     *
     * @return array
     */
    public function getFieldsScript()
    {
        return $this->fieldsScript;
    }

    /**
     * 获取查询字段
     * Author: 查路
     * Date: 2020/4/24 15:36
     *
     * @return array
     */
    public function getField()
    {
        return $this->field;
    }
}