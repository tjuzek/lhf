# rapid development script, in parts AI-assisted, to remove repetitions from 
# the abstracts written by Llama
# check file paths in lines 23 and 24
import re

def remove_repetitions(text):
    # Split text into sentences or parts (adjust the delimiter based on your text structure)
    sentences = re.split(r'(?<!\w\.\w.)(?<![A-Z][a-z]\.)(?<=\.|\?)\s', text)
    
    # Use a set to track seen sentences
    seen = set()
    result = []

    for sentence in sentences:
        if sentence not in seen:
            seen.add(sentence)
            result.append(sentence)

    # Join sentences back into a single text
    cleaned_text = " ".join(result)
    return cleaned_text

input_file = open("../data/llama_abstracts/output_llama_instruct.tsv", "r")
output_file = open("../data/llama_abstracts/output_llama_instruct_repetitions_removed.tsv", "w")

for line in input_file:
    line = line.strip()
    line = line.split("\t")
    text = line[2]
    cleaned_text = remove_repetitions(text)
    line[2] = cleaned_text
    output_file.write("\t".join(line) + "\n")

input_file.close()
output_file.close()

