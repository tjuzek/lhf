"""
A module for processing PubMed text files by converting text to lowercase and removing punctuation.

Usage:
    python3 process_pubmed_files.py --input-dir <input_dir> --output-dir <output_dir>
"""
# Code has been refactored using Copilot and ChatGPT
# Configure logging
# python3 process_pubmed_files.py --input-dir .../data/pubmed_non_processed/years/2020.txt --output-dir ../data/pubmed_processed/years/2020.txt
# for multiple files, run this with a bash loop
import os
import string
import argparse
import logging


logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

def create_directory(directory):
    """Ensure the directory exists."""
    os.makedirs(directory, exist_ok=True)
    logging.info(f"Directory {directory} is ready.")

def process_file(input_path, output_path):
    """
    Process a single file to convert all text to lowercase and remove punctuation.
    """
    with open(input_path, 'r') as input_file, open(output_path, 'w') as output_file:
        for line in input_file:
            line = line.strip().lower()
            line = line.translate(str.maketrans('', '', string.punctuation))
            output_file.write(line + "\n")

def process_directory(input_dir, output_dir):
    """Process all text files in the specified directory."""
    create_directory(output_dir)
    for filename in os.listdir(input_dir):
        if filename.endswith('.txt'):
            input_path = os.path.join(input_dir, filename)
            output_path = os.path.join(output_dir, filename)
            try:
                process_file(input_path, output_path)
                logging.info(f"Processed {filename}")
            except Exception as e:
                logging.error(f"Error processing {filename}: {e}")

def main():
    """Run the script with command-line arguments."""
    parser = argparse.ArgumentParser(description="Process PubMed text files.")
    parser.add_argument('--input-dir', type=str, required=True, help="Input directory containing text files.")
    parser.add_argument('--output-dir', type=str, required=True, help="Output directory for processed files.")
    args = parser.parse_args()

    process_directory(args.input_dir, args.output_dir)


if __name__ == "__main__":
    main()
