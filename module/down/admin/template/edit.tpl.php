<?php
defined('IN_DESTOON') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<div class="tt"><?php echo $tname;?></div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td class="tl">�������� <span class="f_red">*</span></td>
<td><?php echo $_admin == 1 ? category_select('post[catid]', 'ѡ�����', $catid, $moduleid) : ajax_category_select('post[catid]', '', $catid, $moduleid);?> <span id="dcatid" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><?php echo $MOD['name'];?>���� <span class="f_red">*</span></td>
<td><input name="post[title]" type="text" id="title" size="60" value="<?php echo $title;?>"/> <?php echo level_select('post[level]', '����', $level);?> <?php echo dstyle('post[style]', $style);?> <br/><span id="dtitle" class="f_red"></span></td>
</tr>
<tr>
<td class="tl">����ͼƬ </td>
<td><input name="post[thumb]" id="thumb" type="text" size="60" value="<?php echo $thumb;?>"/>&nbsp;&nbsp;<span onclick="Dthumb(<?php echo $moduleid;?>,<?php echo $MOD['thumb_width'];?>,<?php echo $MOD['thumb_height'];?>, $('thumb').value);" class="jt">[�ϴ�]</span>&nbsp;&nbsp;<span onclick="_preview($('thumb').value);" class="jt">[Ԥ��]</span>&nbsp;&nbsp;<span onclick="$('thumb').value='';" class="jt">[ɾ��]</span><span id="dthumb" class="f_red"></span></td>
</tr>

<tr>
<td class="tl">�ļ���ַ <span class="f_red">*</span></td>
<td><input name="post[fileurl]" id="fileurl" type="text" size="60" value="<?php echo $fileurl;?>"/>&nbsp;&nbsp;<span onclick="Dfile(<?php echo $moduleid;?>, $('fileurl').value, 'fileurl', '<?php echo $MOD['upload'];?>');" class="jt">[�ϴ�]</span>&nbsp;&nbsp;<span onclick="if($('fileurl').value) window.open($('fileurl').value);" class="jt">[Ԥ��]</span>&nbsp;&nbsp;<span onclick="$('fileurl').value='';" class="jt">[ɾ��]</span> <span id="dfileurl" class="f_red"></span></td>
</tr>
<tr>
<td class="tl">�ļ����� <span class="f_red">*</span></td>
<td><?php echo ext_select('post[fileext]', $fileext);?></td>
</tr>
<tr>
<td class="tl">�ļ���С <span class="f_red">*</span></td>
<td><input name="post[filesize]" id="filesize" type="text" size="10" value="<?php echo $filesize;?>"/>&nbsp;<?php echo unit_select('post[unit]', $unit);?>&nbsp;<span id="dfilesize" class="f_red"></span></td>
</tr>

<?php echo $FD ? fields_html('<td class="tl">', '<td>', $item) : '';?>
<tr>
<td class="tl">����˵��</td>
<td><textarea name="post[content]" id="content" class="dsn"><?php echo $content;?></textarea>
<?php echo deditor($moduleid, 'content', $MOD['editor'], '98%', 350);?><span id="dcontent" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl">����ϵ��</td>
<td><input name="post[tag]" id="tag" type="text" size="50" value="<?php echo $tag;?>"/> <span id="dtag" class="f_red"></span><?php tips('��дһ�����صĹؼ��ʻ���ϵ�����ƣ��Ա����ͬϵ�е�����');?></td>
</tr>
<tr>
<td class="tl">��Ա�� <span class="f_red">*</span></td>
<td><input name="post[username]" type="text"  size="20" value="<?php echo $username;?>" id="username"/> <a href="javascript:_user($('username').value);" class="t">[����]</a> <span id="dusername" class="f_red"></span></td>
</tr>

