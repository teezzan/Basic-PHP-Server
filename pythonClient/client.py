import requests
import threading
import json

globToken = ''
globRefreshKey = ''
device_id='Test_HARDWARE'
url='http://localhost:8000'


def getToken(device_id):
    global globToken
    global globRefreshKey
    response = requests.post(
        url+'/generate_key', data={'device_id': device_id})
    val = json.loads(response.text)
    globToken = val["access_token"]
    globRefreshKey = val["refresh_token"]


def refreshToken(token):
    global globToken
    global globRefreshKey
    response = requests.post(
        url+'/refresh_key',
        headers={'Authorization': 'Bearer ' + token})
    print(response.text)
    val = json.loads(response.text)
    print('\nNew Token Fetched ')
    globToken = val["access_token"]
    globRefreshKey = val["refresh_token"]


def sendData(payload, token):
    print('\n Posting Data: ' + str(payload))
    response = requests.post(url+'/data', data=payload,
                             headers={'Authorization': 'Bearer ' + token})
    print(response.text)


def sendDataTimer(interval):
    threading.Timer(interval, sendDataTimer, args=(interval,)).start()
    sendData({'amount': 1234, 'currency':'BTC'}, globToken)


def refreshTokenTimer(interval):
    threading.Timer(interval, refreshTokenTimer, args=(interval,)).start()
    print("\n RefreshToken Job Begins!!!")
    refreshToken(globRefreshKey)

#fetching initial Token for Authentication
getToken(device_id)
#Starting Refresh Token faux_CronJob (runs every 15 seconds)
refreshTokenTimer(15)
#Starting Refresh Token faux_CronJob (runs every 4 seconds)
sendDataTimer(4)
