import MySQLdb

try:
    db = MySQLdb.connect(host="127.0.0.1", user="root", passwd="", db="lf_django")
    cursor = db.cursor()
    cursor.execute("SET FOREIGN_KEY_CHECKS = 0;")
    cursor.execute("SHOW TABLES;")
    tables = cursor.fetchall()
    for table in tables:
        cursor.execute(f"DROP TABLE {table[0]};")
    cursor.execute("SET FOREIGN_KEY_CHECKS = 1;")
    print("Todas las tablas de 'lf_django' han sido eliminadas.")
    db.close()
except Exception as e:
    print(f"Error al limpiar la base de datos: {e}")
