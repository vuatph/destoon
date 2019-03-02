<?php
defined('IN_DESTOON') or exit('Access Denied');
require MD_ROOT.'/message.class.php';
$menus = array (
    array('�����ż�', '?moduleid='.$moduleid.'&file='.$file.'&action=send'),
    array('��Ա�ż�', '?moduleid='.$moduleid.'&file='.$file),
    array('ϵͳ�ż�', '?moduleid='.$moduleid.'&file='.$file.'&action=system'),
    array('�ʼ�ת��', '?moduleid='.$moduleid.'&file='.$file.'&action=mail'),
    array('�ż�����', '?moduleid='.$moduleid.'&file='.$file.'&action=clear'),
);
$do = new message;
$this_forward = '?moduleid='.$moduleid.'&file='.$file;

$NAME = array('��ͨ', 'ѯ��', '����', '����', '��ʹ');

switch($action) {
	case 'send':
		if($submit) {
			if($do->_send($message)) {
				dmsg('���ͳɹ�', $this_forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			isset($touser) or $touser = '';
			include tpl('message_send', $module);
		}
	break;
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		if($submit) {
			$do->_edit($message);
			dmsg('�޸ĳɹ�', $this_forward);
		} else {
			extract($do->get_one());
			include tpl('message_edit', $module);
		}
	break;
	case 'clear':
		if($submit) {
			if($do->_clear($message)) {
				dmsg('�����ɹ�', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			$todate = timetodate(strtotime('-1 year'), 3);
			include tpl('message_clear', $module);
		}
	break;
	case 'mail':
		if(isset($send)) {
			isset($num) or $num = 0;
			$hour = intval($hour);
			if(!$hour) $hour = 48;
			$pernum = intval($pernum);
			if(!$pernum) $pernum = 10;
			$pagesize = $pernum;
			$offset = ($page-1)*$pagesize;
			$time = $DT_TIME - $hour*3600;
			$result = $db->query("SELECT * FROM {$DT_PRE}message WHERE isread=0 AND issend=0 AND addtime<$time AND status=3 ORDER BY itemid DESC LIMIT $offset,$pagesize");
			$i = false;
			while($r = $db->fetch_array($result)) {
				$m = $db->get_one("SELECT email FROM {$DT_PRE}member WHERE username='$r[touser]' AND groupid>4");
				if(!$m) continue;
				$linkurl = linkurl($MODULE[2]['linkurl'], 2).'message.php?action=show&itemid='.$r['itemid'];
				$r['fromuser'] or $r['fromuser'] = 'ϵͳ��ʹ';
				$r['content'] = $r['fromuser'].' �� '.timetodate($r['addtime'], 5).' ��������һ��վ���ţ��������£�<br/><br/>'.$r['content'].'<br/><br/>ԭʼ��ַ��<a href="'.$linkurl.'" target="_blank">'.$linkurl.'</a><br/><br/>���ʼ�ͨ�� <a href="'.DT_URL.'" target="_blank">'.$DT['sitename'].'</a> �ʼ�ϵͳ����<br/><br/>�������ϣ���յ������ʼ����뾭����¼��վ����վ���ż���δ���ż����Ϊ�Ѷ�<br/><br/>';
				send_mail($m['email'], $r['title'], $r['content']);
				$db->query("UPDATE {$DT_PRE}message SET issend=1 WHERE itemid=$r[itemid]");
				$i = true;
				$num++;
			}
			if($i) {
				$page++;
				msg('�ѷ��� '.$num.' ���ʼ���ϵͳ���Զ����������Ժ�...', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&page='.$page.'&hour='.$hour.'&pernum='.$pernum.'&num='.$num.'&send=1');
			} else {
				file_put(DT_CACHE.'/message.dat', $DT_TIME);
				msg('�ʼ����ͳɹ� ������ '.$num.' ���ʼ�', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action, 5);
			}
		} else {
			$lasttime = is_file(DT_CACHE.'/message.dat') ? file_get(DT_CACHE.'/message.dat') : 0;
			$lasttime = $lasttime ? timetodate($lasttime, 5) : '';
			include tpl('message_mail', $module);
		}
	break;
	case '_delete':
		if(!$itemid) msg();
		$do->_delete($itemid);
		dmsg('ɾ���ɹ�', $this_forward);
	break;
	case 'system':
		$messages = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}message WHERE groupids!='' ORDER BY itemid DESC");
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 5);
			$r['group'] = '<select>';
			$groupids = explode(',', $r['groupids']);
			foreach($groupids as $groupid) {
				$r['group'] .= '<option>'.$GROUP[$groupid]['groupname'].'</option>';
			}
			$r['group'] .= '</select>';
			$messages[] = $r;
		}
		include tpl('message_system', $module);
	break;
	case 'delete':
		if(!$itemid) msg();
		$do->itemid = $itemid;
		$do->delete();
		dmsg('ɾ���ɹ�', $forward);
	break;
	case 'show':
		$itemid or msg();
		$do->itemid = $itemid;
		$item = $do->get_one();
		$item or msg();
		extract($item);
		include tpl('message_show', $module);		
	break;
	default:
		$sfields = array('����', '������', '�ռ���', 'IP', '����');
		$dfields = array('title', 'fromuser', 'touser', 'ip', 'content');
		$S = array('״̬', '�ݸ���', '������', '�ռ���', '����վ');

		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$typeid = isset($typeid) ? intval($typeid) : -1;
		$read = isset($read) ? intval($read) : -1;
		$send = isset($send) ? intval($send) : -1;
		$status = isset($status) ? intval($status) : 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$status_select = dselect($S, 'status', '', $status);

		$condition = "groupids=''";
		if($keyword) $condition .= " AND $dfields[$fields] LIKE '%$keyword%'";
		if($status) $condition .= " AND status=$status";
		if($typeid > -1) $condition .= " AND typeid=$typeid";
		if($read > -1) $condition .= " AND isread=$read";
		if($send > -1) $condition .= " AND issend=$send";

		$lists = $do->get_list($condition);
		include tpl('message', $module);
	break;
}
?>