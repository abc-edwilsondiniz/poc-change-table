-- busca complemento do produto que sofreram alterações na tabela COMPLEMENTOPRODUTO
SELECT
top 100
    pro.Codpro AS 'codpro',
    pro.dv,
    pro.codfor AS 'id_fornecedor',
    ct.SYS_CHANGE_OPERATION AS 'operation',
    cmp.descricaolonga AS 'nome_original',
    cmp.vendaminima AS 'venda_minima',
    CONCAT(cmp.CODPROFABRICANTE, '') AS 'codpro_fabricante',
    cmp.alturacm AS 'altura',
    cmp.larguracm AS 'largura',
    cmp.comprimentocm AS 'comprimento'
FROM CHANGETABLE (CHANGES [COMPLEMENTOPRODUTO], :lastVersion) AS ct
INNER JOIN produtocad pro on pro.codpro = ct.codpro
INNER JOIN complementoproduto cmp ON pro.codpro = cmp.codpro;