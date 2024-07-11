import pandas as pd
import time

while True:
    # Read the CSV file
    try:
        df = pd.read_csv("merged_output.csv")  # Replace with the actual file name
        print("CSV file read successfully.")
    except Exception as e:
        print(f"Error reading the CSV file: {e}")
        time.sleep(0)
        continue

    # Function to extract the wrong answer question based on wrong_response
    def extract_wrong_question(row):
        # Handle missing values in 'extracted_data' and 'wrong_response'
        extracted_data = row["extracted_data"] if pd.notna(row["extracted_data"]) else ""
        wrong_response = row["wrong_response"] if pd.notna(row["wrong_response"]) else ""
        
        # If wrong_response is empty, return an empty string
        if not wrong_response.strip():
            return ""
        
        # Split data into lines
        extracted_data_lines = extracted_data.split("\n")
        wrong_response_lines = wrong_response.split("\n")
        
        wrong_questions = []
        
        # Iterate through each wrong answer
        for wrong_answer in wrong_response_lines:
            wrong_answer_q_number = wrong_answer.split(":")[0].strip()
            
            # Find the corresponding question in extracted_data
            for question in extracted_data_lines:
                if question.startswith(f"{wrong_answer_q_number}"):
                    wrong_questions.append(question.strip())
                    break

        # Return joined wrong questions or empty string if none found
        return "\n".join(wrong_questions) if wrong_questions else ""

    # Check if the required columns exist
    if "extracted_data" in df.columns and "wrong_response" in df.columns:
        # Apply the function to create the new 'wrong_answered_question' column
        df["wrong_answered_question"] = df.apply(extract_wrong_question, axis=1)

        # Save the modified DataFrame to a new CSV file
        try:
            df.to_csv("merged_output_with_full_wrong_answers.csv", index=False)
            print("New CSV file 'merged_output_with_full_wrong_answers.csv' created with the 'wrong_answered_question' column!")
        except Exception as e:
            print(f"Error writing to the new CSV file: {e}")
    else:
        print("Required columns 'extracted_data' and 'wrong_response' not found in the DataFrame.")

    # Delay between iterations to prevent overwhelming the system
    time.sleep(0)  # Adjust the sleep time as needed (e.g., 60 seconds)
