import apprise
import os
import socket
import sqlite3
from sqlite3 import Error
import time
from datetime import datetime, timedelta

userDir = os.path.expanduser('~')
APPRISE_CONFIG = userDir + '/BirdNET-Pi/apprise.txt'
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

def sendAppriseNotifications(species, confidence, path, settings_dict, db_path=DB_PATH):
    print(sendAppriseNotifications)
    print(settings_dict)
    if os.path.exists(APPRISE_CONFIG) and os.path.getsize(APPRISE_CONFIG) > 0:

        title = settings_dict.get('APPRISE_NOTIFICATION_TITLE')
        body = settings_dict.get('APPRISE_NOTIFICATION_BODY')
        sciName, comName = species.split("_")
        
        try:
            websiteurl = settings_dict.get('BIRDNETPI_URL')
            if len(websiteurl) == 0:
                raise ValueError('Blank URL')
        except Exception as e:
            websiteurl = "http://"+socket.gethostname()+".local"

        listenurl = websiteurl+"?filename="+path
     
        if settings_dict.get('APPRISE_NOTIFY_EACH_DETECTION') == "1":
            notify_body=body.replace("$sciname", sciName).replace("$comname", comName).replace("$confidence", confidence).replace("$listenurl", listenurl)
            notify_title=title.replace("$sciname", sciName).replace("$comname", comName).replace("$confidence", confidence).replace("$listenurl", listenurl)
            notify(notify_body, notify_title)

        APPRISE_NOTIFICATION_NEW_SPECIES_DAILY_COUNT_LIMIT = 1 # Notifies the first N per day.
        if settings_dict.get('APPRISE_NOTIFY_NEW_SPECIES_EACH_DAY') == "1":
            try:
                con = sqlite3.connect(db_path)
                cur = con.cursor()
                today = datetime.now().strftime("%Y-%m-%d")
                cur.execute(f"SELECT DISTINCT(Com_Name), count(Com_Name) FROM detections WHERE Date = date('{today}') GROUP BY Com_Name")
                known_species = cur.fetchall()
                detections = [d[1] for d in known_species if d[0] == comName.replace("'","")]
                numberDetections = 0
                if len(detections):
                    numberDetections = detections[0]
                if numberDetections > 0 and numberDetections <= APPRISE_NOTIFICATION_NEW_SPECIES_DAILY_COUNT_LIMIT:
                    print("send the notification")
                    notify_body=body.replace("$sciname", sciName).replace("$comname", comName).replace("$confidence", confidence).replace("$listenurl", listenurl) + " (first time today)"
                    notify_title=title.replace("$sciname", sciName).replace("$comname", comName).replace("$confidence", confidence).replace("$listenurl", listenurl) + " (first time today)"
                    notify(notify_body, notify_title)
                con.close()
            except sqlite3.Error as e:
                print(e)
                print("Database busy")
                time.sleep(2)

        if settings_dict.get('APPRISE_NOTIFY_NEW_SPECIES') == "1":
            try:
                con = sqlite3.connect(db_path)
                cur = con.cursor()
                today = datetime.now().strftime("%Y-%m-%d")
                cur.execute(f"SELECT DISTINCT(Com_Name), count(Com_Name) FROM detections WHERE Date >= date('{today}', '-7 day') GROUP BY Com_Name")
                known_species = cur.fetchall()
                detections = [d[1] for d in known_species if d[0] == comName.replace("'","")]
                numberDetections = 0
                if len(detections):
                    numberDetections = detections[0]
                if numberDetections > 0 and numberDetections <= 5:
                    notify_body=body.replace("$sciname", sciName).replace("$comname", comName).replace("$confidence", confidence).replace("$listenurl", listenurl) + " (only seen "+str(int(numberDetections))+" times in last 7d)"
                    notify_title=title.replace("$sciname", sciName).replace("$comname", comName).replace("$confidence", confidence).replace("$listenurl", listenurl) + " (only seen "+str(int(numberDetections))+" times in last 7d)"
                    notify(notify_body, notify_title)
                con.close()
            except sqlite3.Error:
                print("Database busy")
                time.sleep(2)


if __name__ == "__main__":
    print("notfications")

    