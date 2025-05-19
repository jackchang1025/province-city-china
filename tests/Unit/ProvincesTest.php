<?php

use Weijiajia\ProvinceCityChina\Provinces;
use Illuminate\Support\Collection;
use Weijiajia\ProvinceCityChina\Entities\Province;

beforeEach(function () {
    $this->provinces = Provinces::make();
});

describe('Provinces', function () {
    it('can get all provinces', function () {
        $provinces = $this->provinces->getProvinces();

        expect($provinces)->toBeInstanceOf(Collection::class);
        expect($provinces->isEmpty())->toBeFalse();
        expect($provinces->first())->toBeInstanceOf(Province::class);
    });

    it('can get province by code', function () {
        $province = $this->provinces->getProvinceByCode('110000'); // 北京市

        expect($province)->toBeInstanceOf(Province::class)
            ->and($province->getCode())->toBe('110000')
            ->and($province->getName())->toBe('北京市');

        $province = $this->provinces->getProvinceByCode('999999'); // 不存在的代码
        expect($province)->toBeNull();
    });

    it('can get province by name', function () {
        $province = $this->provinces->getProvinceByName('北京市');

        expect($province)->toBeInstanceOf(Province::class)
            ->and($province->getCode())->toBe('110000')
            ->and($province->getName())->toBe('北京市');

        $province = $this->provinces->getProvinceByName('不存在的省份');
        expect($province)->toBeNull();
    });

    it('can search provinces by pinyin', function () {
        // 模糊搜索
        $results = $this->provinces->getProvincesByPinyin('bei');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();
        expect($results->contains(fn(Province $p) => $p->getCode() === '110000'))->toBeTrue(); // 北京市

        // 较精确的模糊搜索 (全拼)
        $results = $this->provinces->getProvincesByPinyin('beijing'); // 假设数据中是 beijing
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();
        expect($results->contains(fn(Province $p) => $p->getCode() === '110000'))->toBeTrue(); // 北京市

        // 搜索带 'shi' 的全拼 (假设数据中的拼音不包含 'shi')
        $results = $this->provinces->getProvincesByPinyin('beijingshi');
        expect($results->contains(fn(Province $p) => $p->getCode() === '110000'))->toBeTrue(); // 依然能匹配到 beijing

        // 搜索不存在的拼音
        $results = $this->provinces->getProvincesByPinyin('notexist');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeTrue();
    });
});
