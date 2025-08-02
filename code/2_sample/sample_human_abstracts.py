# simple rapid development script to sample 20k lines from a file
# adjust the file path and sample size as needed
import random

file_path = "../data/non_processed/2020.txt"
sample_size = 20000 #60k is ideal

with open(file_path, "r") as file:
    lines = file.readlines()
    random_sample = random.sample(lines, sample_size)

with open("../data/sample/human_abstracts_50_sample_2020.txt", "w") as file:
    file.writelines(random_sample)

