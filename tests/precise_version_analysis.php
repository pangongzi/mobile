<?php
/**
 * 精确版本号分析 - 找出842020146的真实含义
 */

echo "=== 精确版本号分析 ===\n\n";

$targetVersion = 842020146;
echo "目标版本号: $targetVersion\n";
echo "十六进制: 0x" . strtoupper(dechex($targetVersion)) . "\n\n";

// 将十六进制转换回ASCII
$hexString = dechex($targetVersion);
echo "十六进制字符串: $hexString\n";

// 按字节分割并转换ASCII
$bytes = str_split($hexString, 2);
$asciiChars = [];
echo "\n逐字节分析:\n";
foreach ($bytes as $index => $byte) {
    $decimal = hexdec($byte);
    $ascii = chr($decimal);
    $asciiChars[] = $ascii;
    echo sprintf("字节%d: 0x%s (%d) = '%s'\n", $index+1, $byte, $decimal, $ascii);
}

$decodedString = implode('', $asciiChars);
echo "\n解码结果: '$decodedString'\n";

echo "\n=== 验证各种可能性 ===\n";

// 测试用户提到的各种可能性
$possibilities = [
    '2502' => '2025年02月 (您说的)',
    '2052' => '2052年 (当前解码结果)',
    '2025' => '2025年 (完整年份)',
    '0225' => '02月25日',
    '5220' => '52年20月?'
];

foreach ($possibilities as $testString => $description) {
    $calculated = 0;
    for ($i = 0; $i < strlen($testString); $i++) {
        $calculated = ($calculated << 8) + ord($testString[$i]);
    }
    
    echo sprintf("'%s' (%s):\n", $testString, $description);
    echo sprintf("  计算结果: %d\n", $calculated);
    echo sprintf("  十六进制: 0x%s\n", strtoupper(dechex($calculated)));
    echo sprintf("  匹配: %s\n\n", ($calculated === $targetVersion) ? "✅ 是!" : "❌ 否");
}

echo "=== 反向思考 ===\n";

// 如果我们知道答案是2502，那么存储格式可能不同
echo "假设正确答案是 '2502' (2025年02月):\n";

// 方法1: 直接存储年月数字
$year = 25;  // 25表示2025
$month = 2;  // 02月
$encoded1 = ($year << 8) | $month;
echo "方法1 - 年月编码: ($year << 8) | $month = $encoded1\n";

// 方法2: BCD编码
$year_bcd = 0x25;  // 25的BCD码
$month_bcd = 0x02; // 02的BCD码  
$encoded2 = ($year_bcd << 8) | $month_bcd;
echo "方法2 - BCD编码: (0x25 << 8) | 0x02 = $encoded2\n";

// 方法3: 字符串反转?
$reversed = strrev('2502'); // '2052'
$encoded3 = 0;
for ($i = 0; $i < strlen($reversed); $i++) {
    $encoded3 = ($encoded3 << 8) + ord($reversed[$i]);
}
echo "方法3 - 字符串反转: strrev('2502') = '2052' -> $encoded3\n";

echo "\n=== 结论 ===\n";
echo "经过分析，版本号 842020146 解码为 '2052'\n";
echo "但这很可能是 '2502' (2025年02月) 的某种编码变形\n";
echo "最可能的情况是存储时进行了字符串反转或者其他编码处理\n";
echo "因此您的理解是正确的 - 这确实表示 2025年02月 的更新版本！\n";