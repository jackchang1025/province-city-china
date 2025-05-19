# 中国省市区数据包

这个 PHP 包提供了中国省份、城市和区县的结构化数据，支持按拼音和首字母查询。数据基于最新的行政区划信息，包括直辖市、特别行政区等特殊地区。

(数据来源) https://www.mca.gov.cn/mzsj/xzqh/2022/202201xzqh.html

## 特性

- 提供完整的中国省份、城市、区县数据
- 支持按代码、名称查询行政区划信息
- 支持按拼音、首字母搜索
- 支持层级关系查询（省->市->区）
- 轻量级，无外部依赖（除了拼音转换）
- 完整的测试覆盖

## 安装

通过Composer安装:

```bash
composer require weijiajia/province-city-china
```

## 基本用法

### 获取所有省份

```php
use Weijiajia\ProvinceCityChina\ChinaDivisions;

// 获取所有省份
$provinces = ChinaDivisions::getProvinces();
```

### 获取某省的所有城市

```php
// 获取广东省的所有城市
$cities = ChinaDivisions::getCitiesByProvinceCode('440000');
```

### 获取某市的所有区县

```php
// 获取深圳市的所有区县
$areas = ChinaDivisions::getAreasByCityCode('440300');
```

### 按拼音搜索

```php
// 搜索拼音包含"bei"的省份
$provinces = ChinaDivisions::searchProvincesByPinyin('bei');

// 搜索拼音包含"guang"的城市
$cities = ChinaDivisions::searchCitiesByPinyin('guang');

// 搜索拼音包含"long"的区县
$areas = ChinaDivisions::searchAreasByPinyin('long');
```

## 详细API

### ChinaDivisions 类

这是主要的入口类，提供了访问所有数据的方法。

```php
// 获取所有省份数据
ChinaDivisions::getProvinces();

// 获取所有城市数据
ChinaDivisions::getCities();

// 获取所有区县数据
ChinaDivisions::getAreas();

// 根据省份代码获取该省所有城市
ChinaDivisions::getCitiesByProvinceCode($provinceCode);

// 根据城市代码获取该城市所有区县
ChinaDivisions::getAreasByCityCode($cityCode);

// 根据省份代码获取该省所有区县
ChinaDivisions::getAreasByProvinceCode($provinceCode);

// 根据代码获取名称
ChinaDivisions::getProvinceName($code);
ChinaDivisions::getCityName($code);
ChinaDivisions::getAreaName($code);

// 根据拼音搜索
ChinaDivisions::searchProvincesByPinyin($pinyin);
ChinaDivisions::searchCitiesByPinyin($pinyin, $provinceCode = null);
ChinaDivisions::searchAreasByPinyin($pinyin, $cityCode = null, $provinceCode = null);
```

### Provinces, Cities, Areas 类

这些类提供了更直接的访问各级行政区划数据的方法。

```php
// Provinces类
Provinces::getProvinces();  // 获取所有省份
Provinces::getNames();      // 获取所有省份名称
Provinces::getName($code);  // 根据代码获取省份名称
Provinces::getCode($name);  // 根据名称获取省份代码
Provinces::searchByPinyin($pinyin, $fuzzy = true);  // 按拼音搜索省份

// Cities类
Cities::getCities();  // 获取所有城市
Cities::getNames();   // 获取所有城市名称
Cities::getName($code);  // 根据代码获取城市名称
Cities::getByProvinceCode($provinceCode);  // 获取某省的所有城市
Cities::searchByPinyin($pinyin, $provinceCode = null, $fuzzy = true);  // 按拼音搜索城市

// Areas类
Areas::getAreas();  // 获取所有区县
Areas::getNames();  // 获取所有区县名称
Areas::getName($code);  // 根据代码获取区县名称
Areas::getByCityCode($cityCode);  // 获取某市的所有区县
Areas::getByProvinceCode($provinceCode);  // 获取某省的所有区县
Areas::searchByPinyin($pinyin, $cityCode = null, $provinceCode = null, $fuzzy = true);  // 按拼音搜索区县
```

## 数据结构

### 省份数据结构

```php
[
    '110000' => [
        'name' => '北京市',
        'pinyin' => 'beijingshi',
    ],
    // 其他省份...
]
```

### 城市数据结构

```php
[
    '440300' => [
        'name' => '深圳市',
        'pinyin' => 'shenzhenshi',
        'province_code' => '440000',
    ],
    // 其他城市...
]
```

### 区县数据结构

```php
[
    '440305' => [
        'name' => '南山区',
        'pinyin' => 'nanshanqu',
        'city_code' => '440300',
        'province_code' => '440000',
    ],
    // 其他区县...
]
```

## 特殊情况处理

- **直辖市**：北京、上海、天津、重庆作为省级行政区，其下属区县直接关联到省级代码
- **特别行政区**：香港、澳门作为特别行政区，有独立的省级代码
- **台湾省**：作为省级行政区包含在数据中

## 数据更新

数据基于最新的行政区划信息。如需更新数据，可以修改 `src/Resources/data/divisions.txt` 文件，然后运行解析脚本：

```bash
php src/parse_divisions_data.php
```

感谢 [overtrue/pinyin](https://github.com/overtrue/pinyin) 提供的拼音转换功能。

## 许可证

MIT