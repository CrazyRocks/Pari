<?php
/**
 * Copyright (c) 2012-2014 heungkong.com/kinhom/
 *
 * Kinhom Electronic Commerce Technology Team
 */

namespace Kinhom\Services\Region;

use Kinhom\Services\BaseService;
use Kinhom\Lib\Biz\RegionsLib;
use Kinhom\Models\Regions;
/**
 * 地址
 * @package Kinhom\Services
 */
class RegionService {

    // setter getter

    /**
     * 获取地址选择，默认广东省,广州市
     * @return \stdClass
     */
    public function getRegionsChoice()
    {
        return RegionsLib::getRegionsChooce();
    }
    
    /**
     * 获取地址子列表
     * @return \Kinhom\Lib\Biz\Regions
     */
    public function getChildRegions($parentId)
    {
        if (empty($parentId)) {
            return null;
        }
        return Regions::find(['conditions'=>'parent_id = ?1','bind'=>['1'=>$parentId]]);
    }
    
    /**
     * 获取省级单位
     */
    public function getProvince()
    {
        return Regions::find('region_type=1');
    }

    /**
     * 获取一条地区数据
     * @param int   $regionId 自增ID
     * @return Regions
     */
    public function getRegionOne($regionId)
    {
        if (!empty($regionId) && is_numeric($regionId)) {
            return Regions::findFirst($regionId);
        } else {
            return null;
        }
    }
    
    public function getRegionsModel()
    {
        return new Regions();
    }
    
    public function findFirst($sql)
    {
        return Regions::findFirst($sql);
    }
    
    public function find($sql)
    {
        return Regions::find($sql);
    }
    
    public function getRegionName($sql)
    {
        $d = $this->findFirst($sql);
        return $d->region_name;
    }
}