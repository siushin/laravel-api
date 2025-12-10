<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Siushin\LaravelTool\Enums\LogActionEnum;
use Siushin\LaravelTool\Traits\ModelTool;
use Siushin\LaravelTool\Utils\Tree;
use Siushin\Util\Traits\ParamTool;

/**
 * 模型：组织架构
 */
class SysOrganization extends Model
{
    use HasFactory, ParamTool, ModelTool;

    protected $primaryKey = 'organization_id';
    protected $table      = 'sys_organization';

    protected $fillable = [
        'organization_id', 'organization_name', 'organization_pid', 'full_organization_pid',
    ];

    protected $hidden = [
        'full_organization_pid', 'created_at', 'updated_at',
    ];

    /**
     * 获取组织架构（树状结构）
     * @param array $params
     * @return array
     * @author siushin<siushin@163.com>
     */
    public static function getTreeData(array $params = []): array
    {
        $organization_name = $params['organization_name'] ?? false;

        if ($organization_name) {
            $where[] = ['organization_name', 'like', "%$organization_name%"];
        } else {
            $where = [];
        }

        $searchData = self::query()->where($where)->pluck('full_organization_pid')->toArray();
        // 根据搜索结果，找出所有上级数据
        $all_organization_ids = [];
        foreach ($searchData as $full_organization_pid) {
            array_push($all_organization_ids, ...explode(',', trim($full_organization_pid, ',')));
        }
        $all_organization_ids = array_unique($all_organization_ids);
        $data = self::query()->whereIn('organization_id', $all_organization_ids)->get()->toArray();

        // 加上根数据
        $organization_ids = array_column($data, 'organization_pid');
        if (!in_array(0, $organization_ids)) {
            $rootData = self::query()->where('organization_pid', 0)->limit(1)->get()->toArray();
            $data = array_merge($rootData, $data);
        }

        return (new Tree('organization_id', 'organization_pid'))->getTree($data);
    }

    /**
     * 新增组织架构
     * @param array $params
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function addOrganization(array $params = []): array
    {
        $params['only_empty_check'] = ['organization_pid'];
        self::checkEmptyParam($params, ['organization_name']);

        $params['organization_pid'] == 0 && throw_exception('只能创建一个顶级组织架构');

        // 检查上级组织架构ID有效性
        $parent_full_organization_pid = self::query()->where('organization_id', $params['organization_pid'])->value('full_organization_pid');
        !$parent_full_organization_pid && throw_exception('参数organization_pid无效');
        // 同级查重
        $checkRepeat = self::query()->where('organization_pid', $params['organization_pid'])
            ->where('organization_name', $params['organization_name'])
            ->value('organization_id');
        $checkRepeat && throw_exception('该层级下已有数据，新增失败！');

        // 创建组织架构
        $params['full_organization_pid'] = '';
        $params['organization_pid'] = intval($params['organization_pid']);
        $organization = self::query()->create($params);
        !$organization && throw_exception('新增组织架构失败');

        // 更新完整组织架构ids
        $organization['full_organization_pid'] = $parent_full_organization_pid . $organization['organization_id'] . ',';
        $organization->save();

        logging(LogActionEnum::insert->name, "新增组织架构($organization->organization_name)", $organization->toArray());

        return $organization->toArray();
    }

    /**
     * 更新组织架构
     * @param array $params
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function updateOrganization(array $params = []): array
    {
        self::checkEmptyParam($params, ['organization_id', 'organization_name']);

        $organization_id = (int)$params['organization_id'];

        $info = self::query()->find($params['organization_id']);
        $old_data = $info->toArray();

        !$info && throw_exception(self::notFoundDataMsg($organization_id));

        $info->organization_name = $params['organization_name'];
        $info->save();

        $extend_data = compareArray($info->toArray(), $old_data);
        logging(LogActionEnum::update->name, "更新组织架构($info->organization_name)", $extend_data);

        return [];
    }

    /**
     * 删除组织架构
     * @param array $params
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function deleteOrganization(array $params = []): array
    {
        self::checkEmptyParam($params, ['organization_id']);
        $organization_id = (int)$params['organization_id'];

        $info = self::query()->find($organization_id);
        !$info && throw_exception(self::notFoundDataMsg($organization_id));
        $info->organization_pid == 0 && throw_exception('顶级组织架构不能删除');

        // 删除自己及所有下属组织架构数据
        $delete_sub_ids = self::getSubOrganizationIds($info['full_organization_pid']);
        $delete_total = self::destroy($delete_sub_ids);

        $extend_data = compact('delete_sub_ids');
        logging(LogActionEnum::delete->name, "删除组织架构($info->organization_name)", $extend_data);

        return compact('organization_id', 'delete_total');
    }

    /**
     * 获取所有下属组织架构ID
     * @param string $full_organization_pid
     * @return array
     * @author siushin<siushin@163.com>
     */
    public static function getSubOrganizationIds(string $full_organization_pid): array
    {
        $where[] = ['full_organization_pid', 'like', "%$full_organization_pid%"];
        return self::query()->where($where)->pluck('organization_id')->toArray();
    }

    /**
     * 移动组织架构
     * @param array $params
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function moveOrganization(array $params): array
    {
        self::checkEmptyParam($params, ['organization_id', 'belong_organization_id']);

        DB::transaction(function () use ($params) {
            $organization_id = (int)$params['organization_id'];

            $organization = self::query()->find($organization_id);
            $oldFullOrganizationId = $oldFullOrganizationPid = $organization->full_organization_pid;
            if (!$organization) {
                throw_exception(self::notFoundDataMsg($organization_id));
            }

            $newParentId = (int)$params['belong_organization_id'];
            $newOrganization = self::query()->find($newParentId);
            if (!$newOrganization) {
                throw_exception(self::notFoundDataMsg($newParentId));
            }
            $newFullOrganizationId = $newOrganization->full_organization_pid . $organization_id . ',';

            // 找出所有自己、以及子孙数据
            $allSubOrganizationIds = self::query()
                ->whereLike('full_organization_pid', "%$oldFullOrganizationPid%")
                ->where('organization_id', '!=', $organization_id)
                ->pluck('organization_id')
                ->toArray();

            // 更新组织的 parent ID 和 full_organization_pid
            self::query()->where('organization_id', $organization_id)
                ->update([
                    'organization_pid' => $newOrganization->organization_id,
                    'full_organization_pid' => $newFullOrganizationId
                ]);

            // 更新所有子、孙组织的 full_organization_pid
            self::query()->whereIn('organization_id', $allSubOrganizationIds)
                ->update([
                    'full_organization_pid' => DB::raw("REPLACE(`full_organization_pid`, '$oldFullOrganizationPid', '$newFullOrganizationId')")
                ]);

            $extend_data = compact('newFullOrganizationId', 'oldFullOrganizationId');
            logging(LogActionEnum::update->name, "移动组织架构($newOrganization->organization_name)", $extend_data);
        });

        return [];
    }
}
