<?php
/**
 * 高级版本号分析工具
 * 重点关注十六进制表示的ASCII字符含义
 */

echo "=== 高级版本号分析 ===\n\n";

$versionNumber = 842020146;
echo "版本号: $versionNumber\n";
echo "十六进制: 0x" . strtoupper(dechex($versionNumber)) . "\n\n";

// 关键发现：0x32303532 对应 ASCII 字符
echo "🔑 重要发现：\n";
$hexString = dechex($versionNumber);
echo "十六进制字符串: $hexString\n";

// 将十六进制按字节分割并转换为ASCII
$bytes = str_split($hexString, 2);
$asciiChars = [];

echo "\n字节分析:\n";
foreach ($bytes as $index => $byte) {
    $decimal = hexdec($byte);
    $ascii = chr($decimal);
    $asciiChars[] = $ascii;
    echo "字节" . ($index + 1) . ": 0x$byte (" . $decimal . ") = '$ascii'\n";
}

echo "\nASCII字符串拼接结果: '" . implode('', $asciiChars) . "'\n";

// 验证是否为 "2052"
if (implode('', $asciiChars) === '2052') {
    echo "🎉 找到了！版本号 842020146 对应 ASCII 字符串 '2052'\n";
    echo "这很可能表示 2052年 或 版本2.0.5.2\n\n";
}

// 其他可能的解释
echo "其他可能的解释:\n";

// 1. 如果按照常见的版本号格式解析
echo "1. 传统版本号格式 (MAJOR.MINOR.PATCH.BUILD):\n";
$major = ($versionNumber >> 24) & 0xFF;
$minor = ($versionNumber >> 16) & 0xFF;
$patch = ($versionNumber >> 8) & 0xFF;
$build = $versionNumber & 0xFF;

echo "   主版本: $major\n";
echo "   次版本: $minor\n";
echo "   修订号: $patch\n";
echo "   构建号: $build\n";
echo "   格式: $major.$minor.$patch.$build\n\n";

// 2. 如果是压缩的日期信息
echo "2. 压缩日期信息分析:\n";
// 尝试不同的位组合
$possibleYear = (($versionNumber >> 16) & 0xFFFF);
$possibleMonth = ($versionNumber >> 8) & 0xFF;
$possibleDay = $versionNumber & 0xFF;

echo "   高16位年份: $possibleYear\n";
echo "   中8位月份: $possibleMonth\n";
echo "   低8位日期: $possibleDay\n";

if ($possibleYear >= 2000 && $possibleYear <= 2030 && 
    $possibleMonth >= 1 && $possibleMonth <= 12 && 
    $possibleDay >= 1 && $possibleDay <= 31) {
    echo "   ✓ 可能的有效日期: {$possibleYear}年{$possibleMonth}月{$possibleDay}日\n";
} else {
    echo "   ✗ 不是有效的日期组合\n";
}

echo "\n=== 结论 ===\n";
echo "版本号 842020146 最可能的含义:\n";
echo "✅ ASCII编码: '2052' (对应十六进制 0x32303532)\n";
echo "可能表示:\n";
echo "- 年份: 2052年\n";
echo "- 版本号: 2.0.5.2\n";
echo "- 内部标识: 2052\n\n";

echo "💡 建议:\n";
echo "1. 查看原始数据文件的生成脚本或文档\n";
echo "2. 联系数据维护者确认编码规则\n";
echo "3. 在README中添加版本号格式说明\n";

// 创建一个版本号生成示例
echo "\n=== 版本号生成示例 ===\n";
function createVersionNumber($asciiString) {
    $hex = '';
    for ($i = 0; $i < strlen($asciiString); $i++) {
        $hex .= sprintf('%02x', ord($asciiString[$i]));
    }
    return hexdec($hex);
}

$examples = ['2024', '2025', '1701', 'v2.1'];
echo "不同字符串对应的版本号:\n";
foreach ($examples as $str) {
    $ver = createVersionNumber($str);
    echo "  '$str' => $ver (0x" . strtoupper(dechex($ver)) . ")\n";
}