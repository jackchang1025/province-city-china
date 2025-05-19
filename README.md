# 中国省市区数据包

这个 PHP 包提供了中国省份、城市和区县的结构化数据，支持按拼音查询。数据基于最新的行政区划信息，包括直辖市、特别行政区等特殊地区。

(数据来源) https://www.mca.gov.cn/mzsj/xzqh/2022/202201xzqh.html (请注意，实际使用的数据文件可能需要与最新的官方发布同步)

## 特性

- 提供完整的中国省份、城市、区县数据，以面向对象实体和 `Illuminate\Support\Collection` 形式返回
- 支持按代码获取省份、城市、区县的实体实例
- 支持按拼音模糊搜索省份、城市、区县
- 支持层级关系查询（省份实体 -> 城市集合，城市实体 -> 省份实体/区县集合，区县实体 -> 城市实体/省份实体）
- 轻量级，核心功能无重度外部依赖
- 包含单元测试

## 安装

通过 Composer 安装:

```bash
composer require weijiajia/province-city-china
```

## 基本用法

所有操作都建议通过 `Weijiajia\ProvinceCityChina\ChinaDivisions` 类进行。
该类使用了 `Makeable` Trait，它提供了一个便捷的工厂方法 `make()` 来获取类实例。推荐通过调用 `make()` 来访问其实例方法：
`ChinaDivisions::make()->yourMethod()`

### 获取所有省份

```php
use Weijiajia\ProvinceCityChina\ChinaDivisions;
use Weijiajia\ProvinceCityChina\Entities\Province;

// 获取所有省份（返回 Collection 对象, 包含 Province 实体）
$provinces = ChinaDivisions::make()->getProvinces();

// 遍历所有省份
$provinces->each(function (Province $province) {
    echo "代码: " . $province->getCode() . ", 名称: " . $province->getName() . ", 拼音: " . $province->getPinyin() . "\n";
});

// 获取单个省份实例
$guangdong = ChinaDivisions::make()->getProvinceByCode('440000'); // 广东省
if ($guangdong) {
    echo $guangdong->getName(); // 输出 "广东省"
}
```

### 获取某省的所有城市

```php
use Weijiajia\ProvinceCityChina\ChinaDivisions;
use Weijiajia\ProvinceCityChina\Entities\Province;
use Weijiajia\ProvinceCityChina\Entities\City;

// 方法1: 通过 ChinaDivisions 类直接获取
$citiesInGuangdong = ChinaDivisions::make()->getCitiesByProvinceCode('440000'); // 获取广东省的所有城市

// 方法2: 通过省份实体实例获取
$province = ChinaDivisions::make()->getProvinceByCode('440000');
if ($province) {
    $citiesInGuangdong = $province->getCities(); // 返回 Collection 对象, 包含 City 实体

    $citiesInGuangdong->each(function (City $city) {
        echo "城市代码: " . $city->getCode() . ", 名称: " . $city->getName() . "\n";
        // 获取城市所属的省份实体
        $parentProvince = $city->getProvince();
        if ($parentProvince) {
            echo "所属省份: " . $parentProvince->getName() . "\n";
        }
    });
}

// 获取单个城市实例
$shenzhen = ChinaDivisions::make()->getCitiesService()->getCityByCode('440300'); // 深圳市
if ($shenzhen) {
    echo $shenzhen->getName(); // 输出 "深圳市"
}
```

**注意**：对于直辖市（如北京：'110000'），`ChinaDivisions::make()->getCitiesByProvinceCode('110000')` 或北京省份实体的 `getCities()` 方法将返回一个空的 `Collection`，因为直辖市的行政区划结构中，其市辖区直接关联到省级代码。

### 获取某市的所有区县

```php
use Weijiajia\ProvinceCityChina\ChinaDivisions;
use Weijiajia\ProvinceCityChina\Entities\City;
use Weijiajia\ProvinceCityChina\Entities\Area;

// 方法1: 通过 ChinaDivisions 类直接获取
$areasInShenzhen = ChinaDivisions::make()->getAreasByCityCode('440300'); // 获取深圳市的所有区县

// 方法2: 通过城市实体实例获取
$city = ChinaDivisions::make()->getCitiesService()->getCityByCode('440300');
if ($city) {
    $areasInShenzhen = $city->getAreas(); // 返回 Collection 对象, 包含 Area 实体

    $areasInShenzhen->each(function (Area $area) {
        echo "区县代码: " . $area->getCode() . ", 名称: " . $area->getName() . "\n";
        // 获取区县所属的城市实体
        $parentCity = $area->getCity();
        if ($parentCity) {
            echo "所属城市: " . $parentCity->getName() . "\n";
        }
        // 获取区县所属的省份实体
        $rootProvince = $area->getProvince();
        if ($rootProvince) {
            echo "所属省份: " . $rootProvince->getName() . "\n";
        }
    });
}

// 获取单个区县实例
$nanshan = ChinaDivisions::make()->getAreasService()->getAreasByCode('440305'); // 南山区
if ($nanshan) {
    echo $nanshan->getName(); // 输出 "南山区"
}
```

