#!/usr/bin/env python3
"""Test script for WAHA API - create, start and check session"""
import json
import urllib.request
import time
import sys
import os

API_KEY = os.environ.get('WAHA_API_KEY', 'diegospizza-waha-2026')
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

print(f'\n=== Testing WAHA API ===')
print(f'Session name: {SESSION}')

# 1. Try creating session
print(f'\n1. Creating session "default"...')
status, data = api('POST', '/api/sessions', {'name': SESSION})
print(f'   Status: {status}')
print(f'   Response: {json.dumps(data, indent=2)[:500]}')
time.sleep(2)

# 2. List sessions after create
print(f'\n2. All sessions after create...')
status, data = api('GET', '/api/sessions')
print(f'   Status: {status}')
print(f'   Response: {json.dumps(data, indent=2)[:500]}')

# 3. Start
print(f'\n3. Starting session...')
status, data = api('POST', f'/api/sessions/{SESSION}/start', {})
print(f'   Status: {status}')
print(f'   Response: {json.dumps(data, indent=2)[:500]}')
time.sleep(8)

# 4. Status
print(f'\n4. Status after start...')
status, data = api('GET', f'/api/sessions/{SESSION}')
print(f'   Status: {status}')
print(f'   Response: {json.dumps(data, indent=2)[:500]}')
