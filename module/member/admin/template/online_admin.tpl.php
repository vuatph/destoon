<?php
defined('IN_DESTOON') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="tt">在线管理员</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th>会员名</th>
<th>所在模块</th>
<th>IP</th>
<th>IP所在地</th>
<th>访问时间</th>
<th>URL</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><a href="javascript:_user('<?php echo $v['username'];?>')"><span<?php echo $sid == $v['sid'] ? ' style="color:red;" title="我"' : '';?>><?php echo $v['username'];?></span></a></td>
<td><a href="<?php echo $MODULE[$v['moduleid']]['linkurl'];?>" target="_blank"><?php echo $MODULE[$v['moduleid']]['name'];?></a></td>
<td><?php echo $v['ip'];?></td>
<td><?php echo ip2area($v['ip']);?></td>
<td><?php echo $v['lasttime'];?></td>
<td><input type="text" size="30" value="<?php echo $v['qstring'];?>" title="<?php echo $v['qstring'];?>"/></td>
</tr>
<?php }?>
</table>
<br/>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>