### 按拼音搜索

```php
use Weijiajia\ProvinceCityChina\ChinaDivisions;

// 搜索拼音包含"bei"的省份
$provinces = ChinaDivisions::make()->getProvincesByPinyin('bei');

// 搜索拼音包含"guang"的城市
$cities = ChinaDivisions::make()->getCitiesByPinyin('guang');

// 在广东省 (440000) 内搜索拼音包含 "shen" 的城市
$citiesInGuangdong = ChinaDivisions::make()->getCitiesByPinyin('shen', '440000');

// 搜索拼音包含"nan"的区县
$areas = ChinaDivisions::make()->getAreasByPinyin('nan');

// 在深圳市 (440300) 内搜索拼音包含 "nan" 的区县
$areasInShenzhen = ChinaDivisions::make()->getAreasByPinyin('nan', '440300');

// 在广东省 (440000) 内搜索市辖区拼音包含 "nan" 的区县 (不指定城市代码)
$areasInGuangdong = ChinaDivisions::make()->getAreasByPinyin('nan', null, '440000');
```

## 详细 API

### `ChinaDivisions` 类

这是主要的入口类。推荐通过 `ChinaDivisions::make()->...` 调用其实例方法。

- `ChinaDivisions::make()->getProvinces(): Collection<Province>`
  获取所有省份数据。

- `ChinaDivisions::make()->getCities(): Collection<City>`
  获取所有城市数据。

- `ChinaDivisions::make()->getAreas(): Collection<Area>`
  获取所有区县数据。

- `ChinaDivisions::make()->getProvinceByCode(string $code): ?Province`
  根据省份代码获取省份实体实例。

- `ChinaDivisions::make()->getProvinceName(string $code): ?string`
  根据省份代码获取省份名称。

- `ChinaDivisions::make()->getCitiesByProvinceCode(string $provinceCode): Collection<City>`
  根据省份代码获取该省所有城市。

- `ChinaDivisions::make()->getAreasByCityCode(string $cityCode): Collection<Area>`
  根据城市代码获取该城市所有区县。

- `ChinaDivisions::make()->getAreasByProvinceCode(string $provinceCode): Collection<Area>`
  根据省份代码获取该省所有区县（包括直辖市的区县）。

- `ChinaDivisions::make()->getProvincesByPinyin(string $pinyin): Collection<Province>`
  根据拼音搜索省份。

- `ChinaDivisions::make()->getCitiesByPinyin(string $pinyin, ?string $provinceCode = null): Collection<City>`
  根据拼音搜索城市，可选省份代码进行范围限制。

- `ChinaDivisions::make()->getAreasByPinyin(string $pinyin, ?string $cityCode = null, ?string $provinceCode = null): Collection<Area>`
  根据拼音搜索区县，可选城市代码或省份代码进行范围限制。

- `ChinaDivisions::make()->getProvincesService(): Provinces`
  获取省份服务实例。

- `ChinaDivisions::make()->getCitiesService(): Cities`
  获取城市服务实例。

- `ChinaDivisions::make()->getAreasService(): Areas`
  获取区县服务实例。

**注意:** 获取单个城市或区县实体，需要通过对应的服务类：

- `ChinaDivisions::make()->getCitiesService()->getCityByCode(string $code): ?City`
- `ChinaDivisions::make()->getAreasService()->getAreasByCode(string $code): ?Area` (注意是 `getAreasByCode`)

### 实体类 (`Province`, `City`, `Area`)

这些是代表省份、城市和区县的数据实体。它们包含各自的属性（如代码、名称、拼音）以及获取关联数据的方法。

#### `Province` 实体

通过 `ChinaDivisions::make()->getProvinces()` 或 `ChinaDivisions::make()->getProvinceByCode(string $code)` 获取。

- `getCode(): string` - 获取省份代码。
- `getName(): string` - 获取省份名称。
- `getPinyin(): string` - 获取省份名称的拼音。
- `getCities(): Collection<City>` - 获取该省份下的所有城市。对于直辖市，返回空集合。

示例：