<tr>
<td class="tl"><?php echo $MOD['name'];?>״̬</td>
<td>
<input type="radio" name="post[status]" value="3" <?php if($status == 3) echo 'checked';?>/> ͨ��
<input type="radio" name="post[status]" value="2" <?php if($status == 2) echo 'checked';?>/> ����
<input type="radio" name="post[status]" value="1" <?php if($status == 1) echo 'checked';?> onclick="if(this.checked) $('note').style.display='';"/> �ܾ�
<input type="radio" name="post[status]" value="4" <?php if($status == 4) echo 'checked';?>/> ����
<input type="radio" name="post[status]" value="0" <?php if($status == 0) echo 'checked';?>/> ɾ��
</td>
</tr>
<tr id="note" style="display:<?php echo $status==1 ? '' : 'none';?>">
<td class="tl">�ܾ����� <span class="f_red">*</span></td>
<td><input name="post[note]" type="text"  size="40" value="<?php echo $note;?>"/></td>
</tr>
<tr>
<td class="tl">����ʱ��</td>
<td><input type="text" size="22" name="post[addtime]" value="<?php echo $addtime;?>"/></td>
</tr>
<tr>
<td class="tl">�������</td>
<td><input name="post[hits]" type="text" size="10" value="<?php echo $hits;?>"/></td>
</tr>
<tr>
<td class="tl">���ش���</td>
<td><input name="post[download]" type="text" size="10" value="<?php echo $download;?>"/></td>
</tr>
<tr>
<td class="tl">�����շ�</td>
<td><input name="post[fee]" type="text" size="5" value="<?php echo $fee;?>"/><?php tips('�������0��ʾ�̳�ģ�����ü۸�-1��ʾ���շ�<br/>����0�����ֱ�ʾ�����շѼ۸�');?>
</td>
</tr>
<tr>
<td class="tl">����ģ��</td>
<td><?php echo tpl_select('show', $module, 'post[template]', 'Ĭ��ģ��', $template, 'id="template"');?><?php tips('���û��������Ҫ��һ�㲻��Ҫѡ��<br/>ϵͳ���Զ��̳з����ģ������');?></td>
</tr>
<?php if($MOD['show_html']) { ?>
<tr>
<td class="tl">�Զ����ļ�·��</td>
<td><input type="text" size="50" name="post[filepath]" value="<?php echo $filepath;?>" id="filepath"/>&nbsp;<input type="button" value="�������" onclick="ckpath(<?php echo $moduleid;?>, <?php echo $itemid;?>);" class="btn"/>&nbsp;<?php tips('���԰���Ŀ¼���ļ� ���� destoon/b2b.html<br/>��ȷ��Ŀ¼���ļ����Ϸ��ҿ�д�룬�����������ʧ��');?>&nbsp; <span id="dfilepath" class="f_red"></span></td>
</tr>
<?php } ?>
</table>
<div class="sbt"><input type="submit" name="submit" value=" ȷ �� " class="btn"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="reset" name="reset" value=" �� �� " class="btn"/></div>
</form>
<?php load('clear.js'); ?>
<?php if($action == 'add') { ?>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<div class="tt">��ҳ�ɱ�</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td class="tl">Ŀ����ַ</td>
<td><input name="url" type="text" size="80" value="<?php echo $url;?>"/>&nbsp;&nbsp;<input type="submit" value=" �� ȡ " class="btn"/>&nbsp;&nbsp;<input type="button" value=" �������� " class="btn" onclick="window.open('?file=fetch');"/></td>
</tr>
</table>
</form>
<?php } ?>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'catid_1';
	if($(f).value == 0) {
		Dmsg('��ѡ����������', 'catid', 1);
		return false;
	}
	f = 'title';
	l = $(f).value.length;
	if(l < 2) {
		Dmsg('����д��������', f);
		return false;
	}
	f = 'fileurl';
	l = $(f).value.length;
	if(l < 10) {
		Dmsg('����д���ص�ַ', f);
		return false;
	}
	f = 'filesize';
	l = $(f).value;
	if(!l) {
		Dmsg('����д�ļ���С', f);
		return false;
	}
	<?php echo $FD ? fields_js() : '';?>
	return true;
}
</script>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
</body>
</html>