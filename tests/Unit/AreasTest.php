<?php

use Weijiajia\ProvinceCityChina\Areas;

describe('Areas', function () {
    it('can get all areas', function () {
        $areas = Areas::getAreas();
        
        expect($areas)->toBeArray();
        expect($areas)->not->toBeEmpty();
        
        // 检查南山区是否存在
        expect($areas)->toHaveKey('440305');
        expect($areas['440305']['name'])->toBe('南山区');
        expect($areas['440305']['city_code'])->toBe('440300'); // 深圳市
        expect($areas['440305']['province_code'])->toBe('440000'); // 广东省
    });
    
    it('can get area names', function () {
        $names = Areas::getNames();
        
        expect($names)->toBeArray();
        expect($names)->not->toBeEmpty();
        expect($names['440305'])->toBe('南山区');
        expect($names['440306'])->toBe('宝安区');
    });
    
    it('can get area name by code', function () {
        $name = Areas::getName('440305');
        expect($name)->toBe('南山区');
        
        $name = Areas::getName('440306');
        expect($name)->toBe('宝安区');
        
        $name = Areas::getName('999999');
        expect($name)->toBeNull();
    });
    
    it('can get areas by city code', function () {
        // 深圳市的区县
        $areas = Areas::getByCityCode('440300');
        expect($areas)->toBeArray();
        expect($areas)->not->toBeEmpty();
        expect($areas)->toHaveKey('440305'); // 南山区
        expect($areas)->toHaveKey('440306'); // 宝安区
        
        // 不存在的城市代码
        $areas = Areas::getByCityCode('999999');
        expect($areas)->toBeArray();
        expect($areas)->toBeEmpty();
    });
    
    it('can get areas by province code', function () {
        // 广东省的区县
        $areas = Areas::getByProvinceCode('440000');
        expect($areas)->toBeArray();
        expect($areas)->not->toBeEmpty();
        
        // 深圳市的区县应该在广东省的区县中
        expect($areas)->toHaveKey('440305'); // 南山区
        expect($areas)->toHaveKey('440306'); // 宝安区
        
        // 广州市的区县也应该在广东省的区县中
        expect($areas)->toHaveKey('440103'); // 荔湾区
        
        // 不存在的省份代码
        $areas = Areas::getByProvinceCode('999999');
        expect($areas)->toBeArray();
        expect($areas)->toBeEmpty();
    });
    
    it('can search areas by pinyin', function () {
        // 模糊搜索
        $results = Areas::searchByPinyin('nan');
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        
        // 限定城市的搜索
        $results = Areas::searchByPinyin('nan', '440300');
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        expect($results)->toHaveKey('440305'); // 南山区
        
        // 限定省份的搜索
        $results = Areas::searchByPinyin('nan', null, '440000');
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        
        // 同时限定城市和省份的搜索
        $results = Areas::searchByPinyin('nan', '440300', '440000');
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        expect($results)->toHaveKey('440305'); // 南山区
        
        // 精确搜索
        $results = Areas::searchByPinyin('nanshanqu', null, null, false);
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        expect($results)->toHaveKey('440305'); // 南山区
        
        // 搜索不存在的拼音
        $results = Areas::searchByPinyin('notexist');
        expect($results)->toBeArray();
        expect($results)->toBeEmpty();
    });
    
    
});