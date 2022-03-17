#!/home/pi/BirdNET-Pi/birdnet/bin/python3
import streamlit as st
import pandas as pd
import numpy as np
import plotly.graph_objects as go
from plotly.subplots import make_subplots
from datetime import timedelta, datetime
import sqlite3
from sqlite3 import Connection
from pathlib import Path

URI_SQLITE_DB = "/home/pi/BirdNET-Pi/scripts/birds.db"


@st.cache(hash_funcs={Connection: id})
def get_connection(path: str):
    """Put the connection in cache to reuse if path does not change between Streamlit reruns.
    NB : https://stackoverflow.com/questions/48218065/programmingerror-sqlite-objects-created-in-a-thread-can-only-be-used-in-that-sa
    """
    return sqlite3.connect(path, check_same_thread=False)


def get_data(conn: Connection):
    df1 = pd.read_sql("SELECT * FROM detections", con=conn)
    return df1

conn = get_connection(URI_SQLITE_DB)

#@st.cache()
#def load_data():
#    df1 = pd.read_csv('/home/pi/BirdNET-Pi/BirdDB.txt', sep=';')
#    return df1


# Read in the cereal data
#df = load_data()
df = get_data(conn)
df2=df.copy()
df2['DateTime']=pd.to_datetime(df2['Date'] + " " + df2['Time'])
df2=df2.set_index('DateTime')

# Filter on date range
# Date as calendars
#Start_Date1 = pd.to_datetime(st.sidebar.date_input('Which date do you want to start?', value = df2.index.min()))
#End_Date1 =   pd.to_datetime(st.sidebar.date_input('Which date do you want to end?',  value = df2.index.max()))
# Date as slider
Start_Date = pd.to_datetime(df2.index.min())
End_Date =   pd.to_datetime(df2.index.max())
Date_Slider = st.sidebar.slider('Date Range',
                                value=(Start_Date.to_pydatetime(),
                                       End_Date.to_pydatetime())                                       
                                )
filt = (df2.index >= Date_Slider[0]) &  (df2.index <= Date_Slider[1]+timedelta(days=1))
df2 = df2[filt]

#Create species count for selected date range

Specie_Count=df2['Com_Name'].value_counts()

#Create species treemap

# Create Hourly Crosstab


hourly=pd.crosstab(df2['Com_Name'],df2.index.hour, dropna=False)

# Filter on species
species = list(hourly.index)

top_N = st.sidebar.select_slider(
    'Select Number of Birds to Show',
    list(range(1,len(Specie_Count))),
    value=min(10,len(Specie_Count)-1))

top_N_species = (df2['Com_Name'].value_counts()[:top_N])


specie = st.sidebar.selectbox('Which bird would you like to explore?', species, index=species.index(list(top_N_species.index)[0]))

font_size=15


#specie filter
filt=df2['Com_Name']==specie

df_counts=df2[filt].resample('D').count()

fig = make_subplots(
                    rows=2, cols =2,
                    specs= [[{"type":"xy","rowspan":2}, {"type":"polar"}], [None, {"type":"xy"}]],
                    subplot_titles=('<b style="font-size:x-large;">Species in Date Range</b>',
                                    '<b style="font-size:large;">'+specie+'</b><br>'
                                    '<span style="font-size:medium;">Total Detections:'+str('{:,}'.format(sum(df_counts.Time)))+'<br>'
                                    'Max Confidence:'+str('{:.2f}%'.format(max(df2[df2['Com_Name']==specie]['Confidence'])*100))+'<br>'
                                    'Median Confidence:'+str('{:.2f}%'.format(np.median(df2[df2['Com_Name']==specie]['Confidence'])*100))+'</span>'
                                    
                                    )
                    )
# fig.layout.height=900
# fig.layout.width=1500

#Plot seen species for selected date range and number of species
fig.add_trace(go.Bar(y=top_N_species.index, x=top_N_species, orientation='h'), row=1,col=1)

fig.update_layout(
    margin=dict(l=0, r=50, t=50, b=0),
    yaxis={'categoryorder':'total ascending'})
# Set 360 degrees, 24 hours for polar plot
theta = np.linspace(0.0, 360, 24, endpoint=False)

d=pd.DataFrame(np.zeros((23,1))).squeeze()
radius = hourly.loc[specie]
radius=(d+radius).fillna(0)
fig.add_trace(go.Barpolar(r = radius, theta=theta), row=1, col=2)

fig.update_layout(
    autosize=True,
    width = 1000,
    height = 750,
    showlegend=False,
    polar = dict(
        radialaxis = dict(
            tickfont_size = font_size,
            showticklabels = False),
        angularaxis = dict(
            tickfont_size= font_size,
            rotation = -90,
            direction = 'clockwise',
            tickmode='array',
            tickvals=[0,45,90,135,180,225,270,315],
            ticktext=['12am','3am', '6am','9am','12pm','3pm', '6pm','9pm'],
            hoverformat = ""#"%{theta}: <br>Popularity: %{percent} </br> %{r}"
        ),
        ),
    )

fig.layout.annotations[1].update(x=0.775,y=0.4, font_size=25)

x=df_counts.index
y=df_counts['Com_Name']

fig.add_trace(go.Bar(x=df_counts.index,y=df_counts['Time']), row=2, col=2)

container=st.container()
container.plotly_chart(fig, use_container_width=True)
