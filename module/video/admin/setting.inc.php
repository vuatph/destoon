<?php
defined('IN_DESTOON') or exit('Access Denied');
$tab = isset($tab) ? intval($tab) : 0;
$all = isset($all) ? intval($all) : 0;
if($submit) {
	$tmp = '';
	if($MOD['flvstart'] != $setting['flvstart']) {
		delete_upload($MOD['flvstart']);
		$tmp .= $setting['flvstart'];
	}
	if($MOD['flvend'] != $setting['flvend']) {
		delete_upload($MOD['flvend']);
		$tmp .= $setting['flvend'];
	}
	if($tmp) clear_upload($tmp);
	if($setting['split']) {
		$setting['fulltext'] = 0;
		cache_write($moduleid.'.part', $moduleid);
	} else {
		cache_delete($moduleid.'.part');
	}
	foreach($setting as $k=>$v) {
		if(strpos($k, 'seo_') === false) continue;
		seo_check($v) or msg('SEO信息包含非法字符');
	}
	update_setting($moduleid, $setting);
	cache_module($moduleid);
	if($setting['php_list_urlid'] != $MOD['php_list_urlid'] || $setting['htm_list_urlid'] != $MOD['htm_list_urlid'] || $setting['htm_list_prefix'] != $MOD['htm_list_prefix'] || $setting['list_html'] != $MOD['list_html']) {
		$CATEGORY = cache_read('category-'.$moduleid.'.php');
		$MOD = $setting;
		foreach($CATEGORY as $c) {
			update_category($c);
		}
		cache_category($moduleid);
	}
	if($setting['php_item_urlid'] != $MOD['php_item_urlid'] || $setting['htm_item_urlid'] != $MOD['htm_item_urlid'] || $setting['htm_item_prefix'] != $MOD['htm_item_prefix'] || $setting['show_html'] != $MOD['show_html']) {
		msg('设置保存成功，开始更新地址', '?moduleid='.$moduleid.'&file=html&action=show&update=1&num=1000');
	}
	dmsg('更新成功', '?moduleid='.$moduleid.'&file='.$file.'&tab='.$tab);
} else {
	$r = $db->get_one("SELECT MAX(itemid) AS maxid FROM {$table}");
	$maxid = $r['maxid'];
	extract(dhtmlspecialchars($MOD));
	if($kw) {
		$all = 1;
		ob_start();
	}
	include tpl('setting', $module);
	if($kw) {
		$data = $content = ob_get_contents();
		ob_clean();
		$data = preg_replace('\'(?!((<.*?)|(<a.*?)|(<strong.*?)))('.$kw.')(?!(([^<>]*?)>)|([^>]*?</a>)|([^>]*?</strong>))\'si', '<span class=highlight>'.$kw.'</span>', $data);
		$data = preg_replace('/<span class=highlight>/', '<a name=high></a><span class=highlight>', $data, 1);
		echo $data ? $data : $content;
	}
}
?>