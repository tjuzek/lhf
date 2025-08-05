# How Learning from Human Feedback Influences the Lexical Choices of Large Language Models

This repository contains code and data for our paper: **"How Learning from Human Feedback Influences the Lexical Choices of Large Language Models"**


## Overview
Large Language Models (LLMs) are known to overuse certain words, such as *delve* and *intricate*. This project investigates whether Learning from Human Feedback (LHF) contributes to this phenomenon. We introduce a method for identifying potentially LHF-induced lexical preferences and critically, we conduct an experimental study to test our hypothesis. Our experimental findings are consistent with the hypothesis that Learning from Human Feedback influences the lexical choices of Large Language Models.


## Contents
- **Paper:** The paper can be found under [bias2025_v_2_0_0.pdf](https://github.com/tjuzek/lhf/blob/main/bias2025_v_2_0_0.pdf); some of the procedures are explained in more detail in the paper, and if this is the case, pointers are given. Background, methodology, results, and conclusions are discussed in detail the paper.
- **Code:** These are the scripts used for our work. pipeline.md will talk you through the code step by step. 
- **Data:** The data analysed in the paper, most importantly the raw data of the experiment.


## Citation
The work is to appear in the Proceedings of the [BIAS 2025 Workshop](https://sites.google.com/view/bias-2025-ecmlpkdd/) at [ECML-PKKD](https://ecmlpkdd.org/); it is peer-reviewed and revisions are worked in. We will add an Arxiv link soon. The OSF citation is up already: [https://osf.io/jy48s/](https://osf.io/jy48s/).

<pre>@article{juzek2025word,
  title     = {Word Overuse and Alignment in Large Language Models: The Influence of Learning from Human Feedback},
  author    = {Tom S. Juzek and Zina B. Ward},
  journal   = {arXiv preprint arXiv:2508.01930},
  year      = {2025},
  note      = {Accepted for publication in the Proceedings of the 5th Workshop on Bias and Fairness in AI (BIAS 2025) at ECML PKDD},
  doi       = {10.48550/arXiv.2508.01930},
  url       = {https://doi.org/10.48550/arXiv.2508.01930},
  relatedDOI = {10.17605/OSF.IO/JY48S}
}</pre>



## Licence 

Our own code and data are licensed under a **CC BY-SA** licence. Some code was generated with the assistance of GitHub Copilot and is marked as such. External datasets and third-party resources retain their original licences.  


### Contact

Our websites have our contact details:

- [Tom Juzek](https://mll.fsu.edu/person/tom-juzek)  
- [Zina Ward](https://zinabward.com/)
