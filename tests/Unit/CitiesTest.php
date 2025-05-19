<?php

use Weijiajia\ProvinceCityChina\Cities;
use Illuminate\Support\Collection;
use Weijiajia\ProvinceCityChina\Entities\City;

beforeEach(function () {
    $this->cities = Cities::make();
});

describe('Cities', function () {
    it('can get all cities', function () {
        $cities = $this->cities->getCities();

        expect($cities)->toBeInstanceOf(Collection::class);
        expect($cities->isEmpty())->toBeFalse();
        expect($cities->first())->toBeInstanceOf(City::class);
    });

    it('can get city by code', function () {
        $city = $this->cities->getCityByCode('440100'); // 广州市
        expect($city)->toBeInstanceOf(City::class)
            ->and($city->getName())->toBe('广州市');

        $city = $this->cities->getCityByCode('440300'); // 深圳市
        expect($city)->toBeInstanceOf(City::class)
            ->and($city->getName())->toBe('深圳市');

        $city = $this->cities->getCityByCode('999999'); // 不存在的代码
        expect($city)->toBeNull();
    });

    it('can get cities by province code', function () {
        // 广东省的城市
        $cities = $this->cities->getCitiesByProvinceCode('440000');
        expect($cities)->toBeInstanceOf(Collection::class);
        expect($cities->isEmpty())->toBeFalse();
        expect($cities->first())->toBeInstanceOf(City::class);
        expect($cities->contains(fn(City $city) => $city->getCode() === '440100'))->toBeTrue(); // 广州市

        // 北京市的城市（直辖市） - 根据数据结构，这里应该返回空集合
        $cities = $this->cities->getCitiesByProvinceCode('110000');
        expect($cities)->toBeInstanceOf(Collection::class);
        expect($cities->isEmpty())->toBeTrue(); // 直辖市在此层级没有城市条目

        // 不存在的省份代码
        $cities = $this->cities->getCitiesByProvinceCode('999999');
        expect($cities)->toBeInstanceOf(Collection::class);
        expect($cities->isEmpty())->toBeTrue();
    });

    it('can search cities by pinyin', function () {
        // 模糊搜索
        $results = $this->cities->getCitiesByPinyin('guang');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();
        expect($results->contains(fn(City $city) => $city->getCode() === '440100'))->toBeTrue(); // 广州市

        // 限定省份的模糊搜索
        $results = $this->cities->getCitiesByPinyin('guang', '440000');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();
        expect($results->contains(fn(City $city) => $city->getCode() === '440100'))->toBeTrue(); // 广州市

        // 较精确的模糊搜索 (全拼)
        $results = $this->cities->getCitiesByPinyin('guangzhou');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();
        expect($results->contains(fn(City $city) => $city->getCode() === '440100'))->toBeTrue(); // 广州市

        // 搜索带 'shi' 的全拼 (假设数据中的拼音不包含 'shi')
        // 如果数据中的 pinyin 字段就是 'guangzhou' 而不是 'guangzhoushi'
        $results = $this->cities->getCitiesByPinyin('guangzhoushi');
        // 这个断言取决于您的 pinyin 数据是否包含 'shi'
        // 如果不包含 'shi'，且是模糊搜索，可能依然能匹配到 'guangzhou'
        // 如果希望严格匹配 'guangzhoushi' 而数据中没有，则应为空
        // 假设 'guangzhou' 是数据中的拼音，那么 'guangzhoushi' 仍然可以模糊匹配到
        expect($results->contains(fn(City $city) => $city->getCode() === '440100'))->toBeTrue();

        // 搜索不存在的拼音
        $results = $this->cities->getCitiesByPinyin('notexist');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeTrue();
    });
});
