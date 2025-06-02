
---------DM01------------


DECLARE @periodo varchar(6)

SET @periodo = '202401' --- Periodo a evaluar

SELECT 
		A.cvepresup as Unidad,
		
		(A.Diabetes_20_a_44_M + B.Diabetes_20_a_44_H + C.Det_Diabetes_45_59_M + D.Det_Diabetes_45_59_H + E.Det_Diabetes_60_) AS Numerador,
		
		(A.Pob_20_a_44_M + B.Pob_20_a_44_H + C.Prev_Diabetes_PobM_45_59 + D.Prev_Diabetes_PobH_45_59 + E.Prev_Diabetes_Pob_60_) AS Denominador,

		A.periodo as Periodo	 
		
FROM 
(
	---- Selecci�n de mujeres 20 a 44 a�os de edad
	SELECT cvepresup, Diabetes_20_a_44_M, Pob_20_a_44_M, periodo
	FROM tb_INCompl_DMHTACol_Deleg
	WHERE periodo = @periodo
	
) A

LEFT OUTER JOIN
(
	---- Selecci�n de hombres 20 a 44 a�os de edad
	SELECT cvepresup, Diabetes_20_a_44_H, Pob_20_a_44_H, periodo
	FROM tb_INCompl_DMHTACol_Deleg
	WHERE periodo = @periodo
	
) B

ON A.cvepresup = B.cvepresup

LEFT OUTER JOIN
(
	---- Selecci�n de mujeres 45 a 59 a�os de edad
	SELECT CvePresup, Det_Diabetes_45_59_M, Prev_Diabetes_PobM_45_59, periodo
	FROM dbo.tb_IMCP_08_SM_UM
	WHERE periodo = @periodo

) C

ON A.cvepresup = C.cvepresup

LEFT OUTER JOIN
(
	---- Selecci�n de hombres 45 a 59 a�os de edad
	SELECT CvePresup, Det_Diabetes_45_59_H, Prev_Diabetes_PobH_45_59, periodo
	FROM dbo.tb_IMCP_09_SH_UM
	WHERE periodo = @periodo

) D

ON A.cvepresup = D.cvepresup

LEFT OUTER JOIN
(
	---- Selecci�n de poblaci�n de 60 a�os y m�s de edad
	SELECT CvePresup, Det_Diabetes_60_, Prev_Diabetes_Pob_60_, periodo
	FROM dbo.tb_IMCP_10_SY_UM
	WHERE periodo = @periodo

) E

ON A.cvepresup = E.cvepresup

WHERE right(A.cvepresup,4) <> '9999'

ORDER BY A.cvepresup