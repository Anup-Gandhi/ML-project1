import sys
import json
import joblib
import pandas as pd

# Load the trained model
model_file = "best_random_forest.pkl"
model = joblib.load(model_file)

# Read input data from PHP
input_data = json.loads(sys.argv[1])
features_df = pd.DataFrame([input_data])

# Make prediction
prediction = model.predict(features_df)

# Print result for PHP to read
print(prediction[0])
