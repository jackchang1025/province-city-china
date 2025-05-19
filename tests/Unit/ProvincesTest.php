<?php

use Weijiajia\ProvinceCityChina\Provinces;

describe('Provinces', function () {
    it('can get all provinces', function () {
        $provinces = Provinces::getProvinces();
        
        expect($provinces)->toBeArray();
        expect($provinces)->not->toBeEmpty();
        
        // 检查北京市是否存在
        expect($provinces)->toHaveKey('110000');
        expect($provinces['110000']['name'])->toBe('北京市');
        expect($provinces['110000']['pinyin'])->toBe('beijingshi');
    });
    
    it('can get province names', function () {
        $names = Provinces::getNames();
        
        expect($names)->toBeArray();
        expect($names)->not->toBeEmpty();
        expect($names['110000'])->toBe('北京市');
        expect($names['440000'])->toBe('广东省');
    });
    
    it('can get province name by code', function () {
        $name = Provinces::getName('110000');
        expect($name)->toBe('北京市');
        
        $name = Provinces::getName('440000');
        expect($name)->toBe('广东省');
        
        $name = Provinces::getName('999999');
        expect($name)->toBeNull();
    });
    
    it('can get province code by name', function () {
        $code = Provinces::getCode('北京市');
        expect($code)->toBe('110000');
        
        $code = Provinces::getCode('广东省');
        expect($code)->toBe('440000');
        
        $code = Provinces::getCode('不存在的省份');
        expect($code)->toBeNull();
    });
    
    it('can search provinces by pinyin', function () {
        // 模糊搜索
        $results = Provinces::searchByPinyin('bei');
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        expect($results)->toHaveKey('110000');
        expect($results['110000']['name'])->toBe('北京市');
        
        // 精确搜索
        $results = Provinces::searchByPinyin('beijingshi', false);
        expect($results)->toBeArray();
        expect($results)->not->toBeEmpty();
        expect($results)->toHaveKey('110000');
        
        // 搜索不存在的拼音
        $results = Provinces::searchByPinyin('notexist');
        expect($results)->toBeArray();
        expect($results)->toBeEmpty();
    });

});