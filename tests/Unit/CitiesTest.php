<?php

use Weijiajia\ProvinceCityChina\Cities;

describe('Cities', function () {
    it('can get all cities', function () {
        $cities = Cities::getCities();
        
        expect($cities)->toBeArray();
        expect($cities)->not->toBeEmpty();
        
        // 检查广州市是否存在
        expect($cities)->toHaveKey('440100');
        expect($cities['440100']['name'])->toBe('广州市');
        expect($cities['440100']['province_code'])->toBe('440000');
    });
    
    it('can get city names', function () {
        $names = Cities::getNames();
        
        expect($names)->toBeArray();
        expect($names)->not->toBeEmpty();
        expect($names['440100'])->toBe('广州市');
        expect($names['440300'])->toBe('深圳市');
    });
    
    it('can get city name by code', function () {
        $name = Cities::getName('440100');
        expect($name)->toBe('广州市');
        
        $name = Cities::getName('440300');
        expect($name)->toBe('深圳市');
        
        $name = Cities::getName('999999');
        expect($name)->toBeNull();
    });
    
    it('can get cities by province code', function () {
        // 广东省的城市
        $cities = Cities::getByProvinceCode('440000');
        expect($cities)->toBeArray();
        expect($cities)->not->toBeEmpty();
        expect($cities)->toHaveKey('440100'); // 广州市
        expect($cities)->toHaveKey('440300'); // 深圳市
        
        // 北京市的城市（直辖市特殊处理）
        $cities = Cities::getByProvinceCode('110000');
        expect($cities)->toBeArray();
        
        // 不存在的省份代码
        $cities = Cities::getByProvinceCode('999999');
        expect($cities)->toBeArray();
        expect($cities)->toBeEmpty();
    });
    
    it('can search cities by pinyin', function () {
        // 模糊搜索
        $results = Cities::searchByPinyin('guang');
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        expect($results)->toHaveKey('440100'); // 广州市
        
        // 限定省份的搜索
        $results = Cities::searchByPinyin('guang', '440000');
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        expect($results)->toHaveKey('440100'); // 广州市
        
        // 精确搜索
        $results = Cities::searchByPinyin('guangzhou');
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        expect($results)->toHaveKey('440100');

        $results = Cities::searchByPinyin('guangzhoushi', null, false);
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        expect($results)->toHaveKey('440100');
        
        // 搜索不存在的拼音
        $results = Cities::searchByPinyin('notexist');
        expect($results)->toBeArray();
        expect($results)->toBeEmpty();
    });
    
});