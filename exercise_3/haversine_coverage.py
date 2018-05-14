import itertools
import string
import random

class CoverageMockData:
    LAT_MAX_VAL = 90
    LONG_MAX_VAL = 180

    @classmethod
    def get_locations(cls):
        area_id = itertools.count(1)

        return [{
            'id': next(area_id),
            'zip_code': ''.join(random.choice(string.digits) for x in range(5)),
            'lat': random.uniform(-1, 1) * cls.LAT_MAX_VAL,
            'lng': random.uniform(-1, 1) * cls.LONG_MAX_VAL,
        } for x in range(150)]

    @classmethod
    def get_shoppers(cls):
        return [{
            'id': 'S{}'.format(random.randint(1, 30)),
            'enabled': True,
            'lat': random.uniform(-1, 1) * cls.LAT_MAX_VAL,
            'lng': random.uniform(-1, 1) * cls.LONG_MAX_VAL,
        } for x in range(1000)]

    @staticmethod
    def haversine(lat1, lng1, lat2, lng2):
        return random.random() * 100

class Coverage:
    MAX_RADIUS = 10.0

    @classmethod
    def is_covered(cls, shopper_lat, shopper_lng, location_lat, location_lng):
        distance = CoverageMockData.haversine(shopper_lat, shopper_lng,
                                              location_lat, location_lng)

        return distance <= cls.MAX_RADIUS

    @classmethod
    def calculate_coverage(cls):
        locations = CoverageMockData.get_locations()
        """shoppers = CoverageMockData.get_shoppers()"""
        shoppers = sorted(CoverageMockData.get_shoppers(),
                          key=lambda k: k['id']);
        locations_length = len(locations)
        locations_per_shopper = {}
        current_shopper = None

        for shopper in shoppers:
            if shopper['id'] != current_shopper:
                current_shopper = shopper['id']
                temp_locations = locations
            if shopper['id'] not in locations_per_shopper:
                locations_per_shopper[shopper['id']] = 0

            for index, location in reversed(temp_locations):
                if (cls.is_covered(shopper['lat'], shopper['lng'],
                                    location['lat'], location['lng'])):
                    locations_per_shopper[shopper['id']] += 1
                    temp_locations.pop(index)


        print(sorted([{
            'id': shopper_id,
            'coverage': round((locations / locations_length) * 100, 2),
        } for shopper_id, locations in locations_per_shopper.items()],
        key=lambda k: k['coverage'], reverse=True))


if __name__ == '__main__':
    Coverage.calculate_coverage()
