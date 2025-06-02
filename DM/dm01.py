import pymssql
import pymysql
import sys
import json  # Importar el módulo json
from decimal import Decimal  # Importar Decimal para manejar conversiones

# Obtener el periodo desde los argumentos de línea de comandos
periodo = sys.argv[1] if len(sys.argv) > 1 else '202504'

# Inicializar el diccionario para almacenar los resultados
resultados_finales = []
total_global_numerador = 0
total_global_denominador = 0

def convert_decimal(value):
    """Convierte Decimal a float o devuelve el valor original si no es Decimal."""
    if isinstance(value, Decimal):
        return float(value)
    return value

try:
    # Conectar a la base de datos en el servidor remoto
    with pymssql.connect(
        server='11.42.28.43',
        user='simf_siais',
        password='simf_siais',
        database='DBSIAIS'
    ) as conn:
        cursor = conn.cursor()

        # Obtener las claves presupuestales
        cursor.execute("SELECT DISTINCT CvePresup FROM dbo.tb_IMCP_08_SM_UM WHERE periodo = %s", (periodo,))
        claves_presupuestales = cursor.fetchall()

        # Conectar a la base de datos local (XAMPP)
        local_conn = pymysql.connect(
            host='localhost',
            user='root',  # Cambia esto por tu usuario de MySQL
            password='',  # Cambia esto por tu contraseña de MySQL
            database='ciae'  # Cambia esto por el nombre de tu base de datos
        )
        local_cursor = local_conn.cursor()

        for clave in claves_presupuestales:
            cve_presup = clave[0]

            # Inicializar numeradores y denominadores para la clave actual
            total_numerador = 0
            total_denominador = 0

            # Obtener la unidad correspondiente a la clave presupuestal desde la base de datos local
            local_cursor.execute("""
                SELECT unidad FROM claves_up WHERE clave = %s
            """, (cve_presup,))
            unidad_result = local_cursor.fetchone()
            unidad = unidad_result[0] if unidad_result else "Desconocida"

            # Consultar tb_IMCP_08_SM_UM
            cursor.execute("""
                SELECT SUM(Det_Diabetes_45_59_M) AS Numerador, SUM(PobM_45_59) AS Denominador
                FROM dbo.tb_IMCP_08_SM_UM
                WHERE periodo = %s AND CvePresup = %s
            """, (periodo, cve_presup))
            result = cursor.fetchone()
            if result:
                num = convert_decimal(result[0]) if result[0] is not None else 0
                den = convert_decimal(result[1]) if result[1] is not None else 0
                total_numerador += num
                total_denominador += den

            # Consultar tb_IMCP_09_SH_UM
            cursor.execute("""
                SELECT SUM(Det_Diabetes_45_59_H) AS Numerador, SUM(PobH_45_59) AS Denominador
                FROM dbo.tb_IMCP_09_SH_UM
                WHERE periodo = %s AND CvePresup = %s
            """, (periodo, cve_presup))
            result = cursor.fetchone()
            if result:
                num = convert_decimal(result[0]) if result[0] is not None else 0
                den = convert_decimal(result[1]) if result[1] is not None else 0
                total_numerador += num
                total_denominador += den

            # Consultar tb_IMCP_10_SY_UM
            cursor.execute("""
                SELECT SUM(Det_Diabetes_60_) AS Numerador, SUM(Adultos_60_) AS Denominador
                FROM dbo.tb_IMCP_10_SY_UM
                WHERE periodo = %s AND CvePresup = %s
            """, (periodo, cve_presup))
            result = cursor.fetchone()
            if result:
                num = convert_decimal(result[0]) if result[0] is not None else 0
                den = convert_decimal(result[1]) if result[1] is not None else 0
                total_numerador += num
                total_denominador += den

            # Consultar tb_INCompl_DMHTACol para mujeres 20-44
            cursor.execute("""
                SELECT SUM(Diabetes_20_a_44_M) AS Numerador, SUM(Pob_20_a_44_M) AS Denominador
                FROM dbo.tb_INCompl_DMHTACol
                WHERE periodo = %s AND CvePresup = %s
            """, (periodo, cve_presup))
            result = cursor.fetchone()
            if result:
                num = convert_decimal(result[0]) if result[0] is not None else 0
                den = convert_decimal(result[1]) if result[1] is not None else 0
                total_numerador += num
                total_denominador += den

            # Consultar tb_INCompl_DMHTACol para hombres 20-44
            cursor.execute("""
                SELECT SUM(Diabetes_20_a_44_H) AS Numerador, SUM(Pob_20_a_44_H) AS Denominador
                FROM dbo.tb_INCompl_DMHTACol
                WHERE periodo = %s AND CvePresup = %s
            """, (periodo, cve_presup))
            result = cursor.fetchone()
            if result:
                num = convert_decimal(result[0]) if result[0] is not None else 0
                den = convert_decimal(result[1]) if result[1] is not None else 0
                total_numerador += num
                total_denominador += den

            # Calcular el resultado para la clave actual
            if total_denominador > 0:
                resultado_final = (total_numerador / total_denominador) * 100
            else:
                resultado_final = 0  # Evitar división por cero

            # Almacenar el resultado
            resultados_finales.append({
                'clave_presup': cve_presup,
                'unidad': unidad,
                'numerador': total_numerador,
                'denominador': total_denominador,
                'resultado_final': resultado_final
            })
            
            # Sumar a los totales globales
            total_global_numerador += total_numerador
            total_global_denominador += total_denominador

        # Calcular el porcentaje global
        if total_global_denominador > 0:
            division = total_global_numerador / total_global_denominador
            porcentaje_global = division * 100
            
            # Agregar resultados globales al diccionario
            resultados = {
                'resultados_individuales': resultados_finales,
                'total_global': {
                    'numerador': total_global_numerador,
                    'denominador': total_global_denominador,
                    'porcentaje': porcentaje_global
                }
            }
        else:
            resultados = {
                'resultados_individuales': resultados_finales,
                'total_global': {
                    'numerador': total_global_numerador,
                    'denominador': total_global_denominador,
                    'porcentaje': 0
                }
            }

        # Devolver los resultados como JSON
        print(json.dumps(resultados))

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
