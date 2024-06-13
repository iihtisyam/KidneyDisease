import tensorflow as tf

# Load the model
model = tf.keras.models.load_model('.venv/models/my_model.h5')

# Print model summary
model.summary()

# Print model input details
input_shape = model.input_shape
print("Model input shape:", input_shape)
