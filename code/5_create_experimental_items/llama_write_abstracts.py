# rapid development of a script to generate scientific abstracts using Llama model
# written with AI assistance
# fill in parameters in lines 141-145
from transformers import AutoTokenizer, AutoModelForCausalLM
import torch
import time
import numpy


class LlamaTextProcessor:
    """
    A class to handle text processing, model initialization, and text generation
    using the Llama model.
    """

    def __init__(self, model_name, hf_token):
        """
        Initialize the tokenizer and model, and configure device settings.

        Parameters:
            model_name (str): The name of the model to load.
            hf_token (str): Hugging Face API token for authentication.
        """
        self.device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
        print(f"Using {'GPU: ' + torch.cuda.get_device_name(0) if torch.cuda.is_available() else 'CPU'}")

        # Initialize tokenizer and model
        self.tokenizer = AutoTokenizer.from_pretrained(model_name, token=hf_token)
        self.model = AutoModelForCausalLM.from_pretrained(model_name, token=hf_token)

        # Ensure pad token is set
        if self.tokenizer.pad_token is None:
            if self.tokenizer.eos_token:
                self.tokenizer.pad_token = self.tokenizer.eos_token
            else:
                self.tokenizer.add_special_tokens({'pad_token': '[PAD]'})

        # Resize embeddings and move model to the appropriate device
        self.model.resize_token_embeddings(len(self.tokenizer))
        self.model.to(self.device)

        # Set padding token ID
        self.model.config.pad_token_id = self.tokenizer.pad_token_id

    def generate_response(self, input_text, temperature=0.6, max_tokens=256):
        """
        Generate a response for the given input text.

        Parameters:
            input_text (str): The input text prompt.
            temperature (float): Sampling temperature for generation.
            max_tokens (int): Maximum number of tokens to generate.

        Returns:
            str: Generated response text.
        """
        inputs = self.tokenizer(input_text, return_tensors="pt", padding=True)
        inputs = {k: v.to(self.device) for k, v in inputs.items()}

        with torch.no_grad():
            outputs = self.model.generate(
                inputs["input_ids"],
                attention_mask=inputs["attention_mask"],
                max_length=max_tokens,
                temperature=temperature,
                top_p=0.9,
                do_sample=True,
                pad_token_id=self.tokenizer.pad_token_id
            )

        return self.tokenizer.decode(outputs[0], skip_special_tokens=True)


import time

import time

def write_abstracts(input_path, output_path, temperature, model_processor):
    """
    Process abstracts from the input file, generate continuations using the model,
    and write results to the output file.

    Parameters:
        input_path (str): Path to the input file containing abstracts.
        output_path (str): Path to the output file for results.
        temperature (float): Sampling temperature for the model.
        model_processor (LlamaTextProcessor): Instance of the LlamaTextProcessor class.
        max_retries (int): Maximum number of retries for generating a single response.
    """
    processed_count = 0
    max_retries = 5
    start_time = time.time()

    with open(input_path, "r") as input_file, open(output_path, "w") as output_file:
        for line_of_keywords in input_file:
            try:
                # Preprocess abstract
                line_of_keywords = line_of_keywords.strip()

                if processed_count % 100 == 0:
                    print(f"Processed {processed_count} abstracts", flush=True)

                prompt = f"Based on the following keywords, write a 100-word abstract for a scientific journal article: {line_of_keywords}. Reply with the abstract only."
                max_tokens = 400

                variants = []

                # Generate 100 variants for the given line
                for _ in range(500):
                    processed_count += 1
                    retries = 0
                    success = False
                    while retries < max_retries and not success:
                        try:
                            ai_response = model_processor.generate_response(prompt, temperature=temperature, max_tokens=max_tokens)
                            ai_response = " ".join(ai_response.split())
                            ai_no_prompt = ai_response.replace(prompt, "", 1).strip()
                            variants.append(ai_no_prompt)
                            success = True  # Exit retry loop if successful
                        except Exception as e:
                            retries += 1
                            if retries < max_retries:
                                print(f"Retrying ({retries}/{max_retries}) due to error: {e}")
                            else:
                                print(f"Failed to generate variants for item beginning with {line_of_keywords[:10] }after {max_retries} retries. Skipping.")
                                variants.append("[Error generating response]")  # Placeholder for failed attempts

            except Exception as e:
                print(f"Error processing abstract: {e}")
                continue

            output_file.write("\t".join(variants) + "\n")

    end_time = time.time()
    print(f"Processing complete. Total abstracts processed: {processed_count}")
    print(f"Time taken: {end_time - start_time:.2f} seconds")


if __name__ == "__main__":
    # Constants for model and file paths
    HF_TOKEN = "hf_..." # Hugging Face token
    MODEL_NAME = "meta-llama/Llama-3.2-3B-Instruct" # Llama-3.2-3B-Instruct
    INPUT_FILE_PATH =  "../data/experimental_items/50_sample_2020_ai_keywords.txt"
    OUTPUT_FILE_PATH = "../data/experimental_items/50_sample_2020_ai_abstracts.tsv"
    TEMPERATURE = 0.9

    # Initialize model processor
    model_processor = LlamaTextProcessor(MODEL_NAME, HF_TOKEN)

    # Process abstracts
    write_abstracts(INPUT_FILE_PATH, OUTPUT_FILE_PATH, TEMPERATURE, model_processor)
