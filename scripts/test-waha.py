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
        return resp.status, json.loads(resp.read().decode())
    except urllib.error.HTTPError as e:
        return e.code, json.loads(e.read().decode())
    except Exception as e:
        return 0, {'error': str(e)}

print(f'\n=== Testing WAHA API ===')
print(f'Session name: {SESSION}')

# List sessions first
print(f'\n0. All sessions before...')
status, data = api('GET', '/api/sessions')
print(f'   Status: {status}')
print(f'   Response: {json.dumps(data, indent=2)[:500]}')

# Just try to start directly (WAHA Core should auto-create "default")
print(f'\n1. Trying to start session directly (no create)...')
status, data = api('POST', f'/api/sessions/{SESSION}/start', {})
print(f'   Status: {status}')
print(f'   Response: {json.dumps(data, indent=2)[:500]}')

time.sleep(8)

# Check status
print(f'\n2. Status after start...')
status, data = api('GET', f'/api/sessions/{SESSION}')
print(f'   Status: {status}')
print(f'   Response: {json.dumps(data, indent=2)[:500]}')

# Try again with restart
print(f'\n3. Trying restart...')
status, data = api('POST', f'/api/sessions/{SESSION}/restart', {})
print(f'   Status: {status}')
print(f'   Response: {json.dumps(data, indent=2)[:500]}')

time.sleep(8)

print(f'\n4. Status after restart...')
status, data = api('GET', f'/api/sessions/{SESSION}')
print(f'   Status: {status}')
print(f'   Response: {json.dumps(data, indent=2)[:500]}')
