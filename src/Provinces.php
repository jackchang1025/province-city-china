<?php

namespace Weijiajia\ProvinceCityChina;

use Illuminate\Support\Collection;
use Weijiajia\ProvinceCityChina\Entities\Province;

class Provinces
{
    use Makeable;

    protected ?Collection $provinces = null;

    /**
     * 构造函数
     *
     * @param string $path 数据文件路径
     */
    public function __construct(
        protected string $path = __DIR__ . '/Resources/data/divisions.php'
    ) {

        if (!file_exists($this->path)) {
            throw new \Exception("Data file not found at path: {$this->path}");
        }
    }


    /**
     * 获取所有省份数据
     *
     * @return Collection 省份代码和信息的关联数组或Collection对象
     */
    public function getProvinces(): Collection
    {
        if ($this->provinces === null) {

            $data = require $this->path;

            $this->provinces = collect($data['provinces'])->map(fn(array $info, string $code) => Province::fromArray((string) $code, $info));
        }

        return $this->provinces;
    }

    /**
     * 根据省份代码获取省份实例
     *
     * @param string $code 省份代码
     * @return Province|null 省份实例，不存在时返回null
     */
    public function getProvinceByCode(string $code): ?Province
    {
        return $this->getProvinces()->first(fn(Province $province) => $province->getCode() === $code);
    }

    /**
     * 根据省份名称获取省份实例
     *
     * @param string $name 省份名称
     * @return Province|null 省份实例，不存在时返回null
     */
    public function getProvinceByName(string $name): ?Province
    {
        return $this->getProvinces()->first(fn(Province $province) => $province->getName() === $name);
    }


    /**
     * 根据拼音搜索省份
     *
     * @param string $pinyin 拼音字符串
     * @return Collection 匹配的省份数组或Collection对象
     */
    public function getProvincesByPinyin(string $pinyin): Collection
    {
        return $this->getProvinces()
            ->filter(fn(Province $province) => str_contains(haystack: strtolower($province->getPinyin()), needle: strtolower($pinyin)));
    }
}
