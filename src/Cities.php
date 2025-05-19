<?php

namespace Weijiajia\ProvinceCityChina;

use Illuminate\Support\Collection;
use Weijiajia\ProvinceCityChina\Entities\City;

class Cities
{
    use Makeable;
    protected ?Collection $allCitiesCollection = null;

    /**
     * 构造函数
     *
     * @param string $path 数据文件路径
     * @throws \Exception 如果数据文件不存在
     */
    public function __construct(
        protected string $path = __DIR__ . '/Resources/data/divisions.php'
    ) {
        if (!file_exists($this->path)) {
            throw new \Exception("Data file not found at path: {$this->path}");
        }
    }

    /**
     * 获取所有城市数据
     *
     * @return Collection 城市代码和信息的Collection对象
     */
    public function getCities(): Collection
    {
        if ($this->allCitiesCollection === null) {
            $data = require $this->path;
            $this->allCitiesCollection = collect($data['cities'] ?? [])
                ->map(fn($info, $code) => City::fromArray((string)$code, $info));
        }
        return $this->allCitiesCollection;
    }

    /**
     * 根据城市代码获取城市实例
     *
     * @param string $code 城市代码
     * @return City|null 城市实例，不存在时返回null
     */
    public function getCityByCode(string $code): ?City
    {
        return $this->getCities()->first(fn(City $city) => $city->getCode() === $code);
    }

    /**
     * 根据省份代码获取该省份所有城市
     *
     * @param string $provinceCode 省份代码
     * @return Collection 城市代码和信息的Collection对象
     */
    public function getCitiesByProvinceCode(string $provinceCode): Collection
    {
        return $this->getCities()->filter(fn(City $city) => $city->getProvinceCode() === $provinceCode);
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
        $pinyinLower = strtolower($pinyin);
        return $this->getCities()
            ->filter(function (City $city) use ($pinyinLower, $provinceCode) {
                if ($provinceCode !== null && $city->getProvinceCode() !== $provinceCode) {
                    return false;
                }
                return str_contains(haystack: strtolower($city->getPinyin()), needle: $pinyinLower);
            });
    }
}
