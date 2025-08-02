import pandas as pd
from statsmodels.formula.api import mixedlm

# Load the data
file_path = "../data/experimental_results/filtered_ratings.tsv"
data = pd.read_csv(file_path, sep="\t")

# Add headers to the data
data.columns = ["id", "user", "item", "rating", "ms"]

# Preview the data
print(data.head())

# Fit the Generalized Linear Mixed Model with random effects for both item and user
model = mixedlm("rating ~ 1", data, groups="item", re_formula="~1", vc_formula={"user": "0 + C(user)"})
result = model.fit(method="lbfgs", maxiter=1000)

print(result.summary())


