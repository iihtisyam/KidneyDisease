from flask import Flask, request, jsonify, render_template, abort
from flask_cors import CORS
import tensorflow as tf
import numpy as np
import os
import traceback
import sys
import requests  # To send HTTP requests
import datetime

app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}})  # Allow all origins for all routes

model_path = os.path.join(os.path.dirname(__file__), '.venv/models/ckdPrediction_model.h5')
try:
    model = tf.keras.models.load_model(model_path)
    print("Model loaded successfully.")
    print("Model type:", type(model))
except Exception as e:
    print(f"Error loading model: {e}")
    tb = traceback.format_exception(*sys.exc_info())
    print("".join(tb))
    exit(1)

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json(force=True)
        ic_number = data.get('ic_number')
        features = data.get('features', [])

        if not ic_number or not features:
            return jsonify({'error': 'ICNumber or features not provided'}), 400

        # Select the first 23 features
        features = features[:24]

        if len(features) < 24:
            return jsonify({'error': 'Insufficient features provided, expected 23 features'}), 400
        
        #    # Append an extra feature to make the shape (1, 24)
        # features.append(0)  # Add your desired value here

        # Convert features to float
        features = np.array(features, dtype=float).reshape(1, -1)

        prediction = model.predict(features)
        result = 'Positive for CKD' if prediction[0][0] >= 0.5 else 'Negative for CKD'

        # Current date
        current_date = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')

        # Send data to PHP script
        php_url = 'http://localhost/KidneyDisease/theme/php/medicaltest.php'  # Full URL to PHP script
        payload = {
            'ic_number': ic_number,
            'result': result,
            'date': current_date
        }
        response = requests.post(php_url, json=payload)

        if response.status_code != 200:
            print(f"Error from PHP script: {response.status_code} - {response.text}")
            return jsonify({'error': 'Failed to save result in the database'}), 500

        return jsonify({'prediction': result})
    except Exception as e:
        print(f"Error: {e}")
        tb = traceback.format_exception(*sys.exc_info())
        print("".join(tb))
        abort(500)

@app.errorhandler(404)
def page_not_found(e):
    return render_template('404.html'), 404

@app.errorhandler(500)
def internal_server_error(e):
    return render_template('500.html'), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
