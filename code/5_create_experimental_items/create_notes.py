# Script, written with AI-assistance, to create notes from a text file
# fill in your OpenAI API key in line 18
# and file paths in lines 39 and 40
from openai import OpenAI


def gpt_summarizes_with_keywords(input_text):
    # Create a prompt to instruct GPT-4
    prompt = f"""
    The following text is an abstract from a scientific paper:

    {input_text}

    Summarize the abstract in keywords, separate keywords by commas.
    """

    client = OpenAI(
        api_key="sk-...",  # Replace with your OpenAI API key
    )

    chat_completion = client.chat.completions.create(
        messages=[
            {
                "role": "user",
                "content": prompt,
            }
        ],
        model="gpt-4o-mini",
    )

    # Access the response content correctly
    cleaned_abstract = chat_completion.choices[0].message.content.strip()
    # remove newlines from the response
    cleaned_abstract = cleaned_abstract.replace("\n", " ")
    cleaned_abstract = cleaned_abstract.strip()
    return cleaned_abstract


input_file = open("../data/sample/human_abstracts_50_sample_2020.txt", "r")
output_file = open("../data/experimental_items/50_sample_2020_ai_keywords.txt", "w")

c = 0
for line in input_file:
    line_cp = line
    try:
        c += 1
        print(c, end=" ", flush=True)
        line = line.strip()
        cleaned_text = gpt_summarizes_with_keywords(line)
        output_file.write(cleaned_text + "\n")
    except Exception as e:
        print("Error at abstract number: ", c)
        print(e)
        continue

input_file.close()
output_file.close()