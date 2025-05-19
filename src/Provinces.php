<?php

namespace Weijiajia\ProvinceCityChina;

class Provinces
{
    /**
     * 获取所有省份数据
     *
     * @return array 省份代码和信息的关联数组
     */
    public static function getProvinces()
    {
        $data = require __DIR__ . '/Resources/data/divisions.php';
        return $data['provinces'];
    }
    
    /**
     * 获取所有省份名称
     *
     * @return array 省份代码和名称的关联数组
     */
    public static function getNames(): array
    {
        $provinces = self::getProvinces();
        $names = [];
        
        foreach ($provinces as $code => $info) {
            $names[$code] = $info['name'];
        }
        
        return $names;
    }
    
    /**
     * 根据省份代码获取省份名称
     *
     * @param string $code 省份代码
     * @return string|null 省份名称，不存在时返回null
     */
    public static function getName($code)
    {
        $provinces = self::getProvinces();
        return isset($provinces[$code]) ? $provinces[$code]['name'] : null;
    }
    
    /**
     * 根据省份名称获取省份代码
     *
     * @param string $name 省份名称
     * @return string|null 省份代码，不存在时返回null
     */
    public static function getCode($name): ?string
    {
        $provinces = self::getProvinces();
        foreach ($provinces as $code => $info) {
            if ($info['name'] === $name) {
                return (string)$code;
            }
        }
        return null;
    }
    
    /**
     * 根据拼音搜索省份
     *
     * @param string $pinyin 拼音字符串
     * @param bool $fuzzy 是否模糊匹配，默认为true
     * @return array 匹配的省份数组
     */
    public static function searchByPinyin($pinyin, $fuzzy = true): array
    {
        $pinyin = strtolower($pinyin);
        $provinces = self::getProvinces();
        $result = [];
        
        foreach ($provinces as $code => $info) {
            // 如果是模糊匹配，则使用strpos，否则使用全等比较
            if ($fuzzy) {
                if (strpos(strtolower($info['pinyin']), $pinyin) !== false) {
                    $result[$code] = $info;
                }
            } else {
                if (strtolower($info['pinyin']) === $pinyin) {
                    $result[$code] = $info;
                }
            }
        }
        
        return $result;
    }
}