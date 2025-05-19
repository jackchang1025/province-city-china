<?php

use Weijiajia\ProvinceCityChina\ChinaDivisions;

describe('ChinaDivisions', function () {
    it('can get all provinces', function () {
        $provinces = ChinaDivisions::getProvinces();
        
        expect($provinces)->toBeArray();
        expect($provinces)->not->toBeEmpty();
        expect($provinces)->toHaveKey('110000'); // 北京市
        expect($provinces)->toHaveKey('440000'); // 广东省
    });
    
    it('can get all cities', function () {
        $cities = ChinaDivisions::getCities();
        
        expect($cities)->toBeArray();
        expect($cities)->not->toBeEmpty();
        expect($cities)->toHaveKey('440100'); // 广州市
        expect($cities)->toHaveKey('440300'); // 深圳市
    });
    
    it('can get all areas', function () {
        $areas = ChinaDivisions::getAreas();
        
        expect($areas)->toBeArray();
        expect($areas)->not->toBeEmpty();
        expect($areas)->toHaveKey('440305'); // 南山区
    });
    
    it('can get cities by province code', function () {
        $cities = ChinaDivisions::getCitiesByProvinceCode('440000');
        
        expect($cities)->toBeArray();
        expect($cities)->not->toBeEmpty();
        expect($cities)->toHaveKey('440100'); // 广州市
        expect($cities)->toHaveKey('440300'); // 深圳市
        
        // 测试不存在的省份代码
        $cities = ChinaDivisions::getCitiesByProvinceCode('999999');
        expect($cities)->toBeArray();
        expect($cities)->toBeEmpty();
    });
    
    it('can get areas by city code', function () {
        $areas = ChinaDivisions::getAreasByCityCode('440300');
        
        expect($areas)->toBeArray();
        expect($areas)->not->toBeEmpty();
        expect($areas)->toHaveKey('440305'); // 南山区
        expect($areas)->toHaveKey('440306'); // 宝安区
        
        // 测试不存在的城市代码
        $areas = ChinaDivisions::getAreasByCityCode('999999');
        expect($areas)->toBeArray();
        expect($areas)->toBeEmpty();
    });
    
    it('can get areas by province code', function () {
        $areas = ChinaDivisions::getAreasByProvinceCode('440000');
        
        expect($areas)->toBeArray();
        expect($areas)->not->toBeEmpty();
        expect($areas)->toHaveKey('440305'); // 南山区 (深圳市)
        expect($areas)->toHaveKey('440103'); // 荔湾区 (广州市)
        
        // 测试不存在的省份代码
        $areas = ChinaDivisions::getAreasByProvinceCode('999999');
        expect($areas)->toBeArray();
        expect($areas)->toBeEmpty();
    });
    
    it('can get province name by code', function () {
        $name = ChinaDivisions::getProvinceName('110000');
        expect($name)->toBe('北京市');
        
        $name = ChinaDivisions::getProvinceName('440000');
        expect($name)->toBe('广东省');
        
        $name = ChinaDivisions::getProvinceName('999999');
        expect($name)->toBeNull();
    });
    
    it('can get city name by code', function () {
        $name = ChinaDivisions::getCityName('440100');
        expect($name)->toBe('广州市');
        
        $name = ChinaDivisions::getCityName('440300');
        expect($name)->toBe('深圳市');
        
        $name = ChinaDivisions::getCityName('999999');
        expect($name)->toBeNull();
    });
    
    it('can get area name by code', function () {
        $name = ChinaDivisions::getAreaName('440305');
        expect($name)->toBe('南山区');
        
        $name = ChinaDivisions::getAreaName('440306');
        expect($name)->toBe('宝安区');
        
        $name = ChinaDivisions::getAreaName('999999');
        expect($name)->toBeNull();
    });
    
    it('can search provinces by pinyin', function () {
        $results = ChinaDivisions::searchProvincesByPinyin('bei');
        
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        expect($results)->toHaveKey('110000'); // 北京市
        
        // 测试不存在的拼音
        $results = ChinaDivisions::searchProvincesByPinyin('notexist');
        expect($results)->toBeArray();
        expect($results)->toBeEmpty();
    });
    
    it('can search cities by pinyin', function () {
        // 不限定省份
        $results = ChinaDivisions::searchCitiesByPinyin('guang');
        
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        expect($results)->toHaveKey('440100'); // 广州市
        
        // 限定省份
        $results = ChinaDivisions::searchCitiesByPinyin('guang', '440000');
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        expect($results)->toHaveKey('440100'); // 广州市
        
        // 测试不存在的拼音
        $results = ChinaDivisions::searchCitiesByPinyin('notexist');
        expect($results)->toBeArray();
        expect($results)->toBeEmpty();
        
        // 测试不存在的省份代码
        $results = ChinaDivisions::searchCitiesByPinyin('guang', '999999');
        expect($results)->toBeArray();
        expect($results)->toBeEmpty();
    });
    
    it('can search areas by pinyin', function () {
        // 不限定城市和省份
        $results = ChinaDivisions::searchAreasByPinyin('nan');
        
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        
        // 限定城市
        $results = ChinaDivisions::searchAreasByPinyin('nan', '440300');
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        expect($results)->toHaveKey('440305'); // 南山区
        
        // 限定省份
        $results = ChinaDivisions::searchAreasByPinyin('nan', null, '440000');
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        
        // 同时限定城市和省份
        $results = ChinaDivisions::searchAreasByPinyin('nan', '440300', '440000');
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        expect($results)->toHaveKey('440305'); // 南山区
        
        // 测试不存在的拼音
        $results = ChinaDivisions::searchAreasByPinyin('notexist');
        expect($results)->toBeArray();
        expect($results)->toBeEmpty();
        
        // 测试不存在的城市代码
        $results = ChinaDivisions::searchAreasByPinyin('nan', '999999');
        expect($results)->toBeArray();
        expect($results)->toBeEmpty();
        
        // 测试不存在的省份代码
        $results = ChinaDivisions::searchAreasByPinyin('nan', null, '999999');
        expect($results)->toBeArray();
        expect($results)->toBeEmpty();
    });
    
    it('can handle data integrity between provinces, cities and areas', function () {
        // 测试省份、城市、区县之间的数据完整性
        $provinces = ChinaDivisions::getProvinces();
        $cities = ChinaDivisions::getCities();
        $areas = ChinaDivisions::getAreas();
        
        // 随机选择一个省份
        $provinceCode = '440000'; // 广东省
        $provinceCities = ChinaDivisions::getCitiesByProvinceCode($provinceCode);
        
        // 检查该省份下的所有城市是否都有正确的省份代码
        foreach ($provinceCities as $cityCode => $cityInfo) {
            expect($cityInfo['province_code'])->toBe($provinceCode);
        }
        
        // 随机选择一个城市
        $cityCode = '440300'; // 深圳市
        $cityAreas = ChinaDivisions::getAreasByCityCode($cityCode);
        
        // 检查该城市下的所有区县是否都有正确的城市代码和省份代码
        foreach ($cityAreas as $areaCode => $areaInfo) {
            expect($areaInfo['city_code'])->toBe($cityCode);
            expect($areaInfo['province_code'])->toBe($cities[$cityCode]['province_code']);
        }
        
        // 检查通过省份代码获取的区县是否包含该省份下所有城市的区县
        $provinceAreas = ChinaDivisions::getAreasByProvinceCode($provinceCode);
        $totalAreasCount = 0;
        
        foreach ($provinceCities as $cityCode => $cityInfo) {
            $cityAreas = ChinaDivisions::getAreasByCityCode($cityCode);
            $totalAreasCount += count($cityAreas);
            
            // 检查每个城市的区县是否都包含在省份的区县中
            foreach ($cityAreas as $areaCode => $areaInfo) {
                expect($provinceAreas)->toHaveKey($areaCode);
            }
        }
        
        // 检查省份下的区县总数是否等于该省份下所有城市的区县总数
        expect(count($provinceAreas))->toBe($totalAreasCount);
    });
    
    it('can handle special administrative regions', function () {
        // 测试特别行政区（如香港、澳门）
        $provinces = ChinaDivisions::getProvinces();
        
        // 检查香港特别行政区
        if (isset($provinces['810000'])) {
            expect($provinces['810000']['name'])->toBe('香港特别行政区');
        }
        
        // 检查澳门特别行政区
        if (isset($provinces['820000'])) {
            expect($provinces['820000']['name'])->toBe('澳门特别行政区');
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
            $provinceName = ChinaDivisions::getProvinceName($code);
            expect($provinceName)->toBe($name);
            
            // 直辖市的区县直接关联到省级代码
            $areas = ChinaDivisions::getAreasByProvinceCode($code);
            expect($areas)->toBeArray();
            expect($areas)->not->toBeEmpty();
            
            // 随机检查一个区县的省份代码是否正确
            if (!empty($areas)) {
                $areaCode = array_key_first($areas);
                expect($areas[$areaCode]['province_code'])->toBe($code);
            }
        }
    });
});