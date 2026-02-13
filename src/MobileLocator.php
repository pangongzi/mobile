<?php
// +----------------------------------------------------------------------
// | 功能介绍 手机号码归属地查询
// +----------------------------------------------------------------------
// | @author PanWenHao
// +----------------------------------------------------------------------
// | @copyright PanWenHao Inc.
// +----------------------------------------------------------------------
// 返回数据格式示例:
// array (
//   'mobile' => '13812345678',
//   'province' => '浙江',
//   'city' => '嘉兴',
//   'zip_code' => '314000',
//   'area_code' => '0573',
//   'type' => 1,
//   'type_str' => '移动',
//   'info' => '浙江 | 嘉兴 | 314000 | 0573',
// )

namespace Pangongzi\Mobile;

class MobileLocator
{
  // 默认数据文件路径
  private const DEFAULT_DATA_FILE = __DIR__ . '/data/mobile.dat';

  // 运营商类型映射
  private const OPERATOR_TYPES = [
    0 => '未知',
    1 => '移动',
    2 => '联通',
    3 => '电信',
    4 => '电信虚拟运营商',
    5 => '联通虚拟运营商',
    6 => '移动虚拟运营商',
    7 => '中国广电',
    8 => '中国广电虚拟运营商',
  ];

  // 单例实例
  private static $instance = null;

  // 数据文件内容（内存中）
  private string $data;

  // 文件大小
  private int $fileSize = 0;

  // 索引起始位置
  private int $indexBegin;

  // 索引总数
  private int $indexCount;

  // 当前查询的手机号码
  private string $currentMobile = '';

  /**
   * 私有构造函数，防止直接实例化
   * @param string $dataFile 数据文件路径
   * @throws \Exception 如果文件不存在或读取失败
   */
  private function __construct(string $dataFile = '')
  {
    $dataFile = $dataFile ?: self::DEFAULT_DATA_FILE;

    if (!file_exists($dataFile)) {
      throw new \Exception("Data file not found: $dataFile");
    }

    // 一次性读取整个文件到内存，提高查询性能
    $this->data = file_get_contents($dataFile);
    $this->fileSize = strlen($this->data);

    // 读取索引起始位置（第4-7字节）
    $this->indexBegin = unpack('V', substr($this->data, 4, 4))[1];

    // 计算索引总数
    $this->indexCount = intval(($this->fileSize - $this->indexBegin) / 9);
  }

  /**
   * 获取单例实例
   * @param string $dataFile 数据文件路径
   * @return self
   */
  public static function getInstance(string $dataFile = ''): self
  {
    if (self::$instance === null) {
      self::$instance = new self($dataFile);
    }
    return self::$instance;
  }

  /**
   * 查找手机号码归属地信息
   * @param string|int $mobile 手机号码
   * @return array|null 归属地信息数组，找不到返回null
   */
  public function find($mobile): ?array
  {
    // 参数验证
    if (empty($mobile)) {
      return null;
    }

    // 严格验证手机号格式（11位纯数字）
    if (!preg_match('/^\d{11}$/', (string) $mobile)) {
      return null;
    }

    $this->currentMobile = (string) $mobile;

    // 取手机号前7位作为查询键
    $mobilePrefix = substr($this->currentMobile, 0, 7);

    // 二分查找
    $left = 0;
    $right = $this->indexCount - 1;

    while ($left <= $right) {
      $mid = intval(($left + $right) / 2);
      $currentIndexData = $this->getIndexData($mid);

      if ($currentIndexData['prefix'] == $mobilePrefix) {
        // 找到匹配项，解析详细信息
        return $this->parseLocationInfo(
          $currentIndexData['offset'],
          $currentIndexData['type']
        );
      } elseif ($currentIndexData['prefix'] < $mobilePrefix) {
        $left = $mid + 1;
      } else {
        $right = $mid - 1;
      }
    }

    // 未找到匹配项
    return null;
  }

  /**
   * 获取索引数据
   * @param int $index 索引位置
   * @return array 包含prefix、offset、type的数组
   */
  private function getIndexData(int $index): array
  {
    $position = $this->indexBegin + $index * 9;

    // 读取9字节索引数据：4字节前缀 + 4字节偏移 + 1字节类型
    $data = substr($this->data, $position, 9);

    // 一次性解包所有字段：V=无符号长整型(4字节), C=无符号字符(1字节)
    $unpacked = unpack('Vprefix/Voffset/Ctype', $data);

    return [
      'prefix' => $unpacked['prefix'],
      'offset' => $unpacked['offset'],
      'type' => $unpacked['type']
    ];
  }

  /**
   * 解析归属地信息
   * @param int $offset 记录在文件中的偏移位置
   * @param int $type 运营商类型
   * @return array|null 完整的归属地信息，数据格式错误时返回null
   */
  private function parseLocationInfo(int $offset, int $type): ?array
  {
    // 从指定偏移位置读取以\0结尾的字符串
    $record = '';
    for ($i = $offset; $i < $this->fileSize && $this->data[$i] !== "\0"; $i++) {
      $record .= $this->data[$i];
    }

    // 解析记录内容
    $parts = explode('|', $record);

    if (count($parts) < 4) {
      return null;
    }

    $operatorTypeStr = self::OPERATOR_TYPES[$type] ?? self::OPERATOR_TYPES[0];

    // 构造返回数据
    $result = [
      'mobile' => $this->currentMobile,
      'province' => $parts[0],
      'city' => $parts[1],
      'zip_code' => $parts[2],
      'area_code' => $parts[3],
      'type' => $type,
      'type_str' => $operatorTypeStr
    ];

    // 构造info字段
    $infoParts = array_filter([
      $result['type'],
      $result['type_str'],
      $result['province'],
      $result['city'],
      $result['zip_code'],
      $result['area_code']
    ]);
    $result['info'] = implode(' | ', $infoParts);

    return $result;
  }

  /**
   * 获取数据文件版本号
   * @return int 版本号
   */
  public function getVersion(): int
  {
    return unpack('V', substr($this->data, 0, 4))[1];
  }

  /**
   * 获取索引总数
   * @return int 索引记录数量
   */
  public function getIndexCount(): int
  {
    return $this->indexCount;
  }

  /**
   * 获取运营商类型映射表
   * @return array 运营商类型映射
   */
  public function getOperatorTypes(): array
  {
    return self::OPERATOR_TYPES;
  }

  /**
   * 析构函数，清理资源
   * 由于使用file_get_contents，无需特殊清理
   */
  public function __destruct()
  {
    // 内存会自动回收，无需手动处理
  }
}