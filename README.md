# BirdNET-Lite
TFLite version of BirdNET. Bird sound recognition for more than 6,000 species worldwide.

Center for Conservation Bioacoustics, Cornell Lab of Ornithology, Cornell University

Go to https://birdnet.cornell.edu to learn more about the project.

Want to use BirdNET to analyze a large dataset? Don't hesitate to contact us: ccb-birdnet@cornell.edu

# Setup (Ubuntu 18.04)

TFLite for x86 platforms comes with the standard Tensorflow package. If you are on a different platform, you need to install a dedicated version of TFLite (e.g., a pre-compiled version for Raspberry Pi).

We need to setup TF2.3+ for BirdNET. First, we install Python 3 and pip:

```
sudo apt-get update
sudo apt-get install python3-dev python3-pip
sudo pip3 install --upgrade pip
```

Then, we can install Tensorflow with:

```
sudo pip3 install tensorflow
```

TFLite on x86 platform currently only supports CPUs. 

Note: Make sure to set `CUDA_VISIBLE_DEVICES=""` in your environment variables. Or set `os.environ['CUDA_VISIBLE_DEVICES'] = ''` at the top of your Python script.

In this example, we use Librosa to open audio files. Install Librosa with:

```
sudo pip3 install librosa
sudo apt-get install ffmpeg
```

You can use any other audio lib if you like, or pass raw audio signals to the model.

If you don't use Librosa, make sure to install NumPy:

```
sudo pip3 install numpy
```

Note: BirdNET expects 3-second chunks of raw audio data, sampled at 48 kHz.

# Usage

You can run BirdNET via the command line. You can add a few parameters that affect the output.

The input parameters include:

```
--i, Path to input file.
--o, Path to output file. Defaults to result.csv.
--lat, Recording location latitude. Set -1 to ignore.
--lon, Recording location longitude. Set -1 to ignore.
--week, Week of the year when the recording was made. Values in [1, 48] (4 weeks per month). Set -1 to ignore.
--overlap, Overlap in seconds between extracted spectrograms. Values in [0.0, 2.9]. Defaults tp 0.0.
--sensitivity, Detection sensitivity; Higher values result in higher sensitivity. Values in [0.5, 1.5]. Defaults to 1.0.
--min_conf, Minimum confidence threshold. Values in [0.01, 0.99]. Defaults to 0.1.
--custom_list, Path to text file containing a list of species. Not used if not provided.
```

Note: A custom species list needs to contain one species label per line. Take a look at the `model/label.txt` for the correct species label. Only labels from this text file are valid. You can find an example of a valid custom list in the 'example' folder.

Here are two example commands to run this BirdNET version:

```

python3 analyze.py --i 'example/XC558716 - Soundscape.mp3' --lat 35.4244 --lon -120.7463 --week 18

python3 analyze.py --i 'example/XC563936 - Soundscape.mp3' --lat 47.6766 --lon -122.294 --week 11 --overlap 1.5 --min_conf 0.25 --sensitivity 1.25 --custom_list 'example/custom_species_list.txt'

```

Note: Please make sure to provide lat, lon, and week. BirdNET will work without these values, but the results might be less reliable.

The results of the anlysis will be stored in a result file in CSV format. All confidence values are raw prediction scores and should be post-processed to eliminate occasional false-positive results.

# Contact us

Please don't hesitate to contact us if you have any issues with the code or if you have any other remarks or questions.

Our e-mail address: ccb-birdnet@cornell.edu

We are always open for a collaboration with you.

# Funding

This project is supported by Jake Holshuh (Cornell class of ’69). The Arthur Vining Davis Foundations also kindly support our efforts.

