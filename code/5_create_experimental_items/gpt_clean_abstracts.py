# rapid development script
# written with AI assistance
# fill in API key in line 18 and file paths in lines 42 and 432
from openai import OpenAI


def gpt_removes_commentary(input_text):
    # Create a prompt to instruct GPT-4
    prompt = f"""
    The following text contains a scientific abstract, but sometimes further text:

    {input_text}

    Please remove any irrelevant text, which can include titles, incomplete sentences, even a comment that an abstract is to follow ("Abstract: "). Output only the cleaned abstract.
    """

    client = OpenAI(
        api_key="sk-...", # Replace with your actual API key
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
    cleaned_abstract = cleaned_abstract.replace("\t", " ")
    cleaned_abstract = cleaned_abstract.replace("\"", "")
    # remove double spaces
    cleaned_abstract = " ".join(cleaned_abstract.split())
    return cleaned_abstract


input_file =  open("../data/experimental_items/50_sample_2020_ai_abstracts.tsv", "r") 
output_file = open("../data/experimental_items/50_sample_2020_ai_abstracts_cleaned.tsv", "w")

c = 0
for line in input_file:
    line_cp = line
    line = line.strip()
    abstracts = line.split("\t")
    abstracts_cleaned = []
    for abstract in abstracts:
        print(c, end=" ", flush=True)
        c += 1
        try:
            cleaned_abstract = gpt_removes_commentary(abstract)
            abstracts_cleaned.append(cleaned_abstract)
        except Exception as e:
            print("Error at abstract number: ", c)
            print(e)
            output_file.write(line_cp)
            continue
    output_file.write("\t".join(abstracts_cleaned) + "\n")

input_file.close()
output_file.close()