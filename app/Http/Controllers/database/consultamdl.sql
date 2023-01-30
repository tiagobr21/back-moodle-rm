SELECT DISTINCT  
CASE 
	WHEN X.IDTURMADISCGERENCIAL IS NOT NULL AND X.CODCOLIGADA = 1 AND X.CODFILIAL = 1 AND X.CODTIPOCURSO = 1 THEN 'C1F1E1-TG-2022/2'
	WHEN X.IDTURMADISCGERENCIAL IS NOT NULL AND X.CODCOLIGADA = 1 AND X.CODFILIAL = 1 AND X.CODTIPOCURSO = 3 THEN 'TG-ZL-2022/2'
	WHEN X.IDTURMADISCGERENCIAL IS NOT NULL AND X.CODCOLIGADA = 1 AND X.CODFILIAL = 1 AND X.CODTIPOCURSO = 7 THEN 'TG-ZS-2022/2'
	WHEN X.IDTURMADISCGERENCIAL IS NOT NULL AND X.CODCOLIGADA = 3 AND X.CODFILIAL = 1 AND X.CODTIPOCURSO = 3 THEN 'TG-ZN-2022/2'
	ELSE
	
'C'+CONVERT(VARCHAR, X.CODCOLIGADA)+'F'+CONVERT(VARCHAR, X.CODFILIAL)+'E'+CONVERT(VARCHAR, X.CODTIPOCURSO)+'-'+ 
CASE 
	WHEN X.IDTURMADISCGERENCIAL IS NOT NULL THEN 'TG' ELSE X.CODCURSO END+'-'+ X.CODPERLET + '-' + 
CASE 
	WHEN X.IDTURMADISCGERENCIAL IS NULL THEN convert(varchar,X.CODTURMA) ELSE convert(varchar,X.CODTURMAGERENCIAL) END END AS category_idnumber, 


	CAST(X.CODPERLET + ' - '+ 
CASE 
	WHEN X.CODCOLIGADA = 1 AND X.CODTIPOCURSO = 1 THEN '(SEDE)'
	WHEN X.CODCOLIGADA = 1 AND X.CODTIPOCURSO = 3 THEN '(ZL)'
	WHEN X.CODCOLIGADA = 1 AND X.CODTIPOCURSO = 7 THEN '(ZS)'
	WHEN X.CODCOLIGADA = 3 AND X.CODTIPOCURSO = 3 THEN '(ZN)'
	WHEN X.CODCOLIGADA = 1 AND X.CODTIPOCURSO = 12 THEN '(Coari)'
	WHEN X.CODCOLIGADA = 1 AND X.CODTIPOCURSO = 13 THEN '(Tefé)'
	WHEN X.CODCOLIGADA = 1 AND X.CODTIPOCURSO = 14 THEN '(Parintins)'
	WHEN X.CODCOLIGADA = 1 AND X.CODTIPOCURSO = 11 THEN '(Itacoatiara)'
	WHEN X.CODCOLIGADA = 4  THEN '(FA)' END +
CASE WHEN IDTURMADISCGERENCIAL IS NULL THEN convert(varchar,X.CODTURMA) 
	ELSE convert(varchar,X.CODTURMAGERENCIAL) END+' - '+ X.NOME AS NVARCHAR(500))  AS fullname,
CONCAT(X.CODCOLIGADA , '-' ,CASE WHEN X.IDTURMADISCGERENCIAL IS NULL THEN CONVERT(VARCHAR,X.IDTURMADISC) ELSE CONVERT(VARCHAR,X.IDTURMADISCGERENCIAL) END) AS shortname,
1 as visible,

X.NIVEL_ENSINO, X.CURSO
--, X.FILIAL

