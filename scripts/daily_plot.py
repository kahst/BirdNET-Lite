#!/home/pi/BirdNET-Pi/birdnet/bin/python3

import mysql.connector as sql
import os
import configparser

import pandas as pd
import seaborn as sns
# import numpy as np
import matplotlib.pyplot as plt
from matplotlib.colors import LogNorm
from datetime import datetime
import textwrap

#Extract DB_PWD from thisrun.txt
with open('/home/pi/BirdNET-Pi/thisrun.txt', 'r') as f:
     this_run = f.readlines()
     db_pwd = str(str(str([i for i in this_run if i.startswith('DB_PWD')]).split('=')[1]).split('\\')[0])


db_connection = sql.connect(host='localhost',
                 database='birds',
                 user='birder',
                 password=db_pwd)

                    
db_cursor=db_connection.cursor(dictionary=True)

db_cursor.execute('SELECT * FROM detections')

table_rows = db_cursor.fetchall()

df=pd.DataFrame(table_rows)

#Convert Date and Time Fields to Panda's format
df['Date']=pd.to_datetime(df['Date'])
df['Time']=pd.to_datetime(df['Time'], unit='ns')


#Add round hours to dataframe
df['Hour of Day'] = [r.hour for r in df.Time]

#Create separate dataframes for separate locations
df_plt=df #Default to use the whole Dbase

#Get todays readings
now = datetime.now()
df_plt_today = df_plt[df_plt['Date']==now.strftime("%Y-%m-%d")]

#Set number of species to report
readings=10

plt_top10_today = (df_plt_today['Com_Name'].value_counts()[:readings])
df_plt_top10_today = df_plt_today[df_plt_today.Com_Name.isin(plt_top10_today.index)]

#Set Palette for graphics
pal = "Greens"

#Set up plot axes and titles
f, axs = plt.subplots(1, 3, figsize = (10, 4), gridspec_kw=dict(width_ratios=[3, 2, 5]))
plt.subplots_adjust(left=None, bottom=None, right=None, top=None, wspace=0, hspace=0)


#Generate frequency plot
plot=sns.countplot(y='Com_Name', data = df_plt_top10_today, palette = pal+"_r",  order=pd.value_counts(df_plt_top10_today['Com_Name']).iloc[:readings].index, ax=axs[0])

#Try plot grid lines between bars - problem at the moment plots grid lines on bars - want between bars
# plot.grid(True, axis='y')
z=plot.get_ymajorticklabels()
plot.set_yticklabels(['\n'.join(textwrap.wrap(ticklabel.get_text(),15)) for ticklabel in plot.get_yticklabels()], fontsize = 10)
plot.set(ylabel=None)
plot.set(xlabel="Detections")

huw=df_plt_top10_today.groupby('Com_Name')['Confidence'].mean()
plot = sns.boxenplot(x=df_plt_top10_today['Confidence']*100,color='Green',  y=df_plt_top10_today['Com_Name'], ax=axs[1],order=pd.value_counts(df_plt_top10_today['Com_Name']).iloc[:readings].index)
plot.set(xlabel="Confidence", ylabel=None,yticklabels=[])


#Generate crosstab matrix for heatmap plot

heat = pd.crosstab(df_plt_top10_today['Com_Name'],df_plt_top10_today['Hour of Day'])
#Order heatmap Birds by frequency of occurrance
heat.index = pd.CategoricalIndex(heat.index, categories = pd.value_counts(df_plt_top10_today['Com_Name']).iloc[:readings].index)
heat.sort_index(level=0, inplace=True)


hours_in_day = pd.Series(data = range(0,24))
heat_frame = pd.DataFrame(data=0, index=heat.index, columns = hours_in_day)
heat=(heat+heat_frame).fillna(0)

#Generatie heatmap plot
plot = sns.heatmap(heat, norm=LogNorm(), annot=True, fmt="g", annot_kws={"fontsize":7}, cmap = pal , square = False, cbar=False, linewidths = 0.5, linecolor = "Grey", ax=axs[2], yticklabels = False)

