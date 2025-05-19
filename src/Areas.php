<?php

namespace Weijiajia\ProvinceCityChina;

class Areas
{
    /**
     * 获取所有区县数据
     *
     * @return array 区县代码和信息的关联数组
     */
    public static function getAreas(): array
    {
        $data = require __DIR__ . '/Resources/data/divisions.php';
        return $data['areas'];
    }
    
    /**
     * 获取所有区县名称
     *
     * @return array 区县代码和名称的关联数组
     */
    public static function getNames(): array
    {
        $areas = self::getAreas();
        $names = [];
        
        foreach ($areas as $code => $info) {
            $names[$code] = $info['name'];
        }
        
        return $names;
    }
    
    /**
     * 根据区县代码获取区县名称
     *
     * @param string $code 区县代码
     * @return string|null 区县名称，不存在时返回null
     */
    public static function getName(string $code): ?string
    {
        $areas = self::getAreas();
        return isset($areas[$code]) ? $areas[$code]['name'] : null;
    }
    
    /**
     * 根据城市代码获取区县列表
     *
     * @param string $cityCode 城市代码
     * @return array 该城市下的区县代码和信息的关联数组
     */
    public static function getByCityCode(string $cityCode): array
    {
        $areas = self::getAreas();
        $result = [];
        
        foreach ($areas as $code => $info) {
            if ($info['city_code'] === $cityCode) {
                $result[$code] = $info;
            }
        }
        
        return $result;
    }
    
    /**
     * 根据省份代码获取区县列表
     *
     * @param string $provinceCode 省份代码
     * @return array 该省份下的区县代码和信息的关联数组
     */
    public static function getByProvinceCode(string $provinceCode): array
    {
        $areas = self::getAreas();
        $result = [];
        
        foreach ($areas as $code => $info) {
            if ($info['province_code'] === $provinceCode) {
                $result[$code] = $info;
            }
        }
        
        return $result;
    }
    
    /**
     * 根据拼音搜索区县
     *
     * @param string $pinyin 拼音字符串
     * @param string|null $cityCode 可选的城市代码，用于限制搜索范围
     * @param string|null $provinceCode 可选的省份代码，用于限制搜索范围
     * @param bool $fuzzy 是否模糊匹配，默认为true
     * @return array 匹配的区县数组
     */
    public static function searchByPinyin(string $pinyin, ?string $cityCode = null, ?string $provinceCode = null, bool $fuzzy = true): array
    {
        $pinyin = strtolower($pinyin);
        $areas = self::getAreas();
        $result = [];
        
        foreach ($areas as $code => $info) {
            if ($cityCode !== null && $info['city_code'] !== $cityCode) {
                continue;
            }
            
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