<?php

namespace Database\Seeders;

use App\Enums\DictionaryCategoryEnum;
use App\Models\SysDictionary;
use App\Models\SysDictionaryCategory;
use Illuminate\Database\Seeder;
use Siushin\LaravelTool\Enums\SysUploadFileType;
use Siushin\LaravelTool\Enums\SysUserType;

/**
 * 数据填充：字典
 */
class DictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        // 数据字典分类
        $categories = [];
        foreach (DictionaryCategoryEnum::cases() as $category) {
            $categories[] = [
                'category_name' => $category->value,
                'category_code' => $category->name,
                'tpl_path' => 'tpl/Dictionary.xlsx',
            ];
        }
        SysDictionaryCategory::query()->upsert(
            $categories,
            ['category_code'],
            ['category_name']
        );

        // 数据字典
        $dictionary_map = [
            DictionaryCategoryEnum::UserType->name => SysUserType::cases(),
            DictionaryCategoryEnum::AllowUploadFileType->name => SysUploadFileType::cases(),
        ];
        $dictionary_data = [];
        foreach ($dictionary_map as $category_code => $dictionary_enums) {
            $category_id = SysDictionaryCategory::checkCodeValidate(compact('category_code'));
            foreach ($dictionary_enums as $dictionary_item) {
                $dictionary_data[] = [
                    'category_id' => $category_id,
                    'dictionary_name' => $dictionary_item->name,
                    'dictionary_value' => $dictionary_item->value,
                    'parent_id' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        SysDictionary::query()->insert($dictionary_data);
    }
}
