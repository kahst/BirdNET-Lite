import os
import streamlit as st
import pandas as pd
import numpy as np
import plotly.graph_objects as go
from plotly.subplots import make_subplots
from datetime import timedelta, datetime
from pathlib import Path
import sqlite3
from sqlite3 import Connection

userDir = os.path.expanduser('~')
URI_SQLITE_DB = userDir + '/BirdNET-Pi/scripts/birds.db'

st.set_page_config(layout='wide')

# Remove whitespace from the top of the page and sidebar
st.markdown("""
        <style>
               .css-18e3th9 {
                    padding-top: 2.5rem;
                    padding-bottom: 10rem;
                    padding-left: 5rem;
                    padding-right: 5rem;
                }
               .css-1d391kg {
                    padding-top: 3.5rem;
                    padding-right: 1rem;
                    padding-bottom: 3.5rem;
                    padding-left: 1rem;
                }
        </style>
        """, unsafe_allow_html=True)


@st.cache(hash_funcs={Connection: id})
def get_connection(path: str):
    return sqlite3.connect(path, check_same_thread=False)


def get_data(conn: Connection):
    df1 = pd.read_sql("SELECT * FROM detections", con=conn)
    return df1


conn = get_connection(URI_SQLITE_DB)
# Read in the cereal data
# df = load_data()
df = get_data(conn)
df2 = df.copy()
df2['DateTime'] = pd.to_datetime(df2['Date'] + " " + df2['Time'])
df2 = df2.set_index('DateTime')


# Filter on date range
# Date as calendars
# Start_Date = pd.to_datetime(st.sidebar.date_input('Which date do you want to start?', value = df2.index.min()))
# End_Date =   pd.to_datetime(st.sidebar.date_input('Which date do you want to end?',  value = df2.index.max()))

# Date as slider
Start_Date = pd.to_datetime(df2.index.min()).date()
End_Date = pd.to_datetime(df2.index.max()).date()
Date_Slider = st.slider('Date Range',
                        min_value=Start_Date - timedelta(days=1),
                        max_value=End_Date,
                        value=(Start_Date,
                               End_Date)
                        )


filt = (df2.index >= pd.Timestamp(Date_Slider[0])) & (df2.index <= pd.Timestamp(Date_Slider[1] + timedelta(days=1)))
df2 = df2[filt]

# Create species count for selected date range

Specie_Count = df2['Com_Name'].value_counts()

# Create species treemap

# Create Hourly Crosstab
hourly = pd.crosstab(df2['Com_Name'], df2.index.hour, dropna=False)

# Filter on species
species = list(hourly.index)

cols1, cols2 = st.columns((1, 1))
top_N = cols1.slider(
    'Select Number of Birds to Show',
    min_value=1,
    value=min(10, len(Specie_Count))
)

top_N_species = (df2['Com_Name'].value_counts()[:top_N])


specie = cols2.selectbox('Which bird would you like to explore for the dates ' + str(Date_Slider[0]) + ' to ' + str(Date_Slider[1]) + '?', species,
                         index=species.index(list(top_N_species.index)[0]))


font_size = 15


# specie filter
filt = df2['Com_Name'] == specie

df_counts = df2[filt].resample('D').count()

fig = make_subplots(
    rows=3, cols=2,
    specs=[[{"type": "xy", "rowspan": 3}, {"type": "polar", "rowspan": 2}], [
        {"rowspan": 1}, {"rowspan": 1}], [None, {"type": "xy", "rowspan": 1}]],
    subplot_titles=('<b>Top ' + str(top_N) + ' Species in Date Range ' + str(Date_Slider[0]) + ' to ' + str(Date_Slider[1]) + '</b>',
                    'Total Detect:' + str('{:,}'.format(sum(df_counts.Time))) +
                    '   Confidence Max:' + str('{:.2f}%'.format(max(df2[df2['Com_Name'] == specie]['Confidence']) * 100)) +
                    '   ' + '   Median:' +
                    str('{:.2f}%'.format(np.median(df2[df2['Com_Name'] == specie]['Confidence']) * 100))
                    )
)
fig.layout.annotations[1].update(x=0.7, y=0.25, font_size=15)

# Plot seen species for selected date range and number of species
fig.add_trace(go.Bar(y=top_N_species.index, x=top_N_species, orientation='h'), row=1, col=1)

fig.update_layout(
    margin=dict(l=0, r=0, t=50, b=0),
    yaxis={'categoryorder': 'total ascending'})
# Set 360 degrees, 24 hours for polar plot
theta = np.linspace(0.0, 360, 24, endpoint=False)

d = pd.DataFrame(np.zeros((23, 1))).squeeze()
detections = hourly.loc[specie]
detections = (d + detections).fillna(0)
fig.add_trace(go.Barpolar(r=detections, theta=theta), row=1, col=2)

fig.update_layout(
    autosize=False,
    width=1000,
    height=500,
    showlegend=False,
    polar=dict(
        radialaxis=dict(
            tickfont_size=font_size,
            showticklabels=True,
            hoverformat="#%{theta}: <br>Popularity: %{percent} </br> %{r}"
        ),
        angularaxis=dict(
            tickfont_size=font_size,
            rotation=-90,
            direction='clockwise',
            tickmode='array',
            tickvals=[0, 15, 35, 45, 60, 75, 90, 105, 120, 135, 150, 165,
                      180, 195, 210, 225, 240, 255, 270, 285, 300, 315, 330, 345],
            ticktext=['12am', '1am', '2am', '3am', '4am', '5am', '6am', '7am', '8am', '9am', '10am', '11am',
                      '12pm', '1pm', '2pm', '3pm', '4pm', '5pm', '6pm', '7pm', '8pm', '9pm', '10pm', '11pm'],
            hoverformat="#%{theta}: <br>Popularity: %{percent} </br> %{r}"
        ),
    ),
)


daily = pd.crosstab(df2['Com_Name'], df2.index.date, dropna=False)

fig.add_trace(go.Bar(x=daily.columns, y=daily.loc[specie]), row=3, col=2)

# container=st.container()
# config={'displayModelBar': False}
st.plotly_chart(fig, use_container_width=True)  # , config=config)

# cols3,cols4=st.columns((1,1))
#
# extract_date=Date_Slider
#
# audio_file = open('/home/*/BirdSongs/Extracted/By_Date/2022-03-22/Yellow-streaked_Greenbul/Yellow-streaked_Greenbul-77-2022-03-22-birdnet-15:04:28.mp3', 'rb')
# audio_bytes = audio_file.read()
# cols4.audio(audio_bytes, format='audio/mp3')
