<?php

namespace Weijiajia\ProvinceCityChina;

/**
 * 中国行政区划数据访问的主入口类
 */
class ChinaDivisions
{
    /**
     * 获取所有省份数据
     *
     * @return array 省份代码和信息的关联数组
     */
    public static function getProvinces()
    {
        return Provinces::getProvinces();
    }
    
    /**
     * 获取所有城市数据
     *
     * @return array 城市代码和信息的关联数组
     */
    public static function getCities()
    {
        return Cities::getCities();
    }
    
    /**
     * 获取所有区县数据
     *
     * @return array 区县代码和信息的关联数组
     */
    public static function getAreas()
    {
        return Areas::getAreas();
    }
    
    /**
     * 根据省份代码获取该省所有城市
     *
     * @param string $provinceCode 省份代码
     * @return array 城市代码和信息的关联数组
     */
    public static function getCitiesByProvinceCode($provinceCode)
    {
        return Cities::getByProvinceCode($provinceCode);
    }
    
    /**
     * 根据城市代码获取该城市所有区县
     *
     * @param string $cityCode 城市代码
     * @return array 区县代码和信息的关联数组
     */
    public static function getAreasByCityCode($cityCode)
    {
        return Areas::getByCityCode($cityCode);
    }
    
    /**
     * 根据省份代码获取该省所有区县
     *
     * @param string $provinceCode 省份代码
     * @return array 区县代码和信息的关联数组
     */
    public static function getAreasByProvinceCode($provinceCode)
    {
        return Areas::getByProvinceCode($provinceCode);
    }
    
    /**
     * 根据省份代码获取省份名称
     *
     * @param string $code 省份代码
     * @return string|null 省份名称，不存在时返回null
     */
    public static function getProvinceName($code)
    {
        return Provinces::getName($code);
    }
    
    /**
     * 根据城市代码获取城市名称
     *
     * @param string $code 城市代码
     * @return string|null 城市名称，不存在时返回null
     */
    public static function getCityName($code)
    {
        return Cities::getName($code);
    }
    
    /**
     * 根据区县代码获取区县名称
     *
     * @param string $code 区县代码
     * @return string|null 区县名称，不存在时返回null
     */
    public static function getAreaName($code)
    {
        return Areas::getName($code);
    }
    
    /**
     * 根据拼音搜索省份
     *
     * @param string $pinyin 拼音字符串
     * @return array 匹配的省份数组
     */
    public static function searchProvincesByPinyin($pinyin)
    {
        return Provinces::searchByPinyin($pinyin);
    }
    
    /**
     * 根据拼音搜索城市
     *
     * @param string $pinyin 拼音字符串
     * @param string|null $provinceCode 可选的省份代码，用于限制搜索范围
     * @return array 匹配的城市数组
     */
    public static function searchCitiesByPinyin($pinyin, $provinceCode = null)
    {
        return Cities::searchByPinyin($pinyin, $provinceCode);
    }
    
    /**
     * 根据拼音搜索区县
     *
     * @param string $pinyin 拼音字符串
     * @param string|null $cityCode 可选的城市代码，用于限制搜索范围
     * @param string|null $provinceCode 可选的省份代码，用于限制搜索范围
     * @return array 匹配的区县数组
     */
    public static function searchAreasByPinyin($pinyin, $cityCode = null, $provinceCode = null)
    {
        return Areas::searchByPinyin($pinyin, $cityCode, $provinceCode);
    }
}