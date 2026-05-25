import requests
import json

session = requests.Session()
# Start session to get CSRF token
response = session.get("http://127.0.0.1:8000/login/")
print(f"GET login status: {response.status_code}")

csrf_token = session.cookies.get("csrftoken")
print(f"CSRF Token: {csrf_token}")

login_data = {
    "email": "maxi@gmail.com",
    "password": "LFcarpinter2025",
    "csrfmiddlewaretoken": csrf_token
}

headers = {
    "Referer": "http://127.0.0.1:8000/login/"
}

response = session.post("http://127.0.0.1:8000/login/", data=login_data, headers=headers, allow_redirects=False)
print(f"POST login status: {response.status_code}")
if response.status_code in [301, 302]:
    print(f"Redirects to: {response.headers.get('Location')}")
else:
    print("Login failed, no redirect. Check response text for errors.")
    if "Credenciales inválidas" in response.text:
       print("Error in HTML: Credenciales inválidas")
    else:
       print("Other error")
