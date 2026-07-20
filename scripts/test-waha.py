#!/usr/bin/env python3
"""Test script for WAHA API - check session and fetch QR"""
import json
import urllib.request
import time
import sys
import os

API_KEY = os.environ.get('WAHA_API_KEY', 'diegospizza-waha-2026')
WAHA_URL = 'http://localhost:3000'
SESSION = 'default'

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

print(f'\n=== Testing WAHA API ===')
print(f'Session name: {SESSION}')

# Check session status
print(f'\n1. Session status...')
status, data = api('GET', f'/api/sessions/{SESSION}')
print(f'   Status: {status}')
if status == 200:
    print(f'   Session: {data.get("name")}, Status: {data.get("status")}')

# Try to fetch QR
print(f'\n2. Fetching QR code...')
status, raw, headers = raw_api('GET', f'/api/{SESSION}/auth/qr')
print(f'   HTTP Status: {status}')
print(f'   Content-Type: {headers.get("Content-Type")}')
if status == 200:
    ct = headers.get('Content-Type', '')
    body = raw
    if ct.startswith('image'):
        print(f'   QR IMAGE: {len(body)} bytes, data:image/png;base64,...')
    else:
        try:
            j = json.loads(body)
            print(f'   QR data: {json.dumps(j, indent=2)[:300]}')
        except:
            print(f'   Raw: {body[:200]}')
else:
    print(f'   Error body: {raw.decode()[:200]}')
