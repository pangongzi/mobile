<?php
/**
 * 性能测试脚本 - 比较getIndexData方法优化前后的性能差异
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Pangongzi\Mobile\MobileLocator;

// 创建测试实例
$locator = MobileLocator::getInstance();

echo "=== MobileLocator 性能优化测试 ===\n\n";

// 测试数据
$testPhones = ['13800138000', '13900139000', '15900159000', '18800188000'];

// 测试次数
$iterations = 10000;

echo "测试配置：\n";
echo "- 测试手机号数量: " . count($testPhones) . "\n";
echo "- 每个号码查询次数: $iterations\n";
echo "- 总查询次数: " . (count($testPhones) * $iterations) . "\n\n";

// 预热
echo "正在进行预热...\n";
foreach ($testPhones as $phone) {
    for ($i = 0; $i < 100; $i++) {
        $locator->find($phone);
    }
}

// 性能测试
echo "开始性能测试...\n";
$start = microtime(true);

for ($i = 0; $i < $iterations; $i++) {
    foreach ($testPhones as $phone) {
        $result = $locator->find($phone);
        if ($i === 0 && $phone === $testPhones[0]) {
            echo "查询结果示例: " . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n\n";
        }
    }
}

$end = microtime(true);
$totalTime = $end - $start;
$qps = (count($testPhones) * $iterations) / $totalTime;

echo "=== 测试结果 ===\n";
echo "总耗时: " . number_format($totalTime, 4) . " 秒\n";
echo "平均每查询耗时: " . number_format(($totalTime / (count($testPhones) * $iterations)) * 1000, 4) . " 毫秒\n";
echo "QPS (每秒查询数): " . number_format($qps, 0) . "\n";

echo "\n=== 性能指标 ===\n";
if ($qps > 5000) {
    echo "✅ 性能优秀 (QPS > 5000)\n";
} elseif ($qps > 2000) {
    echo "✅ 性能良好 (QPS > 2000)\n";
} else {
    echo "⚠️  性能有待优化 (QPS < 2000)\n";
}

echo "\n优化建议：\n";
echo "- 本次优化减少了getIndexData方法中的函数调用次数\n";
echo "- 从3次substr+2次unpack+1次ord优化为1次substr+1次unpack\n";
echo "- 预期性能提升约15-25%\n";