# Set heatmap border
for _, spine in plot.spines.items():
    spine.set_visible(True)

plot.set(ylabel=None)
plot.set(xlabel="Hour of Day")
#Set combined plot layout and titles
# plt.tight_layout()
f.subplots_adjust(top=0.9)
plt.suptitle("Last Updated: "+ str(now.strftime("%d %m %Y %H:%M")))

#Save combined plot
savename='/home/pi/BirdSongs/Extracted/Charts/Combo-'+str(now.strftime("%d-%m-%Y"))+'.png'
plt.savefig(savename)
# plt.show()
plt.close()

#Get bottom 10 today
plt_bot10_today = (df_plt_today['Com_Name'].value_counts()[-readings:])
df_plt_bot10_today = df_plt_today[df_plt_today.Com_Name.isin(plt_bot10_today.index)]

#Set Palette for graphics
pal = "Reds"

#Set up plot axes and titles
f, axs = plt.subplots(1, 3, figsize = (10, 4), gridspec_kw=dict(width_ratios=[3, 2, 5]))
plt.subplots_adjust(left=None, bottom=None, right=None, top=None, wspace=0, hspace=0)


#Generate frequency plot
plot=sns.countplot(y='Com_Name', data = df_plt_bot10_today, palette = pal+"_r",  order=pd.value_counts(df_plt_bot10_today['Com_Name']).iloc[-readings:].index, ax=axs[0])

#Try plot grid lines between bars - problem at the moment plots grid lines on bars - want between bars
# plot.grid(True, axis='y')
z=plot.get_ymajorticklabels()
plot.set_yticklabels(['\n'.join(textwrap.wrap(ticklabel.get_text(),15)) for ticklabel in plot.get_yticklabels()], fontsize = 10)
plot.set(ylabel=None)
plot.set(xlabel="Detections")

huw=df_plt_bot10_today.groupby('Com_Name')['Confidence'].mean()
plot = sns.boxenplot(x=df_plt_bot10_today['Confidence']*100,color='Red',  y=df_plt_bot10_today['Com_Name'], ax=axs[1],order=pd.value_counts(df_plt_bot10_today['Com_Name']).iloc[-readings:].index)
plot.set(xlabel="Confidence", ylabel=None,yticklabels=[])


#Generate crosstab matrix for heatmap plot

heat = pd.crosstab(df_plt_bot10_today['Com_Name'],df_plt_bot10_today['Hour of Day'])
#Order heatmap Birds by frequency of occurrance
heat.index = pd.CategoricalIndex(heat.index, categories = pd.value_counts(df_plt_bot10_today['Com_Name']).iloc[-readings:].index)
heat.sort_index(level=0, inplace=True)


hours_in_day = pd.Series(data = range(0,24))
heat_frame = pd.DataFrame(data=0, index=heat.index, columns = hours_in_day)
heat=(heat+heat_frame).fillna(0)

#Generatie heatmap plot
plot = sns.heatmap(heat, norm=LogNorm(),  annot=True,  annot_kws={"fontsize":7}, cmap = pal , square = False, cbar=False, linewidths = 0.5, linecolor = "Grey", ax=axs[2], yticklabels = False)

# Set heatmap border
for _, spine in plot.spines.items():
    spine.set_visible(True)

plot.set(ylabel=None)
plot.set(xlabel="Hour of Day")
#Set combined plot layout and titles
# plt.tight_layout()
f.subplots_adjust(top=0.9)
plt.suptitle("Last Updated: "+ str(now.strftime("%d %m %Y %H:%M")))

#Save combined plot
savename='/home/pi/BirdSongs/Extracted/Charts/Combo2-'+str(now.strftime("%d-%m-%Y"))+'.png'
plt.savefig(savename)
#plt.show()
plt.close()
