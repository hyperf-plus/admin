#增加执行时间展示
ALTER TABLE `new_admin`.`cs_action_log`
ADD COLUMN `execution_time` int(11) NOT NULL DEFAULT 0 COMMENT 'ms' AFTER `status`;