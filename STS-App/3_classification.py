import pandas as pd
import time

while True:
    # Load the CSV file
    df = pd.read_csv('./csv_files/students.csv')

    # Classify students into three categories based on their marks
    def classify_students(row):
        obtained_marks = row['obtained_marks']
        total_marks = row['total_marks']
        percentage = (obtained_marks / total_marks) * 100
        if percentage >= 80:
            return 'Excellent'
        elif percentage >= 60:
            return 'Good'
        elif percentage >= 45:
            return 'Average'
        else:
            return 'Below Average'

    # Apply the classification function to create a new column 'category'
    df['category'] = df.apply(classify_students, axis=1)

    # Save the result to a new CSV file
    df.to_csv('./csv_files/classified_students.csv', index=False)

    print("Classification complete. Results saved to 'classified_students.csv'")
    
    # Optional: Add a delay between iterations to prevent overwhelming the system
    time.sleep(0)  # Adjust the sleep time as needed (e.g., 60 seconds)

