<?php

namespace Weijiajia\ProvinceCityChina\Entities;

use Weijiajia\ProvinceCityChina\ChinaDivisions;
class Area
{
    /**
     * 创建一个新的区县实例
     *
     * @param string $code 区县代码
     * @param string $name 区县名称
     * @param string $pinyin 区县拼音
     * @param string $cityCode 所属城市代码
     * @param string $provinceCode 所属省份代码
     */
    public function __construct(
        protected string $code,
        protected string $name,
        protected string $pinyin,
        protected string $cityCode,
        protected string $provinceCode
    ) {}

    /**
     * 获取区县代码
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * 获取区县名称
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 获取区县拼音
     *
     * @return string
     */
    public function getPinyin(): string
    {
        return $this->pinyin;
    }

    /**
     * 获取所属城市代码
     *
     * @return string
     */
    public function getCityCode(): string
    {
        return $this->cityCode;
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
     * 获取所属城市
     *
     * @return City|null
     */
    public function getCity(): ?City
    {
        return ChinaDivisions::make()->getCitiesService()->getCityByCode($this->cityCode);
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
     * 从原始数据创建区县实例
     *
     * @param string $code 区县代码
     * @param array $data 区县数据
     * @return self
     */
    public static function fromArray(string $code, array $data): self
    {
        return new self(
            $code,
            $data['name'],
            $data['pinyin'],
            $data['city_code'],
            $data['province_code']
        );
    }

    /**
     * 将区县转换为数组
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'pinyin' => $this->pinyin,
            'city_code' => $this->cityCode,
            'province_code' => $this->provinceCode,
        ];
    }
}
