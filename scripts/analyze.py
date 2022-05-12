import argparse
import socket

HEADER = 64
PORT = 5050
FORMAT = 'utf-8'
DISCONNECT_MESSAGE = "!DISCONNECT"
SERVER = "localhost"
ADDR = (SERVER, PORT)

client = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
client.connect(ADDR)


def send(msg):
    message = msg.encode(FORMAT)
    msg_length = len(message)
    send_length = str(msg_length).encode(FORMAT)
    send_length += b' ' * (HEADER - len(send_length))
    client.send(send_length)
    client.send(message)
    print(client.recv(2048).decode(FORMAT))


def main():

    global INCLUDE_LIST
    global EXCLUDE_LIST

    # Parse passed arguments
    parser = argparse.ArgumentParser()
    parser.add_argument('--i', help='Path to input file.')
    parser.add_argument(
        '--o',
        default='result.csv',
        help='Path to output file. Defaults to result.csv.')
    parser.add_argument(
        '--lat',
        type=float,
        default=-1,
        help='Recording location latitude. Set -1 to ignore.')
    parser.add_argument(
        '--lon',
        type=float,
        default=-1,
        help='Recording location longitude. Set -1 to ignore.')
    parser.add_argument(
        '--week',
        type=int,
        default=-1,
        help='Week of the year when the recording was made. Values in [1, 48] (4 weeks per month). Set -1 to ignore.')
    parser.add_argument(
        '--overlap',
        type=float,
        default=0.0,
        help='Overlap in seconds between extracted spectrograms. Values in [0.0, 2.9]. Defaults tp 0.0.')
    parser.add_argument(
        '--sensitivity',
        type=float,
        default=1.0,
        help='Detection sensitivity; Higher values result in higher sensitivity. Values in [0.5, 1.5]. Defaults to 1.0.')
    parser.add_argument(
        '--min_conf',
        type=float,
        default=0.1,
        help='Minimum confidence threshold. Values in [0.01, 0.99]. Defaults to 0.1.')
    parser.add_argument(
        '--include_list',
        default='null',
        help='Path to text file containing a list of included species. Not used if not provided.')
    parser.add_argument(
        '--exclude_list',
        default='null',
        help='Path to text file containing a list of excluded species. Not used if not provided.')
    parser.add_argument(
        '--birdweather_id',
        default='99999',
        help='Private Station ID for BirdWeather.')

    args = parser.parse_args()

    sockParams = ''
    if args.i:
        sockParams += 'i=' + args.i + '||'
    if args.o:
        sockParams += 'o=' + args.o + '||'
    if args.birdweather_id:
        sockParams += 'birdweather_id=' + args.birdweather_id + '||'
    if args.include_list:
        sockParams += 'include_list=' + args.include_list + '||'
    if args.exclude_list:
        sockParams += 'exclude_list=' + args.exclude_list + '||'
    if args.overlap:
        sockParams += 'overlap=' + str(args.overlap) + '||'
    if args.week:
        sockParams += 'week=' + str(args.week) + '||'
    if args.sensitivity:
        sockParams += 'sensitivity=' + str(args.sensitivity) + '||'
    if args.min_conf:
        sockParams += 'min_conf=' + str(args.min_conf) + '||'
    if args.lat:
        sockParams += 'lat=' + str(args.lat) + '||'
    if args.lon:
        sockParams += 'lon=' + str(args.lon) + '||'

    send(sockParams)

    send(DISCONNECT_MESSAGE)
    # time.sleep(3)

###############################################################################
###############################################################################


if __name__ == '__main__':

    main()

    # Example calls
    # python3 analyze.py --i 'example/XC558716 - Soundscape.mp3' --lat 35.4244 --lon -120.7463 --week 18
    # python3 analyze.py --i 'example/XC563936 - Soundscape.mp3' --lat 47.6766
    # --lon -122.294 --week 11 --overlap 1.5 --min_conf 0.25 --sensitivity
    # 1.25 --custom_list 'example/custom_species_list.txt'
