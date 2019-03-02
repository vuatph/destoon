<?php
defined('IN_DESTOON') or exit('Access Denied');
$TYPE = get_type('vote', 1);
$TYPE or msg('��������ͶƱ����', '?file=type&item=vote');
require MD_ROOT.'/vote.class.php';
$do = new vote();
$menus = array (
    array('����ͶƱ', '?moduleid='.$moduleid.'&file='.$file.'&action=add'),
    array('ͶƱ�б�', '?moduleid='.$moduleid.'&file='.$file),
    array('���µ�ַ', '?moduleid='.$moduleid.'&file='.$file.'&action=update'),
    array('����ͶƱ', '?moduleid='.$moduleid.'&file='.$file.'&action=html'),
    array('ͶƱ����', '?file=type&item=vote'),
);
switch($action) {
	case 'add':
		if($submit) {
			if($do->pass($post)) {
				$do->add($post);
				dmsg('���ӳɹ�', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			foreach($do->fields as $v) {
				isset($$v) or $$v = '';
			}
			$vote_min = 1;
			$vote_max = 3;
			$addtime = timetodate($DT_TIME);
			$menuid = 0;
			include tpl('vote_edit', $module);
		}
	break;
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		if($submit) {
			if($do->pass($post)) {
				$do->edit($post);
				dmsg('�޸ĳɹ�', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			extract($do->get_one());
			$addtime = timetodate($addtime);
			$fromtime = $fromtime ? timetodate($fromtime, 3) : '';
			$totime = $totime ? timetodate($totime, 3) : '';
			$menuid = 1;
			include tpl('vote_edit', $module);
		}
	break;
	case 'update':
		$do->update();
		dmsg('���³ɹ�', $forward);
	break;
	case 'html':
		if(!isset($num)) {
			$num = 50;
		}
		if(!isset($fid)) {
			$r = $db->get_one("SELECT min(itemid) AS fid FROM {$DT_PRE}vote");
			$fid = $r['fid'] ? $r['fid'] : 0;
		}
		isset($sid) or $sid = $fid;
		if(!isset($tid)) {
			$r = $db->get_one("SELECT max(itemid) AS tid FROM {$DT_PRE}vote");
			$tid = $r['tid'] ? $r['tid'] : 0;
		}
		if($fid <= $tid) {
			$result = $db->query("SELECT itemid FROM {$DT_PRE}vote WHERE itemid>=$fid ORDER BY itemid LIMIT 0,$num");
			if($db->affected_rows($result)) {
				while($r = $db->fetch_array($result)) {
					$itemid = $r['itemid'];
					tohtml('vote', $module);
				}
				$itemid += 1;
			} else {
				$itemid = $fid + $num;
			}
		} else {
			dmsg('���ɳɹ�', "?moduleid=$moduleid&file=$file");
		}
		msg('ID��'.$fid.'��'.($itemid-1).'���ɳɹ�'.progress($sid, $fid, $tid), "?moduleid=$moduleid&file=$file&action=$action&sid=$sid&fid=$itemid&tid=$tid&num=$num");
	break;
	case 'delete':
		$itemid or msg('��ѡ��ͶƱ');
		$do->delete($itemid);
		dmsg('ɾ���ɹ�', $forward);
	break;
	case 'level':
		$itemid or msg('��ѡ��ͶƱ');
		$level = intval($level);
		$do->level($itemid, $level);
		dmsg('�������óɹ�', $forward);
	break;
	case 'record':
		$itemid or msg();
		$do->itemid = $itemid;
		$item = $do->get_one();
		extract($item);
		$votes = array();
		for($i = 1; $i < 11; $i++) {
			$s = 's'.$i;
			if($$s) $votes[$i] = $$s;
		}
		$condition = "itemid=$itemid";
		if($keyword) $condition .= " AND (ip LIKE '%$keyword%' OR username LIKE '%$keyword%')";
		$lists = $do->get_list_record($condition);
		include tpl('vote_record', $module);
	break;
	default:	
		$sorder  = array('�������ʽ', '����ʱ�併��', '����ʱ������', 'ͶƱ��������', 'ͶƱ��������', '�����������', '�����������', '��ʼʱ�併��', '��ʼʱ������', '����ʱ�併��', '����ʱ������');
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'votes DESC', 'votes ASC', 'hits DESC', 'hits ASC', 'fromtime DESC', 'fromtime ASC', 'totime DESC', 'totime ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		isset($typeid) or $typeid = 0;
		$type_select = type_select('vote', 1, 'typeid', '��ѡ�����', $typeid);
		$order_select  = dselect($sorder, 'order', '', $order);
		$condition = '1';
		if($keyword) $condition .= " AND title LIKE '%$keyword%'";
		if($typeid) $condition .= " AND typeid=$typeid";
		$lists = $do->get_list($condition, $dorder[$order]);
		include tpl('vote', $module);
	break;
}
?>