<?php

namespace Weijiajia\ProvinceCityChina;

use Illuminate\Support\Collection;
use Weijiajia\ProvinceCityChina\Entities\Area;

class Areas
{
    use Makeable;
    protected ?Collection $allAreasCollection = null;

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
     * 获取所有区县数据
     *
     * @return Collection 区县代码和信息的Collection对象
     */
    public function getAreas(): Collection
    {
        if ($this->allAreasCollection === null) {
            $data = require $this->path;
            $this->allAreasCollection = collect($data['areas'] ?? [])
                ->map(fn($info, $code) => Area::fromArray((string)$code, $info));
        }
        return $this->allAreasCollection;
    }

    /**
     * 根据区县代码获取区县实例
     *
     * @param string $code 区县代码
     * @return Area|null 区县实例，不存在时返回null
     */
    public function getAreasByCode(string $code): ?Area
    {
        return $this->getAreas()->first(fn(Area $area) => $area->getCode() === $code);
    }

    /**
     * 根据省份代码获取区县列表
     *
     * @param string $provinceCode 省份代码
     * @return Collection 该省份下的区县Collection对象
     */
    public function getAreasByProvinceCode(string $provinceCode): Collection
    {
        return $this->getAreas()
            ->filter(fn(Area $area) => $area->getProvinceCode() === $provinceCode);
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
        $pinyinLower = strtolower($pinyin);

        return $this->getAreas()
            ->filter(function (Area $area) use ($pinyinLower, $cityCode, $provinceCode) {
                if ($cityCode !== null && $area->getCityCode() !== $cityCode) {
                    return false;
                }
                if ($provinceCode !== null && $area->getProvinceCode() !== $provinceCode) {
                    return false;
                }
                return str_contains(haystack: strtolower($area->getPinyin()), needle: $pinyinLower);
            });
    }

    /**
     * 根据城市代码获取该城市所有区县
     *
     * @param string $cityCode 城市代码
     * @return Collection 区县代码和信息的Collection对象
     */
    public function getAreasByCityCode(string $cityCode): Collection
    {
        return $this->getAreas()->filter(fn(Area $area) => $area->getCityCode() === $cityCode);
    }
}
