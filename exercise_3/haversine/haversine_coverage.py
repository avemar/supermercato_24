import itertools
import string
import random

class CoverageMockData:
    LAT_MAX_VAL = 90
    LONG_MAX_VAL = 180

    @classmethod
    def get_locations(cls):
        """
        Return random locations
        """
        area_id = itertools.count(1)

        return [{
            'id': next(area_id),
            'zip_code': ''.join(random.choice(string.digits) for x in range(5)),
            'lat': random.uniform(-1, 1) * cls.LAT_MAX_VAL,
            'lng': random.uniform(-1, 1) * cls.LONG_MAX_VAL,
        } for x in range(5000)]

    @classmethod
    def get_enabled_shoppers(cls):
        """
        Return random enabled shoppers
        """
        shoppers = [{
            'id': 'S{}'.format(random.randint(1, 30)),
            'enabled': True if random.random() < 0.5 else False,
            'lat': random.uniform(-1, 1) * cls.LAT_MAX_VAL,
            'lng': random.uniform(-1, 1) * cls.LONG_MAX_VAL,
        } for x in range(1000)]

        return list(filter(lambda d: d['enabled'], shoppers))

    @staticmethod
    def haversine(lat1, lng1, lat2, lng2):
        """
        Calculate the distance between two geographical points
        """
        return random.random() * 500


class Coverage:
    MAX_RADIUS = 10.0
    data_provider = CoverageMockData

    @classmethod
    def calculate_coverage(cls, locations, shoppers):
        """
        Compute the location coverage percentage for every shopper
        """
        shoppers = sorted(shoppers, key=lambda k: k['id'])

        if not locations or not shoppers:
            return []

        locations_length = len(locations)
        locations_per_shopper = {}
        current_shopper = None

        for shopper in shoppers:
            if shopper['id'] != current_shopper:
                current_shopper = shopper['id']
                locations_per_shopper[shopper['id']] = 0
                temp_locations = locations.copy()

            for i in range(len(temp_locations)-1, -1, -1):
                if (cls._is_covered(shopper['lat'], shopper['lng'],
                                    temp_locations[i]['lat'],
                                    temp_locations[i]['lng'])):
                    locations_per_shopper[shopper['id']] += 1
                    del temp_locations[i]

        coverage = [{
            'shopper_id': shopper_id,
            'coverage': round((locations / locations_length) * 100, 2),
        } for shopper_id, locations in locations_per_shopper.items()]

        return sorted(coverage, key=lambda k: k['coverage'], reverse=True)

    @classmethod
    def _is_covered(cls, shopper_lat, shopper_lng, location_lat, location_lng):
        """
        Check if a location is covered by a shopper
        """
        distance = cls.data_provider.haversine(shopper_lat, shopper_lng,
                                               location_lat, location_lng)

        return distance <= cls.MAX_RADIUS


if __name__ == '__main__':
    locations =CoverageMockData.get_locations()
    shoppers = CoverageMockData.get_enabled_shoppers()
    Coverage.calculate_coverage(locations, shoppers)
