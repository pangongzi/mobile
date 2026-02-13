<?php
/**
 * 测试优化后的MobileLocator版本
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Pangongzi\Mobile\MobileLocator;

try {
    echo "=== 测试优化后的MobileLocator ===\n\n";
    
    // 初始化
    $locator = MobileLocator::getInstance();
    
    echo "数据文件版本: " . $locator->getVersion() . "\n";
    echo "索引总数: " . $locator->getIndexCount() . "\n";
    echo "运营商类型: " . count($locator->getOperatorTypes()) . "种\n\n";
    
    // 测试用例
    $testPhones = [
        '13812345678',  // 移动
        '13012345678',  // 联通
        '13312345678',  // 电信
        '19212345678',  // 广电
        '17012345678',  // 虚拟运营商
        '12345678901',  // 无效号码
        'abc12345678',  // 非数字
        ''              // 空值
    ];
    
    foreach ($testPhones as $phone) {
        echo "查询号码: $phone\n";
        $result = $locator->find($phone);
        
        if ($result) {
            echo "  省份: {$result['province']}\n";
            echo "  城市: {$result['city']}\n";
            echo "  邮编: {$result['zip_code']}\n";
            echo "  区号: {$result['area_code']}\n";
            echo "  运营商: {$result['type_str']} (类型: {$result['type']})\n";
            echo "  完整信息: {$result['info']}\n";
        } else {
            echo "  未找到归属地信息\n";
        }
        echo "---\n";
    }
    
    // 性能测试
    echo "\n=== 性能测试 ===\n";
    $startTime = microtime(true);
    $testCount = 1000;
    
    for ($i = 0; $i < $testCount; $i++) {
        $randomPhone = '1' . str_pad(rand(300000000, 999999999), 10, '0', STR_PAD_LEFT);
        $locator->find($randomPhone);
    }
    
    $endTime = microtime(true);
    $duration = ($endTime - $startTime) * 1000;
    
    echo "查询 {$testCount} 次耗时: " . number_format($duration, 2) . " ms\n";
    echo "平均每次查询: " . number_format($duration / $testCount, 4) . " ms\n";
    echo "QPS: " . number_format($testCount / ($duration / 1000), 0) . "\n";
    
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
}