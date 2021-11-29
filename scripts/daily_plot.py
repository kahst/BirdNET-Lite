#!/home/pi/BirdNET-Pi/birdnet/bin/python3
import pandas as pd
import seaborn as sns
# import numpy as np
import matplotlib.pyplot as plt
from datetime import datetime
import textwrap

#Read database into Pandas dataframe
df = pd.read_csv('~/BirdNET-Pi/BirdDB.txt', sep=';')

#Convert Date and Time Fields to Panda's format
df['Date']=pd.to_datetime(df['Date'])
df['Time']=pd.to_datetime(df['Time'])


#Create separate dataframes for separate locations
df_clt=df[df.Lat == 35.0]

now = datetime.now()
df_clt_today = df_clt[df_clt['Date']==now.strftime("%d-%m-%y")]

#Get top 10 today
clt_top10_today=(df_clt_today['Com_Name'].value_counts()[:10])
df_clt_top10_today = df_clt_today[df_clt_today.Com_Name.isin(clt_top10_today.index)]

pal = "Greens"

#Set up plot axes and titles
plot=sns.countplot(y='Com_Name', data = df_clt_top10_today, order = pd.value_counts(df_clt_top10_today['Com_Name']).iloc[:10].index)
plot.set_yticklabels(['\n'.join(textwrap.wrap(ticklabel.get_text(),15)) for ticklabel in plot.get_yticklabels()])
plot.set_title('Top 10 Today --'+ str(now.strftime(" Last updated %B %d, %Y %I:%M%P")))
plot.set_xlabel("Detections", fontsize = 12)
plot.set_ylabel("", fontsize = 12)
plt.tight_layout()

#Save plot
savename='/home/pi/BirdSongs/Extracted/Top_10_Today-'+str(now.strftime("%d-%m-%Y"))+'.png'
plt.savefig(savename)
plt.clf()

#Generate heatmap plot
df_clt_top10_today['Hour'] = [r.hour for r in df_clt_top10_today.Time]
heat = pd.crosstab(df_clt_top10_today['Com_Name'],df_clt_top10_today['Hour'])



plot = sns.heatmap(heat, annot=True, annot_kws={"fontsize":7}, cmap = "gray_r", square = False, cbar=False, linewidths = 0)
plot.set_yticklabels(['\n'.join(textwrap.wrap(ticklabel.get_text(),15)) for ticklabel in plot.get_yticklabels()])
plot.set_title('Phenology --'+ str(now.strftime(" Last updated %B %d, %Y %I:%M%P")))
plot.set_xlabel("Hour (24h)", fontsize = 12)
plot.set_ylabel("", fontsize = 12)
plt.tight_layout()

#Save plot
savename='/home/pi/BirdSongs/Extracted/When_today-'+str(now.strftime("%d-%m-%Y"))+'.png'
plt.savefig(savename)

plt.clf()



