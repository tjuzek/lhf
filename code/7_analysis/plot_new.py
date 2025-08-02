# rapid development script to plot our results
# written with AI assistance
import pandas as pd
import matplotlib.pyplot as plt

# -------------------------
# Data and code for bar plot (subplot 0)
# -------------------------

# Data provided by the user for bar plot
data = {
    'item_id': [22, 11, 14, 13, 6, 2, 20, 26, 19, 8, 25, 4, 17, 24, 18, 5, 10, 7, 23, 16, 28, 12, 29, 30, 21, 27, 1, 3, 9, 15],
    'preference_percentage': [71.53, 68.99, 63.77, 63.77, 61.54, 58.78, 57.93, 57.03, 56.69, 56.06, 54.61, 53.96, 53.33, 53.33, 52.21, 52.14, 51.97, 50.36, 50.00, 49.31, 48.85, 48.57, 48.15, 48.06, 45.31, 42.40, 41.48, 41.18, 37.76, 33.58]
}

# Create a DataFrame
df = pd.DataFrame(data)

# Calculate the non-preferred percentage
df['non_preference_percentage'] = 100 - df['preference_percentage']

# Ensure the DataFrame is sorted by preference_percentage in descending order
df_sorted = df.sort_values(by='preference_percentage', ascending=False)

# -------------------------
# Data for scatter plot (subplot 1)
# -------------------------

# Data for bar plot in the right figure are provided in the initial code snippet.
categories = ['robust dispref. low LP-Score', 'marginal dispref. low LP-Score', 'tie', 'marginal pref. high LP-Score', 'robust pref. high LP-Score']
number_of_items_by_category = [4, 7, 1, 12, 6]
colors = ['#F9665E', '#FF9797', '#EEF1E6', '#AFC7D0', '#799FCB']

categories.reverse()
number_of_items_by_category.reverse()
colors.reverse()

# Data for scatter plot
lp_delta = [9.81, 10.436, 10.8, 3.534, 3.774, 4.348, 2.7, 4.68, 3.714, 3.698, 3.27,
            2.656, 3.536, 3.932, 10.95, 3.206, 3.276, 4.234, 3.704, 3.544, 10.706,
            4.506, 3.134, 10.308, 4.548, 11.352, 10.712, 3.108, 3.236, 5.268]
high_lp_preference = [41.48, 58.78, 41.18, 53.96, 52.14, 61.54, 50.36, 56.06, 37.76, 51.97,
                      68.99, 48.57, 63.77, 63.77, 33.58, 49.31, 53.33, 52.21, 56.69, 57.93,
                      45.31, 71.53, 50, 53.33, 54.61, 57.03, 42.4, 48.85, 48.15, 48.06]

# Indices of items to highlight
highlight_indices = [0, 1, 2, 14, 20, 23, 25, 26]

# -------------------------
# Create subplots
# -------------------------

fig, axes = plt.subplots(1, 2, figsize=(16, 6))

# -------------------------
# Horizontal Bar Plot on axes[0]
# -------------------------

# Plot non-preference percentages first
axes[0].barh(df_sorted['item_id'].astype(str),
             df_sorted['non_preference_percentage'],
             color='sandybrown',
             label='Low-LP-Score preferred')

# Plot preference percentages on top using the left parameter to stack them
axes[0].barh(df_sorted['item_id'].astype(str),
             df_sorted['preference_percentage'],
             left=df_sorted['non_preference_percentage'],
             color='mediumpurple',
             label='High-LP-Score preferred')

# Add a vertical red dashed line at x=50
axes[0].axvline(x=50, color='red', linestyle='--', label='50% Line')

# Add labels, title, and legend
axes[0].set_xlabel('Percentage', fontsize=16)
axes[0].set_ylabel('Item ID', fontsize=16)
axes[0].set_title('Low-LP-Score vs. High-LP-Score preferred', fontsize=16)
axes[0].legend(loc='upper right', fontsize=12)

# Invert y-axis so that the highest preference is at the top
axes[0].invert_yaxis()

# Adjust tick label sizes
axes[0].tick_params(axis='x', labelsize=12)
axes[0].tick_params(axis='y', labelsize=11)

# -------------------------
# Scatter Plot on axes[1]
# -------------------------

# Plot each scatter point and highlight selected indices
for i, (x, y) in enumerate(zip(lp_delta, high_lp_preference)):
    if i in highlight_indices:
        axes[1].scatter(x, y, color='purple', edgecolor='black', s=100, alpha=0.8,
                        label="Contain 'nuanced_ADJ'" if i == highlight_indices[0] else "")
    else:
        axes[1].scatter(x, y, color='blue', edgecolor='black', s=100, alpha=0.8)

# Add horizontal lines for thresholds
axes[1].axhline(y=50, color='red', linestyle='--', linewidth=1.5, zorder=0, label='50% Threshold')
axes[1].axhline(y=52.4, color='darkblue', linestyle='-.', linewidth=1.2, zorder=0, label='Grand Mean of All Items')

# Set title and labels for scatter plot
axes[1].set_title("LP-Score Delta vs High LP-Score Variant Preferred (%)", fontsize=14, weight='bold')
axes[1].set_xlabel("LP-Score Delta", fontsize=14, weight='bold')
axes[1].set_ylabel("High LP-Score Variant Preferred (%)", fontsize=14, weight='bold')
axes[1].set_ylim(30, 70)

# Add grid lines and adjust tick parameters
axes[1].grid(True, which='both', linestyle='--', linewidth=0.5, alpha=0.7)
axes[1].tick_params(axis='x', labelsize=12)
axes[1].tick_params(axis='y', labelsize=12)

# Show legend for scatter plot
axes[1].legend(fontsize=12, loc='best')

# -------------------------
# Adjust layout and save the combined plot
# -------------------------

plt.tight_layout()
plt.savefig("../data/experimental_results/plot_new.png", dpi=300, bbox_inches='tight')
# plt.show()
plt.close()

