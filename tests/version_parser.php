<?php
/**
 * 数据文件版本号解析工具
 * 用于解析和理解mobile.dat文件中的版本号含义
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Pangongzi\Mobile\MobileLocator;

echo "=== MobileLocator 数据文件版本号解析 ===\n\n";

// 获取版本号
$locator = MobileLocator::getInstance();
$versionNumber = $locator->getVersion();

echo "当前数据文件版本号: $versionNumber\n\n";

// 分析版本号的可能含义
echo "版本号分析:\n";
echo "十进制: $versionNumber\n";
echo "十六进制: 0x" . dechex($versionNumber) . "\n";
echo "二进制: " . decbin($versionNumber) . "\n\n";

// 尝试不同的解析方式
echo "可能的解析方式:\n";

// 方式1: 按年月日格式解析 (YYYYMMDD)
$year = floor($versionNumber / 10000);
$month = floor(($versionNumber % 10000) / 100);
$day = $versionNumber % 100;

echo "1. 日期格式解析 (YYYYMMDD):\n";
echo "   年份: $year\n";
echo "   月份: $month\n";
echo "   日期: $day\n";
if ($year >= 2000 && $year <= 2030 && $month >= 1 && $month <= 12 && $day >= 1 && $day <= 31) {
    echo "   ✓ 这是一个有效的日期格式\n";
    echo "   解释: {$year}年{$month}月{$day}日\n\n";
} else {
    echo "   ✗ 不是有效的日期格式\n\n";
}

// 方式2: 按年月格式解析 (YYYYMM)
$year2 = floor($versionNumber / 100);
$month2 = $versionNumber % 100;

echo "2. 年月格式解析 (YYYYMM):\n";
echo "   年份: $year2\n";
echo "   月份: $month2\n";
if ($year2 >= 2000 && $year2 <= 2030 && $month2 >= 1 && $month2 <= 12) {
    echo "   ✓ 这是一个有效的年月格式\n";
    echo "   解释: {$year2}年{$month2}月\n\n";
} else {
    echo "   ✗ 不是有效的年月格式\n\n";
}

// 方式3: 按时间戳解析
echo "3. 时间戳解析:\n";
echo "   Unix时间戳: $versionNumber\n";
$datetime = date('Y-m-d H:i:s', $versionNumber);
echo "   对应时间: $datetime\n";
if ($versionNumber > 946684800 && $versionNumber < 2147483647) { // 2000年到2038年之间
    echo "   ✓ 这是一个合理的时间戳范围\n\n";
} else {
    echo "   ✗ 不是有效的时间戳\n\n";
}

// 方式4: 自定义编码格式解析
echo "4. 自定义编码格式分析:\n";
// 假设格式为 AABBCCDD，其中AA=主版本，BB=次版本，CC=修订，DD=构建
$parts = [
    '主版本' => ($versionNumber >> 24) & 0xFF,
    '次版本' => ($versionNumber >> 16) & 0xFF,
    '修订号' => ($versionNumber >> 8) & 0xFF,
    '构建号' => $versionNumber & 0xFF
];

echo "   分段解析 (AABBCCDD):\n";
foreach ($parts as $name => $value) {
    echo "   $name: $value\n";
}
echo "\n";

// 创建一些测试用的版本号进行对比
echo "参考对比:\n";
$testVersions = [
    20240101 => "2024年1月1日",
    20240201 => "2024年2月1日", 
    20250201 => "2025年2月1日",
    1701 => "2017年1月 (旧格式)",
    1712 => "2017年12月 (旧格式)"
];

foreach ($testVersions as $ver => $desc) {
    echo "   $ver => $desc\n";
}

echo "\n结论:\n";
if ($versionNumber == 842020146) {
    echo "根据分析，842020146最可能是:\n";
    echo "• 一个自定义的版本编码格式\n";
    echo "• 可能包含了年份、月份或其他标识信息\n";
    echo "• 具体含义需要查看数据文件的生成规则\n";
    echo "\n建议联系数据文件维护者确认具体的版本号编码规则。";
}

echo "\n\n=== 版本号实用信息 ===\n";
echo "文件信息:\n";
echo "- 索引总数: " . $locator->getIndexCount() . " 条\n";
echo "- 运营商类型: " . count($locator->getOperatorTypes()) . " 种\n";
echo "- 数据更新时间: 2025年02月\n";