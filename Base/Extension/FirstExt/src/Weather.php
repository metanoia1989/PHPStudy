<?php

namespace Metanoia1989\FirstExt;

use Exception;
use GuzzleHttp\Client;
use Metanoia1989\FirstExt\Exception\HttpException;
use Metanoia1989\FirstExt\Exception\InvalidArgumentException;

class Weather 
{
    /**
     * 高德平台key
     *
     * @var string
     */
    protected $key;

    /**
     * guzzle client options
     *
     * @var array
     */
    protected $guzzleOptions = [];

    public function __construct(string $key)
    {
        $this->key = $key;
    } 

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    /**
     * 获取天气
     *
     * @param string $city 城市名/高德地址位置adcode 深圳 {adcode: 440300}
     * @param string $type 返回内容类型 base 实况天气 all 预报天气
     * @param string $format 输出的数据格式，json xml
     * @return array
     */
    public function getWeather($city, string $type = 'base', string $format = 'json')
    {
        $url = "https://restapi.amap.com/v3/weather/weatherInfo";    

        if (!in_array(strtolower($format), ['xml', 'json'])) {
            throw new InvalidArgumentException('Invalid response format: '.$format);
        }
        if (!in_array(strtolower($type), ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid type value(base/all): '.$type);
        }

        $query = array_filter([
            "key" => $this->key,
            "city" => $city,
            "format" => $format,
            "extensions" => $type,
        ]);

        try {
            $response = $this->getHttpClient()->get($url, [
                'query' => $query,
                'verify' => false,
            ])->getBody()->getContents();
            return 'json' === $format ? json_decode($response, true) : $response;
        } catch (Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
    