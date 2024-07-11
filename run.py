import subprocess
import os
import sys

# List of Python files to be executed
python_files = [
    'download_register_table.py',
    'NOTES_PDF_READ.py',
    'TEST_PDF_READ.py',
    'classification.py',
    'Merge-csv.py',
    'final-merge-csv.py',
    'graph.py',
    'graph-admin.py'
]

def run_in_terminal(file):
    # Detect the operating system
    if sys.platform.startswith('linux') or sys.platform == 'darwin':  # Linux or macOS
        command = f"gnome-terminal -- bash -c 'python3 {file}; exec bash'"
    elif sys.platform == 'win32':  # Windows
        command = f'start cmd /K python {file}'
    else:
        raise Exception("Unsupported operating system")

    # Execute the command to open a new terminal and run the script
    subprocess.call(command, shell=True)

if __name__ == "__main__":
    # Change directory to the location of the script if needed
    os.chdir(os.path.dirname(os.path.abspath(__file__)))
    
    # Loop through each Python file and run it in a new terminal
    for py_file in python_files:
        run_in_terminal(py_file)
