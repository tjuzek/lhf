# rapid development script
# written with AI assistance
# Description: This script reads in a file containing item data and calculates the minimum time required to read the item.
# adjust paths in lines 41, 59 to 64
def get_min_time(data_file, min_time_dct):
    with open(data_file, "r") as file:
        for line in file:
            item_id, item_text, no_ratings = line.strip().split("\t")
            min_time = round(((len(item_text) * 25) + 225) / 2.5)
            min_time_dct[item_id] = min_time
    return min_time_dct


def get_incomplete_users(data_file):
    ratings_per_user = dict()
    incomplete_users = set()
    with open(data_file, "r") as file:
        for line in file:
            entry_id, username, item_ID, rating, time = line.strip().split("\t")
            if username in ratings_per_user:
                ratings_per_user[username] += 1
            else:
                ratings_per_user[username] = 1
    for user in ratings_per_user:
        if ratings_per_user[user] < 10:
            incomplete_users.add(user)
    return incomplete_users


def other_exclusions(data_file):
    too_fast_inattentive_users_no_language_user = set()
    with open(data_file, "r") as file:
        for line in file:
            item_ID, username, exclusion_category = line.strip().split("\t")
            if exclusion_category == "0" or exclusion_category == "1":
                too_fast_inattentive_users_no_language_user.add(username)
    return too_fast_inattentive_users_no_language_user


def filter_ratings(data_file, min_time_dct, exclude_users):
    output_file = open("../data/experimental_results/filtered_ratings.tsv", "w")
    with open(data_file, "r") as file:
        for line in file:
            entry_id, username, item_ID, rating, time = line.strip().split("\t")
            if item_ID not in min_time_dct:
                continue
            if int(time) < min_time_dct[item_ID]:
                continue
            if username in exclude_users:
                continue
            if rating == "7":
                rating = "0"
                line = "\t".join([entry_id, username, item_ID, rating, time]) + "\n"
            output_file.write(line)
            
      

min_time_dct = dict()
min_time_dct = get_min_time("../data/experimental_results/item_tracker.tsv", min_time_dct)
incomplete_users = get_incomplete_users("../data/experimental_results/ratings.tsv")
too_fast_inattentive_users_no_language_user = other_exclusions("../data/experimental_results/problematic_users.tsv")
exclude_users = too_fast_inattentive_users_no_language_user.union(incomplete_users)

filter_ratings("../data/experimental_results/ratings.tsv", min_time_dct, exclude_users)
