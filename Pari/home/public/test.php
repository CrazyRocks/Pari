<?php
$sphinx = new \SphinxClient();

$sphinx->SetServer('10.80.1.114', 9312);
$sphinx->SetConnectTimeout(1);
$sphinx->SetArrayResult(true);
$sphinx->SetRankingMode(SPH_RANK_PROXIMITY_BM25);
$sphinx->setLimits(0, 10,10000);
$sphinx->setMatchMode(SPH_MATCH_BOOLEAN);
$sphinx->SetSortMode(SPH_SORT_EXTENDED, "sales_volume DESC");

$sphinx->SetFilter('spec_value_id', ['409']);
//$sphinx->setIndexWeights(array(100, 1));
if (isset($param['catArray']) && !empty($param['catArray'])) {
    $sphinx->SetFilter('gc_id', $param['catArray']);
}
if (isset($param['styleArray']) && !empty($param['styleArray'])) {
    $sphinx->SetFilter('style_id', $param['styleArray']);
}
if (isset($param['brandArray']) && !empty($param['brandArray'])) {
    $sphinx->SetFilter('brand_id', $param['brandArray']);
}
$sphinx->SetGroupBy('goods_id', SPH_GROUPBY_ATTR,'sales_volume DESC');
$res = $sphinx->Query('', 'sku_spec');
echo '<pre>';var_dump($res);die;
;

?>