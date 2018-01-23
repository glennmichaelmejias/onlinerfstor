DELIMITER $$

DROP FUNCTION IF EXISTS `gettortaskstatus` $$
CREATE DEFINER=`root`@`localhost` FUNCTION `gettortaskstatus`(ubuid INT(10),utaskid INT(10),thestatus TEXT) RETURNS text CHARSET latin1
    DETERMINISTIC
BEGIN
  DECLARE select_var TEXT;
  SELECT count(id) INTO select_var FROM butaskrole where buid=ubuid and taskid=utaskid and requesttype=2;
  SET select_var = IF(select_var=0,'true',IF(IFNULL(thestatus,'asdf')='asdf','false','true'));
  RETURN select_var;
END $$

DELIMITER ;