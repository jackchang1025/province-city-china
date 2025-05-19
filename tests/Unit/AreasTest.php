<?php

use Weijiajia\ProvinceCityChina\Areas;
use Illuminate\Support\Collection;
use Weijiajia\ProvinceCityChina\Entities\Area;

beforeEach(function () {
    $this->areas = Areas::make();
});

describe('Areas', function () {
    it('can get all areas', function () {
        $areas = $this->areas->getAreas();

        expect($areas)->toBeInstanceOf(Collection::class);
        expect($areas->isEmpty())->toBeFalse();
        expect($areas->first())->toBeInstanceOf(Area::class);

        // 检查南山区是否存在于集合中
        expect($areas->contains(fn(Area $area) => $area->getCode() === '440305'))->toBeTrue();
        $nanshan = $areas->first(fn(Area $area) => $area->getCode() === '440305');
        expect($nanshan->getName())->toBe('南山区');
        expect($nanshan->getCityCode())->toBe('440300'); // 深圳市
        expect($nanshan->getProvinceCode())->toBe('440000'); // 广东省
    });

    it('can get area by code', function () {
        $area = $this->areas->getAreasByCode('440305'); // 南山区
        expect($area)->toBeInstanceOf(Area::class)
            ->and($area->getName())->toBe('南山区');

        $area = $this->areas->getAreasByCode('440306'); // 宝安区
        expect($area)->toBeInstanceOf(Area::class)
            ->and($area->getName())->toBe('宝安区');

        $area = $this->areas->getAreasByCode('999999'); // 不存在的代码
        expect($area)->toBeNull();
    });

    it('can get areas by city code', function () {
        // 深圳市的区县
        $areas = $this->areas->getAreasByCityCode('440300');
        expect($areas)->toBeInstanceOf(Collection::class);
        expect($areas->isEmpty())->toBeFalse();
        expect($areas->first())->toBeInstanceOf(Area::class);
        expect($areas->contains(fn(Area $area) => $area->getCode() === '440305'))->toBeTrue(); // 南山区
        expect($areas->contains(fn(Area $area) => $area->getCode() === '440306'))->toBeTrue(); // 宝安区

        // 不存在的城市代码
        $areas = $this->areas->getAreasByCityCode('999999');
        expect($areas)->toBeInstanceOf(Collection::class);
        expect($areas->isEmpty())->toBeTrue();
    });

    it('can get areas by province code', function () {
        // 广东省的区县
        $areas = $this->areas->getAreasByProvinceCode('440000');
        expect($areas)->toBeInstanceOf(Collection::class);
        expect($areas->isEmpty())->toBeFalse();
        expect($areas->first())->toBeInstanceOf(Area::class);

        // 深圳市的区县应该在广东省的区县中
        expect($areas->contains(fn(Area $area) => $area->getCode() === '440305'))->toBeTrue(); // 南山区
        expect($areas->contains(fn(Area $area) => $area->getCode() === '440306'))->toBeTrue(); // 宝安区

        // 广州市的区县也应该在广东省的区县中
        expect($areas->contains(fn(Area $area) => $area->getCode() === '440103'))->toBeTrue(); // 荔湾区

        // 不存在的省份代码
        $areas = $this->areas->getAreasByProvinceCode('999999');
        expect($areas)->toBeInstanceOf(Collection::class);
        expect($areas->isEmpty())->toBeTrue();
    });

    it('can search areas by pinyin', function () {
        // 模糊搜索
        $results = $this->areas->getAreasByPinyin('nan');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();
        expect($results->contains(fn(Area $area) => $area->getName() === '南山区' && $area->getCityCode() === '440300'))->toBeTrue();

        // 限定城市的模糊搜索
        $results = $this->areas->getAreasByPinyin('nan', '440300');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();
        expect($results->contains(fn(Area $area) => $area->getCode() === '440305'))->toBeTrue(); // 南山区

        // 限定省份的模糊搜索
        $results = $this->areas->getAreasByPinyin('nan', null, '440000');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();
        expect($results->contains(fn(Area $area) => $area->getName() === '南山区' && $area->getCityCode() === '440300'))->toBeTrue(); // 南山区应在结果中

        // 同时限定城市和省份的模糊搜索
        $results = $this->areas->getAreasByPinyin('nan', '440300', '440000');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();
        expect($results->contains(fn(Area $area) => $area->getCode() === '440305'))->toBeTrue(); // 南山区

        // 较精确的模糊搜索 (全拼)
        $results = $this->areas->getAreasByPinyin('nanshan'); // 假设数据中是 nanshan
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();
        expect($results->contains(fn(Area $area) => $area->getCode() === '440305'))->toBeTrue(); // 南山区

        // 搜索带 'qu' 的全拼 (假设数据中的拼音不包含 'qu')
        $results = $this->areas->getAreasByPinyin('nanshanqu');
        expect($results->contains(fn(Area $area) => $area->getCode() === '440305'))->toBeTrue(); // 依然能匹配到 nanshan

        // 搜索不存在的拼音
        $results = $this->areas->getAreasByPinyin('notexist');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeTrue();
    });
});
