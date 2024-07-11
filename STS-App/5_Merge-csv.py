import pandas as pd
import os
import glob
import time

# List of CSV files to be merged
csv_files = ['register_data.csv', './csv_files/students.csv']  # Replace with your actual file names

# Directory containing the latest CSV file
extracted_test_csv_dir = './extracted_test_csv'

# Directories for response and wrong response text files
response_sheet_dir = './response_sheet'
wrong_response_dir = './wrong_response'

# Output CSV file
output_file = 'merged_output.csv'

# Function to get the latest CSV file in a directory
def get_latest_csv(directory):
    csv_list = glob.glob(os.path.join(directory, '*.csv'))
    if not csv_list:
        print(f"No CSV files found in directory: {directory}")
        return None
    latest_csv = max(csv_list, key=os.path.getctime)
    return latest_csv

# Function to read the content of a text file
def read_text_file(file_path):
    try:
        with open(file_path, 'r') as file:
            return file.read().strip()
    except FileNotFoundError:
        print(f"File not found: {file_path}")
        return ''
    except Exception as e:
        print(f"Error reading file {file_path}: {e}")
        return ''

# Function to merge CSV files based on rollno
def merge_csv_files_on_rollno(csv_files, output_file):
    # Get the latest CSV file from the specified directory
    latest_csv = get_latest_csv(extracted_test_csv_dir)
    if not latest_csv:
        print(f"Latest CSV file not found in directory: {extracted_test_csv_dir}")
        return
    
    print(f"Using latest CSV file: {latest_csv}")
    
    # Initialize an empty dataframe for merging
    merged_df = pd.DataFrame()

    # Iterate over each CSV file
    for file in csv_files:
        # Check if the file exists
        if not os.path.isfile(file):
            print(f"File not found: {file}")
            continue

        # Read the CSV file into a dataframe
        try:
            df = pd.read_csv(file)
            print(f"File {file} read successfully.")
        except Exception as e:
            print(f"Error reading {file}: {e}")
            continue
        
        # Check if the 'rollno' column exists
        if 'rollno' not in df.columns:
            print(f"'rollno' column not found in {file}. Skipping this file.")
            continue

        # Merge the current dataframe with the merged dataframe based on 'rollno'
        if merged_df.empty:
            merged_df = df
        else:
            try:
                merged_df = pd.merge(merged_df, df, on='rollno', how='outer')
                print(f"File {file} merged successfully.")
            except Exception as e:
                print(f"Error merging {file}: {e}")
                continue
    
    # Read the latest CSV file
    try:
        latest_df = pd.read_csv(latest_csv)
        print(f"Latest CSV file {latest_csv} read successfully.")
        # Combine all data into a single string 'extracted_data'
        latest_csv_content = ' '.join(latest_df.astype(str).agg(' '.join, axis=1))
    except Exception as e:
        print(f"Error reading latest CSV file {latest_csv}: {e}")
        latest_csv_content = ''

    # If latest_csv_content is not empty, add its content to every row in merged_df
    if latest_csv_content:
        merged_df['extracted_data'] = latest_csv_content

    # Add response and wrong_response columns
    merged_df['response'] = merged_df['rollno'].apply(lambda x: read_text_file(os.path.join(response_sheet_dir, f"{x}.txt")))
    merged_df['wrong_response'] = merged_df['rollno'].apply(lambda x: read_text_file(os.path.join(wrong_response_dir, f"{x}.txt")))

    # Read the existing output CSV file if it exists
    if os.path.isfile(output_file):
        try:
            existing_df = pd.read_csv(output_file)
            print(f"Existing output file {output_file} read successfully.")
        except Exception as e:
            print(f"Error reading existing output file {output_file}: {e}")
            existing_df = pd.DataFrame()
    else:
        existing_df = pd.DataFrame()

    # Combine existing data with new data
    combined_df = pd.concat([existing_df, merged_df]).drop_duplicates(subset='rollno', keep='last').reset_index(drop=True)

    # Check if there is any data to write
    if combined_df.empty:
        print("No new data to write to the output file.")
        return

    # Write the merged dataframe to the output CSV file
    try:
        combined_df.to_csv(output_file, index=False)
        print(f"New data has been written to {output_file}")
    except Exception as e:
        print(f"Error writing to {output_file}: {e}")

# Run the merge process in an infinite loop with a delay between iterations
while True:
    merge_csv_files_on_rollno(csv_files, output_file)
    time.sleep(0)  # Adjust the sleep time as needed (e.g., 60 seconds)
