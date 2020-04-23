<?php
namespace youranyese\EsQuery;

use Elasticsearch\Client;

/**
 * Created by PhpStorm.
 * Author: 查路
 * Date: 2020/4/23
 * Time: 11:51
 */

interface ConnectInterface
{
    /**
     * 获取es客户端
     * Author: 查路
     * Date: 2020/4/23 13:58
     *
     * @return Client
     */
    public static function getEsClient() : Client;
}