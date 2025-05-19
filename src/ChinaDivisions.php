<?php

namespace Weijiajia\ProvinceCityChina;

use Illuminate\Support\Collection;
use Weijiajia\ProvinceCityChina\Entities\Province;

/**
 * 中国行政区划数据访问的主入口类
 */
class ChinaDivisions
{
    use Makeable;

    protected Provinces $provincesService;
    protected Cities $citiesService;
    protected Areas $areasService;

    /**
     * 构造函数
     *
     * @param string|null $dataPath 数据文件的可选路径，如果为 null 则使用默认路径。
     */
    public function __construct(?string $dataPath = null)
    {
        $path = $dataPath ?? __DIR__ . '/Resources/data/divisions.php';
        $this->provincesService = new Provinces($path);
        $this->citiesService = new Cities($path);
        $this->areasService = new Areas($path);
    }

    /**
     * 获取所有省份数据
     *
     * @return Collection 省份代码和信息的Collection对象
     */
    public function getProvinces(): Collection
    {
        return $this->provincesService->getProvinces();
    }

    /**
     * 获取所有城市数据
     *
     * @return Collection 城市代码和信息的Collection对象
     */
    public function getCities(): Collection
    {
        return $this->citiesService->getCities();
    }

    /**
     * 获取所有区县数据
     *
     * @return Collection 区县代码和信息的Collection对象
     */
    public function getAreas(): Collection
    {
        return $this->areasService->getAreas();
    }

    /**
     * 根据省份代码获取该省所有城市
     *
     * @param string $provinceCode 省份代码
     * @return Collection 城市代码和信息的Collection对象
     */
    public function getCitiesByProvinceCode(string $provinceCode): Collection
    {
        return $this->citiesService->getCitiesByProvinceCode($provinceCode);
    }

    /**
     * 根据城市代码获取该城市所有区县
     *
     * @param string $cityCode 城市代码
     * @return Collection 区县代码和信息的Collection对象
     */
    public function getAreasByCityCode(string $cityCode): Collection
    {
        return $this->areasService->getAreasByCityCode($cityCode);
    }

    /**
     * 根据省份代码获取该省所有区县
     *
     * @param string $provinceCode 省份代码
     * @return Collection 区县代码和信息的Collection对象
     */
    public function getAreasByProvinceCode(string $provinceCode): Collection
    {
        return $this->areasService->getAreasByProvinceCode($provinceCode);
    }

    /**
     * 根据省份代码获取省份名称
     *
     * @param string $code 省份代码
     * @return string|null 省份名称，不存在时返回null
     */
    public function getProvinceName(string $code): ?string
    {
        $province = $this->provincesService->getProvinceByCode($code);
        return $province ? $province->getName() : null;
    }

    /**
     * 根据省份代码获取省份实例
     *
     * @param string $code 省份代码
     * @return Province|null 省份实例，不存在时返回null
     */
    public function getProvinceByCode(string $code): ?Province
    {
        return $this->provincesService->getProvinceByCode($code);
    }

    /**
     * 根据拼音搜索省份
     *
     * @param string $pinyin 拼音字符串
     * @return Collection 匹配的省份Collection对象
     */
    public function getProvincesByPinyin(string $pinyin): Collection
    {
        return $this->provincesService->getProvincesByPinyin($pinyin);
    }

    /**
     * 根据拼音搜索城市
     *
     * @param string $pinyin 拼音字符串
     * @param string|null $provinceCode 可选的省份代码，用于限制搜索范围
     * @return Collection 匹配的城市Collection对象
     */
    public function getCitiesByPinyin(string $pinyin, ?string $provinceCode = null): Collection
    {
        return $this->citiesService->getCitiesByPinyin($pinyin, $provinceCode);
    }

    /**
     * 根据拼音搜索区县
     *
     * @param string $pinyin 拼音字符串
     * @param string|null $cityCode 可选的城市代码，用于限制搜索范围
     * @param string|null $provinceCode 可选的省份代码，用于限制搜索范围
     * @return Collection 匹配的区县Collection对象
     */
    public function getAreasByPinyin(string $pinyin, ?string $cityCode = null, ?string $provinceCode = null): Collection
    {
        return $this->areasService->getAreasByPinyin($pinyin, $cityCode, $provinceCode);
    }

    public function getProvincesService(): Provinces
    {
        return $this->provincesService;
    }

    public function getCitiesService(): Cities
    {
        return $this->citiesService;
    }

    public function getAreasService(): Areas
    {
        return $this->areasService;
    }
}
