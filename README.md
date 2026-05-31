# Word Overuse and Alignment in Large Language Models: The Influence of Learning from Human Feedback

This repository contains code and data for our paper: **"Word Overuse and Alignment in Large Language Models: The Influence of Learning from Human Feedback"**

## Overview
Large Language Models (LLMs) are known to overuse certain words, such as *delve* and *intricate*. This project investigates whether Learning from Human Feedback (LHF) contributes to this phenomenon. We introduce a method for identifying potentially LHF-induced lexical preferences and critically, we conduct an experimental study to test our hypothesis. Our experimental findings are consistent with the hypothesis that Learning from Human Feedback influences the lexical choices of Large Language Models.

## Citation

If you use this code or data, a citation is appreciated (though not required; see the licence).

The version of record is the Springer CCIS chapter (2026); an open preprint is available at arXiv:2508.01930.

```bibtex
@inbook{juzek-ward-2026-word,
  title     = {Word Overuse and Alignment in Large Language Models: The Influence of Learning from Human Feedback},
  author    = {Juzek, Thomas Stephan and Ward, Zina B.},
  booktitle = {Machine Learning and Principles and Practice of Knowledge Discovery in Databases},
  publisher = {Springer Nature Switzerland},
  year      = {2026},
  pages     = {243--259},
  doi       = {10.1007/978-3-032-19096-3_16}
}
```

## Contents
- **Paper:** The paper can be found under [bias2025_v_2_0_0.pdf](https://github.com/tjuzek/lhf/blob/main/bias2025_v_2_0_0.pdf); some of the procedures are explained in more detail in the paper, and if this is the case, pointers are given. Background, methodology, results, and conclusions are discussed in detail the paper.
- **Code:** These are the scripts used for our work. pipeline.md will talk you through the code step by step. 
- **Data:** The data analysed in the paper, most importantly the raw data of the experiment.

## Licence

- **Code** (`code/`): MIT No Attribution (MIT-0). See [`LICENSE`](LICENSE). Use it freely, no attribution required.
- **Data** (`data/`, `appendices/`): CC0 1.0 Universal (public domain dedication). See [`LICENSE-DATA`](LICENSE-DATA).

External datasets and third-party resources retain their original licences.


### Contact

Our websites have our contact details:

- [Tom Juzek](https://mll.fsu.edu/person/tom-juzek)  
- [Zina Ward](https://zinabward.com/)

The included paper PDF is the author/workshop version (version of record © Springer, CCIS 2026), separate from the code and data licences above.

## AI Assistance

Some of the code was written with the assistance of GitHub Copilot (marked as such in the code). Repository polished with Claude Code.
