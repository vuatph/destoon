<?php
defined('IN_DESTOON') or exit('Access Denied');
require MD_ROOT.'/exhibit.class.php';
$do = new exhibit($moduleid);
$menus = array (
    array('����'.$MOD['name'], '?moduleid='.$moduleid.'&action=add'),
    array($MOD['name'].'�б�', '?moduleid='.$moduleid),
    array('���'.$MOD['name'], '?moduleid='.$moduleid.'&action=check'),
    array('����'.$MOD['name'], '?moduleid='.$moduleid.'&action=expire'),
    array('δͨ��'.$MOD['name'], '?moduleid='.$moduleid.'&action=reject'),
    array('����վ', '?moduleid='.$moduleid.'&action=recycle'),
    array('�ƶ�'.$MOD['name'], '?moduleid='.$moduleid.'&action=move'),
);

if(in_array($action, array('add', 'edit'))) {
	$FD = cache_read('fields-'.substr($table, strlen($DT_PRE)).'.php');
	if($FD) require DT_ROOT.'/include/fields.func.php';
	isset($post_fields) or $post_fields = array();
}

if($_catids) {
	$_catid = explode(',', $_catids);
	foreach($CATEGORY as $k=>$c) {
		if($c['parentid'] > 0) continue;
		if(!in_array($k, $_catid)) unset($CATEGORY[$k]);
	}
	foreach($_catid as $t) {
		$_childs .= ','.($CATEGORY[$t]['child'] ? $CATEGORY[$t]['arrchildid'] : $t);
	}
	if($_childs) {
		$_childs = substr($_childs, 1);
		$_child = explode(',', $_childs);
		if(isset($post['catid']) && $post['catid'] && !in_array($post['catid'], $_child)) msg('����Ȩ���д˲���');
		if($itemid) {
			if(is_array($itemid)) {
				foreach($itemis as $_itemid) {
					item_check($_itemid) or msg('����Ȩ���д˲���');
				}
			} else {
				item_check($itemid) or msg('����Ȩ���д˲���');
			}
		}
	}
}

