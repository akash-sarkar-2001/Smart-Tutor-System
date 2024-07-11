import os
import time
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler
import PyPDF2
import pandas as pd

def extract_text_from_pdf(pdf_path):
    try:
        with open(pdf_path, 'rb') as file:
            pdf_reader = PyPDF2.PdfReader(file)
            text_data = []

            for page_num in range(len(pdf_reader.pages)):
                page = pdf_reader.pages[page_num]
                text_data.append(page.extract_text())

            return text_data
    except FileNotFoundError:
        print("Error: PDF file not found.")
        return []
    except PyPDF2.errors.PdfReadError:
        print("Error: Could not read the PDF file.")
        return []

def preprocess_text(text):
    if text:
        processed_text = text.lower().strip()
        return processed_text
    return ''

def save_text_to_csv(text_data, csv_path):
    try:
        df = pd.DataFrame({'text': text_data})
        df.to_csv(csv_path, index=False)
        print("CSV file saved successfully at:", csv_path)
    except Exception as e:
        print("Error occurred while saving CSV file:", e)

def process_pdf_and_save_to_csv(pdf_path, csv_path):
    text_data = extract_text_from_pdf(pdf_path)
    
    if not text_data:
        print("No text data extracted from the PDF.")
        return

    processed_text_data = [preprocess_text(text) for text in text_data]

    save_text_to_csv(processed_text_data, csv_path)

class PDFHandler(FileSystemEventHandler):
    def __init__(self, csv_dir):
        self.csv_dir = csv_dir

    def on_created(self, event):
        if not event.is_directory and event.src_path.endswith('.pdf'):
            pdf_path = event.src_path
            file_name = os.path.splitext(os.path.basename(pdf_path))[0]
            csv_path = os.path.join(self.csv_dir, f"{file_name}.csv")
            print(f"New PDF detected: {pdf_path}")
            process_pdf_and_save_to_csv(pdf_path, csv_path)

def main(pdf_dir, csv_dir):
    event_handler = PDFHandler(csv_dir)
    observer = Observer()
    observer.schedule(event_handler, path=pdf_dir, recursive=False)
    observer.start()
    print(f"Watching directory: {pdf_dir} for new PDF files...")

    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        observer.stop()
    observer.join()

# Example usage
pdf_dir = r'./Notes/common'  # Directory to watch for new PDF files
csv_dir = r'./Notes/extrated-notes-csvs'  # Directory to save the CSV files

if not os.path.exists(pdf_dir):
    os.makedirs(pdf_dir)
if not os.path.exists(csv_dir):
    os.makedirs(csv_dir)

main(pdf_dir, csv_dir)
