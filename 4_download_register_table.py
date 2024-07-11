import pymysql
import csv
import time

# Database connection parameters
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',  # Replace with your MySQL root password
    'database': 'sts'
}

while True:
    # Connect to the database
    connection = pymysql.connect(**db_config)

    try:
        with connection.cursor() as cursor:
            # SQL query to fetch data from the register table
            query = "SELECT * FROM register"
            cursor.execute(query)
            
            # Fetch all the rows
            rows = cursor.fetchall()

            # Get column names from cursor description
            columns = [desc[0] for desc in cursor.description]

            # Write to CSV file
            with open('./register_data.csv', 'w', newline='') as csvfile:
                csvwriter = csv.writer(csvfile)
                # Write the header
                csvwriter.writerow(columns)
                # Write the data rows
                csvwriter.writerows(rows)
                
            print("Data has been written to register_data.csv")

    finally:
        # Close the database connection
        connection.close()
    
    # Optional: Add a delay between iterations to prevent overwhelming the system
    time.sleep(0)  # Adjust the sleep time as needed (e.g., 60 seconds)
