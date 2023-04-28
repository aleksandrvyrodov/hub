DELETE FROM `fusion5vn4q_pages_section`
WHERE `id` IN(
    SELECT `hub`.`section`
    FROM (
        SELECT `to`.`section`, COUNT(`to`.`section`) AS `count`
        FROM fusion5vn4q_pages_hub AS `to` INNER JOIN (
            SELECT `section`, COUNT(`section`) AS `count`
                FROM fusion5vn4q_pages_hub
                WHERE `data` IN($in) AND `section` IS NOT NULL
                GROUP BY `section`) AS `from`
            ON `to`.`section` = `from`.`section`
            GROUP BY `to`.`section`) AS `hub` INNER JOIN (
                SELECT `section`, COUNT(`section`) AS `count`
                FROM fusion5vn4q_pages_hub
                WHERE `data` IN($in) AND `section` IS NOT NULL
                GROUP BY `section`) AS `del` USING(`section`)
    WHERE `del`.`count` = `hub`.`count`)