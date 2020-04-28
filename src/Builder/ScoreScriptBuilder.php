<?php
/**
 * Created by PhpStorm.
 * Author: 查路
 * Date: 2020/4/24
 * Time: 11:48
 */

namespace youranyese\EsQuery\Builder;


abstract class ScoreScriptBuilder
{
    /**
     * Author: 查路
     * Date: 2020/4/24 14:10
     *
     * @var array 自定义评分脚本
     */
    protected $scoreScript = [];

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
     * 设置自定义评分脚本
     * Author: 查路
     * Date: 2020/4/24 15:26
     *
     * @param string $name 脚本名称
     * @param array  $param 脚本参数
     *
     * @return $this
     */
    public function setScoreScript(string $name, array $param = [])
    {
        if (!method_exists($this, $name)) {
            throw new \InvalidArgumentException(static::class.'未定义脚本方法：'.$name);
        }

        $data = call([$this, $name], [$param]);
        if (!isset($data['script']) || empty($data['script'])) {
            throw new \InvalidArgumentException(static::class.'->'.$name.'()：返回值错误，必含script');
        }

        $script = [
            'lang' => 'painless',
            'source' => $data['script'],
        ];
        if (isset($data['param']) && !empty($data['param'])) {
            $script['params'] = $data['param'];
        }

        $this->scoreScript = $script;
        
        return $this;
    }

    /**
     * 获取自定义评分脚本
     * Author: 查路
     * Date: 2020/4/24 15:28
     *
     * @return array
     */
    public function getScoreScript()
    {
        return $this->scoreScript;
    }
}