```php
$province = ChinaDivisions::make()->getProvinceByCode('440000'); // 广东省
if ($province) {
    echo $province->getName(); // "广东省"
    $cities = $province->getCities();
    $cities->each(function ($city) { /* ... */ });
}
```

#### `City` 实体

通过 `ChinaDivisions::make()->getCities()`、`ChinaDivisions::make()->getCitiesService()->getCityByCode(string $code)`，或从 `Province` 实体获取。

- `getCode(): string` - 获取城市代码。
- `getName(): string` - 获取城市名称。
- `getPinyin(): string` - 获取城市名称的拼音。
- `getProvinceCode(): string` - 获取所属省份的代码。
- `getProvince(): ?Province` - 获取所属省份的实体实例。
- `getAreas(): Collection<Area>` - 获取该城市下的所有区县。

示例：

```php
$city = ChinaDivisions::make()->getCitiesService()->getCityByCode('440300'); // 深圳市
if ($city) {
    echo $city->getName(); // "深圳市"
    $province = $city->getProvince();
    if ($province) {
        echo $province->getName(); // "广东省"
    }
    $areas = $city->getAreas();
    $areas->each(function ($area) { /* ... */ });
}
```

#### `Area` 实体

通过 `ChinaDivisions::make()->getAreas()`、`ChinaDivisions::make()->getAreasService()->getAreasByCode(string $code)`，或从 `City` 实体获取。

- `getCode(): string` - 获取区县代码。
- `getName(): string` - 获取区县名称。
- `getPinyin(): string` - 获取区县名称的拼音。
- `getCityCode(): string` - 获取所属城市的代码。
- `getProvinceCode(): string` - 获取所属省份的代码。
- `getCity(): ?City` - 获取所属城市的实体实例。
- `getProvince(): ?Province` - 获取所属省份的实体实例。

示例：

```php
$area = ChinaDivisions::make()->getAreasService()->getAreasByCode('440305'); // 南山区
if ($area) {
    echo $area->getName(); // "南山区"
    $city = $area->getCity();
    if ($city) {
        echo $city->getName(); // "深圳市"
    }
    $province = $area->getProvince();
    if ($province) {
        echo $province->getName(); // "广东省"
    }
}
```

### 服务类 (`Provinces`, `Cities`, `Areas`)

这些服务类负责各自层级数据的加载、缓存和查询逻辑。通常情况下，你不需要直接与这些服务类交互，而是通过 `ChinaDivisions` 类的方法来间接使用它们。

如果你确实需要直接访问这些服务（例如，调用服务类特有的、未在 `ChinaDivisions` 中暴露的方法），可以这样做：

- `ChinaDivisions::make()->getProvincesService(): Provinces`
- `ChinaDivisions::make()->getCitiesService(): Cities`
- `ChinaDivisions::make()->getAreasService(): Areas`

然后可以调用服务实例上的方法，例如：

```php
$provincesService = ChinaDivisions::make()->getProvincesService();
$allProvinces = $provincesService->getProvinces();
$guangdong = $provincesService->getProvinceByCode('440000');

$citiesService = ChinaDivisions::make()->getCitiesService();
$allCities = $citiesService->getCities();
$shenzhen = $citiesService->getCityByCode('440300');

$areasService = ChinaDivisions::make()->getAreasService();
$allAreas = $areasService->getAreas();
$nanshan = $areasService->getAreasByCode('440305');
```

## 数据结构

原始数据存储在 `src/Resources/data/divisions.php` 文件中。该文件返回一个包含省、市、区信息的 PHP 数组。
数据加载后，会转换为相应的实体对象 (`Province`, `City`, `Area`)，并通过 `Illuminate\Support\Collection` 进行管理。

## 数据更新说明

本包中的数据尽可能与官方发布保持一致。数据源通常是民政部发布的最新行政区划代码。
链接：[中华人民共和国行政区划代码](http://www.mca.gov.cn/article/sj/xzqh/)

如果你发现数据有误或需要更新，请考虑以下步骤：

1.  从官方渠道获取最新的行政区划数据。
2.  根据 `src/Resources/data/divisions.php` 的格式更新数据。你需要确保省、市、区的层级关系和编码正确。
3.  运行测试以确保你的更改没有破坏功能。
4.  可以提交一个 Pull Request，我们非常欢迎贡献！

## 运行测试

```bash
composer test
```

## 贡献

欢迎提交 Pull Requests。对于重大的更改，请先开启一个 issue 来讨论你想要做的修改。

请确保为适当的更改更新测试。

## 许可证

[MIT](https://choosealicense.com/licenses/mit/)
