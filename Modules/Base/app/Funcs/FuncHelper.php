<?php

/**
 * 助手函数：通用辅助函数
 */

/**
 * 从枚举注释中提取中文描述
 * @param UnitEnum $case 枚举实例
 * @return string|null 中文描述，如果未找到则返回 null
 * @throws ReflectionException
 * @author siushin<siushin@163.com>
 */
function getEnumComment(UnitEnum $case): ?string
{
    $reflection = new ReflectionClass($case::class);
    $file = $reflection->getFileName();
    if ($file && file_exists($file)) {
        $lines = file($file);
        // 获取枚举值（BackedEnum 有 value 属性，Pure Enum 没有）
        $enumValue = ($case instanceof BackedEnum) ? $case->value : $case->name;

        // 查找匹配的 case 行
        foreach ($lines as $line) {
            // 匹配 case CaseName = 'value'; // 中文描述
            if (preg_match('/case\s+' . preg_quote($case->name, '/') . '\s*=\s*[\'"]' . preg_quote($enumValue, '/') . '[\'"]\s*;\s*\/\/\s*(.+)/', $line, $matches)) {
                return trim($matches[1]);
            }
        }
    }
    return null;
}

/**
 * 构建枚举字段的注释字符串
 * @param array  $enumCases 枚举cases数组
 * @param string $typeName  类型名称（如：账号类型、组织架构类型等）
 * @return string 格式：类型名[value:描述,value:描述,...]
 * @throws ReflectionException
 * @author siushin<siushin@163.com>
 */
function buildEnumComment(array $enumCases, string $typeName): string
{
    $commentParts = [];
    foreach ($enumCases as $case) {
        $description = getEnumComment($case);
        $value = ($case instanceof BackedEnum) ? $case->value : $case->name;
        if ($description) {
            $commentParts[] = $value . ':' . $description;
        } else {
            $commentParts[] = $value;
        }
    }
    return $typeName . '[' . implode(',', $commentParts) . ']';
}

