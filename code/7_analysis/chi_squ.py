import scipy.stats as stats

chi2, p = stats.chisquare([2117, 1922], [2019.5, 2019.5])

print(f"Chi-square statistic: {chi2}")
print(f"P-value: {p}")
