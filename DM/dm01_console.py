import pymssql
import pymysql
import sys
import json
from decimal import Decimal

# Obtener el periodo desde los argumentos de línea de comandos
periodo = sys.argv[1] if len(sys.argv) > 1 else '202501'

# Función para convertir Decimal a float
def convert_decimal(value):
    if isinstance(value, Decimal):
        return float(value)
    return value

try:
    # Conectar a la base de datos en el servidor remoto
    with pymssql.connect(
        server='172.25.31.212',
        user='simf_siais',
        password='simf_siais',
        database='DBSIAIS'
    ) as conn:
        cursor = conn.cursor()

        # Obtener las claves presupuestales
        cursor.execute("SELECT DISTINCT CvePresup FROM dbo.tb_IMCP_08_SM_UM WHERE periodo = %s", (periodo,))
        claves_presupuestales = cursor.fetchall()

        resultados_finales = []
        total_global_numerador = 0
        total_global_denominador = 0

        # Conectar a la base de datos local (XAMPP)
        local_conn = pymysql.connect(
            host='localhost',
            user='root',
            password='',
            database='ciae'
        )
        local_cursor = local_conn.cursor()

        for clave in claves_presupuestales:
            cve_presup = clave[0]
            
            # Obtener la unidad correspondiente a la clave presupuestal
            local_cursor.execute("""
                SELECT unidad FROM claves_up WHERE clave = %s
            """, (cve_presup,))
            unidad_result = local_cursor.fetchone()
            unidad = unidad_result[0] if unidad_result else "Desconocida"

            # Diccionario para almacenar los resultados de cada consulta
            resultados_consultas = {
                'clave_presup': cve_presup,
                'unidad': unidad,
                'consultas': [],
                'total_numerador': 0,
                'total_denominador': 0,
                'promedio_general': 0
            }

            # 1. Consultar tb_IMCP_08_SM_UM - Mujeres 45-59
            cursor.execute("""
                SELECT SUM(Det_Diabetes_45_59_M) AS Numerador, SUM(PobM_45_59) AS Denominador
                FROM dbo.tb_IMCP_08_SM_UM
                WHERE periodo = %s AND CvePresup = %s AND Prestador NOT LIKE '%TOTAL DE LA UNIDAD%'
            """, (periodo, cve_presup))
            result = cursor.fetchone()
            if result and result[0] is not None and result[1] is not None:
                num = convert_decimal(result[0])
                den = convert_decimal(result[1])
                porcentaje = (num / den * 100) if den > 0 else 0
                resultados_consultas['consultas'].append({
                    'consulta': 'Mujeres 45-59 años (tb_IMCP_08_SM_UM)',
                    'numerador': num,
                    'denominador': den,
                    'porcentaje': porcentaje
                })
                resultados_consultas['total_numerador'] += num
                resultados_consultas['total_denominador'] += den

            # 2. Consultar tb_IMCP_09_SH_UM - Hombres 45-59
            cursor.execute("""
                SELECT SUM(Det_Diabetes_45_59_H) AS Numerador, SUM(PobH_45_59) AS Denominador
                FROM dbo.tb_IMCP_09_SH_UM
                WHERE periodo = %s AND CvePresup = %s AND Prestador NOT LIKE '%TOTAL DE LA UNIDAD%'
            """, (periodo, cve_presup))
            result = cursor.fetchone()
            if result and result[0] is not None and result[1] is not None:
                num = convert_decimal(result[0])
                den = convert_decimal(result[1])
                porcentaje = (num / den * 100) if den > 0 else 0
                resultados_consultas['consultas'].append({
                    'consulta': 'Hombres 45-59 años (tb_IMCP_09_SH_UM)',
                    'numerador': num,
                    'denominador': den,
                    'porcentaje': porcentaje
                })
                resultados_consultas['total_numerador'] += num
                resultados_consultas['total_denominador'] += den

            # 3. Consultar tb_IMCP_10_SY_UM - Adultos 60+
            cursor.execute("""
                SELECT SUM(Det_Diabetes_60_) AS Numerador, SUM(Adultos_60_) AS Denominador
                FROM dbo.tb_IMCP_10_SY_UM
                WHERE periodo = %s AND CvePresup = %s AND Prestador NOT LIKE '%TOTAL DE LA UNIDAD%'
            """, (periodo, cve_presup))
            result = cursor.fetchone()
            if result and result[0] is not None and result[1] is not None:
                num = convert_decimal(result[0])
                den = convert_decimal(result[1])
                porcentaje = (num / den * 100) if den > 0 else 0
                resultados_consultas['consultas'].append({
                    'consulta': 'Adultos 60+ años (tb_IMCP_10_SY_UM)',
                    'numerador': num,
                    'denominador': den,
                    'porcentaje': porcentaje
                })
                resultados_consultas['total_numerador'] += num
                resultados_consultas['total_denominador'] += den

            # 4. Consultar tb_INCompl_DMHTACol - Mujeres 20-44
            cursor.execute("""
                SELECT SUM(Diabetes_20_a_44_M) AS Numerador, SUM(Pob_20_a_44_M) AS Denominador
                FROM dbo.tb_INCompl_DMHTACol
                WHERE periodo = %s AND CvePresup = %s AND Prestador NOT LIKE '%TOTAL DE LA UNIDAD%'
            """, (periodo, cve_presup))
            result = cursor.fetchone()
            if result and result[0] is not None and result[1] is not None:
                num = convert_decimal(result[0])
                den = convert_decimal(result[1])
                porcentaje = (num / den * 100) if den > 0 else 0
                resultados_consultas['consultas'].append({
                    'consulta': 'Mujeres 20-44 años (tb_INCompl_DMHTACol)',
                    'numerador': num,
                    'denominador': den,
                    'porcentaje': porcentaje
                })
                resultados_consultas['total_numerador'] += num
                resultados_consultas['total_denominador'] += den

            # 5. Consultar tb_INCompl_DMHTACol - Hombres 20-44
            cursor.execute("""
                SELECT SUM(Diabetes_20_a_44_H) AS Numerador, SUM(Pob_20_a_44_H) AS Denominador
                FROM dbo.tb_INCompl_DMHTACol
                WHERE periodo = %s AND CvePresup = %s AND Prestador NOT LIKE '%TOTAL DE LA UNIDAD%'
            """, (periodo, cve_presup))
            result = cursor.fetchone()
            if result and result[0] is not None and result[1] is not None:
                num = convert_decimal(result[0])
                den = convert_decimal(result[1])
                porcentaje = (num / den * 100) if den > 0 else 0
                resultados_consultas['consultas'].append({
                    'consulta': 'Hombres 20-44 años (tb_INCompl_DMHTACol)',
                    'numerador': num,
                    'denominador': den,
                    'porcentaje': porcentaje
                })
                resultados_consultas['total_numerador'] += num
                resultados_consultas['total_denominador'] += den

            # Calcular el promedio general para esta clave presupuestal
            if resultados_consultas['total_denominador'] > 0:
                resultados_consultas['promedio_general'] = (resultados_consultas['total_numerador'] / resultados_consultas['total_denominador']) * 100
            
            # Agregar a los resultados finales
            resultados_finales.append(resultados_consultas)
            
            # Sumar a los totales globales
            total_global_numerador += resultados_consultas['total_numerador']
            total_global_denominador += resultados_consultas['total_denominador']

        # Calcular el porcentaje global
        if total_global_denominador > 0:
            porcentaje_global = (total_global_numerador / total_global_denominador) * 100
        else:
            porcentaje_global = 0

        # Crear el resultado final en formato JSON
        resultado_json = {
            'resultados_detallados': resultados_finales,
            'total_global': {
                'numerador': total_global_numerador,
                'denominador': total_global_denominador,
                'porcentaje': porcentaje_global
            }
        }

        # Devolver los resultados como JSON
        print(json.dumps(resultado_json, indent=2))

        # Cerrar la conexión local
        local_cursor.close()
        local_conn.close()

except pymssql.OperationalError as e:
    print(json.dumps({"error": "Error de conexión al servidor remoto", "details": str(e)}))
except pymysql.MySQLError as e:
    print(json.dumps({"error": "Error de conexión a la base de datos local", "details": str(e)}))
except pymssql.DatabaseError as e:
    print(json.dumps({"error": "Error al consultar la base de datos remota", "details": str(e)}))
except Exception as e:
    print(json.dumps({"error": "Error inesperado", "details": str(e)}))
