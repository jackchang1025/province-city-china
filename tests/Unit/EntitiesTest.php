<?php

use Weijiajia\ProvinceCityChina\ChinaDivisions;
use Weijiajia\ProvinceCityChina\Entities\Province;
use Weijiajia\ProvinceCityChina\Entities\City;
use Weijiajia\ProvinceCityChina\Entities\Area;
use Illuminate\Support\Collection;

beforeEach(function () {
    $this->chinaDivisions = ChinaDivisions::make();
});

describe('Entities', function () {
    it('can get provinces as collection', function () {

        $provinces = ChinaDivisions::getProvincesss();
        $provinces = $this->chinaDivisions->getProvinces();

        expect($provinces)->toBeInstanceOf(Collection::class);
        expect($provinces->isEmpty())->toBeFalse();

        $firstProvince = $provinces->first();
        expect($firstProvince)->toBeInstanceOf(Province::class);

        // 检查北京市是否存在
        $beijing = $provinces->first(function (Province $province) {
            return $province->getCode() === '110000';
        });

        expect($beijing)->not->toBeNull();
        expect($beijing->getName())->toBe('北京市');
        expect($beijing->getPinyin())->toBe('beijingshi');
        $beijingCities = $beijing->getCities();
        expect($beijingCities)->toBeInstanceOf(Collection::class)->and($beijingCities->isEmpty())->toBeTrue();
    });

    it('can get cities as collection', function () {
        $cities = $this->chinaDivisions->getCities();

        expect($cities)->toBeInstanceOf(Collection::class);
        expect($cities->isEmpty())->toBeFalse();

        $firstCity = $cities->first();
        expect($firstCity)->toBeInstanceOf(City::class);

        // 检查深圳市是否存在
        $shenzhen = $cities->first(function (City $city) {
            return $city->getCode() === '440300';
        });

        expect($shenzhen)->not->toBeNull();
        expect($shenzhen->getName())->toBe('深圳市');
        expect($shenzhen->getPinyin())->toBe('shenzhenshi');
        expect($shenzhen->getProvinceCode())->toBe('440000');
    });

    it('can get areas as collection', function () {
        $areas = $this->chinaDivisions->getAreas();

        expect($areas)->toBeInstanceOf(Collection::class);
        expect($areas->isEmpty())->toBeFalse();

        $firstArea = $areas->first();
        expect($firstArea)->toBeInstanceOf(Area::class);

        // 检查南山区是否存在
        $nanshan = $areas->first(function (Area $area) {
            return $area->getCode() === '440305';
        });

        expect($nanshan)->not->toBeNull();
        expect($nanshan->getName())->toBe('南山区');
        expect($nanshan->getPinyin())->toBe('nanshanqu');
        expect($nanshan->getCityCode())->toBe('440300');
        expect($nanshan->getProvinceCode())->toBe('440000');
    });

    it('can get cities by province code', function () {
        $cities = $this->chinaDivisions->getCitiesByProvinceCode('440000');

        expect($cities)->toBeInstanceOf(Collection::class);
        expect($cities->isEmpty())->toBeFalse();

        $firstCity = $cities->first();
        expect($firstCity)->toBeInstanceOf(City::class);

        // 检查所有城市的省份代码是否正确
        $cities->each(function (City $city) {
            expect($city->getProvinceCode())->toBe('440000');
        });
    });

    it('can get areas by city code', function () {
        $areas = $this->chinaDivisions->getAreasByCityCode('440300');

        expect($areas)->toBeInstanceOf(Collection::class);
        expect($areas->isEmpty())->toBeFalse();

        $firstArea = $areas->first();
        expect($firstArea)->toBeInstanceOf(Area::class);

        // 检查所有区县的城市代码是否正确
        $areas->each(function (Area $area) {
            expect($area->getCityCode())->toBe('440300');
        });
    });

    it('can get province by code', function () {
        $province = $this->chinaDivisions->getProvinceByCode('440000');

        expect($province)->toBeInstanceOf(Province::class);
        expect($province->getName())->toBe('广东省');
        expect($province->getPinyin())->toBe('guangdongsheng');
    });

    it('can get city by code', function () {
        $city = $this->chinaDivisions->getCitiesService()->getCityByCode('440300');

        expect($city)->toBeInstanceOf(City::class);
        expect($city->getName())->toBe('深圳市');
        expect($city->getPinyin())->toBe('shenzhenshi');
        expect($city->getProvinceCode())->toBe('440000');
    });

    it('can get area by code', function () {
        $area = $this->chinaDivisions->getAreasService()->getAreasByCode('440305');

        expect($area)->toBeInstanceOf(Area::class);
        expect($area->getName())->toBe('南山区');
        expect($area->getPinyin())->toBe('nanshanqu');
        expect($area->getCityCode())->toBe('440300');
        expect($area->getProvinceCode())->toBe('440000');
    });

    it('can get cities from province', function () {
        // Test with a regular province (e.g., Guangdong)
        $provinceGuangdong = $this->chinaDivisions->getProvinceByCode('440000'); // 广东省
        expect($provinceGuangdong)->toBeInstanceOf(Province::class);

        $citiesFromGuangdong = $provinceGuangdong->getCities();
        expect($citiesFromGuangdong)->toBeInstanceOf(Collection::class)
            ->and($citiesFromGuangdong->isEmpty())->toBeFalse();

        $firstCityFromGuangdong = $citiesFromGuangdong->first();
        expect($firstCityFromGuangdong)->toBeInstanceOf(City::class);

        // Check if all cities from Guangdong have the correct province code
        // Re-fetch provinceGuangdong to ensure it's in scope if linter is confused
        $gdProvince = $this->chinaDivisions->getProvinceByCode('440000');
        $citiesFromGuangdong->each(function (City $city) use ($gdProvince) {
            expect($city->getProvinceCode())->toBe($gdProvince->getCode());
        });

        // Test with a municipality (e.g., Beijing)
        $provinceBeijing = $this->chinaDivisions->getProvinceByCode('110000'); // 北京市
        expect($provinceBeijing)->toBeInstanceOf(Province::class);

        $citiesFromBeijing = $provinceBeijing->getCities();
        expect($citiesFromBeijing)->toBeInstanceOf(Collection::class)
            ->and($citiesFromBeijing->isEmpty())->toBeTrue(); // Municipalities have no cities at this level
    });

    it('can get areas from city', function () {
        $city = $this->chinaDivisions->getCitiesService()->getCityByCode('440300');
        $areas = $city->getAreas();

        expect($areas)->toBeInstanceOf(Collection::class);
        expect($areas->isEmpty())->toBeFalse();

        $firstArea = $areas->first();
        expect($firstArea)->toBeInstanceOf(Area::class);

        // 检查所有区县的城市代码是否正确
        $areas->each(function (Area $area) use ($city) {
            expect($area->getCityCode())->toBe($city->getCode());
        });
    });

    it('can get province from city', function () {
        $city = $this->chinaDivisions->getCitiesService()->getCityByCode('440300');
        $province = $city->getProvince();

        expect($province)->toBeInstanceOf(Province::class);
        expect($province->getCode())->toBe($city->getProvinceCode());
    });

    it('can get city and province from area', function () {
        $area = $this->chinaDivisions->getAreasService()->getAreasByCode('440305');

        $city = $area->getCity();
        expect($city)->toBeInstanceOf(City::class);
        expect($city->getCode())->toBe($area->getCityCode());

        $province = $area->getProvince();
        expect($province)->toBeInstanceOf(Province::class);
        expect($province->getCode())->toBe($area->getProvinceCode());
    });

    it('can search provinces by pinyin', function () {
        $provinces = $this->chinaDivisions->getProvincesByPinyin('guang');

        expect($provinces)->toBeInstanceOf(Collection::class);
        expect($provinces->isEmpty())->toBeFalse();

        $firstProvince = $provinces->first();
        expect($firstProvince)->toBeInstanceOf(Province::class);

        // 检查所有省份的拼音是否包含搜索字符串
        $provinces->each(function (Province $province) {
            expect(str_contains(strtolower($province->getPinyin()), 'guang'))->toBeTrue();
        });
    });

    it('can search cities by pinyin', function () {
        $cities = $this->chinaDivisions->getCitiesByPinyin('shen');

        expect($cities)->toBeInstanceOf(Collection::class);
        expect($cities->isEmpty())->toBeFalse();

        $firstCity = $cities->first();
        expect($firstCity)->toBeInstanceOf(City::class);

        // 检查所有城市的拼音是否包含搜索字符串
        $cities->each(function (City $city) {
            expect(str_contains(strtolower($city->getPinyin()), 'shen'))->toBeTrue();
        });
    });

    it('can search areas by pinyin', function () {
        $areas = $this->chinaDivisions->getAreasByPinyin('nan');

        expect($areas)->toBeInstanceOf(Collection::class);
        expect($areas->isEmpty())->toBeFalse();

        $firstArea = $areas->first();
        expect($firstArea)->toBeInstanceOf(Area::class);

        // 检查所有区县的拼音是否包含搜索字符串
        $areas->each(function (Area $area) {
            expect(str_contains(strtolower($area->getPinyin()), 'nan'))->toBeTrue();
        });
    });
});
