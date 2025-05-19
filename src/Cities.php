<?php

namespace Weijiajia\ProvinceCityChina;

class Cities
{
    /**
     * 获取所有城市数据
     *
     * @return array 城市代码和信息的关联数组
     */
    public static function getCities(): array
    {
        $data = require __DIR__ . '/Resources/data/divisions.php';
        return $data['cities'];
    }
    
    /**
     * 获取所有城市名称
     *
     * @return array 城市代码和名称的关联数组
     */
    public static function getNames(): array
    {
        $cities = self::getCities();
        $names = [];
        
        foreach ($cities as $code => $info) {
            $names[$code] = $info['name'];
        }
        
        return $names;
    }
    
    /**
     * 根据城市代码获取城市名称
     *
     * @param string $code 城市代码
     * @return string|null 城市名称，不存在时返回null
     */
    public static function getName(string $code): ?string
    {
        $cities = self::getCities();
        return isset($cities[$code]) ? $cities[$code]['name'] : null;
    }
    
    /**
     * 根据省份代码获取城市列表
     *
     * @param string $provinceCode 省份代码
     * @return array 该省份下的城市代码和信息的关联数组
     */
    public static function getByProvinceCode(string $provinceCode): array
    {
        $cities = self::getCities();
        $result = [];
        
        foreach ($cities as $code => $info) {
            if ($info['province_code'] === $provinceCode) {
                $result[$code] = $info;
            }
        }
        
        return $result;
    }
    
    /**
     * 根据拼音搜索城市
     *
     * @param string $pinyin 拼音字符串
     * @param string|null $provinceCode 可选的省份代码，用于限制搜索范围
     * @param bool $fuzzy 是否模糊匹配，默认为true
     * @return array 匹配的城市数组
     */
    public static function searchByPinyin(string $pinyin, ?string $provinceCode = null, bool $fuzzy = true): array
    {
        $pinyin = strtolower($pinyin);
        $cities = self::getCities();
        $result = [];
        
        foreach ($cities as $code => $info) {
            if ($provinceCode !== null && $info['province_code'] !== $provinceCode) {
                continue;
            }
            
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