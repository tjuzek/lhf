# Instructions for reproducing our results

**N.B.:** In the `.py` files, you often need to adjust file paths and add API tokens. This is marked at the beginning of the scripts.


## Step 1: Download and process PubMed data

To get started, in `0_get_human_data`, we need to download the PubMed dataset (`download_dataset.py`), extract the abstracts from it (`extract_abstracts.py`), and pre-process the abstracts (`process_pubmed_files.py`). POS-tag the corpus using `pos_tag.py` in `1_postag`.


## Step 2: Sample abstracts from 2020

Next, we sample `n` abstracts from 2020 using `sample_human_abstracts.py` in `2_sample`.


## Step 3: Generate AI-continuations

Let Llama continue abstracts using `llama_write_oop.py` in `3_llama_abstracts`. Clean the abstracts with `remove_bases_repetitions.py` and `gpt_clean_abstracts.py`. The base model just continues, which is fine, so `gpt_clean_abstracts.py` will not remove much from it. However, the instruct model drifts off, with meta comments and reflections, so `gpt_clean_abstracts.py` is necessary for the instruct output.

At this point, use the POS-tagging script again on the cleaned base vs instruct output. Once this is done, in `4_find_focal_words`, use `brute_force_div.py` to detect differences in (POS-tagged) word usage. Reviewing the list in `change_reversed.tsv` (found in `data/focal_words`), we can already see many potentially LHF-induced items. The file `buzzwords.ods` was counter-checked against human 2020 vs. 2024 usage, compared to base vs. instruct over-usage. This is the automated procedure to assist in identifying LHF-induced items.


## Step 4: Experimental validation of LHF connection

The next step in the paper is to generate experimental items to validate the LHF connection. We sampled 50 human abstracts from 2020. Notably, Llama is not good at rewriting passages but performs better when writing from relatively elaborate notes. So, our approach is as follows:

- Have GPT generate high-quality notes based on the human abstracts using `create_notes.py` in `5_experimental_items`.  
- Use `llama_writes_abstracts.py` to generate multiple abstract variants from the notes. The output file is too large for GitHub, but an example is available in `data/experimental_items/`.  
- Clean the Llama-generated abstracts using `gpt_clean_abstracts.py`, which produces `50_sample_2020_ai_abstracts_cleaned.tsv`.  
- Annotate the abstracts with `annotate_len_filter_abstracts.py`. This script performs two tasks:  
  1. POS-tags the Llama abstract variants.  
  2. Calculates the "buzziness" score for each abstract, across all variants.  

The "buzziness" score is based on `change_reversed.tsv`, as explained in the paper. For the experiment, we need abstract pairs: one variant with few buzzwords and another with many. Having multiple variants helps us find pairs with a strong contrast.  

To facilitate manual checking, we separate the items using `separate_items.py`, then apply `get_min_max.py` and select the 30 items with the highest min-max contrast. These are listed in `items.ods`.


## Step 5: Running the experiment

There are different ways to run the experiment. We used a custom-made, self-hosted website with a LAMP stack, so rerunning the experiment is non-trivial. Since details vary on one's implementation (which OS, which provider, etc.), we just give the code, which is in `6_website`.


## Step 6: Analysing experimental results

The experimental results are stored in `/data/experimental_results`, and the scripts in `/code/7_analysis` are used for analysis and plotting.  

Technically, we start with `filter_data.py`, which generates `ratings.tsv` (already provided in the repository).
