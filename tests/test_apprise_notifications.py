import os
import sqlite3
import pytest

from scripts.notifications import sendAppriseNotifications
from datetime import datetime, timedelta

def create_test_db(db_file):
    """ create a database connection to a SQLite database """
    conn = None
    try:
        conn = sqlite3.connect(db_file)
        sql_create_detections_table = """ CREATE TABLE IF NOT EXISTS detections (
                                        id integer PRIMARY KEY,
                                        Com_Name text NOT NULL,
                                        Date date NULL,
                                        Time time NULL
                                    ); """
        cur = conn.cursor()
        cur.execute(sql_create_detections_table)
        sql = ''' INSERT INTO detections(Com_Name, Date)
              VALUES(?,?) '''
        
        cur = conn.cursor()
        cur.execute(sql, ["Great Crested Flycatcher", datetime.now()])
        conn.commit()

    except Error as e:
        print(e)
    finally:
        if conn:
            conn.close()


@pytest.fixture(autouse=True)
def clean_up_after_each_test():
    yield
    os.remove("test.db")

def test_daily_notifications(mocker):
    notify_call = mocker.patch('scripts.notifications.notify')
    create_test_db("test.db")
    assert(0 == 0)
    sendAppriseNotifications("Myiarchus crinitus_Great Crested Flycatcher", "91", "filename", "test.db")
    assert(notify_call.call_count == 2)
    assert(
        notify_call.call_args_list[0][0][0][0] == "A Great Crested Flycatcher (Myiarchus crinitus) was just detected with a confidence of 91 (only seen 1 times today)"
    )
    assert(
        notify_call.call_args_list[1][0][0] == "A Great Crested Flycatcher (Myiarchus crinitus) was just detected with a confidence of 91 (only seen 1 times in last 7d)"
    )   
