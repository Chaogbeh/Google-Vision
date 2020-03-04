# Google-Vision

Google Cloud’s Vision API offers powerful pre-trained machine learning models through REST and RPC APIs. Assign labels to images and quickly classify them into millions of predefined categories. Detect objects and faces, read printed and handwritten text, and build valuable metadata into your image catalog.

These files are necessary for extracting labels from images using the Google Vision API.

In order to consume the api you will need to have your application hosted on Google's Compute engine or App engine. Billing has to be enabled for your project as well as the vision api.

Google provides a PHP client library to consume this API locally, this can be installed by running the command below in your command line

```
cd myProject
composer require google/cloud-vision
```

The file gVision.php intercepts http requests, gets the image (base64 image or a Link) from the request body and stores it in a Google bucket (I use Google storage manager to store my files). The link to this image is sent to the file: GoogleVisionManager.php. This file does the label detection.
