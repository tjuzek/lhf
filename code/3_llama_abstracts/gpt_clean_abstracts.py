# rapid development script to clean up the output of the Llama model
# in parts AI-assisted code
# insert the OpenAI API key in the code, line 18, insert file paths in the code, lines 38 and 39
from openai import OpenAI


def gpt_removes_commentary(input_text):
    # Create a prompt to instruct GPT-4
    prompt = f"""
    The following text is meant to be a continuation of a scientific abstract. In some of the continuations, however, the AI finishes the abstract and continues with commentary. Please detect potential switches, and remove any commentary:

    {input_text}

    Output only the cleaned abstract. If the entire text is commentary, output an empty string.
    """

    client = OpenAI(
        api_key="",
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
    return cleaned_abstract


input_file = open("../data/llama_abstracts/output_llama_instruct_repetitions_removed.tsv", "r")
output_file = open("../data/llama_abstracts/output_llama_instruct_repetitions_commentry_removed.tsv", "w")

c = 0
for line in input_file:
    line_cp = line
    try:
        c += 1
        print(c, end=" ", flush=True)
        line = line.strip()
        line = line.split("\t")
        text = line[2]
        cleaned_text = gpt_removes_commentary(text)
        line[2] = cleaned_text
        output_file.write("\t".join(line) + "\n")
    except Exception as e:
        print("Error at abstract number: ", c)
        print(e)
        output_file.write(line_cp)
        continue

input_file.close()
output_file.close()
