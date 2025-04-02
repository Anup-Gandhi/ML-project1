import sys
import pandas as pd
import joblib

# Load dataset and model
csv_file = "drugbank_with_descriptors.csv"
model_file = "best_random_forest.pkl"

df = pd.read_csv(csv_file)
model = joblib.load(model_file)

def get_features(id_1, id_2):
    row1 = df[df["DrugBank_ID"] == id_1]
    row2 = df[df["DrugBank_ID"] == id_2]

    if row1.empty or row2.empty:
        return None

    features = {
        'MolWt_X1': row1['MolWt'].values[0], 
        'LogP_X1': row1['LogP'].values[0], 
        'NumHDonors_X1': row1['NumHDonors'].values[0], 
        'NumHAcceptors_X1': row1['NumHAcceptors'].values[0], 
        'TPSA_X1': row1['TPSA'].values[0], 
        'MolWt_X2': row2['MolWt'].values[0], 
        'LogP_X2': row2['LogP'].values[0], 
        'NumHDonors_X2': row2['NumHDonors'].values[0], 
        'NumHAcceptors_X2': row2['NumHAcceptors'].values[0], 
        'TPSA_X2': row2['TPSA'].values[0]
    }

    return pd.DataFrame([features])

# Get inputs
id_1 = sys.argv[1]
id_2 = sys.argv[2]

features_df = get_features(id_1, id_2)

if features_df is None:
    print("Error: One or both DrugBank IDs not found")
    sys.exit(1)

# Make prediction
prediction = model.predict(features_df)
print(prediction[0])
