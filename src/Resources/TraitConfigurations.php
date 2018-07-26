<?php

namespace Zoho\Resources;

trait TraitConfigurations {

    /**
     * @param array $configurations
     */
    protected function setDataFromArray($configurations) {
        foreach ($configurations as $key => $value) {
            $$fieldName = $key;
            if (property_exists($this, ${$fieldName})) {
                $this->${$fieldName} = $value;
            } else {
                throw new \Exception("Property ${$fieldName} does not exist in class " . get_class($this));
            }
        }
    }

}
