<?php

namespace Zoho\Resources;

trait TraitConfigurations {

    /**
     * @param array $configurations
     */
    protected function setDataFromArray($configurations) {
        foreach ($configurations as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            } else {
                throw new \Exception("Property $key does not exist in class " . get_class($this));
            }
        }
    }

}
