<?php

/**
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Geocoder\Dumper;

use Geocoder\Model\Address;

/**
 * @author Jan Sorgalla <jsorgalla@googlemail.com>
 */
class GeoJson implements Dumper
{
    /**
     * {@inheritDoc}
     */
    public function dump(Address $address)
    {
        return $this->build($address);
    }

    public function build(Address $address)
    {
        $properties = array_filter($address->toArray(), function ($value) {
            return !empty($value);
        });

        unset(
            $properties['latitude'],
            $properties['longitude'],
            $properties['bounds']
        );

        if (array_key_exists('adminLevels', $properties)) {
            $levels = [];
            foreach ($properties['adminLevels'] as $k => $a) {
                $levels[] = array_merge($a, ['level' => $k]);
            }
            $properties['adminLevels'] = $levels;
        }

        if (0 === count($properties)) {
            $properties = null;
        }

        $data = [
          'type' => 'Feature',
          'geometry' => [
            'type'          => 'Point',
            'coordinates'   => [ $address->getLongitude(), $address->getLatitude() ]
          ],
          'properties' => $properties,
        ];

        if (null !== $bounds = $address->getBounds()) {
            if ($bounds->isDefined()) {
                $data['bounds'] = $bounds->toArray();
            }
        }

        return $data;
    }

}

