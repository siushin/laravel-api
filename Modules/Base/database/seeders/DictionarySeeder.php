<?php

namespace Modules\Base\Database\Seeders;

use Modules\Base\Enums\DictionaryCategoryEnum;
use Modules\Base\Models\Dictionary;
use Modules\Base\Models\DictionaryCategory;
use Illuminate\Database\Seeder;
use Siushin\LaravelTool\Enums\RequestSourceEnum;
use Siushin\LaravelTool\Enums\UploadFileTypeEnum;

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
                'tpl_path'      => 'tpl/Dictionary.xlsx',
            ];
        }
        DictionaryCategory::query()->upsert(
            $categories,
            ['category_code'],
            ['category_name']
        );

        // 数据字典
        $dictionary_map = [
            DictionaryCategoryEnum::UserType->name            => RequestSourceEnum::cases(),
            DictionaryCategoryEnum::AllowUploadFileType->name => UploadFileTypeEnum::cases(),
        ];
        $dictionary_data = [];
        foreach ($dictionary_map as $category_code => $dictionary_enums) {
            $category_id = DictionaryCategory::checkCodeValidate(compact('category_code'));
            foreach ($dictionary_enums as $dictionary_item) {
                $dictionary_data[] = [
                    'category_id'      => $category_id,
                    'dictionary_name'  => $dictionary_item->name,
                    'dictionary_value' => $dictionary_item->value,
                    'parent_id'        => 0,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }
        }
        Dictionary::query()->insert($dictionary_data);
    }
}
