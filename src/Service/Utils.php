<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace App\Service;

use JsonException;

class Utils
{

    /**
     * @throws JsonException
     */
    public function serializedArrayToArray(array $serialized): array
    {
        $wrkArray = json_decode($serialized['data'], true, 512, JSON_THROW_ON_ERROR);

        $finalArray = [];
        array_walk($wrkArray, static function ($item) use (&$finalArray) {
            $finalArray[$item['name']] = $item['value'];
        });

        return $finalArray;
    }
}
