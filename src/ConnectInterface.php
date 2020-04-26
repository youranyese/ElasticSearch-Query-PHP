<?php
namespace youranyese\EsQuery;

interface ConnectInterface
{
    /**
     * ES客户端
     * Author: 查路
     * Date: 2020/4/26 10:19
     *
     * @return \Elasticsearch\Client
     */
    public static function getClient() : \Elasticsearch\Client;

    /**
     * ES索引前缀
     * Author: 查路
     * Date: 2020/4/26 10:19
     *
     * @return string
     */
    public static function getIndexPrefix() : string ;

    /**
     * es索引后缀
     * Author: 查路
     * Date: 2020/4/26 11:32
     *
     * @return string
     */
    public static function getIndexSuffix() : string ;
}