<?php

namespace Weijiajia\ProvinceCityChina\Entities;

use Illuminate\Support\Collection;
use Weijiajia\ProvinceCityChina\ChinaDivisions;
class City
{
    /**
     * 创建一个新的城市实例
     *
     * @param string $code 城市代码
     * @param string $name 城市名称
     * @param string $pinyin 城市拼音
     * @param string $provinceCode 所属省份代码
     */
    public function __construct(
        protected string $code,
        protected string $name,
        protected string $pinyin,
        protected string $provinceCode
    ) {}

    /**
     * 获取城市代码
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * 获取城市名称
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 获取城市拼音
     *
     * @return string
     */
    public function getPinyin(): string
    {
        return $this->pinyin;
    }

    /**
     * 获取所属省份代码
     *
     * @return string
     */
    public function getProvinceCode(): string
    {
        return $this->provinceCode;
    }

    /**
     * 获取所属省份
     *
     * @return Province|null
     */
    public function getProvince(): ?Province
    {
        return ChinaDivisions::make()->getProvincesService()->getProvinceByCode($this->provinceCode);
    }

    /**
     * 获取该城市下的所有区县
     *
     * @return Collection
     */
    public function getAreas(): Collection
    {
        return ChinaDivisions::make()->getAreasService()->getAreasByCityCode($this->code);
    }

    /**
     * 从原始数据创建城市实例
     *
     * @param string $code 城市代码
     * @param array $data 城市数据
     * @return self
     */
    public static function fromArray(string $code, array $data): self
    {
        return new self(
            $code,
            $data['name'],
            $data['pinyin'],
            $data['province_code']
        );
    }

    /**
     * 将城市转换为数组
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'pinyin' => $this->pinyin,
            'province_code' => $this->provinceCode,
        ];
    }
}
