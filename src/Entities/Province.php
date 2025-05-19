<?php

namespace Weijiajia\ProvinceCityChina\Entities;

use Illuminate\Support\Collection;
use Weijiajia\ProvinceCityChina\ChinaDivisions;
class Province
{
    /**
     * 创建一个新的省份实例
     *
     * @param string $code 省份代码
     * @param string $name 省份名称
     * @param string $pinyin 省份拼音
     */
    public function __construct(
        protected string $code,
        protected string $name,
        protected string $pinyin
    ) {}

    /**
     * 获取省份代码
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * 获取省份名称
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 获取省份拼音
     *
     * @return string
     */
    public function getPinyin(): string
    {
        return $this->pinyin;
    }

    /**
     * 获取该省份下的所有城市
     *
     * @return Collection
     */
    public function getCities(): Collection
    {
        return ChinaDivisions::make()->getCitiesService()->getCitiesByProvinceCode($this->code);
    }

    /**
     * 从原始数据创建省份实例
     *
     * @param string $code 省份代码
     * @param array $data 省份数据
     * @return self
     */
    public static function fromArray(string $code, array $data): self
    {
        return new self(
            $code,
            $data['name'],
            $data['pinyin']
        );
    }

    /**
     * 将省份转换为数组
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'pinyin' => $this->pinyin,
        ];
    }
}
