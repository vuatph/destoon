<?php
defined('IN_DESTOON') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="tt">�����̻�</div>
<form method="post" action="?" id="dform">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="send" value="1"/>
<input type="hidden" name="first" value="1"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td class="tl">�ʼ����� <span class="f_red">*</span></td>
<td><input type="text" size="50" name="title" value="<?php echo $title;?>"/></td>
</tr>
<tr>
<td class="tl">ÿ�ַ��� <span class="f_red">*</span></td>
<td><input type="text" size="5" name="num" value="<?php echo $num;?>"/> ��</td>
</tr>
<tr>
<td class="tl">�̻����� <span class="f_red">*</span></td>
<td><input type="text" size="5" name="total" value="<?php echo $total;?>"/> ��</td>
</tr>
<tr>
<td class="tl">��ѯ����</td>
<td><input type="text" size="60" name="sql" value="<?php echo $sql;?>"/> <?php tips('���ӵ�SQL��ѯ���� ��AND��ͷ');?></td>
</tr>
<tr>
<td class="tl">����ʽ <span class="f_red">*</span></td>
<td><input type="text" size="20" name="ord" value="<?php echo $ord;?>"/></td>
</tr>
<tr>
<td class="tl">ѡ��ģ��</td>
<td><?php echo tpl_select('alert', 'mail', 'template', 'Ĭ��ģ��', '');?></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value=" ��ʼ���� " class="btn"/></div>
</form>
<script type="text/javascript">Menuon(0);</script>
</body>
</html>