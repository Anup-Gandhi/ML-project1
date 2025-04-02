import { useState } from "react";

export default function DrugInteractionPredictor() {
  const [id1, setId1] = useState("");
  const [id2, setId2] = useState("");
  const [prediction, setPrediction] = useState(null);
  const [error, setError] = useState(null);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError(null);
    setPrediction(null);

    try {
      const response = await fetch("http://localhost/backend/predict.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id1, id2 }),
      });

      const data = await response.json();
      if (data.error) {
        setError(data.error);
      } else {
        setPrediction(data.prediction);
      }
    } catch (err) {
      setError("Failed to fetch prediction");
    }
  };

  return (
    <div className="p-6 max-w-md mx-auto bg-white rounded-xl shadow-md space-y-4">
      <h1 className="text-xl font-bold text-center">Drug Interaction Predictor</h1>
      <form onSubmit={handleSubmit} className="space-y-4">
        <input
          type="text"
          placeholder="Enter First DrugBank ID"
          value={id1}
          onChange={(e) => setId1(e.target.value)}
          className="w-full p-2 border rounded"
          required
        />
        <input
          type="text"
          placeholder="Enter Second DrugBank ID"
          value={id2}
          onChange={(e) => setId2(e.target.value)}
          className="w-full p-2 border rounded"
          required
        />
        <button type="submit" className="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">
          Predict
        </button>
      </form>
      {error && <p className="text-red-500 text-center">{error}</p>}
      {prediction !== null && <p className="text-green-500 text-center">Prediction: {prediction}</p>}
    </div>
  );
}