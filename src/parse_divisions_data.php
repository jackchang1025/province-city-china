<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Overtrue\Pinyin\Pinyin;

/**
 * 解析行政区划原始文本数据，转换为结构化PHP数组
 * 
 * @param string $rawTextData 原始文本数据
 * @return string 生成的PHP代码字符串
 */
function parseDivisionData(string $rawTextData): string
{
    $lines = explode("\n", $rawTextData);
    $provinces = [];
    $cities = [];
    $areas = [];

    $currentProvinceCode = null;
    $currentCityCode = null;

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            continue;
        }

        // 匹配行政区划代码和名称
        if (preg_match('/^(\d{6})\s+(.+)$/', $line, $matches)) {
            $code = $matches[1];
            $name = trim($matches[2]);
            
            // 使用 Pinyin 类的静态方法生成拼音
            $pinyinStr = Pinyin::permalink($name, '');

            // 省级（以0000结尾）
            if (substr($code, 2) === '0000') {
                $provinces[$code] = [
                    'name' => $name, 
                    'pinyin' => $pinyinStr
                ];
                $currentProvinceCode = $code;
                // 直辖市的区县直接关联到省级
                if (in_array(substr($code, 0, 2), ['11', '12', '31', '50'])) {
                    $currentCityCode = $code;
                } else {
                    $currentCityCode = null;
                }
            }
            // 市级（以00结尾但不是0000）
            elseif (substr($code, 4) === '00' && substr($code, 2) !== '0000') {
                if ($currentProvinceCode) {
                    $cities[$code] = [
                        'name' => $name,
                        'pinyin' => $pinyinStr,
                        'province_code' => $currentProvinceCode
                    ];
                    $currentCityCode = $code;
                }
            }
            // 区县级
            else {
                if ($currentProvinceCode) {
                    // 如果没有明确的市级，则关联到省级（直辖市或省直辖县级行政区）
                    $effectiveCityCode = $currentCityCode ?: substr($code, 0, 4) . '00';
                    
                    // 特殊处理：如果是省直辖县级行政区（如济源市）
                    if (substr($code, 2, 2) === '90') {
                        $effectiveCityCode = $currentProvinceCode;
                    }
                    
                    $areas[$code] = [
                        'name' => $name,
                        'pinyin' => $pinyinStr,
                        'city_code' => $effectiveCityCode,
                        'province_code' => $currentProvinceCode
                    ];
                }
            }
        }
    }

    // 特殊处理：台湾、香港、澳门
    $specialRegions = [
        '710000' => '台湾省',
        '810000' => '香港特别行政区',
        '820000' => '澳门特别行政区'
    ];
    
    foreach ($specialRegions as $code => $name) {
        if (!isset($provinces[$code]) && strpos($rawTextData, $code) !== false) {
            $provinces[$code] = [
                'name' => $name, 
                'pinyin' => Pinyin::permalink($name, '')
            ];
        }
    }

    // 生成PHP代码
    $output = "<?php\n\n// 中国行政区划数据\n";
    $output .= "// 拼音字段已使用 overtrue/pinyin 库生成\n\n";
    $output .= "return [\n";
    $output .= "    'provinces' => " . var_export_pretty($provinces) . ",\n\n";
    $output .= "    'cities' => " . var_export_pretty($cities) . ",\n\n";
    $output .= "    'areas' => " . var_export_pretty($areas) . ",\n";
    $output .= "];\n";

    return $output;
}

/**
 * 美化var_export输出的数组格式
 * 
 * @param array $data 要导出的数组
 * @return string 格式化后的PHP数组代码
 */
function var_export_pretty($data) {
    $export = var_export($data, true);
    $export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $export);
    $export = preg_replace("/'([^']+)' => /", "'$1' => ", $export);
    $export = preg_replace('/array \(/', '[', $export);
    $export = preg_replace('/\)/', ']', $export);
    return $export;
}

// 脚本执行部分
$rawTextFilePath = __DIR__ . '/Resources/data/divisions.txt';
$outputFilePath = __DIR__ . '/Resources/data/divisions.php';

if (!file_exists($rawTextFilePath)) {
    die("错误：原始数据文件不存在：{$rawTextFilePath}\n");
}

$rawText = file_get_contents($rawTextFilePath);
if ($rawText === false) {
    die("错误：无法读取原始数据文件：{$rawTextFilePath}\n");
}

echo "正在解析行政区划数据...\n";
$phpArrayString = parseDivisionData($rawText);

if (file_put_contents($outputFilePath, $phpArrayString)) {
    echo "成功解析数据并写入文件：{$outputFilePath}\n";
    echo "请检查生成的文件，确认无误后可以将其重命名为divisions.php替换原始文件。\n";
} else {
    echo "错误：无法写入解析后的数据到文件：{$outputFilePath}\n";
}