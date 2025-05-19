<?php

use Weijiajia\ProvinceCityChina\ChinaDivisions;
use Illuminate\Support\Collection;
use Weijiajia\ProvinceCityChina\Entities\Area;
use Weijiajia\ProvinceCityChina\Entities\City;
use Weijiajia\ProvinceCityChina\Entities\Province;

beforeEach(function () {
    $this->chinaDivisions = ChinaDivisions::make();
});

describe('ChinaDivisions', function () {
    it('can get all provinces', function () {
        $provinces = $this->chinaDivisions->getProvinces();

        expect($provinces)->toBeInstanceOf(Collection::class);
        expect($provinces->isEmpty())->toBeFalse();
        expect($provinces->first())->toBeInstanceOf(Province::class);
        expect($provinces->contains(fn(Province $p) => $p->getCode() === '110000'))->toBeTrue(); // 北京市
        expect($provinces->contains(fn(Province $p) => $p->getCode() === '440000'))->toBeTrue(); // 广东省
    });

    it('can get all cities', function () {
        $cities = $this->chinaDivisions->getCities();

        expect($cities)->toBeInstanceOf(Collection::class);
        expect($cities->isEmpty())->toBeFalse();
        expect($cities->first())->toBeInstanceOf(City::class);
        expect($cities->contains(fn(City $c) => $c->getCode() === '440100'))->toBeTrue(); // 广州市
        expect($cities->contains(fn(City $c) => $c->getCode() === '440300'))->toBeTrue(); // 深圳市
    });

    it('can get all areas', function () {
        $areas = $this->chinaDivisions->getAreas();

        expect($areas)->toBeInstanceOf(Collection::class);
        expect($areas->isEmpty())->toBeFalse();
        expect($areas->first())->toBeInstanceOf(Area::class);
        expect($areas->contains(fn(Area $a) => $a->getCode() === '440305'))->toBeTrue(); // 南山区
    });

    it('can get cities by province code', function () {
        $cities = $this->chinaDivisions->getCitiesByProvinceCode('440000');

        expect($cities)->toBeInstanceOf(Collection::class);
        expect($cities->isEmpty())->toBeFalse();
        expect($cities->first())->toBeInstanceOf(City::class);
        expect($cities->contains(fn(City $c) => $c->getCode() === '440100'))->toBeTrue(); // 广州市
        expect($cities->contains(fn(City $c) => $c->getCode() === '440300'))->toBeTrue(); // 深圳市

        // 测试不存在的省份代码
        $cities = $this->chinaDivisions->getCitiesByProvinceCode('999999');
        expect($cities)->toBeInstanceOf(Collection::class);
        expect($cities->isEmpty())->toBeTrue();
    });

    it('can get areas by city code', function () {
        $areas = $this->chinaDivisions->getAreasByCityCode('440300');

        expect($areas)->toBeInstanceOf(Collection::class);
        expect($areas->isEmpty())->toBeFalse();
        expect($areas->first())->toBeInstanceOf(Area::class);
        expect($areas->contains(fn(Area $a) => $a->getCode() === '440305'))->toBeTrue(); // 南山区
        expect($areas->contains(fn(Area $a) => $a->getCode() === '440306'))->toBeTrue(); // 宝安区

        // 测试不存在的城市代码
        $areas = $this->chinaDivisions->getAreasByCityCode('999999');
        expect($areas)->toBeInstanceOf(Collection::class);
        expect($areas->isEmpty())->toBeTrue();
    });

    it('can get areas by province code', function () {
        $areas = $this->chinaDivisions->getAreasByProvinceCode('440000');

        expect($areas)->toBeInstanceOf(Collection::class);
        expect($areas->isEmpty())->toBeFalse();
        expect($areas->first())->toBeInstanceOf(Area::class);
        expect($areas->contains(fn(Area $a) => $a->getCode() === '440305'))->toBeTrue(); // 南山区 (深圳市)
        expect($areas->contains(fn(Area $a) => $a->getCode() === '440103'))->toBeTrue(); // 荔湾区 (广州市)

        // 测试不存在的省份代码
        $areas = $this->chinaDivisions->getAreasByProvinceCode('999999');
        expect($areas)->toBeInstanceOf(Collection::class);
        expect($areas->isEmpty())->toBeTrue();
    });

    it('can get province name by code', function () {
        $province = $this->chinaDivisions->getProvinceByCode('110000');
        expect($province)->toBeInstanceOf(Province::class);
        expect($province->getName())->toBe('北京市');

        $province = $this->chinaDivisions->getProvinceByCode('440000');
        expect($province)->toBeInstanceOf(Province::class);
        expect($province->getName())->toBe('广东省');

        $province = $this->chinaDivisions->getProvinceByCode('999999');
        expect($province)->toBeNull();
    });

    it('can get city name by code', function () {
        $city = $this->chinaDivisions->getCitiesService()->getCityByCode('440100');
        expect($city)->toBeInstanceOf(City::class);
        expect($city->getName())->toBe('广州市');

        $city = $this->chinaDivisions->getCitiesService()->getCityByCode('440300');
        expect($city)->toBeInstanceOf(City::class);
        expect($city->getName())->toBe('深圳市');

        $city = $this->chinaDivisions->getCitiesService()->getCityByCode('999999');
        expect($city)->toBeNull();
    });

    it('can get area name by code', function () {
        $area = $this->chinaDivisions->getAreasService()->getAreasByCode('440305');
        expect($area)->toBeInstanceOf(Area::class);
        expect($area->getName())->toBe('南山区');

        $area = $this->chinaDivisions->getAreasService()->getAreasByCode('440306');
        expect($area)->toBeInstanceOf(Area::class);
        expect($area->getName())->toBe('宝安区');

        $area = $this->chinaDivisions->getAreasService()->getAreasByCode('999999');
        expect($area)->toBeNull();
    });

    it('can search provinces by pinyin', function () {
        $results = $this->chinaDivisions->getProvincesService()->getProvincesByPinyin('bei');

        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();
        expect($results->contains(fn(Province $p) => $p->getCode() === '110000'))->toBeTrue(); // 北京市

        // 测试不存在的拼音
        $results = $this->chinaDivisions->getProvincesService()->getProvincesByPinyin('notexist');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeTrue();
    });

    it('can search cities by pinyin', function () {
        // 不限定省份
        $results = $this->chinaDivisions->getCitiesService()->getCitiesByPinyin('guang');

        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();
        expect($results->contains(fn(City $c) => $c->getCode() === '440100'))->toBeTrue(); // 广州市

        // 限定省份
        $results = $this->chinaDivisions->getCitiesService()->getCitiesByPinyin('guang', '440000');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();
        expect($results->contains(fn(City $c) => $c->getCode() === '440100'))->toBeTrue(); // 广州市

        // 测试不存在的拼音
        $results = $this->chinaDivisions->getCitiesService()->getCitiesByPinyin('notexist');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeTrue();

        // 测试不存在的省份代码
        $results = $this->chinaDivisions->getCitiesService()->getCitiesByPinyin('guang', '999999');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeTrue();
    });

    it('can search areas by pinyin', function () {
        // 不限定城市和省份
        $results = $this->chinaDivisions->getAreasService()->getAreasByPinyin('nan');

        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();

        // 限定城市
        $results = $this->chinaDivisions->getAreasService()->getAreasByPinyin('nan', '440300');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();
        expect($results->contains(fn(Area $a) => $a->getCode() === '440305'))->toBeTrue(); // 南山区

        // 限定省份
        $results = $this->chinaDivisions->getAreasService()->getAreasByPinyin('nan', null, '440000');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();

        // 同时限定城市和省份
        $results = $this->chinaDivisions->getAreasService()->getAreasByPinyin('nan', '440300', '440000');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeFalse();
        expect($results->contains(fn(Area $a) => $a->getCode() === '440305'))->toBeTrue(); // 南山区

        // 测试不存在的拼音
        $results = $this->chinaDivisions->getAreasService()->getAreasByPinyin('notexist');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeTrue();

        // 测试不存在的城市代码
        $results = $this->chinaDivisions->getAreasService()->getAreasByPinyin('nan', '999999');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeTrue();

        // 测试不存在的省份代码
        $results = $this->chinaDivisions->getAreasService()->getAreasByPinyin('nan', null, '999999');
        expect($results)->toBeInstanceOf(Collection::class);
        expect($results->isEmpty())->toBeTrue();
    });

    it('can handle data integrity between provinces, cities and areas', function () {
        // 测试省份、城市、区县之间的数据完整性
        $provinces = $this->chinaDivisions->getProvincesService();
        $cities    = $this->chinaDivisions->getCitiesService(); // 获取所有城市用于查找省份代码

        // 随机选择一个省份
        $provinceCode = '440000'; // 广东省
        $province = $provinces->getProvinceByCode($provinceCode);
        expect($province)->toBeInstanceOf(Province::class);

        $provinceCities = $this->chinaDivisions->getCitiesService()->getCitiesByProvinceCode($provinceCode);

        // 检查该省份下的所有城市是否都有正确的省份代码
        $provinceCities->each(function (City $city) use ($provinceCode) {
            expect($city->getProvinceCode())->toBe($provinceCode);
        });

        // 随机选择一个城市
        $cityCode = '440300'; // 深圳市
        $city = $cities->getCityByCode($cityCode);
        expect($city)->toBeInstanceOf(City::class);

        $cityAreas = $this->chinaDivisions->getAreasService()->getAreasByCityCode($cityCode);

        // 检查该城市下的所有区县是否都有正确的城市代码和省份代码
        $cityAreas->each(function (Area $area) use ($cityCode, $city) {
            expect($area->getCityCode())->toBe($cityCode);
            expect($area->getProvinceCode())->toBe($city->getProvinceCode());
        });

        // 检查通过省份代码获取的区县是否包含该省份下所有城市的区县
        $provinceAreas = $this->chinaDivisions->getAreasService()->getAreasByProvinceCode($provinceCode);
        $totalAreasCount = 0;

        $provinceCities->each(function (City $currentCity) use ($provinceAreas, &$totalAreasCount) {
            $cityAreasForCount = $this->chinaDivisions->getAreasService()->getAreasByCityCode($currentCity->getCode());
            $totalAreasCount += $cityAreasForCount->count();

            // 检查每个城市的区县是否都包含在省份的区县中
            $cityAreasForCount->each(function (Area $area) use ($provinceAreas) {
                expect($provinceAreas->contains(fn(Area $pa) => $pa->getCode() === $area->getCode()))->toBeTrue();
            });
        });

        // 检查省份下的区县总数是否等于该省份下所有城市的区县总数
        // 注意：对于直辖市，其下的区县直接属于省，所以这个检查可能不适用于所有情况
        // 但对于普通省份（如广东省），这个检查应该是成立的
        if ($province->getCode() !== '110000' && $province->getCode() !== '120000' && $province->getCode() !== '310000' && $province->getCode() !== '500000') {
            expect($provinceAreas->count())->toBe($totalAreasCount);
        }
    });

    it('can handle special administrative regions', function () {
        // 测试特别行政区（如香港、澳门）
        $provinces = $this->chinaDivisions->getProvincesService();

        // 检查香港特别行政区
        $hk = $provinces->getProvinceByCode('810000');
        if ($hk) {
            expect($hk->getName())->toBe('香港特别行政区');
        }

        // 检查澳门特别行政区
        $macau = $provinces->getProvinceByCode('820000');
        if ($macau) {
            expect($macau->getName())->toBe('澳门特别行政区');
        }
    });

    it('can handle direct-administered municipalities', function () {
        // 测试直辖市（北京、上海、天津、重庆）
        $directMunicipalities = [
            '110000' => '北京市',
            '120000' => '天津市',
            '310000' => '上海市',
            '500000' => '重庆市'
        ];

        foreach ($directMunicipalities as $code => $name) {
            $code = (string)$code; // 确保 $code 是字符串类型
            $province = $this->chinaDivisions->getProvinceByCode($code);
            expect($province)->toBeInstanceOf(Province::class);
            expect($province->getName())->toBe($name);

            // 直辖市的区县直接关联到省级代码
            $areas = $this->chinaDivisions->getAreasService()->getAreasByProvinceCode($code);
            expect($areas)->toBeInstanceOf(Collection::class);
            expect($areas->isEmpty())->toBeFalse(); // 直辖市应该有区县

            // 随机检查一个区县的省份代码是否正确
            if (!$areas->isEmpty()) {
                /** @var Area $firstArea */
                $firstArea = $areas->first();
                expect($firstArea)->toBeInstanceOf(Area::class);
                expect($firstArea->getProvinceCode())->toBe($code);
            }
        }
    });
});
