SELECT 
    pro.codpro,
    pro.dv,
    TRIM(pro.codinterno) AS 'referencia',
    TRIM(CONCAT((i.quant - i.qtdereserv), '')) AS 'estoque_atual',
    i.filial 
FROM
    CHANGETABLE (CHANGES [ITEMFILEST], :lastVersion) AS ct
INNER JOIN itemfilest i ON i.codpro = ct.codpro and i.filial = ct.filial
INNER JOIN produtocad pro ON pro.codpro = i.codpro AND pro.dv = i.dv 
WHERE (i.quant - i.qtdereserv) > 0
