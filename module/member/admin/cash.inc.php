<?php
defined('IN_DESTOON') or exit('Access Denied');
$menus = array (
    array('��ֵ��¼', '?moduleid='.$moduleid.'&file=charge'),
    array('���׼�¼', '?moduleid='.$moduleid.'&file=trade'),
    array('���ּ�¼', '?moduleid='.$moduleid.'&file=cash'),
    array('��Ϣ֧��', '?moduleid='.$moduleid.'&file=pay'),
);
$BANKS = explode('|', trim($MOD['cash_banks']));
$dstatus = array('�ȴ�����', '�ܾ�����', '֧��ʧ��', '����ɹ�');
$_status = array('<span style="color:blue;">�ȴ�����</span>', '<span style="color:#666666;">�ܾ�����</span>', '<span style="color:red;">֧��ʧ��</span>', '<span style="color:green;">����ɹ�</span>');
$table = $DT_PRE.'finance_cash';
if($action == 'edit' || $action == 'show') {
	$itemid or msg('δָ����¼');
	$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid ");
	$item or msg('��¼������');
	$item['addtime'] = timetodate($item['addtime'], 5);
	$item['edittime'] = timetodate($item['edittime'], 5);
	$member = $db->get_one("SELECT * FROM {$DT_PRE}member WHERE username='$item[username]'");
}
switch($action) {
	case 'edit':
		if($item['status'] > 0) msg('������������');
		if($submit) {
			isset($status) or msg('��ָ���������');
			$money = $item['amount'] + $item['fee'];
			if($status == 3) {
				money_lock($member['username'], -$money);
				money_record($member['username'], -$item['amount'], $item['bank'], $_username, '���ֳɹ�');
				money_record($member['username'], -$item['fee'], $item['bank'], $_username, '����������');
			} else if($status == 2 || $status == 1) {
				$note or msg('����дԭ��ע');
				money_lock($member['username'], -$money);
				money_add($member['username'], $money);
			} else {
				msg();
			}
			$db->query("UPDATE {$table} SET status=$status,editor='$_username',edittime=$DT_TIME,note='$note' WHERE itemid=$itemid");
			dmsg('�����ɹ�', $forward);
		} else {
			include tpl('cash_edit', $module);
		}
	break;
	case 'show':
		if($item['status'] == 0) msg('������δ����');
		include tpl('cash_show', $module);
	break;
	case 'delete':
		$itemid or msg('δѡ���¼');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$db->query("DELETE FROM {$table} WHERE itemid IN ($itemids)");
		dmsg('ɾ���ɹ�', $forward);
	break;
	default:
		$sfields = array('������', '��Ա��', '���', '������', '�տʽ', '�տ��ʺ�', '�տ���', '��ע', '������');
		$dfields = array('username', 'username', 'bank', 'amount', 'fee', 'note', 'editor');
		$sorder  = array('����ʽ', '����', '�������', '�����ѽ���', '����������', 'ʱ�併��', 'ʱ������');
		$dorder  = array('itemid DESC', 'amount DESC', 'amount ASC', 'fee DESC', 'fee ASC', 'addtime DESC', 'addtime ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$status = isset($status) && isset($dstatus[$status]) ? intval($status) : '';
		isset($username) or $username = '';
		isset($fromtime) or $fromtime = '';
		isset($totime) or $totime = '';
		isset($dfromtime) or $dfromtime = '';
		isset($dtotime) or $dtotime = '';
		isset($bank) or $bank = '';
		isset($order) && isset($dorder[$order]) or $order = 0;
		isset($timetype) or $timetype = 'addtime';
		isset($mtype) or $mtype = 'amount';
		isset($minamount) or $minamount = '';
		isset($maxamount) or $maxamount = '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$status_select = dselect($dstatus, 'status', '״̬', $status, '', 1, '', 1);
		$order_select = dselect($sorder, 'order', '', $order);
		$condition = '1';
		if($keyword) $condition .= " AND $dfields[$fields] LIKE '%$keyword%'";
		if($bank) $condition .= " AND bank='$bank'";
		if($fromtime) $condition .= " AND $timetype>".(strtotime($fromtime.' 00:00:00'));
		if($totime) $condition .= " AND $timetype<".(strtotime($totime.' 23:59:59'));
		if($status !== '') $condition .= " AND status='$status'";
		if($username) $condition .= " AND username='$username'";
		if($itemid) $condition .= " AND itemid=$itemid";
		if($minamount != '') $condition .= " AND $mtype>=$minamount";
		if($maxamount != '') $condition .= " AND $mtype<=$maxamount";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $condition");
		$pages = pages($r['num'], $page, $pagesize);		
		$cashs = array();
		$result = $db->query("SELECT * FROM {$table} WHERE $condition ORDER BY $dorder[$order] LIMIT $offset,$pagesize");
		$amount = $fee = 0;
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 5);
			$r['edittime'] = $r['edittime'] ? timetodate($r['edittime'], 5) : '--';
			$r['dstatus'] = $_status[$r['status']];
			$amount += $r['amount'];
			$fee += $r['fee'];
			$cashs[] = $r;
		}
		include tpl('cash', $module);
	break;
}
?>