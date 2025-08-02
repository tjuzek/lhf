# rapid development script to write abstracts with Llama
# refactored with Copilot
# fill in the Huggingface API token in line 137, model in 138, file paths in line 139 and 140
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


def split_text(text):
    """
    Split a text into two approximately equal halves.

    Parameters:
        text (str): The text to split.

    Returns:
        tuple: First and second halves of the text.
    """
    words = text.split()
    mid = len(words) // 2
    return " ".join(words[:mid]), " ".join(words[mid:])


def process_abstracts(input_path, output_path, temperature, model_processor):
    """
    Process abstracts from the input file, generate continuations using the model,
    and write results to the output file.

    Parameters:
        input_path (str): Path to the input file containing abstracts.
        output_path (str): Path to the output file for results.
        model_processor (LlamaTextProcessor): Instance of the LlamaTextProcessor class.
    """
    processed_count = 0
    start_time = time.time()

    with open(input_path, "r") as input_file, open(output_path, "w") as output_file:
        for abstract in input_file:
            try:
                # Preprocess abstract
                abstract = abstract.strip()
                if len(abstract.split()) < 40:
                    continue

                processed_count += 1
                if processed_count % 100 == 0:
                    print(f"Processed {processed_count} abstracts")

                # Split abstract and generate response
                first_half, second_half = split_text(abstract)
                prompt = f"Continue the following academic article: \"{first_half} "
                max_tokens = len(abstract.split()) * 3

                ai_response = model_processor.generate_response(prompt, temperature=temperature, max_tokens=max_tokens)
                ai_response = " ".join(ai_response.split())
                ai_continuation = ai_response.replace(prompt, "", 1).strip()

                # Write result to file
                output_file.write(f"{first_half}\t{second_half}\t{ai_continuation}\n")

            except Exception as e:
                print(f"Error processing abstract: {e}")
                continue

    end_time = time.time()
    print(f"Processing complete. Total abstracts processed: {processed_count}")
    print(f"Time taken: {end_time - start_time:.2f} seconds")


if __name__ == "__main__":
    # Constants for model and file paths
    HF_TOKEN = ""
    MODEL_NAME = "meta-llama/Llama-3.2-3B-Instruct" # Llama-3.2-3B-Instruct
    INPUT_FILE_PATH = "../data/sample/human_abstracts_sample_2020.txt"
    OUTPUT_FILE_PATH = "../data/llama_abstracts/output_llama_instruct.tsv"
    TEMPERATURE = 0.8

    # Initialize model processor
    model_processor = LlamaTextProcessor(MODEL_NAME, HF_TOKEN)

    # Process abstracts
    process_abstracts(INPUT_FILE_PATH, OUTPUT_FILE_PATH, TEMPERATURE, model_processor)
