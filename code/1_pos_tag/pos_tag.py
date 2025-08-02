"""
POS Tagging Pipeline

This script processes text files in a specified input directory, applies part-of-speech (POS) tagging using SpaCy,
and saves the tagged output to a specified output directory. It is designed for efficient batch processing of text data.

Refactored using ChatGPT. 

Requirements:
- Python 3.6+
- SpaCy library with the 'en_core_web_sm' model installed
"""
# make sure to adjust the file paths in lines 72 and 73
import spacy
import os

def load_spacy_model():
    """
    Load and return the SpaCy language model.

    Returns:
        nlp: A SpaCy language model.
    """
    return spacy.load("en_core_web_sm")

def pos_tag_sentence(sentence, nlp):
    """
    Perform part-of-speech tagging on a given sentence.

    Args:
        sentence (str): The input sentence to tag.
        nlp: A SpaCy language model.

    Returns:
        str: The sentence with tokens replaced by their lemma and POS tag.
    """
    doc = nlp(sentence)
    tagged_sentence = " ".join([f"{token.lemma_}_{token.pos_}" for token in doc])
    return tagged_sentence

def process_files(input_dir, output_dir, nlp):
    """
    Process all text files in the input directory, apply POS tagging, and save results.

    Args:
        input_dir (str): Path to the directory containing input text files.
        output_dir (str): Path to the directory where tagged files will be saved.
        nlp: A SpaCy language model.
    """
    if not os.path.exists(output_dir):
        os.makedirs(output_dir)

    for filename in os.listdir(input_dir):
        if filename.endswith(".txt"):
            output_file = os.path.join(output_dir, filename)
            if os.path.exists(output_file):
                print(f"Skipping {filename} as it already exists in the output directory.")
                continue

            print(f"Processing {filename}")
            input_file = os.path.join(input_dir, filename)

            with open(input_file, "r") as infile, open(output_file, "w") as outfile:
                for line in infile:
                    tagged_line = pos_tag_sentence(line.strip(), nlp)
                    outfile.write(tagged_line + "\n")

def main():
    """
    Main function to execute the POS tagging pipeline.
    """
    input_dir = "..data/pubmed_pos_tagged/years"
    output_dir = "../data/pubmed_pos_tagged"

    nlp = load_spacy_model()
    process_files(input_dir, output_dir, nlp)


if __name__ == "__main__":
    main()
