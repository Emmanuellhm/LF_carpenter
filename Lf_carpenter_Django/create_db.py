import MySQLdb

try:
    db = MySQLdb.connect(host="127.0.0.1", user="root", passwd="")
    cursor = db.cursor()
    cursor.execute("CREATE DATABASE IF NOT EXISTS lf_django CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;")
    print("Base de datos 'lf_django' creada o ya existente.")
    db.close()
except Exception as e:
    print(f"Error al crear la base de datos: {e}")
