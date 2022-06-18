import apprise
import os
import socket
import sqlite3
from sqlite3 import Error
import time
from datetime import datetime, timedelta

userDir = os.path.expanduser('~')
APPRISE_CONFIG = userDir + '/BirdNET-Pi/apprise.txt'
THIS_RUN_PATH = userDir + '/BirdNET-Pi/scripts/thisrun.txt'
DB_PATH = userDir + '/BirdNET-Pi/scripts/birds.db'

def notify(body, title):
    apobj = apprise.Apprise()
    config = apprise.AppriseConfig()
    config.add(APPRISE_CONFIG)
    apobj.add(config)
    apobj.notify(
        body=body,
        title=title,
    )

def get_setting(param):
    with open(THIS_RUN_PATH, 'r') as f:
        this_run = f.readlines()
    return str(str(str([i for i in this_run if i.startswith(param)]).split('=')[1]).split('\\')[0]).replace('"', '')

def sendAppriseNotifications(species, confidence, path, db_path=DB_PATH):
    if os.path.exists(APPRISE_CONFIG) and os.path.getsize(APPRISE_CONFIG) > 0:

        title = get_setting('APPRISE_NOTIFICATION_TITLE')
        body = get_setting('APPRISE_NOTIFICATION_BODY')
        
        try:
            websiteurl = get_setting('BIRDNETPI_URL')
            if len(websiteurl) == 0:
                raise ValueError('Blank URL')
        except Exception as e:
            websiteurl = "http://"+socket.gethostname()+".local"

        listenurl = websiteurl+"?filename="+path
     
        if get_setting('APPRISE_NOTIFY_EACH_DETECTION') == "1":
            apobj = apprise.Apprise()
            config = apprise.AppriseConfig()
            config.add(APPRISE_CONFIG)
            apobj.add(config)

            apobj.notify(
                body=body.replace("$sciname", species.split("_")[0]).replace("$comname", species.split("_")[1]).replace("$confidence", confidence).replace("$listenurl", listenurl),
                title=title.replace("$sciname", species.split("_")[0]).replace("$comname", species.split("_")[1]).replace("$confidence", confidence).replace("$listenurl", listenurl),
            )

        APPRISE_NOTIFICATION_NEW_SPECIES_DAILY = True
        APPRISE_NOTIFICATION_NEW_SPECIES_DAILY_COUNT_LIMIT = 2 # Notifies the first N per day.
        if APPRISE_NOTIFICATION_NEW_SPECIES_DAILY:
            try:
                con = sqlite3.connect(db_path)
                cur = con.cursor()
                cur.execute("SELECT DISTINCT(Com_Name), count(Com_Name) FROM detections WHERE date > (SELECT DATETIME('now', '-1 day')) GROUP BY Com_Name")
                known_species = cur.fetchall()
                sciName, comName = species.split("_")
                numberDetections = [d[1] for d in known_species if d[0] == comName.replace("'","")][0]
                if numberDetections < APPRISE_NOTIFICATION_NEW_SPECIES_DAILY_COUNT_LIMIT:
                    notify_body=body.replace("$sciname", sciName).replace("$comname", comName).replace("$confidence", confidence).replace("$listenurl", listenurl) + " (only seen "+str(int(numberDetections))+" times today)",
                    notify_title=title.replace("$sciname", sciName).replace("$comname", comName).replace("$confidence", confidence).replace("$listenurl", listenurl) + " (only seen "+str(int(numberDetections))+" times today)",
                    notify(notify_body, notify_title)
                con.close()
            except sqlite3.Error as e:
                print(e)
                print("Database busy")
                time.sleep(2)

        if get_setting('APPRISE_NOTIFY_NEW_SPECIES') == "1":
            try:
                con = sqlite3.connect(db_path)
                cur = con.cursor()
                cur.execute("SELECT DISTINCT(Com_Name), count(Com_Name) FROM detections WHERE date > (SELECT DATETIME('now', '-7 day')) GROUP BY Com_Name")
                known_species = cur.fetchall()
                sciName, comName = species.split("_")
                numberDetections = [d[1] for d in known_species if d[0] == comName.replace("'","")][0]
                if numberDetections <= 5:
                    notify_body=body.replace("$sciname", sciName).replace("$comname", comName).replace("$confidence", confidence).replace("$listenurl", listenurl) + " (only seen "+str(int(numberDetections))+" times in last 7d)"
                    notify_title=title.replace("$sciname", sciName).replace("$comname", comName).replace("$confidence", confidence).replace("$listenurl", listenurl) + " (only seen "+str(int(numberDetections))+" times in last 7d)"
                    notify(notify_body, notify_title)
                con.close()
            except sqlite3.Error:
                print("Database busy")
                time.sleep(2)


if __name__ == "__main__":
    print("notfications")

    