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
        } for x in range(10)]

    @classmethod
    def get_shoppers(cls):
        return [{
            'id': 'S{}'.format(random.randint(1, 30)),
            'enabled': True,
            'lat': random.uniform(-1, 1) * cls.LAT_MAX_VAL,
            'lng': random.uniform(-1, 1) * cls.LONG_MAX_VAL,
        } for x in range(10)]

    @staticmethod
    def haversine(lat1, lng1, lat2, lng2):
        return random.random() * 100

class Coverage:
    MAX_RADIUS = 10.0

    def is_covered(self, shopper_lat, shopper_lng, location_lat, location_lng)
        distance = CoverageMockData.haversine(shopper_lat, shopper_lng,
                                              location_lat, location_lng)

        return distance <= self.MAX_RADIUS

    @classmethod
    def calculate_coverage(cls):
        locations = CoverageMockData.get_locations();
        shoppers = sorted(CoverageMockData.get_shoppers(),
                          key=lambda k: k['id']);
        locations_length = len(locations)
        locations_covered = {}
        coverage = []
        current_shopper = None

        for shopper in shoppers:
            if (current_shopper)
            locations_covered = 0
            for location in locations:
                if (self.is_covered(shopper['lat'], shopper['lng'],
                                    location['lat'], location['lng'])):
                    locations_covered[shopper['id']] += 1
                    coverage.append({
                        'id': shopper['id'], 'coverage': locations_length
                    })



if __name__ == '__main__':
    Coverage.test()