if(in_array($action, array('', 'check', 'expire', 'reject', 'recycle'))) {
	$sfields = array('ģ��', '����', 'չ������', 'չ����ַ', 'չ������', '���쵥λ', '�а쵥λ', '��ϵ��', '��Ա��', 'IP');
	$dfields = array('keyword', 'title', 'city', 'address', 'hallname', 'sponsor', 'undertaker', 'truename', 'username', 'ip');
	$sorder  = array('�������ʽ', '��ʼʱ�併��', '��ʼʱ������', '����ʱ�併��', '����ʱ������', '����ʱ�併��', '����ʱ������', '�����������', '�����������', '��ϢID����', '��ϢID����');
	$dorder  = array($MOD['order'], 'fromtime DESC', 'fromtime ASC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC', 'hits DESC', 'hits ASC', 'itemid DESC', 'itemid ASC');
	$level = isset($level) ? intval($level) : 0;
	$process = isset($process) ? intval($process) : 0;
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	isset($order) && isset($dorder[$order]) or $order = 0;
	$level = isset($level) ? intval($level) : 0;

	isset($datetype) && in_array($datetype, array('edittime', 'addtime', 'fromtime', 'totime')) or $datetype = 'fromtime';
	$fromdate = isset($fromdate) && preg_match("/^([0-9]{8})$/", $fromdate) ? $fromdate : '';
	$fromtime = $fromdate ? strtotime($fromdate.' 0:0:0') : 0;
	$todate = isset($todate) && preg_match("/^([0-9]{8})$/", $todate) ? $todate : '';
	$totime = $todate ? strtotime($todate.' 23:59:59') : 0;

	$thumb = isset($thumb) ? intval($thumb) : 0;
	$guest = isset($guest) ? intval($guest) : 0;
	$itemid or $itemid = '';

	$fields_select = dselect($sfields, 'fields', '', $fields);
	$level_select = level_select('level', '����', $level);
	$order_select  = dselect($sorder, 'order', '', $order);

	$condition = '';
	if($_childs) $condition .= " AND catid IN (".$_childs.")";
	if($keyword) $condition .= " AND $dfields[$fields] LIKE '%$keyword%'";
	if($catid) $condition .= ($CATEGORY[$catid]['child']) ? " AND catid IN (".$CATEGORY[$catid]['arrchildid'].")" : " AND catid=$catid";
	if($level) $condition .= " AND level=$level";
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($thumb) $condition .= " AND thumb!=''";
	if($guest) $condition .= " AND username=''";
	if($itemid) $condition = " AND itemid=$itemid";
	if($process == 1) {
		$condition .= " AND fromtime>$DT_TIME";
	} else if($process == 2) {
		$condition .= " AND fromtime<$DT_TIME AND totime>$DT_TIME";
	} else if($process == 3) {
		$condition .= " AND totime<$DT_TIME";
	}
	if($level) $condition .= " AND level=$level";

	$timetype = strpos($dorder[$order], 'edit') === false ? 'add' : '';
}
switch($action) {
	case 'add':
		if($submit) {
			if($do->pass($post)) {
				if($FD) fields_check($post_fields);
				$do->add($post);
				if($FD) fields_update($post_fields, $table, $do->itemid);
				dmsg('���ӳɹ�', '?moduleid='.$moduleid.'&action='.$action.'&catid='.$post['catid']);
			} else {
				msg($do->errmsg);
			}
		} else {
			foreach($do->fields as $v) {
				isset($$v) or $$v = '';
			}
			$content = '';
			$status = 3;
			$addtime = timetodate($DT_TIME);
			$item = array();
			$menuid = 0;
			$tname = $menus[$menuid][0];
			isset($url) or $url = '';
			if($url) {
				$tmp = fetch_url($url);
				if($tmp) extract($tmp);
			}
			include tpl('edit', $module);
		}
	break;
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		if($submit) {
			if($do->pass($post)) {
				if($FD) fields_check($post_fields);
				$do->edit($post);
				if($FD) fields_update($post_fields, $table, $do->itemid);
				dmsg('�޸ĳɹ�', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			$item = $do->get_one();
			extract($item);
			$fromtime = timetodate($fromtime, 3);
			$totime = timetodate($totime, 3);
			$addtime = timetodate($addtime);
			$menuon = array('5', '4', '2', '1', '3');
			$menuid = $menuon[$status];
			$tname = '�޸�'.$MOD['name'];
			include tpl($action, $module);
		}
	break;
	case 'move':
		if($submit) {
			$fromids or msg('����д��ԴID');
			if($tocatid) {
				$db->query("UPDATE {$table} SET catid=$tocatid WHERE `{$fromtype}` IN ($fromids)");
				dmsg('�ƶ��ɹ�', $forward);
			} else {
				msg('��ѡ��Ŀ�����');
			}
		} else {
			$itemid = $itemid ? implode(',', $itemid) : '';
			$menuid = 6;
			include tpl($action, $module);
		}
	break;
	case 'update':
		is_array($itemid) or msg('��ѡ��'.$MOD['name']);
		foreach($itemid as $v) {
			$do->update($v);
		}
		dmsg('���³ɹ�', $forward);
	break;
	case 'tohtml':
		is_array($itemid) or msg('��ѡ��'.$MOD['name']);
		foreach($itemid as $itemid) {
			tohtml('show', $module);
		}
		dmsg('���³ɹ�', $forward);
	break;
	case 'delete':
		$itemid or msg('��ѡ��'.$MOD['name']);
		isset($recycle) ? $do->recycle($itemid) : $do->delete($itemid);
		dmsg('ɾ���ɹ�', $forward);
	break;
	case 'restore':
		$itemid or msg('��ѡ��'.$MOD['name']);
		$do->restore($itemid);
		dmsg('��ԭ�ɹ�', $forward);
	break;
	case 'restore':
		$itemid or msg('��ѡ��'.$MOD['name']);
		$do->restore($itemid);
		dmsg('��ԭ�ɹ�', $forward);
	break;
	case 'clear':
		$do->clear();
		dmsg('��ճɹ�', $forward);
	break;
	case 'level':
		$itemid or msg('��ѡ��'.$MOD['name']);
		$level = intval($level);
		$do->level($itemid, $level);
		dmsg('�������óɹ�', $forward);
	break;
	case 'recycle':
		$lists = $do->get_list('status=0'.$condition, $dorder[$order]);
		$menuid = 5;
		include tpl('index', $module);
	break;
	case 'reject':
		if($itemid) {
			$do->reject($itemid);
			dmsg('�ܾ��ɹ�', $forward);
		} else {
			$lists = $do->get_list('status=1'.$condition, $dorder[$order]);
			$menuid = 4;
			include tpl('index', $module);
		}
	break;
	case 'expire':
		if(isset($refresh)) {
			if(isset($delete)) {
				$days = isset($days) ? intval($days) : 0;
				$days or msg('����д����');
				$do->clear("status=4 AND totime<$DT_TIME-$days*24*3600");
				dmsg('ɾ���ɹ�', $forward);
			} else {
				$do->expire();
				dmsg('ˢ�³ɹ�', $forward);
			}
		} else {
			$lists = $do->get_list('status=4'.$condition);
			$menuid = 3;
			include tpl('index', $module);
		}
	break;
	case 'check':
		if($itemid) {
			$do->check($itemid);
			dmsg('��˳ɹ�', $forward);
		} else {
			$lists = $do->get_list('status=2'.$condition, $dorder[$order]);
			$menuid = 2;
			include tpl('index', $module);
		}
	break;
	default:
		$lists = $do->get_list('status=3'.$condition, $dorder[$order]);
		$menuid = 1;
		include tpl('index', $module);
	break;
}
?>