FROM (
SELECT DISTINCT  

STURMADISC.CODCOLIGADA, GCOLIGADA.NOME AS COLIGADA, STURMADISC.CODFILIAL, GFILIAL.NOME AS FILIAL, 
STIPOCURSO.CODTIPOCURSO,STIPOCURSO.NOME NIVEL_ENSINO, SCURSO.CODCURSO, SCURSO.NOME CURSO, 
SHABILITACAO.NOME AS HABILITACAO, 
STURMADISC.CODTURMA, SPLETIVO.CODPERLET,
SALUNO.RA, PPESSOA.NOME ALUNO, STURMADISC.CODDISC, SDISCIPLINA.NOME,
STURMADISC.IDTURMADISC, STURMADISCCOMPL.IDMDL,sturmadisc.TIPO,

TURMAGERENCIAL.IDTURMADISC AS IDTURMADISCGERENCIAL, TURMAGERENCIAL.CODTURMA AS CODTURMAGERENCIAL,

CONVERT(VARCHAR, STURMADISC.CODCOLIGADA) + '-'+CONVERT(VARCHAR, STURMADISC.CODFILIAL) + '-'+CONVERT(VARCHAR, STIPOCURSO.CODTIPOCURSO)+'-'+
CONVERT(VARCHAR, SCURSO.CODCURSO)+'-'+CONVERT(VARCHAR, SPLETIVO.CODPERLET) AS CATEGORIA_MOODLE,

CONVERT(VARCHAR, SPLETIVO.CODPERLET)+ '-'+ CONVERT(VARCHAR(200), SDISCIPLINA.NOME) AS NOME_DISC_MOODLE,
CONVERT(VARCHAR, STURMADISC.CODCOLIGADA)+ '-'+ CONVERT(VARCHAR(200), STURMADISC.IDTURMADISC) AS CODDISC_MOODLE


FROM STURMADISC (NOLOCK)
	INNER JOIN STURMADISCCOMPL (NOLOCK) ON STURMADISCCOMPL.CODCOLIGADA = STURMADISC.CODCOLIGADA AND STURMADISCCOMPL.IDTURMADISC = STURMADISC.IDTURMADISC
	INNER JOIN SPLETIVO (NOLOCK) ON STURMADISC.CODCOLIGADA=SPLETIVO.CODCOLIGADA AND SPLETIVO.IDPERLET=STURMADISC.IDPERLET
	INNER JOIN SDISCIPLINA (NOLOCK) ON STURMADISC.CODCOLIGADA=SDISCIPLINA.CODCOLIGADA AND STURMADISC.CODDISC=SDISCIPLINA.CODDISC
	INNER JOIN SMATRICULA (NOLOCK) ON STURMADISC.CODCOLIGADA=SMATRICULA.CODCOLIGADA AND STURMADISC.IDTURMADISC=SMATRICULA.IDTURMADISC
	INNER JOIN SSTATUS (NOLOCK) ON SSTATUS.CODCOLIGADA=SMATRICULA.CODCOLIGADA AND SSTATUS.CODSTATUS=SMATRICULA.CODSTATUS
	INNER JOIN SMATRICPL (NOLOCK) ON SMATRICPL.CODCOLIGADA=SMATRICULA.CODCOLIGADA AND SMATRICPL.RA=SMATRICULA.RA AND SMATRICPL.IDPERLET=SMATRICULA.IDPERLET 
				AND SMATRICPL.IDHABILITACAOFILIAL=SMATRICULA.IDHABILITACAOFILIAL
	INNER JOIN SALUNO (NOLOCK) ON SALUNO.CODCOLIGADA=SMATRICPL.CODCOLIGADA AND SALUNO.RA=SMATRICPL.RA
	INNER JOIN PPESSOA (NOLOCK) ON PPESSOA.CODIGO=SALUNO.CODPESSOA
	INNER JOIN SHABILITACAOFILIAL (NOLOCK) ON SHABILITACAOFILIAL.CODCOLIGADA=SMATRICPL.CODCOLIGADA AND SHABILITACAOFILIAL.IDHABILITACAOFILIAL=STURMADISC.IDHABILITACAOFILIAL
	LEFT OUTER JOIN SHABILITACAO  ON SHABILITACAO.CODCOLIGADA = SHABILITACAOFILIAL.CODCOLIGADA AND SHABILITACAO.CODCURSO = SHABILITACAOFILIAL.CODCURSO 
		AND SHABILITACAO.CODHABILITACAO = SHABILITACAOFILIAL.CODHABILITACAO
	INNER JOIN SCURSO (NOLOCK) ON SCURSO.CODCOLIGADA=SHABILITACAOFILIAL.CODCOLIGADA AND SCURSO.CODCURSO=SHABILITACAOFILIAL.CODCURSO
	INNER JOIN STIPOCURSO (NOLOCK) ON STIPOCURSO.CODCOLIGADA=SHABILITACAOFILIAL.CODCOLIGADA AND STIPOCURSO.CODTIPOCURSO=SHABILITACAOFILIAL.CODTIPOCURSO
	LEFT JOIN STIPOALUNO (NOLOCK) ON STIPOALUNO.CODCOLIGADA = SALUNO.CODCOLIGADA AND STIPOALUNO.CODTIPOALUNO = SALUNO.CODTIPOALUNO
									AND STIPOALUNO.CODTIPOCURSO = STIPOCURSO.CODTIPOCURSO
	
	LEFT JOIN STURMADISCGERENCIADA (NOLOCK) ON STURMADISCGERENCIADA.CODCOLIGADA = STURMADISC.CODCOLIGADA AND STURMADISCGERENCIADA.IDTURMADISCGERENCIADA = STURMADISC.IDTURMADISC
	
	LEFT JOIN STURMADISC TURMAGERENCIAL (NOLOCK) ON TURMAGERENCIAL.CODCOLIGADA = STURMADISCGERENCIADA.CODCOLIGADA AND TURMAGERENCIAL.IDTURMADISC = STURMADISCGERENCIADA.IDTURMADISC 
	LEFT JOIN SPROFESSORTURMA  PROFESSORTURMA_GERENCIAL (NOLOCK)  ON PROFESSORTURMA_GERENCIAL.CODCOLIGADA = STURMADISC.CODCOLIGADA AND PROFESSORTURMA_GERENCIAL.IDTURMADISC = TURMAGERENCIAL.IDTURMADISC 
	LEFT JOIN SPROFESSOR  PROFESSOR_GERENCIAL (NOLOCK)   ON STURMADISC.CODCOLIGADA = PROFESSOR_GERENCIAL.CODCOLIGADA AND PROFESSORTURMA_GERENCIAL.CODPROF = PROFESSOR_GERENCIAL.CODPROF
    LEFT JOIN PPESSOA     PPESSOA_PROF_GERENCIAL (NOLOCK)   ON PROFESSOR_GERENCIAL.CODPESSOA = PPESSOA_PROF_GERENCIAL.CODIGO


	INNER JOIN GCOLIGADA (NOLOCK) ON GCOLIGADA.CODCOLIGADA = STURMADISC.CODCOLIGADA
	INNER JOIN GFILIAL (NOLOCK) ON GFILIAL.CODCOLIGADA = STURMADISC.CODCOLIGADA AND GFILIAL.CODFILIAL = STURMADISC.CODFILIAL
	

WHERE ( STIPOCURSO.NOME LIKE 'GRAD%' AND STURMADISC.TIPO = 'P' )
AND NOT (SMATRICULA.CODCOLIGADA = 3 AND SHABILITACAOFILIAL.CODTIPOCURSO = 1)
and SPLETIVO.CODPERLET IN ('2023/1')
AND (UPPER(SSTATUS.DESCRICAO) LIKE 'MATRIC%' )
				
		) X 
	
	   where NOT exists (SELECT * FROM OPENQUERY(DIGITAL, 'SELECT DISTINCT FULLNAME, SHORTNAME FROM digital.mdl_course 
															WHERE fullname like ''%2023/1%''') DIGITAL_FAMETRO 
												WHERE DIGITAL_FAMETRO.SHORTNAME = CONCAT(X.CODCOLIGADA , '-' ,CASE WHEN X.IDTURMADISCGERENCIAL IS NULL THEN CONVERT(VARCHAR,X.IDTURMADISC) ELSE CONVERT(VARCHAR,X.IDTURMADISCGERENCIAL) END)) 
									
		ORDER BY 2,3