<?php

namespace App\Repository;
use Nette\Utils\Arrays;
use Nette\Utils\Json;
class OrderRepository
{

	public function loadData() : array
	{
		$data = [];
		foreach (Json::decode($this->getJson(), forceArrays: true) as $item) {
			$data[] = $this->flatten($item);
		}
		return $data;
	}

	public function changeStatus($id, $newStatus) : void
	{

	}
	private function getJson() : string
	{
		return '[{
        "id": 332,
        "orderNumber": "P3920-212",
        "customerOrderNumber": "mazlík",
        "createdAt": "2018-12-11T11:40:57+00:00",
        "closedAt": null,
        "status": {
            "id": "ACT",
            "name": "Active",
            "createdAt": "2024-06-04T07:56:21+00:00",
            "user": {
                "userName": "jan.omacka",
                "fullName": "Jan Omáčka"
            }
        },
        "customer": {
            "id": 23,
            "name": "Miroslav Novák"
        },
        "contract": {
            "id": 21,
            "name": "customer-sale"
        },
        "requestedDeliveryAt": "2018-12-11T11:50:00+00:00"
    },
    {
        "id": 619,
        "orderNumber": "X4920-902",
        "customerOrderNumber": "",
        "createdAt": "2019-12-02T08:40:57+00:00",
        "closedAt": null,
        "status": {
            "id": "NEW",
            "name": "New",
            "createdAt": "2022-06-04T07:56:21+00:00",
            "user": {
                "userName": "filip.houst",
                "fullName": "Filip Houšť"
            }
        },
        "customer": {
            "id": 409,
            "name": "Jan Mikulovský"
        },
        "contract": {
            "id": 321,
            "name": "customer-sale"
        },
        "requestedDeliveryAt": "2019-12-11T11:50:00+00:00"
    }]';
	}

	private static function flatten(array $data) : array
	{
		$change = True;
		while ($change) {
			$change = False;
			foreach ($data as $key => $value) {
				if (!is_array( $value)) {
					continue;
				}
				foreach ($value as $k => $v) {
					$data["{$key}.{$k}"] = $v;
				}
				unset($data[$key]);
				$change = True;
			}
		}
		return $data;
	}



}
