DROP View view_estatistikak;

CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `erantzun`@`localhost` 
    SQL SECURITY DEFINER
VIEW `view_estatistikak` AS
    SELECT 
        `eskakizunak`.`enpresa_id` AS `enpresa_id`,
        CAST(CONCAT(YEAR(`eskakizunak`.`noiz`),
                    '-',
                    LPAD(MONTH(`eskakizunak`.`noiz`), 2, '0'),
                    '-01')
            AS DATE) AS `data`,
        YEAR(`eskakizunak`.`noiz`) AS `urtea`,
        MONTH(`eskakizunak`.`noiz`) AS `hilabetea`,
        COUNT(0) AS `eskakizunak`
    FROM
        `eskakizunak`
    GROUP BY `eskakizunak`.`enpresa_id` , `data` , `urtea` , `hilabetea`
    ORDER BY `urtea` DESC;