#!/usr/bin/env python3
"""Stop and restart WAHA session to generate QR code"""
import json
import urllib.request
import time
import base64
import sys

API_KEY = 'diegospizza-waha-2026'
WAHA_URL = 'http://localhost:3000'
SESSION = 'default'


def api(method, path, body=None):
    data = json.dumps(body).encode('utf-8') if body else None
    req = urllib.request.Request(
        f'{WAHA_URL}{path}',
        data=data,
        headers={'X-Api-Key': API_KEY, 'Content-Type': 'application/json'} if body else {'X-Api-Key': API_KEY},
        method=method
    )
    try:
        resp = urllib.request.urlopen(req)
        raw = resp.read().decode()
        try:
            return resp.status, json.loads(raw) if raw else {}
        except:
            return resp.status, {'raw': raw[:200]}
    except urllib.error.HTTPError as e:
        raw = e.read().decode()
        try:
            return e.code, json.loads(raw) if raw else {}
        except:
            return e.code, {'raw': raw[:200]}
    except Exception as e:
        return 0, {'error': str(e)}


def raw_api(method, path, body=None):
    data = json.dumps(body).encode('utf-8') if body else None
    req = urllib.request.Request(
        f'{WAHA_URL}{path}',
        data=data,
        headers={'X-Api-Key': API_KEY},
        method=method
    )
    try:
        resp = urllib.request.urlopen(req)
        return resp.status, resp.read(), resp.headers
    except urllib.error.HTTPError as e:
        return e.code, e.read(), e.headers


# Check current status
status, data = api('GET', f'/api/sessions/{SESSION}')
current = data.get('status', 'UNKNOWN')
print(f'Current status: {current}')

if current == 'SCAN_QR_CODE':
    # QR already available, just fetch it
    print('QR already available. Fetching...')
    st, body, headers = raw_api('GET', f'/api/{SESSION}/auth/qr')
    ct = headers.get('Content-Type', '')
    if st == 200 and ct.startswith('image'):
        b64data = base64.b64encode(body).decode()
                        print(f'QR: data:image/png;base64,{b64data[:50]}...')
                        print('QR READY - scan from /admin/chat-bot')
        sys.exit(0)
    else:
        print(f'QR fetch failed: {st} {body.decode()[:200]}')
        sys.exit(1)

if current in ('WORKING', 'CONNECTED'):
    print('Session already connected! No QR needed.')
    sys.exit(0)

# Stop session first (if it exists)
print('Stopping session...')
api('POST', f'/api/sessions/{SESSION}/stop', {})
time.sleep(2)

# Start session to generate QR
print('Starting session to generate QR...')
status, data = api('POST', f'/api/sessions/{SESSION}/start', {})
print(f'Start result: {status}')
time.sleep(5)

# Check status
status, data = api('GET', f'/api/sessions/{SESSION}')
new_status = data.get('status', 'UNKNOWN')
print(f'New status: {new_status}')

# If it went to WORKING instead of SCAN_QR_CODE (no auth needed),
# it means session is already authenticated
if new_status == 'WORKING':
    print('Session went to WORKING (already connected)')
    sys.exit(0)

if new_status == 'SCAN_QR_CODE':
    print('QR CODE GENERATED - scan from /admin/chat-bot')
    # Fetch and display QR
    st, body, headers = raw_api('GET', f'/api/{SESSION}/auth/qr')
    ct = headers.get('Content-Type', '')
    if st == 200 and ct.startswith('image'):
        b64data = base64.b64encode(body).decode()
    print(f'QR: data:image/png;base64,{b64data[:50]}...')
    sys.exit(0)

print(f'Unexpected status: {new_status}')
sys.exit(1)
