"""
A module to extract abstracts from PubMed XML files and save them grouped by publication year.

Usage:
    python extract_abstracts.py
"""
# adjust the file paths in line 20 and 21
import xml.etree.ElementTree as ET
import os
import re
import logging
from pathlib import Path


# Code has been refactored using Copilot and ChatGPT
# Setup logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

# Constants
DIRECTORY_PATH = Path("../data/pubmed_non_processed")
OUTPUT_DIR = Path("../data/pubmed_non_processed/years")
SPACE_RE = re.compile(r' +')


def process_pubmed_file(file_path):
    """
    Process a single PubMed XML file to extract and save abstracts.
    """
    try:
        tree = ET.parse(file_path)
        root = tree.getroot()
        abstracts = {}

        for pubmed_article in root.findall('.//PubmedArticle'):
            year_element = pubmed_article.find('.//PubDate/Year')
            abstract_text_elements = pubmed_article.findall('.//Abstract/AbstractText')
            
            if year_element is not None and abstract_text_elements:
                abstract_text = ' '.join(''.join(abstract.itertext()) for abstract in abstract_text_elements)
                abstract_text = abstract_text.replace("\n", " ").strip()
                abstract_text = SPACE_RE.sub(' ', abstract_text)

                year = year_element.text
                if year not in abstracts:
                    abstracts[year] = []
                abstracts[year].append(abstract_text)
    
        for year, abstract_list in abstracts.items():
            year_file = OUTPUT_DIR / f"{year}.txt"
            with open(year_file, 'a') as file:
                file.write('\n'.join(abstract_list) + '\n')
    except ET.ParseError as e:
        logging.error(f"XML parsing error in {file_path}: {e}")
    except Exception as e:
        logging.error(f"Error processing file {file_path}: {e}")

def main():
    OUTPUT_DIR.mkdir(parents=True, exist_ok=True)
    for filename in DIRECTORY_PATH.glob("*.xml"):
        logging.info(f"Processing {filename}...")
        process_pubmed_file(filename)


if __name__ == "__main__":
    main()
