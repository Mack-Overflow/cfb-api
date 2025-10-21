#!/usr/bin/env python3
import csv
import pymysql  # pip install pymysql

CSV_PATH = "teams.csv"
CHECK_CONFERENCE = True  # set False to ignore conference check

conn = pymysql.connect(
    host="127.0.0.1",
    user="laravel",
    password="laravel",
    database="laravel",
    charset="utf8mb4",
    cursorclass=pymysql.cursors.DictCursor,
    autocommit=False,
)

updated = 0
with conn:
    with conn.cursor() as cur, open(CSV_PATH, newline="", encoding="utf-8") as f:
        reader = csv.DictReader(f)
        for row in reader:
            cfbd_id = row.get("id")
            logo = row.get("logo")
            conference = row.get("conference")

            if not cfbd_id or not logo:
                continue

            if CHECK_CONFERENCE:
                sql = """
                    UPDATE teams
                    SET logo = %s
                    WHERE cfbd_id = %s
                      AND COALESCE(conference, '') = COALESCE(%s, '')
                """
                params = (logo, cfbd_id, conference)
            else:
                sql = "UPDATE teams SET logo = %s WHERE cfbd_id = %s"
                params = (logo, cfbd_id)

            cur.execute(sql, params)
            updated += cur.rowcount

    conn.commit()

print(f"Updated {updated} rows.")
