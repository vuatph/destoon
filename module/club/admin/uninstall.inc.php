<?php
defined('DT_ADMIN') or exit('Access Denied');
$db->query("DROP TABLE IF EXISTS `".$DT_PRE.$module."`");
$db->query("DROP TABLE IF EXISTS `".$DT_PRE.$module."_data`");
$db->query("DROP TABLE IF EXISTS `".$DT_PRE.$module."_fans`");
$db->query("DROP TABLE IF EXISTS `".$DT_PRE.$module."_group`");
$db->query("DROP TABLE IF EXISTS `".$DT_PRE.$module."_manage`");
$db->query("DROP TABLE IF EXISTS `".$DT_PRE.$module."_reply`");
?>