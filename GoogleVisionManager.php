<?php
require SITE_ROOT . '/vendor/autoload.php';


use Google\Cloud\Vision\V1\ImageAnnotatorClient;

class GoogleVisionManager
{
    private $whitelist;

    function __construct()
    {
        $this->whitelist = file(SITE_ROOT . '/cloud-vision-tag-whitelist', FILE_IGNORE_NEW_LINES);
    }

    function getImageLabels(string $imageLocation) {
        # instantiates a client
        $imageAnnotator = new ImageAnnotatorClient();

        # performs label detection on the image file
        $response = $imageAnnotator->labelDetection($imageLocation);
        $labels = $response->getLabelAnnotations();

        // If no labels were acquired then use an empty list
        if(!$labels) {
            $labels = [];
        }

        // Apply the whitelist of labels
        $filteredLabels = [];

        foreach ($labels as $label) {
            // Keep the label if its in the whitelist
            if(in_array($label, $this->whitelist)) {
                array_push($filteredLabels, $label);
            }
        }

        $labelDescriptions = [];

        foreach ($labels as $label) {
            array_push($labelDescriptions, $label->getDescription());
        }

        return $labelDescriptions;
    }

    public static function getInstance() {
        static $instance = null;
        if($instance === null) {
            $instance = new GoogleVisionManager();
        }
        return $instance;
    }
}
