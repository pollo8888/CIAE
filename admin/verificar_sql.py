# verificar_sql.py
import sys
import json
import pymssql

def verificar_conexion(ip, usuario='simf_siais', contrasena='simf_siais', base_datos='DBSIAIS'):
    try:
        # Conectar a SQL Server
        conn = pymssql.connect(
            server=ip,
            user=usuario,
            password=contrasena,
            database=base_datos,
            timeout=30
        )
        
        cursor = conn.cursor()
        
        # Contar tablas
        cursor.execute("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'")
        table_count = cursor.fetchone()[0]
        
        cursor.close()
        conn.close()
        
        return {
            "server": ip,
            "status": "online",
            "tables": table_count
        }
        
    except Exception as e:
        return {
            "server": ip,
            "status": "error",
            "error": str(e)
        }

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"status": "error", "error": "IP no proporcionada"}))
        sys.exit(1)
    
    ip = sys.argv[1]
    
    # Puedes expandir esto para aceptar más parámetros desde la línea de comandos
    resultado = verificar_conexion(ip)
    print(json.dumps(resultado))
