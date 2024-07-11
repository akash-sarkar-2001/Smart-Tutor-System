import mysql.connector
import os
import re

# Database configuration
servername = "localhost"
username = "root"
password = ""
dbname = "sts"

# Create connection
conn = mysql.connector.connect(
    host=servername,
    user=username,
    password=password,
    database=dbname
)

# Check connection
if conn.is_connected():
    print("Connected to the database")
else:
    print("Connection failed")

# Create cursor
cursor = conn.cursor()

# Fetch distinct subjects from sub_list table
cursor.execute("SELECT DISTINCT subject_ FROM sub_list")
subjectResult = cursor.fetchall()

if subjectResult:
    # Create Notes directory if it doesn't exist
    notesDir = './Notes'
    if not os.path.isdir(notesDir):
        os.mkdir(notesDir)

    # Iterate through each subject and create a directory for it
    for row in subjectResult:
        subject = row[0]
        # Sanitize the subject name to be used as a directory name, preserving & character
        sanitizedSubject = re.sub(r'[^a-zA-Z0-9_\s&-]', '_', subject)  # Sanitize the subject name
        subjectDir = os.path.join(notesDir, sanitizedSubject)

        if not os.path.isdir(subjectDir):
            os.makedirs(subjectDir)
            print("Directory created for subject:", subject)
        else:
            print("Directory already exists for subject:", subject)
else:
    print("No subjects found in the sub_list table.")

# Close cursor and connection
cursor.close()
conn.close()
