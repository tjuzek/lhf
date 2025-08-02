# rapid development script
# written with AI assistance
# check file paths in lines 8 and 9
import pandas as pd
import os

# Define file paths
input_file = '..data/experimental_items/50_sample_2020_ai_abstracts_annotated.tsv'
output_dir = '..data/experimental_items/separate_items/'

# Ensure the output directory exists
os.makedirs(output_dir, exist_ok=True)

# Define column names
columns = ['id', 'sequence', 'pos', 'len', 'score', 'perplexity']

# Read the TSV file without headers
df = pd.read_csv(input_file, sep='\t', header=None, names=columns)

c = 50
# Group by 'id' and save each group to a separate TSV file, only if there are more than five entries for any given id
for category_id, group in df.groupby('id'):
    if len(group) > 30:
        output_file = os.path.join(output_dir, f'{category_id}.tsv')
        group.to_csv(output_file, sep='\t', index=False, header=False)
        pass
    else:
        c -= 1

print(c)