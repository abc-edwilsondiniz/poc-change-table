-- busca produtos que sofreram alterações na tabela PRODUTOCAD
SELECT
top 100
	Pro.Codpro AS 'codpro',
	ct.dv AS 'dv',
	ct.SYS_CHANGE_OPERATION AS 'operation',
	TRIM(CONCAT(Pro.codinterno, '')) AS 'referencia',
	cmp.descricaolonga AS 'nome_original',
	TRIM(pro.codigoncm) AS 'ncm',
	TRIM(Pro.modelo) AS 'modelo',
	cmp.vendaminima AS 'venda_minima',
	CONCAT(cmp.CODPROFABRICANTE, '') AS 'codpro_fabricante',
	Pro.unid1 AS 'un1',
	Pro.unid2 AS 'un2',
	Pro.faconv AS 'faconv',
	pro.disponibilidade AS 'cod_disponibilidade',
	(SELECT abc.nome FROM abc_disponibilidade abc WHERE abc.disponibilidade = pro.disponibilidade) AS disponibilidade,
	SUBSTRING(pro.clasprod, 1, 6) AS 'classe',
	SUBSTRING(pro.clasprod, 1, 6) AS 'cod_classe',
	(SELECT pcla.descr FROM classifcad pcla WHERE substring(pro.clasprod, 1, 2) = pcla.clasprod ) AS n1,
	(SELECT pcla.descr FROM classifcad pcla WHERE substring(pro.clasprod, 1, 4) = pcla.clasprod ) AS n2,
	(SELECT pcla.descr FROM classifcad pcla WHERE substring(pro.clasprod, 1, 6) = pcla.clasprod ) AS n3,
	pro.codfor AS 'id_fornecedor',
	fnd.NOME AS 'fornecedor',
	(SELECT TOP 1 prov.sigla FROM provincia prov, endereco_r en, cidade_r d WHERE pro.codfor = en.ritem AND en.rcidade = d.oid AND d.rprovincia = prov.oid) AS 'estado_fornecedor_origem',
	cmp.alturacm AS 'altura',
	cmp.larguracm AS 'largura',
	pro.pesounit AS 'peso',
	cmp.comprimentocm AS 'comprimento',
	pro.precocomp AS 'custo_atual',
	pro.icmultcomp AS 'icms_ultima_compra',
	pro.dtultcomp AS 'data_ult_compra',
	(ISNULL((SELECT top 1 valorcusto
	            FROM pesquisa
	            WHERE codigoexterno = pro.codpro
	            ORDER BY criadoem DESC), 0)) AS 'custo_ult_pesq',
	(SELECT qtmincompr
	    FROM itemfilest
	    WHERE filial = '10'
	    AND codpro = pro.codpro) AS 'qtd_min_compra',
	TRIM(CONCAT((SELECT top 1 referencia
	                FROM prodrefcad
	                WHERE codpro = pro.codpro
	                AND CodigoBarraEAN IS NOT NULL
	                AND CodigoBarraEAN NOT IN ('')), '')) AS 'ean',
	pro.cf as 'cf',
	pro.cm AS 'codigo_mens',
	CASE tab.usomens
	    WHEN 'A' THEN 'ST'
	    WHEN 'T' THEN 'D/C'
	    WHEN 'B' THEN 'D/C_BASE_REDUZIDA'
	    WHEN 'I' THEN 'ISENTO' ------- ISENTO -----
	END AS 'tributacao_mg',
	CASE
	    WHEN pro.origem IN ('N',
	                        'M',
	                        'L') THEN 'N'
	    WHEN pro.origem IN ('I',
	                        'G',
	                        'H') THEN 'I'
	END AS 'origem',
	RIGHT(TRIM(pro.codinterno), 1) AS 'ref_end'
FROM CHANGETABLE (CHANGES [PRODUTOCAD], :lastVersion) AS ct
INNER JOIN produtocad pro on pro.codpro = ct.codpro and pro.dv = ct.dv
INNER JOIN complementoproduto CMP ON pro.codpro = cmp.codpro
INNER JOIN fornececad fnd ON pro.codfor = fnd.oid
INNER JOIN item ite ON pro.disponibilidade = ite.oid
LEFT JOIN tabmenscad tab ON tab.cm = pro.cm;	
	

