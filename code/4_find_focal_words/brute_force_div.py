# rapid development code, refactored with ChatGPT for best practices
# fill in the file paths in lines 142, 143, and 144, and run the script
import string
from collections import Counter
from scipy.stats import chi2_contingency
from typing import Dict, Tuple, List


def count_words(lines: List[str]) -> Tuple[Dict[str, int], int]:
    """
    Count word frequencies in a list of text lines.
    Lines are stripped of whitespace, lowercased, and stripped of punctuation.
    
    Returns:
        A tuple of:
          - A dictionary mapping words to their counts.
          - The total number of words.
    """
    translator = str.maketrans("", "", string.punctuation)
    word_counter = Counter()
    total_words = 0

    for line in lines:
        cleaned_line = line.strip().lower().translate(translator)
        words = cleaned_line.split()
        word_counter.update(words)
        total_words += len(words)

    return dict(word_counter), total_words


def chi_sq_test_for_significance(
    word: str,
    freq_2020_abs: Dict[str, int],
    freq_2024_abs: Dict[str, int],
    total_words_2020: int,
    total_words_2024: int,
    min_opm_2020: float,
) -> bool:
    """
    Performs a chi-square test on the 2x2 contingency table for a given word,
    using absolute frequencies from 2020 and 2024.

    If the word did not occur in 2020, it uses min_opm_2020 as the count.
    Returns True if the p-value is less than 0.05.
    """
    if word in freq_2020_abs:
        count_2020 = freq_2020_abs[word]
        count_2024 = freq_2024_abs[word]
        data = [
            [count_2020, total_words_2020 - count_2020],
            [count_2024, total_words_2024 - count_2024],
        ]
    else:
        count_2024 = freq_2024_abs[word]
        data = [
            [min_opm_2020, total_words_2020 - 1],
            [count_2024, total_words_2024 - count_2024],
        ]

    _, p_value, _, _ = chi2_contingency(data)
    return p_value < 0.05


def load_text_file(file_path: str) -> List[str]:
    """
    Reads a text file and returns a list of its lines.
    """
    with open(file_path, "r", encoding="utf-8") as f:
        return f.readlines()


def normalize_frequencies(freq: Dict[str, int], total_words: int, factor: int = 1_000_000) -> Dict[str, float]:
    """
    Converts word counts to occurrences per 'factor' words.
    """
    return {word: (count / total_words) * factor for word, count in freq.items()}


def filter_frequencies(freq: Dict[str, float], threshold: float = 1.0) -> Dict[str, float]:
    """
    Returns a new dictionary containing only those words with a frequency
    (per million words) greater than or equal to the threshold.
    """
    return {word: opm for word, opm in freq.items() if opm >= threshold}


def calculate_frequency_difference(
    freq_2020_norm: Dict[str, float],
    freq_2024_norm: Dict[str, float],
    min_opm_2020: float,
) -> Dict[str, float]:
    """
    Calculates the percentage change in normalized frequency from 2020 to 2024
    for each word that appears in the 2024 dataset.
    """
    diff = {}
    for word, opm_2024 in freq_2024_norm.items():
        if word in freq_2020_norm:
            opm_2020 = freq_2020_norm[word]
            change = ((opm_2024 - opm_2020) / opm_2020) * 100
        else:
            change = ((opm_2024 - min_opm_2020) / min_opm_2020) * 100
        diff[word] = change
    return diff


def write_results(
    output_file_path: str,
    sorted_freq_diff: Dict[str, float],
    freq_2020_norm: Dict[str, float],
    freq_2024_norm: Dict[str, float],
    freq_2020_abs: Dict[str, int],
    freq_2024_abs: Dict[str, int],
    total_words_2020: int,
    total_words_2024: int,
    min_opm_2020: float,
) -> None:
    """
    Writes the results to a TSV file with columns for word, percentage change,
    normalized frequencies for 2020 and 2024, and whether the change is significant.
    """
    header = "word\tchange (%)\topm 2020\topm 2024\tsignificant\n"
    with open(output_file_path, "w", encoding="utf-8") as out_file:
        out_file.write(header)
        for word, change in sorted_freq_diff.items():
            significant = chi_sq_test_for_significance(
                word,
                freq_2020_abs,
                freq_2024_abs,
                total_words_2020,
                total_words_2024,
                min_opm_2020,
            )
            opm_2020 = freq_2020_norm.get(word, min_opm_2020)
            opm_2024 = freq_2024_norm[word]
            out_file.write(f"{word}\t{change:.2f}\t{opm_2020:.2f}\t{opm_2024:.2f}\t{significant}\n")


def main():
    # File paths
    file_path_2020 = "" # enter the path to your base/2020 file
    file_path_2024 = "" # enter the path to your instruct/2024 file
    output_file_path = "" # enter the path to your output file

    # Load text files
    text_2020 = load_text_file(file_path_2020)
    text_2024 = load_text_file(file_path_2024)

    # Count words and get total counts (absolute frequencies)
    freq_2020_abs, total_words_2020 = count_words(text_2020)
    freq_2024_abs, total_words_2024 = count_words(text_2024)

    # Create normalized frequency dictionaries (occurrences per million words)
    freq_2020_norm = normalize_frequencies(freq_2020_abs, total_words_2020)
    freq_2024_norm = normalize_frequencies(freq_2024_abs, total_words_2024)

    # Filter 2024 frequencies below threshold (less than 1 opm)
    freq_2024_norm = filter_frequencies(freq_2024_norm, threshold=1.0)

    # Minimum occurrences per million words in 2020 for a word not present
    min_opm_2020 = 1_000_000 / total_words_2020

    # Calculate frequency differences (percentage change)
    freq_diff = calculate_frequency_difference(freq_2020_norm, freq_2024_norm, min_opm_2020)

    # Sort the differences in descending order
    sorted_freq_diff = dict(sorted(freq_diff.items(), key=lambda item: item[1], reverse=True))

    # Write the results to file
    write_results(
        output_file_path,
        sorted_freq_diff,
        freq_2020_norm,
        freq_2024_norm,
        freq_2020_abs,
        freq_2024_abs,
        total_words_2020,
        total_words_2024,
        min_opm_2020,
    )

    print(total_words_2024)


if __name__ == "__main__":
    main()
