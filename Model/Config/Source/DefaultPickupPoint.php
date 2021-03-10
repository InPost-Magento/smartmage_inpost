<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class DefaultPickupPoint
 */
class DefaultPickupPoint implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray() : array
    {
        //todo create method that return value from https://api-shipx-pl.easypack24.net/v1/dispatch_points
        $exampleJsonValue = '{
    "href": "https://api-shipx-pl.easypack24.net/v1/organizations/1/dispatch_points",
    "count": 15,
    "per_page": 30,
    "page": 1,
    "items": [
        {
        "href": "https://api-shipx-pl.easypack24.net/v1/dispatch_points/1",
  "id": 1,
  "name": "My dispatch point",
  "office_hours": "8-16",
  "phone": "777888999",
  "email": null,
  "comments": null,
  "status": "created",
  "address": {
            "id": 230,
    "street": "Długa",
    "building_number": "24",
    "city": "Krakow",
    "post_code": "30-624",
    "country_code": "PL"
  }
  },
          {
        "href": "https://api-shipx-pl.easypack24.net/v1/dispatch_points/2",
  "id": 2,
  "name": "My dispatch point 2",
  "office_hours": "8-16",
  "phone": "777888999",
  "email": null,
  "comments": null,
  "status": "created",
  "address": {
            "id": 230,
    "street": "Krótka",
    "building_number": "10",
    "city": "Warszawa",
    "post_code": "10-100",
    "country_code": "PL"
  }
}]}';
        $exampleJsonValue = json_decode($exampleJsonValue);
        $data = [];
        foreach ($exampleJsonValue->items as $item) {
            $data[] = [
                'value' => $item->id,
                'label' => $item->name . ' ' . $item->address->city . ' ' . $item->address->post_code .
                    ' ' . $item->address->street . ' ' . $item->address->building_number
            ];
        }
        return $data;
    }
}
