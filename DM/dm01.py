import pymssql

print("Iniciando conexión...")

try:
    conn = pymssql.connect(
        server='11.42.28.43',
        user='simf_siais',
        password='simf_siais',
        database='DBSIAIS'  # Nos conectamos directamente a DBSIAIS
    )
    print("✅ Conexión establecida a DBSIAIS")

    cursor = conn.cursor()
    cursor.execute("SELECT name FROM sysobjects WHERE type='U'")  # 'U' = user table

    tablas = cursor.fetchall()
    print("\n📋 Tablas encontradas en DBSIAIS:\n")
    for tabla in tablas:
        print(f" - {tabla[0]}")

    conn.close()
    print("\nConexión cerrada")

except Exception as e:
    print("❌ Error al consultar las tablas:")
    print(e)



