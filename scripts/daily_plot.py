#!/home/pi/BirdNET-Pi/birdnet/bin/python3
import pandas as pd
import seaborn as sns
# import numpy as np
import matplotlib.pyplot as plt
from matplotlib.colors import LogNorm
from datetime import datetime
import textwrap



#Read database into Pandas dataframe
df = pd.read_csv('~/BirdNET-Pi/BirdDB.txt', sep=';')

#Convert Date and Time Fields to Panda's format
df['Date']=pd.to_datetime(df['Date'])
df['Time']=pd.to_datetime(df['Time'])

#Add round hours to dataframe
df['Hour of Day'] = [r.hour for r in df.Time]

#Create separate dataframes for separate locations
df_jhb=df[df.Lat > -32]
df_ec = df[df.Lat < -32]

#Get todays readings for Joburg
now = datetime.now()
df_jhb_today = df_jhb[df_jhb['Date']==now.strftime("%Y-%m-%d")]

# Definition to start getting top N detections - work in process
def filter_by_freq(df: pd.DataFrame, column: str, min_freq: int) -> pd.DataFrame:
    """Filters the DataFrame based on the value frequency in the specified column.

    :param df: DataFrame to be filtered.
    :param column: Column name that should be frequency filtered.
    :param min_freq: Minimal value frequency for the row to be accepted.
    :return: Frequency filtered DataFrame.
    """
    # Frequencies of each value in the column.
    freq = df[column].value_counts()
    # Select frequent values. Value is in the index.
    frequent_values = freq[freq >= min_freq].index
    # Return only rows with value frequency above threshold.
    return df[df[column].isin(frequent_values)]

#Get top readings today
min_valuecounts = 2


jhb_gt_min = filter_by_freq (df_jhb_today,'Com_Name', min_valuecounts)

jhb_gt_min_counts = jhb_gt_min['Com_Name'].value_counts()
print(jhb_gt_min_counts)


jhb_top10_today = (df_jhb_today['Com_Name'].value_counts()[:10])
df_jhb_top10_today = df_jhb_today[df_jhb_today.Com_Name.isin(jhb_top10_today.index)]

#Get bottom 10 today
jhb_bot10_today=(df_jhb_today['Com_Name'].value_counts()[-10:])
df_jhb_bot10_today = df_jhb_today[df_jhb_today.Com_Name.isin(jhb_bot10_today.index)]

#Set Palette for graphics
pal = "Greens"

#Set up plot axes and titles
f, axs = plt.subplots(1, 2, figsize = (10, 4), gridspec_kw=dict(width_ratios=[3, 5]))
plt.subplots_adjust(left=None, bottom=None, right=None, top=None, wspace=0, hspace=None)


#Generate frequency plot
plot=sns.countplot(y='Com_Name',  data = df_jhb_top10_today, palette = pal+"_r", order=pd.value_counts(df_jhb_top10_today['Com_Name']).iloc[:20].index, ax=axs[0])

#Try plot grid lines between bars - problem at the moment plots grid lines on bars - want between bars
# plot.grid(True, axis='y')

plot.set_yticklabels(['\n'.join(textwrap.wrap(ticklabel.get_text(),15)) for ticklabel in plot.get_yticklabels()])
plot.set(ylabel=None)
plot.set(xlabel="Detections")

#Generate crosstab matrix for heatmap plot

heat = pd.crosstab(df_jhb_top10_today['Com_Name'],df_jhb_top10_today['Hour of Day'])
#Order heatmap Birds by frequency of occurrance
heat.index = pd.CategoricalIndex(heat.index, categories = pd.value_counts(df_jhb_top10_today['Com_Name']).iloc[:10].index)
heat.sort_index(level=0, inplace=True)


hours_in_day = pd.Series(data = range(0,24))
heat_frame = pd.DataFrame(data=0, index=heat.index, columns = hours_in_day)
heat=(heat+heat_frame).fillna(0)

#Generatie heatmap plot
plot = sns.heatmap(heat, norm=LogNorm(), annot=True,  annot_kws={"fontsize":7}, cmap = pal , square = False, cbar=False, linewidths = 0.5, linecolor = "Grey", ax=axs[1], yticklabels = False)

# Set heatmap border
for _, spine in plot.spines.items():
    spine.set_visible(True)

plot.set(ylabel=None)
plot.set(xlabel="Hour of Day")
#Set combined plot layout and titles
plt.tight_layout()
f.subplots_adjust(top=0.9)
plt.suptitle("Last Updated: "+ str(now.strftime("%B, %d at %I:%M%P")))

#Save combined plot
savename='/home/pi/BirdSongs/Extracted/Combo-'+str(now.strftime("%d-%m-%Y"))+'.png'
plt.savefig(savename)
plt.close()


#Get bottom 10 today
jhb_bot10_today=(df_jhb_today['Com_Name'].value_counts()[-10:])
df_jhb_bot10_today = df_jhb_today[df_jhb_today.Com_Name.isin(jhb_bot10_today.index)]

#Set Palette for graphics
pal = "Reds"

#Set up plot axes and titles
f, axs = plt.subplots(1, 2, figsize = (8, 4), gridspec_kw=dict(width_ratios=[3, 5]))

#Generate frequency plot
plot=sns.countplot(y='Com_Name', data = df_jhb_bot10_today, palette = pal+"_r", order=pd.value_counts(df_jhb_bot10_today['Com_Name']).iloc[:10].index, ax=axs[0])
plot.set_yticklabels(['\n'.join(textwrap.wrap(ticklabel.get_text(),17)) for ticklabel in plot.get_yticklabels()])
plot.set(ylabel=None)
plot.set(xlabel="no. of detections")
#Generate crosstab matrix for heatmap plot
heat = pd.crosstab(df_jhb_bot10_today['Com_Name'],df_jhb_bot10_today['Hour of Day'])

#Order heatmap Birds by frequency of occurrance
heat.index = pd.CategoricalIndex(heat.index, categories = pd.value_counts(df_jhb_bot10_today['Com_Name']).iloc[:10].index)
heat.sort_index(level=0, inplace=True)
heat_frame = pd.DataFrame(data=0, index=heat.index, columns = hours_in_day)
heat=(heat+heat_frame).fillna(0)

#Generate heatmap plot
plot = sns.heatmap(heat, norm=LogNorm(), annot=True, annot_kws={"fontsize":7}, cmap = pal , square = False, cbar=False, linewidths = 0.5, linecolor = "Grey", ax=axs[1], yticklabels = False)

# Set heatmap border
for _, spine in plot.spines.items():
    spine.set_visible(True)
plot.set(ylabel=None)

#Set combined plot layout and titles
plt.tight_layout()
f.subplots_adjust(top=0.9)
plt.suptitle("Bottom 10 Detected: "+ str(now.strftime("%d-%h-%Y %H:%M")))
plot.set(xlabel="Hour of Day")
#Save combined plot
savename='/home/pi/BirdSongs/Extracted/Combo2-'+str(now.strftime("%d-%m-%Y"))+'.png'
plt.savefig(savename)

plt.close()
